<?php

namespace App\Jobs\ElasticSearch;

use Elasticsearch\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RemoveFromElasticJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        readonly private string $index,
        readonly private string $id,
        readonly private ?string $type = null
    ) {
    }

    public function handle(): void
    {
        $client = app(Client::class);
        $client->delete([
            'index' => $this->index,
            'type' => $this->type,
            'id' => $this->id,
        ]);
    }
}
