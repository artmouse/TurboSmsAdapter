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
use SmsGate\Adapter\TurboSms\Configuration;

/**
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ConfigurationTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessCreate(): void
    {
        $configuration = new Configuration('login', 'password');

        self::assertEquals('login', $configuration->getLogin());
        self::assertEquals('password', $configuration->getPassword());
    }
}
