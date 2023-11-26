<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientCollection;
use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PatientController extends Controller
{
    public function __construct(
        readonly protected PatientService $service
    )
    {
    }

    public function index(Request $request): PatientCollection
    {
        return $this->service->index($request);
    }

    public function store(StorePatientRequest $request): JsonResponse
    {
        return $this->service->store($request);
    }

    public function update(UpdatePatientRequest $request, Patient $patient): JsonResponse
    {
        return $this->service->update($patient, $request);
    }

    public function destroy(Patient $patient): Response
    {
        $this->service->destroy($patient);
        return response()->noContent();
    }
}
