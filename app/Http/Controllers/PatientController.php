<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Http\JsonResponse;

class PatientController extends Controller
{
    public function __construct(
        readonly protected PatientService $service
    )
    {
    }

    public function index()
    {
        //
    }

    public function store(StorePatientRequest $request): JsonResponse
    {
        return $this->service->store($request);
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        //
    }

    public function destroy(Patient $patient)
    {
        //
    }
}
