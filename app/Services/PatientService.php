<?php

namespace App\Services;

use App\Http\Requests\ImportCSVRequest;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientCollection;
use App\Http\Resources\PatientListResource;
use App\Http\Resources\PatientResource;
use App\Jobs\ImportPatientJob;
use App\Models\Patient;
use Arr;
use Cache;
use Elasticsearch\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Rap2hpoutre\FastExcel\FastExcel;
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
                PatientListResource::collection($patients),
                $search['hits']['total']['value'], $perPage
            );

            return new PatientCollection($paginator);
        }

        return new PatientCollection(Patient::paginate($perPage));
    }

    public function store(StorePatientRequest $request): JsonResponse
    {
        $patient = Patient::create($request->safe()->except('address', 'photo'));

        $photo = $request->file('photo')->store('patients', 'public');

        $patient->update([
            'photo' => $photo
        ]);

        if ($request->has('address')) {
            $addresses = $request->get('address');
            $patient->addresses()->createMany($addresses);
        }

        Cache::forget('total_patients');

        return response()->json(new PatientResource($patient), 201);
    }

    public function update(Patient $patient, UpdatePatientRequest $request): JsonResponse
    {
        $patient->fill($request->safe()->except('address', 'photo'));

        if (Storage::fileExists($patient->photo)) {
            Storage::delete($patient->photo);
        }

        if($request->hasFile('photo')) {
            $photo = $request->file('photo')->store('patients', 'public');

            $patient->photo = $photo;
        }
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
        Cache::forget('total_patients');

        $patient->addresses()->delete();
        $patient->delete();
    }

    public function importCSV(ImportCSVRequest $request)
    {
        $file = $request->file('file');
        $filePath = $file->storeAs('public/temp/csv', $file->getClientOriginalName());

        $path = storage_path('app/'.$filePath);

        (new FastExcel)->import($path, fn(array $line) => ImportPatientJob::dispatch($line));

        unlink($path);

        return response()->json();
    }

    public function exportCSV()
    {
        $patients = Patient::limit(10)->get();
        $patients = $patients->map(function (Patient $patient) {
            $address = $patient->addresses->first();
            return [
                'document' => $patient->document,
                'cns' => $patient->cns,
                'name' => $patient->name,
                'mother_name' => $patient->mother_name,
                'birthdate' => $patient->birthdate,
                'phone' => $patient->phone,
                'address_zipcode' => $address->zipcode,
                'address_street' => $address->street,
                'address_number' => $address->number,
                'address_neighborhood' => $address->neighborhood,
                'address_complement' => $address->complement,
                'address_city' => $address->city,
                'address_state' => $address->state,
            ];
        });

        return (new FastExcel($patients))->export(public_path('patients.csv'));
    }
}
