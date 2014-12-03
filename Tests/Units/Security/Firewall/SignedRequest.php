<?php

namespace Rezzza\SecurityBundle\Tests\Units\Security\Firewall;

use mageekguy\atoum;

use Rezzza\SecurityBundle\Security\Firewall\SignedRequest as SUT;

class SignedRequest extends atoum\test
{
    public function test_authenticate_invalid_signature_lead_to_exception()
    {
        $this
            ->given(
                $mockSignatureConfig = new \mock\Rezzza\SecurityBundle\Security\Firewall\SignatureConfig(true, 'sha1', 's3cr3t'),
                $mockReplayProtection = new \mock\Rezzza\SecurityBundle\Security\Firewall\ReplayProtection(true, 100),
                $sut = new SUT('GET', 'localhost', '/url', 'content', 123)
            )
            ->exception(function () use ($sut, $mockSignatureConfig, $mockReplayProtection) {
                $sut->authenticateSignature('LALALALAAL', $mockSignatureConfig, $mockReplayProtection);
            })
                ->isInstanceOf('Rezzza\SecurityBundle\Security\Firewall\InvalidSignatureException')
        ;
    }

    public function test_replay_protected_denied_lead_to_exception()
    {
        $this
            ->given(
                $mockSignatureConfig = new \mock\Rezzza\SecurityBundle\Security\Firewall\SignatureConfig(true, 'sha1', 's3cr3t'),
                $mockReplayProtection = new \mock\Rezzza\SecurityBundle\Security\Firewall\ReplayProtection(true, 100),
                $mockReplayProtection->getMockController()->accept = false,
                $sut = new SUT('GET', 'localhost', '/url', 'content', 123)
            )
            ->exception(function () use ($sut, $mockSignatureConfig, $mockReplayProtection) {
                $sut->authenticateSignature('68a9f810beed3c8bbbf98096a60d36ade5f81d42', $mockSignatureConfig, $mockReplayProtection);
            })
                ->isInstanceOf('Rezzza\SecurityBundle\Security\Firewall\ExpiredSignatureException')
        ;
    }

    public function test_it_should_authenticated_valid_signature_not_expired()
    {
        $this
            ->given(
                $mockSignatureConfig = new \mock\Rezzza\SecurityBundle\Security\Firewall\SignatureConfig(true, 'sha1', 's3cr3t'),
                $mockReplayProtection = new \mock\Rezzza\SecurityBundle\Security\Firewall\ReplayProtection(true, 100),
                $mockReplayProtection->getMockController()->accept = true,
                $sut = new SUT('GET', 'localhost', '/url', 'content', 123)
            )
            ->when(
                $authenticated = $sut->authenticateSignature('68a9f810beed3c8bbbf98096a60d36ade5f81d42', $mockSignatureConfig, $mockReplayProtection)
            )
                ->boolean($authenticated)
                    ->isTrue()
        ;
    }

    public function test_signature_generated_with_replay_protection_should_not_be_the_same_without()
    {
        $this
            ->given(
                $mockSignatureConfig = new \mock\Rezzza\SecurityBundle\Security\Firewall\SignatureConfig(false, 'sha1', 's3cr3t'),
                $mockReplayProtection = new \mock\Rezzza\SecurityBundle\Security\Firewall\ReplayProtection(true, 100),
                $mockReplayProtection->getMockController()->accept = true,
                $sut = new SUT('GET', 'localhost', '/url', 'content', 123)
            )
            ->exception(function () use ($sut, $mockSignatureConfig, $mockReplayProtection) {
                $sut->authenticateSignature('68a9f810beed3c8bbbf98096a60d36ade5f81d42', $mockSignatureConfig, $mockReplayProtection);
            })
                ->isInstanceOf('Rezzza\SecurityBundle\Security\Firewall\InvalidSignatureException')
        ;
    }
}
