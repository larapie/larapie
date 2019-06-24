<?php
/**
 * Created by PhpStorm.
 * User: arthur
 * Date: 30.10.18
 * Time: 14:01.
 */

namespace App\Packages\Telescope\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PublishTelescopeAssetsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'telescope:publish:assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish Telescope Assets.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call('vendor:publish', [
            '--tag'   => 'telescope-assets',
            '--force' => true,
        ]);
        $this->info('Telescope assets published.');
    }
}
