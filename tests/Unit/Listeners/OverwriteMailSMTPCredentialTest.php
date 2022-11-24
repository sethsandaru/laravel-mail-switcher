<?php

namespace SethPhat\MailSwitcher\Tests\Unit\Listeners;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Event;
use SethPhat\MailSwitcher\Tests\TestCase;
use Illuminate\Mail\Events\MessageSending;
use SethPhat\MailSwitcher\Models\MailCredential;
use SethPhat\MailSwitcher\Exceptions\EmptyCredentialException;
use SethPhat\MailSwitcher\Listeners\OverwriteMailSMTPCredential;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Email;

/**
 * Class OverwriteMailSMTPCredentialTest.
 */
class OverwriteMailSMTPCredentialTest extends TestCase
{
    protected MailManager $mailManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailManager = app(MailManager::class);
    }

    public function testClassDidListenToMailSendingEvent(): void
    {
        Event::fake();

        Event::assertListening(
            MessageSending::class,
            OverwriteMailSMTPCredential::class
        );

        event(new MessageSending(new Email(), []));

        // asserts
        Event::assertDispatched(MessageSending::class);
    }

    public function testEmailCredentialOverwroteFromLaravelSuccessfully(): void
    {
        Event::fake();

        $mailCredential = MailCredential::factory()->create();

        event(new MessageSending(new Email(), []));
        (new OverwriteMailSMTPCredential())
            ->handle(
                new MessageSending(new Email(), [])
            );

        // asserts
        /** @var EsmtpTransport $smtpTransport */
        $smtpTransport = app(MailManager::class)->mailer()->getSymfonyTransport();

        $this->assertSame($smtpTransport->getUsername(), $mailCredential->email);
        $this->assertSame($smtpTransport->getPassword(), $mailCredential->password);
        $this->assertSame($smtpTransport->getStream()->getHost(), $mailCredential->server);
        $this->assertSame($smtpTransport->getStream()->getPort(), $mailCredential->port);
        $this->assertSame($smtpTransport->getStream()->isTls(), true);
    }

    public function testFailToOverwriteBecauseNoMoreCredentialThrowException(): void
    {
        $this->expectException(EmptyCredentialException::class);
        MailCredential::factory()->create([
            'threshold' => 10,
            'current_threshold' => 10,
        ]);

        Event::fake();
        event(new MessageSending(new Email(), []));
        (new OverwriteMailSMTPCredential())
            ->handle(
                new MessageSending(new Email(), [])
            );
    }
}
