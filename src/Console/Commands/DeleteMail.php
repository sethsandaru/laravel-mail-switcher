<?php


namespace SethPhat\MailSwitcher\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use SethPhat\MailSwitcher\Models\MailCredential;

class DeleteMail extends Command
{
    protected $signature = "ms:delete {credentialId}";
    protected $description = "Delete an existing Email Credential in Mail Switcher";

    /**
     * Action process
     *
     */
    public function handle() {
        $credentialId = $this->argument('credentialId');

        /** @var MailCredential $mailCredential */
        $mailCredential = MailCredential::findOrFail($credentialId);

        $this->warn("You might want to backup this again:");
        $this->info("Server: {$mailCredential->server}");
        $this->info("Port: {$mailCredential->port}");
        $this->info("User/Email: {$mailCredential->email}");
        $this->info("Password: {$mailCredential->password}");
        $this->info("Encryption: {$mailCredential->encryption}");

        $mailCredential->delete();

        $this->info('Your SMTP Email credential has been deleted!');
    }
}
