<?php

namespace App\Http\Resources;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Patient */
class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'document' => $this->document,
            'cns' => $this->cns,
            'photo' => $this->photo,
            'name' => $this->name,
            'mother_name' => $this->mother_name,
            'birthdate' => $this->birthdate,
            'phone' => $this->phone,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'addresses' => PatientAddressResource::collection($this->addresses),
        ];
    }
}
