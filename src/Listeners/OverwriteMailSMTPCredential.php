<?php

namespace SethPhat\MailSwitcher\Listeners;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Events\MessageSending;
use SethPhat\MailSwitcher\Models\MailCredential;
use SethPhat\MailSwitcher\Exceptions\EmptyCredentialException;

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
        /** @var \Swift_SmtpTransport $smtpTransport */
        $smtpTransport = $this->app->mailer('smtp')->getSwiftMailer()->getTransport();
        $mailCredential = MailCredential::getAvailableCredential();

        if (is_null($mailCredential)) {
            throw new EmptyCredentialException();
        }

        // cache
        MailCredential::$currentInstance = $mailCredential;

        // set
        $smtpTransport->setUsername($mailCredential->email);
        $smtpTransport->setPassword($mailCredential->password);
        $smtpTransport->setEncryption($mailCredential->encryption);
        $smtpTransport->setHost($mailCredential->server);
        $smtpTransport->setPort($mailCredential->port);

        Log::info("[MailSwitcher] Switched the MailCredential to: {$mailCredential->email}|{$mailCredential->server}");
    }
}
