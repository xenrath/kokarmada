<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale('id');
        Blade::directive('rupiah', function ($expression) {
            return "Rp<?php echo number_format($expression,0,',','.'); ?>";
        });
    }
}
