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

namespace SmsGate\Adapter\TurboSms\Tests\Soap;

use PHPUnit\Framework\TestCase;
use SmsGate\Adapter\TurboSms\Soap\Authenticator\TurboSmsSoapAuthenticatorInterface;
use SmsGate\Adapter\TurboSms\Soap\ResponseParser\ResponseParserInterface;
use SmsGate\Adapter\TurboSms\Soap\TurboSmsSoapAdapter;
use SmsGate\Error;
use SmsGate\Message;
use SmsGate\Phone;
use SmsGate\Result;
use SmsGate\ResultCollection;

/**
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class TurboSmsSoapAdapterTest extends TestCase
{
    /**
     * @var \SoapClient|\PHPUnit_Framework_MockObject_MockObject
     */
    private $client;

    /**
     * @var ResponseParserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $responseParser;

    /**
     * @var TurboSmsSoapAuthenticatorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $authenticator;

    /**
     * @var TurboSmsSoapAdapter
     */
    private $adapter;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->client = $this->createMock(\SoapClient::class);
        $this->authenticator = $this->createMock(TurboSmsSoapAuthenticatorInterface::class);
        $this->responseParser = $this->createMock(ResponseParserInterface::class);

        $this->adapter = new TurboSmsSoapAdapter($this->client, $this->responseParser, $this->authenticator);

        $this->authenticator->expects(self::any())
            ->method('authenticate');
    }

    /**
     * @test
     */
    public function shouldSuccessGetBalance(): void
    {
        $getBalanceResult = (object) ['GetCreditBalanceResult' => '1.01'];

        $this->client->expects(self::once())
            ->method('__soapCall')
            ->with('GetCreditBalance', [])
            ->willReturn($getBalanceResult);

        $result = $this->adapter->getBalance();

        self::assertSame(1.01, $result);
    }

    /**
     * @test
     *
     * @expectedException \SmsGate\Adapter\TurboSms\Exception\MissingSenderException
     * @expectedExceptionMessage The "sender" of message is required for TurboSMS adapter.
     */
    public function shouldFailSendWithoutSender(): void
    {
        $this->adapter->send(new Message('some'), new Phone('380991234567'));
    }

    /**
     * @test
     */
    public function shouldSuccessSendMessage(): void
    {
        $sendSmsArguments = $this->createSendSmsArguments('+380981234567', '+380991234567');

        $sendSmsResult = (object) [
            'SendSMSResult' => (object) [
                'ResultArray' => [
                    'first line of result',
                    '6352e55c-a74a-11e7-abc4-cec278b6b50a',
                    '6eb1e1c8-a74a-11e7-abc4-cec278b6b50a',
                ],
            ],
        ];

        $this->client->expects(self::once())
            ->method('__soapCall')
            ->with('SendSMS', $sendSmsArguments)
            ->willReturn($sendSmsResult);

        $this->responseParser->expects(self::once())
            ->method('parse')
            ->with('first line of result')
            ->willReturn('success');

        $message = new Message('New product on site.', 'TurboSMS');
        $result = $this->adapter->send($message, new Phone('380981234567'), new Phone('380991234567'));

        self::assertEquals(new ResultCollection(
            new Result(new Phone('380981234567'), true, '6352e55c-a74a-11e7-abc4-cec278b6b50a'),
            new Result(new Phone('380991234567'), true, '6eb1e1c8-a74a-11e7-abc4-cec278b6b50a')
        ), $result);
    }

    /**
     * @test
     *
     * @expectedException \SmsGate\Adapter\TurboSms\Exception\SignatureNotAllowedException
     * @expectedExceptionMessage The signature (sender) "TurboSMS" not allowed.
     */
    public function shouldFailSendSmsIfSignatureNotAllowed(): void
    {
        $sendSmsArguments = $this->createSendSmsArguments('+380991234567');

        $sendSmsResult = (object) [
            'SendSMSResult' => (object) [
                'ResultArray' => 'result of send sms',
            ],
        ];

        $this->client->expects(self::once())
            ->method('__soapCall')
            ->with('SendSMS', $sendSmsArguments)
            ->willReturn($sendSmsResult);

        $this->responseParser->expects(self::once())
            ->method('parse')
            ->with('result of send sms')
            ->willReturn('signature_not_allowed');

        $message = new Message('New product on site.', 'TurboSMS');
        $this->adapter->send($message, new Phone('380991234567'));
    }

    /**
     * @test
     *
     * @expectedException \SmsGate\Adapter\TurboSms\Exception\SignatureInvalidException
     * @expectedExceptionMessage Invalid signature (sender): TurboSMS
     */
    public function shouldFailSendSmsIfSignatureIsInvalid(): void
    {
        $sendSmsArguments = $this->createSendSmsArguments('+380991234567');

        $sendSmsResult = (object) [
            'SendSMSResult' => (object) [
                'ResultArray' => 'result of send sms',
            ],
        ];

        $this->client->expects(self::once())
            ->method('__soapCall')
            ->with('SendSMS', $sendSmsArguments)
            ->willReturn($sendSmsResult);

        $this->responseParser->expects(self::once())
            ->method('parse')
            ->with('result of send sms')
            ->willReturn('signature_invalid');

        $message = new Message('New product on site.', 'TurboSMS');
        $this->adapter->send($message, new Phone('380991234567'));
    }

    /**
     * @test
     *
     * @expectedException \SmsGate\Exception\SendSmsException
     * @expectedExceptionMessage Cannot resolve the error for sending SMS. Error: result of send sms
     */
    public function shouldFailSendSmsIfReturnScalarAndErrorIsUnknown(): void
    {
        $sendSmsArguments = $this->createSendSmsArguments('+380991234567');

        $sendSmsResult = (object) [
            'SendSMSResult' => (object) [
                'ResultArray' => 'result of send sms',
            ],
        ];

        $this->client->expects(self::once())
            ->method('__soapCall')
            ->with('SendSMS', $sendSmsArguments)
            ->willReturn($sendSmsResult);

        $this->responseParser->expects(self::once())
            ->method('parse')
            ->with('result of send sms')
            ->willReturn('some');

        $message = new Message('New product on site.', 'TurboSMS');
        $this->adapter->send($message, new Phone('380991234567'));
    }

    /**
     * @test
     *
     * @expectedException \SmsGate\Adapter\TurboSms\Exception\MissingRequiredParameterException
     * @expectedExceptionMessage Missing required parameters.
     */
    public function shouldFailSendSmsIfMissingRequiredParameters(): void
    {
        $sendSmsArguments = $this->createSendSmsArguments('+380991234567');

        $sendSmsResult = (object) [
            'SendSMSResult' => (object) [
                'ResultArray' => ['first line of result'],
            ],
        ];

        $this->client->expects(self::once())
            ->method('__soapCall')
            ->with('SendSMS', $sendSmsArguments)
            ->willReturn($sendSmsResult);

        $this->responseParser->expects(self::once())
            ->method('parse')
            ->with('first line of result')
            ->willReturn('missing_required_parameters');

        $message = new Message('New product on site.', 'TurboSMS');
        $this->adapter->send($message, new Phone('380991234567'));
    }

    /**
     * @test
     *
     * @expectedException \SmsGate\Exception\SendSmsException
     * @expectedExceptionMessage Fail send sms. Result messages: ["first line of result"]
     */
    public function shouldFailSendSmsWithUnknownError(): void
    {
        $sendSmsArguments = $this->createSendSmsArguments('+380991234567');

        $sendSmsResult = (object) [
            'SendSMSResult' => (object) [
                'ResultArray' => ['first line of result'],
            ],
        ];

        $this->client->expects(self::once())
            ->method('__soapCall')
            ->with('SendSMS', $sendSmsArguments)
            ->willReturn($sendSmsResult);

        $this->responseParser->expects(self::once())
            ->method('parse')
            ->with('first line of result')
            ->willReturn('some_foo');

        $message = new Message('New product on site.', 'TurboSMS');
        $this->adapter->send($message, new Phone('380991234567'));
    }

    /**
     * @test
     */
    public function shouldSuccessSendSmsWithErrors(): void
    {
        $sendSmsArguments = $this->createSendSmsArguments('+380991234567', '+380981234567', '+380671234567');

        $sendSmsResult = (object) [
            'SendSMSResult' => (object) [
                'ResultArray' => [
                    'first line of result',
                    '77002c02-a75b-11e7-abc4-cec278b6b50a',
                    'result of 380981234567',
                    'result of 380671234567',
                ],
            ],
        ];

        $this->client->expects(self::once())
            ->method('__soapCall')
            ->with('SendSMS', $sendSmsArguments)
            ->willReturn($sendSmsResult);

        $this->responseParser->expects(self::exactly(3))
            ->method('parse')
            ->with(self::logicalOr(
                'first line of result',
                'result of 380981234567',
                'result of 380671234567'
            ))
            ->willReturnMap([
                ['first line of result', 'success'],
                ['result of 380981234567', 'invalid_phone'],
                ['result of 380671234567', 'some_foo_bar'],
            ]);

        $message = new Message('New product on site.', 'TurboSMS');
        $result = $this->adapter->send($message, new Phone('380991234567'), new Phone('380981234567'), new Phone('380671234567'));

        self::assertEquals(new ResultCollection(
            Result::successfully(new Phone('380991234567'), '77002c02-a75b-11e7-abc4-cec278b6b50a'),
            Result::failed(new Phone('380981234567'), new Error('InvalidPhoneNumber', 'result of 380981234567')),
            Result::failed(new Phone('380671234567'), new Error('Unknown', 'result of 380671234567'))
        ), $result);
    }

    /**
     * Create send sms arguments
     *
     * @param array ...$phones
     *
     * @return array
     */
    private function createSendSmsArguments(...$phones): array
    {
        return [
            [
                'sender'      => 'TurboSMS',
                'destination' => implode(',', $phones),
                'text'        => 'New product on site.',
                'wappush'     => '',
            ],
        ];
    }
}
