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

namespace SmsGate\Adapter\TurboSms\Tests\Soap;

use PHPUnit\Framework\TestCase;
use SmsGate\Adapter\TurboSms\Soap\SoapClientFactory;

/**
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class SoapClientFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessCreate(): void
    {
        $client = (new SoapClientFactory())->create();

        self::assertInstanceOf(\SoapClient::class, $client);
    }
}
