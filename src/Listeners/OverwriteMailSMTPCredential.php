<?php

namespace SethPhat\MailSwitcher\Listeners;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Events\MessageSending;
use SethPhat\MailSwitcher\Models\MailCredential;
use SethPhat\MailSwitcher\Exceptions\EmptyCredentialException;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class OverwriteMailSMTPCredential
{
    /**
     * @var MailManager
     */
    protected $app;

    public function __construct()
    {
        $this->app = app(MailManager::class);
    }

    /**
     * Handle the Overwrite Process.
     */
    public function handle(MessageSending $event)
    {
        $mailCredential = MailCredential::getAvailableCredential();

        if (is_null($mailCredential)) {
            throw new EmptyCredentialException();
        }

        // cache
        MailCredential::$currentInstance = $mailCredential;

        /** @var EsmtpTransport $smtpTransport */
        $smtpTransport = $this->app->createSymfonyTransport([
            'transport' => 'smtp',
            'encryption' => $mailCredential->encryption,
            'host' => $mailCredential->server,
            'port' => $mailCredential->port,
            'username' => $mailCredential->email,
            'password' => $mailCredential->password,
        ]);

        // set to MailManager
        $this->app->forgetMailers();
        $this->app->extend('smtp', fn () => $smtpTransport);

        Log::info("[MailSwitcher] Switched the MailCredential to: {$mailCredential->email}|{$mailCredential->server}");
    }
}
