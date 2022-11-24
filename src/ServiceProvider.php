<?php

namespace SethPhat\MailSwitcher;

use Illuminate\Support\Facades\Event;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Mail\Events\MessageSending;
use SethPhat\MailSwitcher\Console\Commands\AddMail;
use SethPhat\MailSwitcher\Console\Commands\ListMail;
use SethPhat\MailSwitcher\Console\Commands\DeleteMail;
use SethPhat\MailSwitcher\Console\Commands\ResetThreshold;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use SethPhat\MailSwitcher\Listeners\OverwriteMailSMTPCredential;
use SethPhat\MailSwitcher\Listeners\IncreaseCurrentUsageAfterSentEmail;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/Database/Migrations');
        $this->commands([
            ListMail::class,
            AddMail::class,
            DeleteMail::class,
            ResetThreshold::class,
        ]);

        Event::listen(MessageSending::class, OverwriteMailSMTPCredential::class);
        Event::listen(MessageSent::class, IncreaseCurrentUsageAfterSentEmail::class);
    }
}
