<?php

namespace App\Http\Resources;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Storage;

/** @mixin Patient */
class PatientResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'document' => $this->document,
            'cns' => $this->cns,
            'photo' => Str::startsWith($this->photo, 'http') ? $this->photo : Storage::url($this->photo),
            'name' => $this->name,
            'mother_name' => $this->mother_name,
            'birthdate' => $this->birthdate,
            'phone' => $this->phone,
            'addresses' => PatientAddressResource::collection($this->whenLoaded('addresses')),
        ];
    }
}
