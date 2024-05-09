<?php

namespace App\Models\CineStarCard;

use App\Exceptions\CineStarCard\CinemaCoordinateUpdateException;
use App\Facades\Geocoding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cinema extends Model
{
    use HasFactory;

    protected $table = 'cinestarcard_cinemas';

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
    ];

    /**
     * @throws CinemaCoordinateUpdateException
     */
    public function updateCoordinates(): void
    {
        $query = $this->name.' Deutschland';
        $address = Geocoding::data($query);

        if (! $address) {
            throw new CinemaCoordinateUpdateException('No unique address could be determined');
        }

        $this->latitude = $address->coordinates->latitude;
        $this->longitude = $address->coordinates->longitude;
        $this->save();
    }
}
