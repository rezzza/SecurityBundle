<?php

namespace Rezzza\SecurityBundle\Tests\Units\Request\Obfuscator;

use mageekguy\atoum;
use Rezzza\SecurityBundle\Request\Obfuscator\RequestObfuscator as TestedClass;

/**
 * RequestObfuscator
 *
 * @uses atoum\test
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RequestObfuscator extends atoum\test
{
    public function dataProviderObfuscate()
    {
        return array(
            // string on data.
            //@ObfuscateRequest(content="*")
            array(
                array('content' => 'foo'),
                array('content' => '*'),
                array('content' => 'XXX')
            ),
            //@ObfuscateRequest(content=".")
            array(
                array('content' => 'foobar'),
                array('content' => '.'),
                array('content' => 'XXXXXX')
            ),
            //@ObfuscateRequest()
            array(
                array('content' => 'foobar'),
                array(),
                array('content' => 'foobar')
            ),
            //@ObfuscateRequest(otherkey="*")
            array(
                array('content' => 'foobar'),
                array('otherkey' => '*'),
                array('content' => 'foobar')
            ),
            // array on data, simple patterns
            //@ObfuscateRequest(content="*")
            array(
                array('content' => array('key1' => 'foo', 'key2' => 'bar')),
                array('content' => '*'),
                array('content' => 'X')
            ),
            //@ObfuscateRequest(content="key1")
            array(
                array('content' => array('key1' => 'foo', 'key2' => 'bar')),
                array('content' => 'key1'),
                array('content' => array('key1' => 'XXX', 'key2' => 'bar'))
            ),
            //@ObfuscateRequest(content={"key1"})
            array(
                array('content' => array('key1' => array('foo' => 'pouet'), 'key2' => 'bar')),
                array('content' => array('key1')),
                array('content' => array('key1' => 'X', 'key2' => 'bar'))
            ),
            //@ObfuscateRequest(content={"key1", "key2"})
            array(
                array('content' => array('key1' => array('foo' => 'pouet'), 'key2' => 'bar')),
                array('content' => array('key1', 'key2')),
                array('content' => array('key1' => 'X', 'key2' => 'XXX'))
            ),
            //@ObfuscateRequest(content={"key1[key2]"})
            array(
                array('content' => array('key1' => array('key2' => 'pouet'), 'key2' => 'bar')),
                array('content' => array('key1[key2]')),
                array('content' => array('key1' => array('key2' => 'XXXXX'), 'key2' => 'bar'))
            ),
            //@ObfuscateRequest(content={"key1[key2][key3]"})
            array(
                array('content' => array('key1' => array('key2' => array('key3' => 'foo')))),
                array('content' => array('key1[key2][key3]')),
                array('content' => array('key1' => array('key2' => array('key3' => 'XXX')))),
            ),
            //@ObfuscateRequest(content={"key1[*]"})
            array(
                array('content' => array('key1' => array('key2' => array('key3' => 'foo')))),
                array('content' => array('key1[*]')),
                array('content' => array('key1' => 'X')),
            ),
        );
    }

    /**
     * @dataProvider dataProviderObfuscate
     */
    public function testObfuscate(array $data, array $patterns, array $expectedData)
    {
        $this->if($obfuscator = new TestedClass())
            ->array($obfuscator->obfuscate($data, $patterns))
            ->isIdenticalTo($expectedData);
    }
}
