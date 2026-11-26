<?php

namespace App\Providers;

use App\Models\Genre;
use App\Services\CartService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::defaultView('vendor.pagination.velin');
        Paginator::defaultSimpleView('vendor.pagination.velin');

        Blade::directive('inr', function (string $expression) {
            return "<?php echo \\App\\Support\\Money::format({$expression}); ?>";
        });

        View::composer('layouts.store', function ($view): void {
            $view->with([
                'navGenres' => Genre::query()->orderBy('name')->get(),
                'cartCount' => app(CartService::class)->count(),
            ]);
        });
    }
}
