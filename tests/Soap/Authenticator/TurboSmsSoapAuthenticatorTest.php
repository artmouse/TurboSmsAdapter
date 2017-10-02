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

namespace SmsGate\Adapter\TurboSms\Tests\Soap\Authenticator;

use PHPUnit\Framework\TestCase;
use SmsGate\Adapter\TurboSms\Configuration;
use SmsGate\Adapter\TurboSms\Soap\Authenticator\TurboSmsSoapAuthenticator;
use SmsGate\Adapter\TurboSms\Soap\ResponseParser\ResponseParserInterface;

/**
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class TurboSmsSoapAuthenticatorTest extends TestCase
{
    /**
     * @var \SoapClient|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $client;

    /**
     * @var Configuration|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configuration;

    /**
     * @var ResponseParserInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $responseParser;

    /**
     * @var TurboSmsSoapAuthenticator
     */
    protected $authenticator;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->client = $this->createMock(\SoapClient::class);
        $this->configuration = $this->createMock(Configuration::class);
        $this->responseParser = $this->createMock(ResponseParserInterface::class);

        $this->authenticator = new TurboSmsSoapAuthenticator($this->client, $this->configuration, $this->responseParser);
    }

    /**
     * @test
     */
    public function shouldSuccessAuthenticate(): void
    {
        $this->registerConfiguration('login', 'password');

        $authSoapArguments = [
            [
                'login'    => 'login',
                'password' => 'password',
            ],
        ];

        $authSoapResult = (object) ['AuthResult' => 'This is auth result'];

        $this->client->expects(self::exactly(1))
            ->method('__soapCall')
            ->with('Auth', $authSoapArguments)
            ->willReturn($authSoapResult);

        $this->responseParser->expects(self::once())
            ->method('parse')
            ->with('This is auth result')
            ->willReturn('success');

        $this->authenticator->authenticate();
    }

    /**
     * @test
     *
     * @expectedException \SmsGate\Exception\FailAuthenticationException
     * @expectedExceptionMessage Fail authentication
     */
    public function shouldFailAuthenticate(): void
    {
        $this->registerConfiguration('login', 'password');

        $authSoapArguments = [
            [
                'login'    => 'login',
                'password' => 'password',
            ],
        ];

        $authSoapResult = (object) ['AuthResult' => 'This is auth result'];

        $this->client->expects(self::exactly(1))
            ->method('__soapCall')
            ->with('Auth', $authSoapArguments)
            ->willReturn($authSoapResult);

        $this->responseParser->expects(self::once())
            ->method('parse')
            ->with('This is auth result')
            ->willReturn('fail');

        $this->authenticator->authenticate();
    }

    /**
     * Register the configuration of adapter
     *
     * @param string $login
     * @param string $password
     */
    private function registerConfiguration(string $login, string $password): void
    {
        $this->configuration->expects(self::any())
            ->method('getLogin')
            ->willReturn($login);

        $this->configuration->expects(self::any())
            ->method('getPassword')
            ->willReturn($password);
    }
}
