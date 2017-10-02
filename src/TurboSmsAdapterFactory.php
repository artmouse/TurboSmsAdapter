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

namespace SmsGate\Adapter\TurboSms;

use SmsGate\Adapter\TurboSms\Soap\Authenticator\TurboSmsSoapAuthenticator;
use SmsGate\Adapter\TurboSms\Soap\ResponseParser\ResponseParserFactory;
use SmsGate\Adapter\TurboSms\Soap\SoapClientFactory;
use SmsGate\Adapter\TurboSms\Soap\TurboSmsSoapAdapter;

/**
 * The factory for easy create SMS Gate adapters for TurboSMS
 *
 * @author Vitaiy Zhuk <zhuk2205@gmail.com>
 */
class TurboSmsAdapterFactory
{
    /**
     * Create the SOAP adapter for TurboSMS
     *
     * @param string $login
     * @param string $password
     *
     * @return TurboSmsSoapAdapter
     */
    public static function soap(string $login, string $password): TurboSmsSoapAdapter
    {
        $responseParser = (new ResponseParserFactory())->create();
        $soapClient = (new SoapClientFactory())->create();
        $configuration = new Configuration($login, $password);
        $authenticator = new TurboSmsSoapAuthenticator($soapClient, $configuration, $responseParser);

        return new TurboSmsSoapAdapter($soapClient, $responseParser, $authenticator);
    }
}
