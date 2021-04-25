<?php


namespace SethPhat\MailSwitcher\Test\Unit\Listeners;


use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Event;
use SethPhat\MailSwitcher\Listeners\OverwriteMailSMTPCredential;
use SethPhat\MailSwitcher\Models\MailCredential;
use Tests\TestCase;


/**
 * Class OverwriteMailSMTPCredentialTest
 * @package SethPhat\MailSwitcher\Test\Unit\Listeners
 */
class OverwriteMailSMTPCredentialTest extends TestCase
{

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
