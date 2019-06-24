<?php
/**
 * Created by PhpStorm.
 * User: arthur
 * Date: 30.10.18
 * Time: 14:01.
 */

namespace App\Packages\Horizon\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class PublishHorizonAssetsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'horizon:publish:assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish Horizon Assets.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call('vendor:publish', [
            '--provider'=> "Laravel\Horizon\HorizonServiceProvider",
        ]);
        $this->info('Horizon assets published.');
    }
}
