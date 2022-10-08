<?php

namespace SethPhat\MailSwitcher\Tests\Unit\Listeners;

use Illuminate\Mail\MailManager;
use Illuminate\Support\Facades\Event;
use SethPhat\MailSwitcher\Tests\TestCase;
use Illuminate\Mail\Events\MessageSending;
use SethPhat\MailSwitcher\Models\MailCredential;
use SethPhat\MailSwitcher\Exceptions\EmptyCredentialException;
use SethPhat\MailSwitcher\Listeners\OverwriteMailSMTPCredential;

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

        event(new MessageSending(new \Swift_Message(), []));

        // asserts
        Event::assertDispatched(MessageSending::class);
    }

    public function testEmailCredentialOverwroteFromLaravelSuccessfully(): void
    {
        Event::fake();

        $mailCredential = MailCredential::factory()->create();

        event(new MessageSending(new \Swift_Message(), []));
        (new OverwriteMailSMTPCredential())
            ->handle(
                new MessageSending(new \Swift_Message(), [])
            );

        // asserts
        /** @var \Swift_SmtpTransport $smtpTransport */
        $smtpTransport = $this->mailManager->mailer('smtp')
            ->getSwiftMailer()
            ->getTransport();

        $this->assertSame($smtpTransport->getUsername(), $mailCredential->email);
        $this->assertSame($smtpTransport->getPassword(), $mailCredential->password);
        $this->assertSame($smtpTransport->getHost(), $mailCredential->server);
        $this->assertSame($smtpTransport->getPort(), $mailCredential->port);
        $this->assertSame($smtpTransport->getEncryption(), $mailCredential->encryption);
    }

    public function testFailToOverwriteBecauseNoMoreCredentialThrowException(): void
    {
        $this->expectException(EmptyCredentialException::class);
        MailCredential::factory()->create([
            'threshold' => 10,
            'current_threshold' => 10,
        ]);

        Event::fake();
        event(new MessageSending(new \Swift_Message(), []));
        (new OverwriteMailSMTPCredential())
            ->handle(
                new MessageSending(new \Swift_Message(), [])
            );
    }
}
