<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Functionality;
use App\Models\Initiative;
use App\Models\InitiativeEnvironment;
use App\Models\Project;
use App\Models\Release;
use App\Models\ReleaseTicket;
use App\Models\Section;
use App\Models\Ticket;
use App\Models\TicketAction;
use App\Models\TimeBooking;
use App\Observers\ClientObserver;
use App\Observers\FunctionalityObserver;
use App\Observers\InitiativeEnvironmentObserver;
use App\Observers\InitiativeObserver;
use App\Observers\ProjectObserver;
use App\Observers\ReleaseObserver;
use App\Observers\ReleaseTicketObserver;
use App\Observers\SectionObserver;
use App\Observers\TicketActionObserver;
use App\Observers\TicketObserver;
use App\Observers\TimeBookingObserver;
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
        Functionality::observe(FunctionalityObserver::class);
        Ticket::observe(TicketObserver::class);
        InitiativeEnvironment::observe(InitiativeEnvironmentObserver::class);
        Project::observe(ProjectObserver::class);
        TicketAction::observe(TicketActionObserver::class);
        Release::observe(ReleaseObserver::class);
        ReleaseTicket::observe(ReleaseTicketObserver::class);
        TimeBooking::observe(TimeBookingObserver::class);
        Event::listen(
            SocialiteWasCalled::class,
            GraphExtendSocialite::class . '@handle',
        );
    }
}
