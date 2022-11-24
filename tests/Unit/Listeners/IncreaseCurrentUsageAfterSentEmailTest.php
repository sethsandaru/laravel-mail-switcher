<?php

namespace SethPhat\MailSwitcher\Tests\Unit\Listeners;

use Illuminate\Mail\SentMessage;
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

        event(new MessageSent($this->createMock(SentMessage::class), []));

        // asserts
        Event::assertDispatched(MessageSent::class);
    }

    public function testCurrentThresholdOfCredentialDidIncreaseAfterSentEmail(): void
    {
        Event::fake();

        $mailCredential = MailCredential::factory()->create();

        event(new MessageSent($this->createMock(SentMessage::class), []));
        (new IncreaseCurrentUsageAfterSentEmail())->handle(
            new MessageSent($this->createMock(SentMessage::class), [])
        );

        $mailCredential->refresh();
        $this->assertNotEquals(0, $mailCredential->current_threshold);
        $this->assertGreaterThan(0, $mailCredential->current_threshold);
        $this->assertEquals(1, $mailCredential->current_threshold);
    }
}
