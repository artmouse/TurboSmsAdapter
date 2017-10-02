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

namespace SmsGate\Adapter\TurboSms\Tests\Soap\ResponseParser;

use PHPUnit\Framework\TestCase;
use SmsGate\Adapter\TurboSms\Soap\ResponseParser\ResponseParserChain;
use SmsGate\Adapter\TurboSms\Soap\ResponseParser\ResponseParserFactory;
use SmsGate\Adapter\TurboSms\Soap\ResponseParser\XmlFileResponseParser;

/**
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ResponseParserFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessCreate(): void
    {
        $responseParser = (new ResponseParserFactory())->create();

        self::assertEquals(new ResponseParserChain(
            new XmlFileResponseParser(__DIR__.'/../../../src/Resources/soap/auth-responses.xml'),
            new XmlFileResponseParser(__DIR__.'/../../../src/Resources/soap/send-sms-responses.xml')
        ), $responseParser);
    }
}
