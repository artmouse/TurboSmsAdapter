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

namespace SmsGate\Adapter\TurboSms\Soap;

use SmsGate\Adapter\AdapterInterface;
use SmsGate\Adapter\TurboSms\Exception\MissingRequiredParameterException;
use SmsGate\Adapter\TurboSms\Exception\MissingSenderException;
use SmsGate\Adapter\TurboSms\Exception\SignatureInvalidException;
use SmsGate\Adapter\TurboSms\Exception\SignatureNotAllowedException;
use SmsGate\Adapter\TurboSms\Soap\Authenticator\TurboSmsSoapAuthenticatorInterface;
use SmsGate\Adapter\TurboSms\Soap\ResponseParser\ResponseParserInterface;
use SmsGate\Error;
use SmsGate\ErrorReasons;
use SmsGate\Exception\SendSmsException;
use SmsGate\Message;
use SmsGate\Phone;
use SmsGate\Result;
use SmsGate\ResultCollection;

/**
 * The SMS Gate adapter for send SMS via TurboSMS with use SOAP protocol.
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class TurboSmsSoapAdapter implements AdapterInterface
{
    /**
     * @var \SoapClient
     */
    private $soapClient;

    /**
     * @var ResponseParserInterface
     */
    private $responseParser;

    /**
     * @var TurboSmsSoapAuthenticatorInterface
     */
    private $authenticator;

    /**
     * Constructor.
     *
     * @param \SoapClient                        $soapClient
     * @param ResponseParserInterface            $responseParser
     * @param TurboSmsSoapAuthenticatorInterface $authenticator
     */
    public function __construct(
        \SoapClient $soapClient,
        ResponseParserInterface $responseParser,
        TurboSmsSoapAuthenticatorInterface $authenticator
    ) {
        $this->soapClient = $soapClient;
        $this->responseParser = $responseParser;
        $this->authenticator = $authenticator;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Message $message, Phone ...$recipients): ResultCollection
    {
        if (!$message->getSender()) {
            throw new MissingSenderException('The "sender" of message is required for TurboSMS adapter.');
        }

        $this->authenticator->authenticate();

        $phoneNumbers = array_map(function (Phone $recipient) {
            return '+'.$recipient->getValue();
        }, $recipients);

        $result = $this->soapClient->__soapCall('SendSMS', [
            [
                'sender'      => $message->getSender(),
                'destination' => implode(',', $phoneNumbers),
                'text'        => $message->getMessage(),
                'wappush'     => '',
            ],
        ]);

        // @codingStandardsIgnoreStart
        $resultMessages = $result->SendSMSResult->ResultArray;

        // @codingStandardsIgnoreEnd

        return $this->processResultMessages($message, $recipients, $resultMessages);
    }

    /**
     * Get the balance
     *
     * @return float
     */
    public function getBalance(): float
    {
        $this->authenticator->authenticate();

        return (float) $this->soapClient->__soapCall('GetCreditBalance', [])
            ->GetCreditBalanceResult;
    }

    /**
     * Try to resolve error reason
     *
     * @param string $message
     *
     * @return string
     */
    private function tryResolveErrorReason(string $message): string
    {
        $key = $this->responseParser->parse($message);

        if ($key === 'invalid_phone') {
            return ErrorReasons::INVALID_PHONE_NUMBER;
        }

        return ErrorReasons::UNKNOWN;
    }

    /**
     * Process the response from TurboSMS
     *
     * @param Message      $message
     * @param array        $recipients
     * @param string|array $resultMessages
     *
     * @return ResultCollection
     *
     * @throws \Exception
     */
    private function processResultMessages(Message $message, array $recipients, $resultMessages): ResultCollection
    {
        if (is_scalar($resultMessages)) {
            $key = $this->responseParser->parse($resultMessages);

            if ($key === 'signature_not_allowed') {
                throw new SignatureNotAllowedException(sprintf(
                    'The signature (sender) "%s" not allowed.',
                    $message->getSender()
                ));
            } elseif ($key === 'signature_invalid') {
                throw new SignatureInvalidException(sprintf(
                    'Invalid signature (sender): %s',
                    $message->getSender()
                ));
            }

            throw new SendSmsException(sprintf(
                'Cannot resolve the error for sending SMS. Error: %s',
                $resultMessages
            ));
        }

        $sendResult = $resultMessages[0];
        $sendResultKey = $this->responseParser->parse($sendResult);

        if ($sendResultKey === 'missing_required_parameters') {
            throw new MissingRequiredParameterException('Missing required parameters.');
        }

        if ($sendResultKey !== 'success' && $sendResultKey !== 'fail') {
            throw new SendSmsException(sprintf(
                'Fail send sms. Result messages: %s',
                json_encode($resultMessages)
            ));
        }

        array_shift($resultMessages);

        $resultData = [];

        foreach ($recipients as $recipient) {
            $messageId = array_shift($resultMessages);
            $success = preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $messageId);

            if ($success) {
                $resultData[] = Result::successfully($recipient, $messageId);
            } else {
                $reason = $this->tryResolveErrorReason($messageId);
                $resultData[] = Result::failed($recipient, new Error($reason, $messageId));
            }
        }

        return new ResultCollection(...$resultData);
    }
}
