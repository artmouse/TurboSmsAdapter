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

namespace SmsGate\Adapter\TurboSms\Soap;

/**
 * The factory for easy create the SOAP client for TurboSMS.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class SoapClientFactory
{
    /**
     * Create the default SOAP client for TurboSMS
     *
     * @return \SoapClient
     */
    public function create(): \SoapClient
    {
        return new \SoapClient('http://turbosms.in.ua/api/wsdl.html');
    }
}
