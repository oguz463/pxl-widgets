<?php

namespace App\Jobs;

use App\Models\Person;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecordWillBeAddedToDB implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $record;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $record)
    {
        $this->record = $record;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Person::create([
            "name" => $this->record["name"],
            "address" => $this->record["address"],
            "checked" => $this->record["checked"],
            "description" => $this->record["description"],
            "interest" => $this->record["interest"],
            "date_of_birth" => Carbon::parse($this->record["date_of_birth"]),
            "email" => $this->record["email"],
            "account" => $this->record["account"],
            "credit_card" => $this->record["credit_card"]
        ]);
    }
}
