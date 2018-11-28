<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\MinkExtension\Context\RawMinkContext;

use Rezzza\SecurityBundle\Security\Firewall\SignatureConfig;
use Rezzza\SecurityBundle\Security\Firewall\SignedRequest;

class FeatureContext extends RawMinkContext implements Context, SnippetAcceptingContext
{
    private $signatureConfig;

    public function __construct(SignatureConfig $signatureConfig)
    {
        $this->signatureConfig = $signatureConfig;
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
        $signedRequest = new SignedRequest('GET', 'dev.security.com', $url, '', $timestamp);

        return $signedRequest->buildSignature($this->signatureConfig);
    }
}
