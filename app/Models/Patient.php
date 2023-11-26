<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'document', 'cns', 'name', 'mother_name', 'birthdate', 'photo', 'phone'
    ];

    protected $casts = [
        'birthdate' => 'date'
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(PatientAddress::class);
    }
}
