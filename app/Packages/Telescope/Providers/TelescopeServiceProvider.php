<?php

namespace App\Packages\Telescope\Providers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;
use Laravel\Telescope\EntryType;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{

    protected $ignoredDatabaseTables = [
        'permissions',
        'roles',
        'model_has_roles',
        'model_has_permissions',
        'role_has_permissions',
        'migrations',
        'telescope_monitoring',
        'telescope_entries_tags',
        'telescope_entries'
    ];

    public function boot()
    {
        //DB::connection('mongodb')->enableQueryLog();
        //DB::connection('telescope')->disableQueryLog();
        Redis::enableEvents();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);

        Telescope::night();

        Telescope::filter(function (IncomingEntry $entry) {
            if (is_bool($filter = $this->filterHorizonEntries($entry))) {
                return $filter;
            }

            if (is_bool($filter = $this->filterCorsRequests($entry))) {
                return $filter;
            }

            if (is_bool($filter = $this->filterIgnoredDatabaseTables($entry))) {
                return $filter;
            }

            return $entry->isReportableException() ||
                $entry->isFailedJob() ||
                $entry->isScheduledTask() ||
                $entry->hasMonitoredTag();
        });
    }

    protected function filterHorizonEntries(IncomingEntry $entry)
    {
        if ($entry->type === EntryType::REQUEST
            && isset($entry->content['uri'])
            && Str::contains($entry->content['uri'], 'horizon')) {
            return false;
        }

        if ($entry->type === EntryType::EVENT
            && isset($entry->content['name'])
            && Str::contains($entry->content['name'], 'Horizon')) {
            return false;
        }
    }

    protected function filterIgnoredDatabaseTables(IncomingEntry $entry)
    {
        if ($entry->type === EntryType::QUERY && isset($entry->content['sql'])) {
            foreach ($this->ignoredDatabaseTables as $table) {
                if (Str::contains($entry->content['sql'], "`$table`"))
                    return false;
            }
        }
    }

    protected function filterCorsRequests(IncomingEntry $entry)
    {
        if ($entry->type === EntryType::REQUEST
            && isset($entry->content['method'])
            && $entry->content['method'] === 'OPTIONS') {
            return false;
        }
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     *
     * @return void
     */
    protected function authorization()
    {
        parent::authorization();
    }

}
