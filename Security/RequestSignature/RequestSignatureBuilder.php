<?php

namespace Rezzza\SecurityBundle\Security\RequestSignature;

use Rezzza\SecurityBundle\Security\Firewall\Context;

/**
 * RequestSignatureBuilder
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RequestSignatureBuilder
{
    /**
     * @param Context $context context
     *
     * @return string
     */
    public function build(Context $context)
    {
        $payload   = array();

        if ($context->get('firewall.replay_protection', false)) {
            $payload[] = $context->get('request.signature_time', time());
        }

        $payload[] = strtoupper($context->get('request.method'));
        $payload[] = $context->get('request.host');
        $payload[] = $context->get('request.path_info');
        $payload[] = $context->get('request.content');

        return hash_hmac(
            $context->get('firewall.algorithm', 'sha1'),
            implode("\n", $payload),
            $context->get('firewall.secret')
        );
    }
}
