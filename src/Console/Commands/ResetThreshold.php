<?php


namespace SethPhat\MailSwitcher\Console\Commands;


use Illuminate\Console\Command;
use SethPhat\MailSwitcher\Models\MailCredential;

class ResetThreshold extends Command
{
    protected $signature = "ms:reset";
    protected $description = "Run to clear the Credential that has been reset for the next threshold-time";

    public function handle()
    {
        $mailCredentials = MailCredential::all();
        $this->info("Clearing the Credential with pass the threshold date.");

        foreach ($mailCredentials as $mailCredential) {
            if ($mailCredential->isAvailableToClearThreshold) {
                $mailCredential->threshold_start = null;
                $mailCredential->save();

                $this->info("Cleared for ID: {$mailCredential->id} - Server: {$mailCredential->server} - {$mailCredential->email}");
            }
        }

    }
}
