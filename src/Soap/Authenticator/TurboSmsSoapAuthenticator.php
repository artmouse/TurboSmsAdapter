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

namespace SmsGate\Adapter\TurboSms\Soap\Authenticator;

use SmsGate\Adapter\TurboSms\Configuration;
use SmsGate\Adapter\TurboSms\Soap\ResponseParser\ResponseParserInterface;
use SmsGate\Exception\FailAuthenticationException;

/**
 * Default authenticator for TurboSMS SOAP
 *
 * @author Vitaliy Zhuk <zhuk2205@gmail.com>
 */
class TurboSmsSoapAuthenticator implements TurboSmsSoapAuthenticatorInterface
{
    /**
     * @var \SoapClient
     */
    private $soapClient;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ResponseParserInterface
     */
    private $responseParser;

    /**
     * Constructor.
     *
     * @param \SoapClient             $soapClient
     * @param Configuration           $configuration
     * @param ResponseParserInterface $responseParser
     */
    public function __construct(
        \SoapClient $soapClient,
        Configuration $configuration,
        ResponseParserInterface $responseParser
    ) {
        $this->soapClient = $soapClient;
        $this->configuration = $configuration;
        $this->responseParser = $responseParser;
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(): void
    {
        $credentials = [
            'login'    => $this->configuration->getLogin(),
            'password' => $this->configuration->getPassword(),
        ];

        $result = $this->soapClient->__soapCall('Auth', [$credentials]);

        // @codingStandardsIgnoreStart
        $authResult = $result->AuthResult;
        // @codingStandardsIgnoreEnd

        $resultKey = $this->responseParser->parse($authResult);

        if ($resultKey !== 'success') {
            throw new FailAuthenticationException('Fail authentication');
        }
    }
}
