<?php

namespace App\Console\Commands;

use App\Http\Controllers\DrugsAndMedicalProductController;
use App\Models\DrugsAndMedicalProduct;
use Illuminate\Console\Command;

class PrepareDrugsAndMedicalProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

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
        DrugsAndMedicalProduct::truncate();
        app(DrugsAndMedicalProductController::class)->start_creating_responses();
        return Command::SUCCESS;
    }
}
