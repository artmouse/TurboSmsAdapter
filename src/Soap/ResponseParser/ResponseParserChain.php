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
 * The chain of response parser.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class ResponseParserChain implements ResponseParserInterface
{
    /**
     * @var ResponseParserInterface[]
     */
    private $parsers;

    /**
     * Constructor.
     *
     * @param ResponseParserInterface[] ...$parsers
     */
    public function __construct(ResponseParserInterface ...$parsers)
    {
        $this->parsers = $parsers;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(string $message): ?string
    {
        foreach ($this->parsers as $parser) {
            $key = $parser->parse($message);

            if ($key) {
                return $key;
            }
        }

        return null;
    }
}
