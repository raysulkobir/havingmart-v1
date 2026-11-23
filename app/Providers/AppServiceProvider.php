<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
      Schema::defaultStringLength(191);
      // useBootstrap() only exists on Laravel 8+; on Laravel 6 Bootstrap is already the default paginator.
      if (method_exists(Paginator::class, 'useBootstrap')) {
          Paginator::useBootstrap();
      }
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }
}
