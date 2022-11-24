<?php

namespace SethPhat\MailSwitcher\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Events\MessageSent;
use SethPhat\MailSwitcher\Models\MailCredential;

class IncreaseCurrentUsageAfterSentEmail
{
    public function handle(MessageSent $event)
    {
        $mailCredential = MailCredential::$currentInstance;
        if ($mailCredential === null) {
            $mailCredential = MailCredential::getAvailableCredential();
        }

        // increase the usage
        ++$mailCredential->current_threshold;
        $mailCredential->save();

        Log::info("[MailSwitcher] Mail Sent by using: {$mailCredential->email}|{$mailCredential->server}. Usage Left: {$mailCredential->usageLeft}");
    }
}
