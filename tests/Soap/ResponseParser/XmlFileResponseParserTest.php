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
use SmsGate\Adapter\TurboSms\Soap\ResponseParser\XmlFileResponseParser;

/**
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class XmlFileResponseParserTest extends TestCase
{
    /**
     * @var string
     */
    private $tmpFile;

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        if ($this->tmpFile) {
            unlink($this->tmpFile);
            $this->tmpFile = null;
        }
    }

    /**
     * @test
     */
    public function shouldSuccessCreate(): void
    {
        $customXml = <<<XML
<messages>
    <message key="some1">Some1</message>
    <message key="some2" regexp="true">/^Some2.+/</message>
</messages>
XML;

        $tmpFile = tempnam(sys_get_temp_dir(), 'sms-gate.trubosms.soap');
        file_put_contents($tmpFile, $customXml);

        $parser = new XmlFileResponseParser($tmpFile);

        self::assertEquals('some1', $parser->parse('Some1'));
        self::assertEquals('some2', $parser->parse('Some2 foo bar'));
        self::assertNull($parser->parse('some foo bar qwerty'));
    }

    /**
     * @test
     *
     * @expectedException \InvalidArgumentException
     */
    public function shouldFailIfFileNotFound(): void
    {
        $tmpFile = tempnam(sys_get_temp_dir(), 'sms-gate.trubosms.soap');
        unlink($tmpFile);

        new XmlFileResponseParser($tmpFile);
    }
}
