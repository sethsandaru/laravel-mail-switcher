<?php


namespace SethPhat\MailSwitcher\Console\Commands;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use SethPhat\MailSwitcher\Models\MailCredential;

class AddMail extends Command
{
    protected $signature = "ms:add";
    protected $description = "Add an email into Mail Switcher";

    /**
     * Action process
     *
     */
    public function handle() {
        $questions = [
            'email' => '(Required) Login Credential (Username or Email)',
            'password' => '(Required) Login Password',
            'server' => '(Required) SMTP Server',
            'port' => '(Required) SMTP Port',
            'encryption' => [
                'question' => 'Email Encryption (Default: TLS)',
                'default' => 'TLS'
            ],
            'threshold' => [
                'question' => 'Email Threshold (Per Month) (Default: 1000)',
                'default' => 1000
            ],
            'threshold_type' => [
                'question' => 'Threshold Type (daily/weekly/monthly) (Default: monthly)',
                'default' => MailCredential::THRESHOLD_TYPE_MONTHLY
            ],
        ];

        $payload = [];
        foreach ($questions as $key => $question) {
            $payload[$key] = $this->ask(
                $question['question'] ?? $question
            );

            if (empty($payload[$key])) {
                if (!is_array($question)) {
                    return $this->error(ucfirst($key) . ' is required. Aborted');
                } else {
                    $payload[$key] = $question['default'];
                }
            }
        }

        // okay add new
        MailCredential::create($payload);

        $this->info('Your SMTP Email credential has been added!');
    }
}
