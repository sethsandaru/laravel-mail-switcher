<?php

namespace SethPhat\MailSwitcher\Console\Commands;

use Illuminate\Console\Command;
use SethPhat\MailSwitcher\Models\MailCredential;

class ListMail extends Command
{
    protected $signature = 'ms:list {--force: Force to show all including expired list}';
    protected $description = 'List all the current available SMTP that can sent out emails';

    /**
     * Action process.
     */
    public function handle()
    {
        $isShowAll = $this->hasOption('force');

        $query = MailCredential::query()->orderBy('current_threshold', 'DESC');

        if (!$isShowAll) {
            $query->whereColumn('current_threshold', '<', 'threshold');
        }

        $mailCredentials = $query->get();

        $headers = ['ID', 'Email / Host', 'Threshold Type', 'Threshold', 'Current Usage'];
        $rows = $mailCredentials->map(function ($mailCredential) {
            return [
                $mailCredential->id,
                $mailCredential->email.' / '.$mailCredential->server,
                $mailCredential->threshold_type,
                $mailCredential->threshold,
                $mailCredential->current_threshold,
            ];
        });

        $this->info('SMTP Credentials List');
        if ($isShowAll) {
            $this->warn('[Force Mode is on - Show ALL]');
        }

        $this->table($headers, $rows);
    }
}
