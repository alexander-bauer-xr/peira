<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;

use Illuminate\Support\Facades\Blade;
use App\Helpers\HtmlTransformHelper;

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
        // Check for a ?lang=de|en parameter in the URL and set app locale
        $lang = Request::get('lang');

        if (in_array($lang, ['en', 'de'])) {
            App::setLocale($lang);
        }

        Blade::directive('replaceVideo', function ($expression) {
            return "<?php echo \\App\\Helpers\\HtmlTransformHelper::processHtml($expression); ?>";
        });
    }
}