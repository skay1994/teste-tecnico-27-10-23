<?php

namespace App\Services;

use App\Http\Requests\StorePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;

class PatientService
{
    public function store(StorePatientRequest $request): JsonResponse
    {
        $patient = Patient::create($request->safe()->except('address', 'photo'));

        $request->file('photo')->store();

        if ($request->has('address')) {
            $addresses = $request->get('address');
            $patient->addresses()->createMany($addresses);
        }

        return response()->json(new PatientResource($patient), 201);
    }
}
