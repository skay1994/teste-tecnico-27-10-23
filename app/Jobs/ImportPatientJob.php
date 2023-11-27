<?php

namespace App\Jobs;

use App\Models\Patient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImportPatientJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(protected array $data)
    {
    }

    public function handle(): void
    {
        $this->data['document'] = preg_replace('/\D/', '', $this->data['document']);
        $this->data['cns'] = preg_replace('/\D/', '', $this->data['cns']);

        if(
            Patient::where('document', $this->data['document'])->exists() ||
            Patient::where('cns', $this->data['cns'])->exists())
        {
            return;
        }

        $patient = Patient::create([
            'document' => $this->data['document'],
            'cns' => $this->data['cns'],
            'name' => $this->data['name'],
            'mother_name' => $this->data['mother_name'],
            'phone' => $this->data['phone'],
            'birth_date' => $this->data['birthdate'],
        ]);
        $patient->addresses()->create([
            'zipcode' => $this->data['address_zipcode'],
            'street' => $this->data['address_street'],
            'number' => $this->data['address_number'],
            'neighborhood' => $this->data['address_neighborhood'],
            'complement' => $this->data['address_complement'],
            'city' => $this->data['address_city'],
            'state' => $this->data['address_state'],
        ]);
    }
}
