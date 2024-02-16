<?php

namespace Razinal\Satusehatsync;

use Illuminate\Support\ServiceProvider;

class SimSatuSehatProvider  extends ServiceProvider {

    public function boot(): void
    {

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // Publish Config
        $this->publishes([__DIR__.'/config/satusehatsimrs.php' => config_path('satusehatsimrs.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__.'/config/satusehatsimrs.php', 'satusehatsimrs');

    }

    public function register(): void
    {

    }
}
