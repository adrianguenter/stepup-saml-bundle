<?php

/**
 * Copyright 2014 SURFnet bv
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Surfnet\SamlBundle\Tests;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use PHPUnit\Framework\TestCase as TestCase;
use Surfnet\SamlBundle\Exception\RuntimeException;
use Surfnet\SamlBundle\Monolog\SamlAuthenticationLogger;

final class SamlAuthenticationLoggerTest extends MockeryTestCase
{
    /**
     * @test
     */
    public function it_returns_a_logger_for_an_authentication()
    {
        $requestId = md5('boesboes');

        $innerLogger = m::mock('Psr\Log\LoggerInterface');
        $innerLogger->shouldReceive('emergency')->with('message2', ['sari' => $requestId])->once();

        $logger = new SamlAuthenticationLogger($innerLogger);
        $logger = $logger->forAuthentication($requestId);
        $logger->emergency('message2');
    }

    /**
     * @test
     */
    public function it_throws_when_no_authentication()
    {
        $logger = new SamlAuthenticationLogger(m::mock('Psr\Log\LoggerInterface'));
        $this->expectException(RuntimeException::class);
        $logger->emergency('message2');
    }
}
