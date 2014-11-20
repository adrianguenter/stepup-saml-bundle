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

namespace Surfnet\SamlBundle\Entity;

use Symfony\Component\Routing\RouterInterface;
use SAML2_Configuration_PrivateKey as PrivateKey;

class HostedEntities
{
    /**
     * @var ServiceProvider
     */
    private $serviceProvider;

    /**
     * @var array
     */
    private $serviceProviderConfiguration;

    /**
     * @var IdentityProvider
     */
    private $identityProvider;

    /**
     * @var array
     */
    private $identityProviderConfiguration;

    /**
     * @var \Symfony\Component\Routing\RouterInterface
     */
    private $router;

    public function __construct(
        RouterInterface $router,
        array $serviceProviderConfiguration = null,
        array $identityProviderConfiguration = null
    ) {
        $this->router = $router;
        $this->serviceProviderConfiguration = $serviceProviderConfiguration;
        $this->identityProviderConfiguration = $identityProviderConfiguration;
    }

    /**
     * @return null|ServiceProvider
     */
    public function getServiceProvider()
    {
        if (!empty($this->serviceProvider)) {
            return $this->serviceProvider;
        }

        if (!$this->serviceProviderConfiguration['enabled']) {
            return null;
        }

        $configuration = $this->createStandardEntityConfiguration($this->serviceProviderConfiguration);
        $configuration['assertionConsumerUrl'] = $this->generateUrl(
            $this->serviceProviderConfiguration['assertion_consumer_route']
        );

        return $this->serviceProvider = new ServiceProvider($configuration);
    }

    /**
     * @return null|IdentityProvider
     */
    public function getIdentityProvider()
    {
        if (!empty($this->identityProvider)) {
            return $this->identityProvider;
        }

        if (!$this->identityProviderConfiguration['enabled']) {
            return null;
        }

        $configuration = $this->createStandardEntityConfiguration($this->identityProviderConfiguration);
        $configuration['ssoUrl'] = $this->generateUrl(
            $this->identityProviderConfiguration['sso_route']
        );

        return $this->identityProvider = new IdentityProvider($configuration);
    }

    /**
     * @param array $entityConfiguration
     * @return array
     */
    private function createStandardEntityConfiguration($entityConfiguration)
    {
        $privateKey = new PrivateKey($entityConfiguration['private_key'], PrivateKey::NAME_DEFAULT);

        return [
            'entityId'                   => $this->generateUrl($entityConfiguration['entity_id_route']),
            'certificateFile'            => $entityConfiguration['public_key'],
            'privateKeys'                => [$privateKey],
            'blacklistedAlgorithms'      => [],
            'assertionEncryptionEnabled' => false
        ];
    }

    /**
     * @param string $route
     * @return string
     */
    private function generateUrl($route)
    {
        return $this->router->generate($route, [], RouterInterface::ABSOLUTE_URL);
    }
}
