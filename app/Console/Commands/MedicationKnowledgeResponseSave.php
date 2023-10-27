<?php

namespace App\Console\Commands;

use App\Http\Controllers\MedicationKnowledgeController;
use Illuminate\Console\Command;

class MedicationKnowledgeResponseSave extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:medication_knowledge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        app(MedicationKnowledgeController::class)->start_creating_responses();
        dump("Done");
        return Command::SUCCESS;
    }
}
