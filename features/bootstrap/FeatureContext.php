<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;

class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    private $signatureBuilder;

    private $firewallContext;

    public function __construct($signatureBuilder, $firewallContext)
    {
        $this->signatureBuilder = $signatureBuilder;
        $this->firewallContext = $firewallContext;
    }

    /**
     * @When I am on :url with good signature
     */
    public function iAmOnWithGoodSignature($url)
    {
        $timestamp = time();
        $signature = $this->buildSignature($url, $timestamp);
        $urlSigned = $this->buildUrlSigned($url, $signature, $timestamp);

        $this->visitPath($urlSigned);
    }

    /**
     * @When I am on :url with good signature but I wait :seconds seconds before perform request
     */
    public function iAmOnWithGoodSignatureButIWaitSecondsBeforePerformRequest($url, $seconds)
    {
        $timestamp = time();
        $signature = $this->buildSignature($url, $timestamp);
        $url = $this->buildUrlSigned($url, $signature, $timestamp);

        sleep($seconds);

        $this->visitPath($url);
    }

    /**
     * @When I am on :url with wrong signature
     */
    public function iAmOnWithWrongSignature($url)
    {
        $urlSigned = $this->buildUrlSigned($url, 'peutimporte', time());

        $this->visitPath($urlSigned);
    }

    private function buildUrlSigned($url, $signature, $timestamp)
    {
        return sprintf('%s?_signature=%s&_signature_ttl=%s', $url, $signature, $timestamp);
    }

    private function buildSignature($url, $timestamp)
    {
        $this->firewallContext
            ->set('request.method', 'GET')
            ->set('request.host', 'security-bundle.vlr.localtest')
            ->set('request.path_info', $url)
            ->set('request.signature_time', $timestamp)
        ;

        return $this->signatureBuilder->build($this->firewallContext);
    }
}
