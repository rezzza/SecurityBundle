<?php

namespace Rezzza\SecurityBundle\Security\Firewall;

use Rezzza\SecurityBundle\Security\RequestSignatureToken;
use Rezzza\SecurityBundle\Security\Firewall\RequestSignatureEntryPoint;

/**
 * Context
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class Context
{
    /**
     * @var array
     */
    protected $data = array();

    /**
     * @param RequestSignatureToken $token token
     */
    public function hydrateWithToken(RequestSignatureToken $token)
    {
        $request = $token->request;

        $this->set('request.method', $request->server->get('REQUEST_METHOD'));
        $this->set('request.host', $request->server->get('HTTP_HOST'));
        $this->set('request.path_info', $request->getPathInfo());
        $this->set('request.content', rawurldecode($request->getContent()));
        $this->set('request.signature_time', $token->signatureTime);
    }

    /**
     * @param RequestSignatureEntryPoint $entryPoint entryPoint
     */
    public function hydrateWithEntryPoint(RequestSignatureEntryPoint $entryPoint)
    {
        $this->set('firewall.replay_protection', $entryPoint->get('replay_protection'));
        $this->set('firewall.algorithm', $entryPoint->get('algorithm'));
        $this->set('firewall.secret', $entryPoint->get('secret'));
    }

    /**
     * @param string $key   key
     * @param mixed  $value value
     *
     * @return Context
     */
    public function set($key, $value)
    {
        $this->data[$key] = $value;

        return $this;
    }

    /**
     * @param string $key     key
     * @param mixed  $default default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return isset($this->data[$key]) ? $this->data[$key] : $default;
    }
}
