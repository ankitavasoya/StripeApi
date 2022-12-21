<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use App\Models\Cashier\User;
use Illuminate\Support\Facades\Schema;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Cashier::ignoreMigrations();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        Cashier::useCustomerModel(User::class);
        Cashier::calculateTaxes();
    }
}
