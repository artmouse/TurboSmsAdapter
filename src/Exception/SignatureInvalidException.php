<?php

declare(strict_types = 1);

/*
 * This file is part of the TurboSMS package
 *
 * (c) SmsGate
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code
 */

namespace SmsGate\Adapter\TurboSms\Exception;

use SmsGate\Exception\SendSmsExceptionInterface;

/**
 * Throw this exception if the signature (name of sender) is invalid.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class SignatureInvalidException extends \Exception implements SendSmsExceptionInterface
{
}
