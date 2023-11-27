<?php

namespace App\Http\Resources;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Storage;

/** @mixin Patient */
class PatientListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'document' => $this->document,
            'cns' => $this->cns,
            'name' => $this->name,
            'mother_name' => $this->mother_name,
            'birthdate' => $this->birthdate->format('d/m/Y') .' '. $this->birthdate->longAbsoluteDiffForHumans(),
            'phone' => $this->phone,
        ];
    }
}
