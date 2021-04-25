<?php


namespace SethPhat\MailSwitcher\Listeners;


use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;
use SethPhat\MailSwitcher\Models\MailCredential;

class IncreaseCurrentUsageAfterSentEmail
{
    public function handle(MessageSent $event)
    {
        $mailCredential = MailCredential::$currentInstance;
        if (empty($mailCredential)) {
            $mailCredential = MailCredential::getAvailableCredential();
        }

        // increase the usage
        $mailCredential->current_threshold++;
        $mailCredential->save();

        Log::info("[MailSwitcher] Mail Sent by using: {$mailCredential->email}|{$mailCredential->server}. Usage Left: {$mailCredential->usageLeft}");
    }
}
