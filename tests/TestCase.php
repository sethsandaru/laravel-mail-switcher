<?php


namespace SethPhat\MailSwitcher\Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use SethPhat\MailSwitcher\ServiceProvider as MailSwitcherServiceProvider;

abstract class TestCase extends BaseTestCase
{
    use WithFaker;
    use DatabaseTransactions;

    protected function getPackageProviders($app)
    {
        return [
            MailSwitcherServiceProvider::class,
        ];
    }
}
