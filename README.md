# Laravel Mail Switcher

[![codecov](https://codecov.io/gh/sethsandaru/laravel-mail-switcher/branch/master/graph/badge.svg?token=S1GSHCQB55)](https://codecov.io/gh/sethsandaru/laravel-mail-switcher)

Laravel Mail Credentials Switcher is a library which helps you to:

- Manage your Mail Service Credentials
- Configure the Laravel's Mail Driver and using the available credential
- Switch to another credential if the previous one was out of usage of the current month

## Use-case

You have a personal Laravel Application (small or medium) or even you're a Startup. And of course, you have a tight budget.

So you can't spend much money for Email Provider Services to send out your email to your Users/Customers.

There are a lot of Email Provider Services out there that actually give you a specific amount per month (for free) to use to send out emails.

So, with Laravel Mail Switcher, you will have a big advantage to use that, so you don't have to change `ENV` everytime one of services is running out of usage.

### Email Services with Free Usage
- Mailgun: 5000 emails for 3-month (about 1666/month)
- Mailjet: 6000 emails per month (but 200 per day)
- Sendgrid: 100 emails per day (3000/month)
- Socketlabs: 2000/month (first month: 40000)
- Sendinblue: 300 per day (9000/month)

And many more...

### Limitation

Laravel Mail Switcher is only support for `SMTP` driver at the moment.

Coming soon for others.

## Requirement
- Laravel 8.x
- PHP 7.4

## Installation
Coming soon (this below is just a prototype, I haven't released anything yet)

```
composer require sethsandaru/laravel-mail-switcher
```

## Usage
Laravel Mail Switcher doesn't need a GUI to work with. We will do all the stuff in Artisan Console.

### List All Emails
```
php artisan ms:list
```

Note: You can add **--force** to show all Credentials (even the exceeded usage credentials)

### Add Email Credential
```
php artisan ms:add
```

You will see some questions that need your answers in order to add. Follow the instruction!!

### Delete an Credential
```
php artisan ms:delete {credentialId}
```

### Reset Threshold of expired Credentials
```
php artisan ms:reset
```

Like, your email credential is `daily` usage and exceeded yesterday. So, today we're gonna recover it to use it again.

## Cronjob Setup
By default, I will let you configure the Cron Job / Task Scheduling in your `Kernal.php`

Best practice should be daily check at 00:00

```php
$schedule->command('ms:reset')->dailyAt("00:00");
```

or every minute:

```php
$schedule->command('ms:reset')->everyMinute();
```

## Improve this library?

Feel free to fork it and send me the PR, I'll happily review it and merge it (if it's totally make sense of course).

Remember to write Unit Test also! Otherwise, I'll reject it.

## Copyright
2021 by Seth Phat
