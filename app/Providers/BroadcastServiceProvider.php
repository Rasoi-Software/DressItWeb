<?php

namespace App\Providers;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
         Broadcast::routes();

        // Only needed for private/presence channels; harmless for public
        require base_path('routes/channels.php');
    }
}
