<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PatientAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'zipcode', 'street', 'number', 'complement', 'neighborhood', 'city', 'state'
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
