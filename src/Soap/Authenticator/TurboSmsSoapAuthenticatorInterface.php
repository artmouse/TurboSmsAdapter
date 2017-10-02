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

namespace SmsGate\Adapter\TurboSms\Soap\Authenticator;

use SmsGate\Exception\FailAuthenticationException;

/**
 * All authenticator for authenticate TurboSMS SOAP should implement this interface.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface TurboSmsSoapAuthenticatorInterface
{
    /**
     * Authenticate on SOAP layer.
     *
     * @throws FailAuthenticationException
     */
    public function authenticate(): void;
}
