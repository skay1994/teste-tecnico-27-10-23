<?php

namespace App\Observers;

use App\Jobs\ElasticSearch\AddToElasticJob;
use App\Jobs\ElasticSearch\RemoveFromElasticJob;
use App\Models\Patient;

class PatientObserver
{
    /**
     * Handle the Patient "created" event.
     */
    public function created(Patient $patient): void
    {
        $data = $patient->only(['phone', 'document', 'name', 'mother_name', 'birthdate', 'cns']);
        $data['birthdate'] = $patient->birthdate->format('Y-m-d');

        AddToElasticJob::dispatch($patient->getTable(), $patient->getKey(), $data);
    }

    /**
     * Handle the Patient "updated" event.
     */
    public function updated(Patient $patient): void
    {
        $data = $patient->only(['phone', 'document', 'name', 'mother_name', 'birthdate', 'cns']);
        $data['birthdate'] = $patient->birthdate->format('Y-m-d');

        AddToElasticJob::dispatch($patient->getTable(), $patient->getKey(), $data);
    }

    /**
     * Handle the Patient "deleted" event.
     */
    public function deleted(Patient $patient): void
    {
        RemoveFromElasticJob::dispatch($patient->getTable(), $patient->getKey());
    }

    /**
     * Handle the Patient "restored" event.
     */
    public function restored(Patient $patient): void
    {
        $data = $patient->only(['phone', 'document', 'name', 'mother_name', 'birthdate', 'cns']);
        $data['birthdate'] = $patient->birthdate->format('Y-m-d');

        AddToElasticJob::dispatch($patient->getTable(), $patient->getKey(), $data);
    }

    /**
     * Handle the Patient "force deleted" event.
     */
    public function forceDeleted(Patient $patient): void
    {
        RemoveFromElasticJob::dispatch($patient->getTable(), $patient->getKey());
    }
}
