# Laravel Mail Switcher
![Build and Test](https://github.com/sethsandaru/laravel-mail-switcher/actions/workflows/build.yaml/badge.svg)
[![codecov](https://codecov.io/gh/sethsandaru/laravel-mail-switcher/branch/master/graph/badge.svg?token=S1GSHCQB55)](https://codecov.io/gh/sethsandaru/laravel-mail-switcher)
[![Latest Stable Version](https://poser.pugx.org/sethsandaru/laravel-mail-switcher/v)](//packagist.org/packages/sethsandaru/laravel-mail-switcher)

Laravel Mail Credentials Switcher is a library which helps you to:

- Manage your Mail Service Credentials
- Configure the Laravel's Mail Driver and using the available credential
- Switch to another credential if the previous one was out of usage of the current day/week/month
- Automatically reset the usage (daily/weekly/monthly) of the credentials

## Use-case

You have a personal Laravel Application (small or medium) or even you're a Startup. Of course, you have a **tight budget**.

So you probably can't spend much money for Email Provider Services to send out your email to your Users/Customers.

There are a lot of Email Provider Services out there that actually give you a specific amount per month (for free) to send out emails.

So, with Laravel Mail Switcher, you will have a big advantage to achieve that.

- You don't have to change `ENV` everytime one of services is running out of usage.
- You don't need to manually check to see if the email is running out.

All you need to do, is prepare the credential/information and let Laravel Mail Switcher will do that for you.

### Email Services with Free Usage
- Mailgun: 5000 emails for 3-month (about 1666/month)
- Mailjet: 6000 emails per month (but 200 per day)
- Sendgrid: 100 emails per day (3000/month)
- Socketlabs: 2000/month (first month: 40000)
- Sendinblue: 300 per day (9000/month)

And many more... With Laravel Mail Switcher, you can manage the credentials and use all of them until the free usage ran out!

### Limitation

Laravel Mail Switcher is only support for `SMTP` driver at the moment.

Coming soon for others.

## Requirement
- Laravel 8.x or 9.x
- PHP 8.0 & 8.1

## Installation
```
composer require sethsandaru/laravel-mail-switcher
```

## How to use?
Laravel Mail Switcher doesn't need a GUI to work with. We will do all the stuff in **Artisan Console**.

First, you need to run the migration:

```
php artisan migrate
```

Then, you can traverse the instructions below!!

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

## Tech Specs / QA Times

### Why did I choose to Overwrite the SMTP by listen to Mail's Events instead of ServiceProvider?
Because in real-life projects, not all the time, we will send out the emails. If I go with that way, then it probably costs 1 query everytime 
there is a connection to our application which isn't good and nice at all.


## Improve this library?

Feel free to fork it and send me the PR, I'll happily review and merge it (if it's totally make sense of course).

Remember to write Unit Test also! Otherwise, I'll reject it.

Coding Style must follow PSR-1 & PSR-12.

## Note
After your project is growing up, making good, then, don't forget to subscribe to an Email Service Provider for long-term supports and absolutely stable in production.

## Copyright
2022 by Seth Phat
