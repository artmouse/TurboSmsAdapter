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
use SmsGate\Adapter\TurboSms\Soap\ResponseParser\MessageElement;

/**
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class MessageElementTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessCreateWithoutRegexp(): void
    {
        $message = new MessageElement('some', 'some text', false);

        self::assertEquals('some', $message->getKey());
        self::assertTrue($message->match('some text'));
        self::assertFalse($message->match('-some text-'));
    }

    /**
     * @test
     */
    public function shouldSuccessCreateWithRegexp(): void
    {
        $message = new MessageElement('some', '/^some text.+/', true);

        self::assertEquals('some', $message->getKey());
        self::assertTrue($message->match('some text 1234567'));
        self::assertFalse($message->match('-some text 1234567'));
    }
}
