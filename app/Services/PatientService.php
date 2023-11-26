<?php

namespace App\Services;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use Arr;
use Elasticsearch\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Storage;

class PatientService
{

    public function index(Request $request)
    {
        $perPage = $request->get('perPage', 10);

        if ($request->has('s')) {
            $client = app(Client::class);

            $search = $client->search([
                'index' => 'patients',
                "from" => $request->get('from'),
                "size" => $perPage,
                'body' => [
                    'query' => [
                        'multi_match' => [
                            'fields' => ['document', 'cns', 'name^2', 'mother_name^1', 'phone'],
                            'query' => $request->get('s'),
                        ],
                    ],
                ],
            ]);

            if (empty($search['hits']['hits'])) {
                return new PatientCollection(Patient::paginate($perPage));
            }

            $ids = Arr::pluck($search['hits']['hits'], '_id');
            $patients = Patient::whereIn('id', $ids)->get()
                ->sortBy(fn($patient) => array_search($patient->getKey(), $ids));

            $paginator = new LengthAwarePaginator(
                PatientResource::collection($patients),
                $search['hits']['total']['value'], $perPage
            );

            return new PatientCollection($paginator);
        }

        return new PatientCollection(Patient::paginate($perPage));
    }

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

        if (Storage::fileExists($patient->photo)) {
            Storage::delete($patient->photo);
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

    public function destroy(Patient $patient): void
    {
        $patient->addresses()->delete();
        $patient->delete();
    }
}
