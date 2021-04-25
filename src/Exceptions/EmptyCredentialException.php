<?php


namespace SethPhat\MailSwitcher\Exceptions;

use Exception;

class EmptyCredentialException extends Exception
{
    protected $message = "There are no available credentials left to send out the email";
}
