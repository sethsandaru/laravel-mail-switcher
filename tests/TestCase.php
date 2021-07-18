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

    protected function getEnvironmentSetUp($app)
    {
        include_once __DIR__ . "/../src/Database/Migrations/2021_04_23_000000_create_mail_switcher_credentials_table.php";
        include_once __DIR__ . "/../src/Database/Migrations/2021_07_18_000000_add_driver_to_mail_switcher_credentials_table.php";

        (new \CreateMailSwitcherCredentialsTable())->up();
        (new \AddDriverToMailSwitcherCredentialsTable())->up();
    }
}
