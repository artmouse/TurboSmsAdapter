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
 * The interface for control response parsers. The TurboSMS return simple strings in Soap response
 * and we should control error by this message. This parser should return the key of error by message.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
interface ResponseParserInterface
{
    /**
     * Try to parse error reason by message
     *
     * @param string $message
     *
     * @return null|string
     */
    public function parse(string $message): ?string;
}
