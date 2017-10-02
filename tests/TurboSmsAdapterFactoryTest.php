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

namespace SmsGate\Adapter\TurboSms\Tests;

use PHPUnit\Framework\TestCase;
use SmsGate\Adapter\TurboSms\Soap\TurboSmsSoapAdapter;
use SmsGate\Adapter\TurboSms\TurboSmsAdapterFactory;

/**
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class TurboSmsAdapterFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessCreate(): void
    {
        $adapter = TurboSmsAdapterFactory::soap('login', 'password');

        self::assertInstanceOf(TurboSmsSoapAdapter::class, $adapter);
    }
}
