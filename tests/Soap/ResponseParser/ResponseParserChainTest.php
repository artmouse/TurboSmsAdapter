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
use SmsGate\Adapter\TurboSms\Soap\ResponseParser\ResponseParserInterface;

/**
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ResponseParserChainTest extends TestCase
{
    /**
     * @test
     */
    public function shouldSuccessParse(): void
    {
        $parser1 = $this->createMock(ResponseParserInterface::class);
        $parser2 = $this->createMock(ResponseParserInterface::class);
        $parser3 = $this->createMock(ResponseParserInterface::class);

        $chain = new ResponseParserChain($parser1, $parser2, $parser3);

        $parser1->expects(self::once())
            ->method('parse')
            ->with('some')
            ->willReturn(null);

        $parser2->expects(self::once())
            ->method('parse')
            ->with('some')
            ->willReturn('some-key');

        $parser3->expects(self::never())
            ->method('parse');

        $result = $chain->parse('some');

        self::assertEquals('some-key', $result);
    }

    /**
     * @test
     */
    public function shouldReturnNullIfAnyParserCannotParse(): void
    {
        $chain = new ResponseParserChain();

        self::assertNull($chain->parse('some'));
    }
}
