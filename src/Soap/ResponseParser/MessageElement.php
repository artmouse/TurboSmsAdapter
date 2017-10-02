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
 * The default element of messages.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class MessageElement
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $message;

    /**
     * @var bool
     */
    private $regexp;

    /**
     * Constructor.
     *
     * @param string $key
     * @param string $message
     * @param bool   $regex
     */
    public function __construct(string $key, string $message, bool $regex = false)
    {
        $this->key = $key;
        $this->message = $message;
        $this->regexp = $regex;
    }

    /**
     * Get the key
     *
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Is message matches?
     *
     * @param string $message
     *
     * @return bool
     */
    public function match(string $message): bool
    {
        if ($this->regexp) {
            return (bool) preg_match($this->message, $message);
        }

        return mb_strtolower($this->message) === mb_strtolower($message);
    }
}
