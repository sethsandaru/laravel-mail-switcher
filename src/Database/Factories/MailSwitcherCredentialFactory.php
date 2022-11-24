<?php

namespace SethPhat\MailSwitcher\Database\Factories;

use SethPhat\MailSwitcher\Models\MailCredential;
use Illuminate\Database\Eloquent\Factories\Factory;

class MailSwitcherCredentialFactory extends Factory
{
    protected $model = MailCredential::class;

    public function definition(): array
    {
        return [
            'email' => $this->faker->email,
            'password' => $this->faker->password,
            'server' => $this->faker->ipv4,
            'port' => 465,
            'encryption' => 'tls',
            'threshold' => $this->faker->numberBetween(1, 99),
            'current_threshold' => 0,
            'threshold_type' => MailCredential::THRESHOLD_TYPE_MONTHLY,
        ];
    }
}
