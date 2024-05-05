<?php

namespace App\Models;

use App\Facades\ApplePush;
use App\Models\PassDetails\PassDetails;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use PKPass\PKPass;
use PKPass\PKPassException;

class Pass extends Model
{
    use HasFactory;

    protected $fillable = [
        'pass_type_id',
        'serial_number',
        'authentication_token',
        'last_requested_at',
        'passdetails_id',
        'passdetails_type',
    ];

    public function __construct(array $attributes = [])
    {
        $attributes = $attributes + [
            'serial_number' => Str::uuid()->toString(),
            'authentication_token' => Str::random(30),
        ];

        parent::__construct($attributes);
    }

    protected function casts()
    {
        return [
            'last_requested_at' => 'timestamp',
        ];
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'registrations')
            ->withTimestamps();
    }

    public function details(): MorphTo
    {
        return $this->morphTo();
    }

    public function downloadLink(): string
    {
        $token = Str::random(30);

        Cache::put('pass-download:'.$token, $this->id, now()->addMinutes(60));

        return route('pass.download', [
            'token' => $token,
        ]);
    }

    public function getCertificatePath(): string
    {
        return resource_path("passes/{$this->pass_type_id}/{$this->pass_type_id}.p12");
    }

    public function getResourceFiles(): array
    {
        return glob(resource_path("passes/{$this->pass_type_id}/resources/").'*');
    }

    public function pushToDevices(): void
    {
        foreach ($this->devices as $device) {
            ApplePush::sendPass($this, $device);
        }
    }

    /**
     * @throws PKPassException
     */
    public function generate(bool $output = false): string
    {
        /** @var PassDetails $details */
        $details = $this->details;

        $pass = new PKPass($this->getCertificatePath(), config('passkit.certificate_password'));
        $data = [
            'formatVersion' => 1,
            'teamIdentifier' => config('passkit.team_identifier'),
            'passTypeIdentifier' => $this->pass_type_id,
            'serialNumber' => $this->serial_number,
            'webServiceURL' => route('passkit.webServiceURL'),
            'authenticationToken' => $this->authentication_token,
        ] + $details->getJsonData();

        $pass->setData($data);

        foreach ($this->getResourceFiles() as $file) {
            $pass->addFile($file);
        }

        return $pass->create($output);
    }
}
