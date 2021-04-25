<?php


namespace SethPhat\MailSwitcher;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use SethPhat\MailSwitcher\Console\Commands\AddMail;
use SethPhat\MailSwitcher\Console\Commands\ListMail;
use SethPhat\MailSwitcher\Console\Commands\ResetThreshold;
use SethPhat\MailSwitcher\Listeners\IncreaseCurrentUsageAfterSentEmail;
use SethPhat\MailSwitcher\Listeners\OverwriteMailSMTPCredential;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . "/Database/Migrations");
        $this->commands([
            ListMail::class,
            AddMail::class,
            ResetThreshold::class,
        ]);

        Event::listen(MessageSending::class, OverwriteMailSMTPCredential::class);
        Event::listen(MessageSent::class, IncreaseCurrentUsageAfterSentEmail::class);
    }
}
