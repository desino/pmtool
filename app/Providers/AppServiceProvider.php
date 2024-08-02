<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Initiative;
use App\Models\Section;
use App\Observers\ClientObserver;
use App\Observers\InitiativeObserver;
use App\Observers\SectionObserver;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Graph\GraphExtendSocialite;
use SocialiteProviders\Manager\SocialiteWasCalled;

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
        Schema::defaultStringLength(191);
        Client::observe(ClientObserver::class);
        Initiative::observe(InitiativeObserver::class);
        Section::observe(SectionObserver::class);
        Event::listen(
            SocialiteWasCalled::class,
            GraphExtendSocialite::class.'@handle',
        );
    }
}
