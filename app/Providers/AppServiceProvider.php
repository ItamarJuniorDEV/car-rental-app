<?php

namespace App\Providers;

use App\Models\Brand;
use App\Models\Car;
use App\Models\Client;
use App\Models\Line;
use App\Models\Rental;
use App\Policies\BrandPolicy;
use App\Policies\CarPolicy;
use App\Policies\ClientPolicy;
use App\Policies\LinePolicy;
use App\Policies\RentalPolicy;
use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Repositories\Contracts\CarRepositoryInterface;
use App\Repositories\Contracts\ClientRepositoryInterface;
use App\Repositories\Contracts\LineRepositoryInterface;
use App\Repositories\Contracts\RentalRepositoryInterface;
use App\Repositories\Eloquent\BrandRepository;
use App\Repositories\Eloquent\CarRepository;
use App\Repositories\Eloquent\ClientRepository;
use App\Repositories\Eloquent\LineRepository;
use App\Repositories\Eloquent\RentalRepository;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(BrandRepositoryInterface::class, BrandRepository::class);
        $this->app->bind(LineRepositoryInterface::class, LineRepository::class);
        $this->app->bind(CarRepositoryInterface::class, CarRepository::class);
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(RentalRepositoryInterface::class, RentalRepository::class);
    }

    public function boot()
    {
        Gate::policy(Brand::class, BrandPolicy::class);
        Gate::policy(Line::class, LinePolicy::class);
        Gate::policy(Car::class, CarPolicy::class);
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Rental::class, RentalPolicy::class);
    }
}
