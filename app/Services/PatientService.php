<?php

namespace App\Services;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;

class PatientService
{
    public function store(StorePatientRequest $request): JsonResponse
    {
        $patient = Patient::create($request->safe()->except('address', 'photo'));

        $photo = $request->file('photo')->store();

        $patient->update([
            'photo' => $photo
        ]);

        if ($request->has('address')) {
            $addresses = $request->get('address');
            $patient->addresses()->createMany($addresses);
        }

        return response()->json(new PatientResource($patient), 201);
    }

    public function update(Patient $patient, UpdatePatientRequest $request): JsonResponse
    {
        $patient->fill($request->safe()->except('address', 'photo'));

        if(\Storage::fileExists($patient->photo)) {
            \Storage::delete($patient->photo);
        }

        $photo = $request->file('photo')->store();

        $patient->photo = $photo;
        $patient->save();

        if ($request->has('address')) {
            $patient->addresses()->delete();
            $addresses = $request->get('address');
            $patient->addresses()->createMany($addresses);
        }

        return response()->json(new PatientResource($patient));
    }
}
