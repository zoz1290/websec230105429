<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Model => Policy mappings here
    ];

    public function boot()
    {
        $this->registerPolicies();
    }
}
