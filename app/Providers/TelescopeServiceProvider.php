<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Laravel\Telescope\IncomingEntry;
use Laravel\Telescope\Telescope;
use Laravel\Telescope\TelescopeApplicationServiceProvider;

class TelescopeServiceProvider extends TelescopeApplicationServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Telescope::night();

        // Modified tag function to capture all log levels
        Telescope::tag(function (IncomingEntry $entry) {
            if ($entry->type === 'log') {
                $level = $entry->content['level'] ?? 'unknown';

                return ['logs', $level];
            }

            return [];
        });

        $this->hideSensitiveRequestDetails();

        // Modified filter to capture all log entries
        Telescope::filter(function (IncomingEntry $entry) {
            if ($entry->type === 'log') {
                return true; // Allow all log entries
            }

            return true; // Or add your other conditions here
        });
    }

    /**
     * Prevent sensitive request details from being logged by Telescope.
     */
    protected function hideSensitiveRequestDetails(): void
    {
        if ($this->app->environment('local')) {
            return;
        }

        Telescope::hideRequestParameters(['_token']);

        Telescope::hideRequestHeaders([
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
        ]);
    }

    /**
     * Register the Telescope gate.
     *
     * This gate determines who can access Telescope in non-local environments.
     */
    protected function gate(): void
    {
        Gate::define('viewTelescope', function ($user) {
            return in_array($user->email, [
                //
            ]);
        });
    }
}
