<?php

namespace App\Console\Commands;

use App\Models\BnfDetail;
use App\Models\NafdacProduct;
use Illuminate\Console\Command;

class PrepareMedicationTypeObject extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'prepare:medication_object';

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


        return Command::SUCCESS;
    }
}
