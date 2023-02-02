<?php

declare(strict_types=1);

namespace Rezzza\SecurityBundle\Tests\Units\Security\Firewall;

use atoum\atoum;
use Rezzza\SecurityBundle\Security\Firewall\ReplayProtection as SUT;

class ReplayProtection extends atoum\test
{
    /**
     * @dataProvider dummyCases
     */
    public function test_disabled_state_should_accept_all_signatureTime($lifetime, $signatureTime, $currentTime): void
    {
        $this
            ->given(
                $sut = new SUT(false, $lifetime),
            )
            ->when(
                $accepted = $sut->accept($signatureTime, $currentTime),
            )
            ->then
                ->boolean($accepted)
                    ->isTrue()
        ;
    }

    public function dummyCases()
    {
        return [
            [100, 123456789, 9876543],
        ];
    }

    /**
     * @dataProvider goodCases
     */
    public function test_it_should_accept_signatureTime_still_valid($lifetime, $signatureTime, $currentTime): void
    {
        $this
            ->given(
                $sut = new SUT(true, $lifetime),
            )
            ->when(
                $accepted = $sut->accept($signatureTime, $currentTime),
            )
            ->then
                ->boolean($accepted)
                    ->isTrue()
        ;
    }

    public function goodCases()
    {
        return [
            [10, 1417626128, 1417626138],
            [500, 1417626100, 1417626250],
            [500, 1417626100, 1417626099],
            [555, 1417626111, 1417626111],
        ];
    }

    /**
     * @dataProvider wrongCases
     */
    public function test_it_should_not_accept_signatureTime_expired($lifetime, $signatureTime, $currentTime): void
    {
        $this
            ->given(
                $sut = new SUT(true, $lifetime),
            )
            ->when(
                $accepted = $sut->accept($signatureTime, $currentTime),
            )
            ->then
                ->boolean($accepted)
                    ->isFalse()
        ;
    }

    public function wrongCases()
    {
        return [
            [10, 1417626128, 1417626139],
            [500, 1417626100, 1417625100],
            [555, 1417626111, 1417626679],
        ];
    }
}
