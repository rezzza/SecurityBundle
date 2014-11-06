<?php

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;

use Behat\MinkExtension\Context\RawMinkContext;

/**
 * Defines application features from the specific context.
 */
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
        $this->visitPath(
            $this->buildUrlSigned($url, time())
        );
    }

    /**
     * @When I am on :url with good signature but I wait :seconds seconds before perform request
     */
    public function iAmOnWithGoodSignatureButIWaitSecondsBeforePerformRequest($url, $seconds)
    {
        $url = $this->buildUrlSigned($url, time());

        sleep($seconds);

        $this->visitPath($url);
    }

    private function buildUrlSigned($url, $timestamp)
    {
        $this->firewallContext
            ->set('request.method', 'GET')
            ->set('request.host', 'localhost:8888')
            ->set('request.path_info', $url)
            ->set('request.signature_time', $timestamp)
        ;
        $signature = $this->signatureBuilder->build($this->firewallContext);

        return sprintf('%s?_signature=%s&_signature_ttl=%s', $url, $signature, $timestamp);
    }
}
