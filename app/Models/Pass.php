<?php

namespace App\Models;

use App\Models\PassDetails\PassDetails;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use PKPass\PKPass;
use PKPass\PKPassException;

class Pass extends Model
{
    protected $fillable = [
        'pass_type_id',
        'serial_number',
        'authentication_token',
        'last_requested_at',
        'passdetails_id',
        'passdetails_type',
    ];

    protected function casts()
    {
        return [
            'last_requested_at' => 'timestamp',
        ];
    }

    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(Device::class, 'registrations');
    }

    public function details(): MorphTo
    {
        return $this->morphTo();
    }

    public function getCertificatePath(): string
    {
        return resource_path("passes/{$this->pass_type_id}/certificate.p12");
    }

    public function getResourceFiles(): array
    {
        return glob(resource_path("passes/{$this->pass_type_id}/resources/").'*');
    }

    /**
     * @throws PKPassException
     */
    public function generatePass(): string
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

        return $pass->create();
    }
}
