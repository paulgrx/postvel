<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShowApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:show-api-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show API token';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->components->info(sprintf('API token is [%s]', env('API_TOKEN')));
    }
}
