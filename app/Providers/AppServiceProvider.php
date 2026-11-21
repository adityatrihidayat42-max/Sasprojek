<?php

namespace {
    if (!function_exists('rp')) {
        function rp($n): string
        {
            return 'Rp ' . number_format((float) $n, 0, ',', '.');
        }
    }
}

namespace App\Providers {
    use Carbon\Carbon;
    use Illuminate\Pagination\Paginator;
    use Illuminate\Support\ServiceProvider;

    class AppServiceProvider extends ServiceProvider
    {
        public function register(): void
        {
        }

        public function boot(): void
        {
            Paginator::useBootstrapFive();
            Carbon::setLocale('id');
        }
    }
}
