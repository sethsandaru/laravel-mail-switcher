<?php


namespace SethPhat\MailSwitcher\Tests\Unit\Listeners;


use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use SethPhat\MailSwitcher\Listeners\OverwriteMailSMTPCredential;
use SethPhat\MailSwitcher\Models\MailCredential;
use SethPhat\MailSwitcher\Tests\TestCase;

/**
 * Class OverwriteMailSMTPCredentialTest
 * @package SethPhat\MailSwitcher\Test\Unit\Listeners
 */
class OverwriteMailSMTPCredentialTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->event = app(Event::class);
    }

    public function testEmailCredentialOverwroteFromLaravelSuccessfully()
    {
        Event::fake();

        $mailCredential = MailCredential::factory()->create();

        Event::assertDispatched(function(MessageSent $event) use ($mailCredential) {
            /** @var \Swift_SmtpTransport $smtpTransport */
            $smtpTransport = $this->app->get('smtp');

            $this->assertSame($smtpTransport->getUsername(), $mailCredential->email);
            $this->assertSame($smtpTransport->getPassword(), $mailCredential->password);
            $this->assertSame($smtpTransport->getHost(), $mailCredential->server);
            $this->assertSame($smtpTransport->getPort(), $mailCredential->port);
            $this->assertSame($smtpTransport->getEncryption(), $mailCredential->encryption);
        });

        Event::assertListening(
            MessageSent::class,
            OverwriteMailSMTPCredential::class
        );
    }
}
