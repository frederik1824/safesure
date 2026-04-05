<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        \Illuminate\Database\Eloquent\Model::preventLazyLoading(! app()->isProduction());

        // Implicitly grant "Super-Admin" role all permissions
        // This works in the app by using gate-related functions like auth()->user->can() and @can()
        \Illuminate\Support\Facades\Gate::before(function ($user, $ability) {
            return $user->hasRole('Super-Admin') ? true : null;
        });

        \Illuminate\Support\Facades\Event::listen(
            \Illuminate\Auth\Events\Login::class,
            \App\Listeners\UpdateLastLogin::class
        );

        // Registro de Observadores para Sincronización con Firebase (Safesure Integration)
        \App\Models\Afiliado::observe(\App\Observers\AfiliadoObserver::class);
        \App\Models\Empresa::observe(\App\Observers\EmpresaObserver::class);
    }
}
