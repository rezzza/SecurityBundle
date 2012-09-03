<?php

namespace Rezzza\SecurityBundle\Security\Firewall;

/**
 * RequestSignatureEntryPoint
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RequestSignatureEntryPoint
{
    private $datas = array();

    /**
     * @param array $config config
     */
    public function __construct(array $config)
    {
        $this->datas['algorythm']                   = $config['algorythm'];
        $this->datas['secret']                      = $config['secret'];
        $this->datas['ignore']                      = $config['ignore'];
        $this->datas['parameter']                   = $config['parameter'];
        $this->datas['replay_protection']           = $config['replay_protection']['enabled'];
        $this->datas['replay_protection_lifetime']  = $config['replay_protection']['lifetime'];
        $this->datas['replay_protection_parameter'] = $config['replay_protection']['parameter'];
    }

    /**
     * @return boolean
     */
    public function isIgnored()
    {
        return (bool) $this->datas['ignore'];
    }

    /**
     * get a data
     *
     * @param string $key key
     *
     * @return void
     */
    public function get($key)
    {
        return $this->datas[$key];
    }
}
