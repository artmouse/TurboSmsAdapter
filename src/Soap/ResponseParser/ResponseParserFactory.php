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

namespace SmsGate\Adapter\TurboSms\Soap\ResponseParser;

/**
 * The factory for easy create response parser for TurboSMS.
 *
 * @author Vitaiy Zhuk <zhuk2205@gmail.com>
 */
class ResponseParserFactory
{
    /**
     * Create default response parser
     *
     * @return ResponseParserInterface
     */
    public function create(): ResponseParserInterface
    {
        return new ResponseParserChain(
            new XmlFileResponseParser(__DIR__.'/../../Resources/soap/auth-responses.xml'),
            new XmlFileResponseParser(__DIR__.'/../../Resources/soap/send-sms-responses.xml')
        );
    }
}
