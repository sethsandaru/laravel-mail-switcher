<?php


namespace SethPhat\MailSwitcher\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Log;
use SethPhat\MailSwitcher\Exceptions\EmptyCredentialException;
use SethPhat\MailSwitcher\Models\MailCredential;

class OverwriteMailSMTPCredential
{
    /**
     * @var MailManager $app
     */
    protected $app;

    public function __construct()
    {
        $this->app = app(MailManager::class);
    }

    /**
     * Handle the Overwrite Process
     *
     * @param MessageSending $event
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
