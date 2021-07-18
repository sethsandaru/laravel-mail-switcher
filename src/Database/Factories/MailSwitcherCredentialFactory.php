<?php


namespace SethPhat\MailSwitcher\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use SethPhat\MailSwitcher\Models\MailCredential;

class MailSwitcherCredentialFactory extends Factory
{
    protected $model = MailCredential::class;

    public function definition()
    {
        return [
            'email' => $this->faker->email,
            'password' => $this->faker->password,
            'server' => $this->faker->ipv4,
            'port' => 25,
            'encryption' => 'tls',
            'threshold' => $this->faker->numberBetween(1, 99),
            'current_threshold' => 0,
            'threshold_type' => MailCredential::THRESHOLD_TYPE_MONTHLY,
            'driver' => 'smtp',
        ];
    }
}
