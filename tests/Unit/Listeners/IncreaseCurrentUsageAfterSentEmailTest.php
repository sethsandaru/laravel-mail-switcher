<?php

namespace SethPhat\MailSwitcher\Tests\Unit\Listeners;

use Illuminate\Support\Facades\Event;
use Illuminate\Mail\Events\MessageSent;
use SethPhat\MailSwitcher\Tests\TestCase;
use SethPhat\MailSwitcher\Models\MailCredential;
use SethPhat\MailSwitcher\Listeners\IncreaseCurrentUsageAfterSentEmail;

class IncreaseCurrentUsageAfterSentEmailTest extends TestCase
{
    public function testClassDidListenToMailSendingEvent(): void
    {
        Event::fake();

        Event::assertListening(
            MessageSent::class,
            IncreaseCurrentUsageAfterSentEmail::class
        );

        event(new MessageSent(new \Swift_Message(), []));

        // asserts
        Event::assertDispatched(MessageSent::class);
    }

    public function testCurrentThresholdOfCredentialDidIncreaseAfterSentEmail(): void
    {
        Event::fake();

        $mailCredential = MailCredential::factory()->create();

        event(new MessageSent(new \Swift_Message(), []));
        (new IncreaseCurrentUsageAfterSentEmail())->handle(
            new MessageSent(new \Swift_Message(), [])
        );

        $mailCredential->refresh();
        $this->assertNotEquals(0, $mailCredential->current_threshold);
        $this->assertGreaterThan(0, $mailCredential->current_threshold);
        $this->assertEquals(1, $mailCredential->current_threshold);
    }
}
