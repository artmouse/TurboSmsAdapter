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
 * The response parser with use XML files for store strings.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class XmlFileResponseParser implements ResponseParserInterface
{
    /**
     * @var MessageElement[]
     */
    private $messages;

    /**
     * Constructor.
     *
     * @param string $filePath
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function __construct(string $filePath)
    {
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException(sprintf(
                'The file "%s" was not found.',
                $filePath
            ));
        }

        $content = file_get_contents($filePath);

        $xml = new \SimpleXMLElement($content);

        $messages = [];

        /** @var \SimpleXMLElement $element */
        foreach ($xml->message as $element) {
            $regexp = (string) $element['regexp'] === 'true';
            $messages[] = new MessageElement((string) $element['key'], trim((string) $element), $regexp);
        }

        $this->messages = $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function parse(string $message): ?string
    {
        foreach ($this->messages as $messageElement) {
            if ($messageElement->match(trim($message))) {
                return $messageElement->getKey();
            }
        }

        return null;
    }
}
