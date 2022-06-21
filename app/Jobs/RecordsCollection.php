<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordsCollection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Define the keys for matching database columns
     *
     * @var array
     */
    protected $keysToCompare = ["name", "address", "checked", "description", "interest", "date_of_birth", "email", "account", "credit_card"];
    protected $records;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $records)
    {
        $this->records = $records;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->records as $record) {
            if ($this->keysToCompare == array_keys($record)) {
                RecordToAddDB::dispatch($record)->onQueue('high');
            }
        }
    }
}
