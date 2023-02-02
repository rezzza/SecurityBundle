<?php

declare(strict_types=1);

namespace Rezzza\SecurityBundle\Tests\Units\Request\Obfuscator;

use atoum\atoum;
use Rezzza\SecurityBundle\Request\Obfuscator\RequestObfuscator as TestedClass;

/**
 * RequestObfuscator.
 *
 * @uses atoum\test
 *
 * @author Stephane PY <py.stephane1@gmail.com>
 */
class RequestObfuscator extends atoum\test
{
    public function dataProviderObfuscate()
    {
        return [
            // string on data.
            // @ObfuscateRequest(content="*")
            [
                ['content' => 'foo'],
                ['content' => '*'],
                ['content' => 'XXX'],
            ],
            // @ObfuscateRequest(content=".")
            [
                ['content' => 'foobar'],
                ['content' => '.'],
                ['content' => 'XXXXXX'],
            ],
            // @ObfuscateRequest()
            [
                ['content' => 'foobar'],
                [],
                ['content' => 'foobar'],
            ],
            // @ObfuscateRequest(otherkey="*")
            [
                ['content' => 'foobar'],
                ['otherkey' => '*'],
                ['content' => 'foobar'],
            ],
            // array on data, simple patterns
            // @ObfuscateRequest(content="*")
            [
                ['content' => ['key1' => 'foo', 'key2' => 'bar']],
                ['content' => '*'],
                ['content' => 'X'],
            ],
            // @ObfuscateRequest(content="key1")
            [
                ['content' => ['key1' => 'foo', 'key2' => 'bar']],
                ['content' => 'key1'],
                ['content' => ['key1' => 'XXX', 'key2' => 'bar']],
            ],
            // @ObfuscateRequest(content={"key1"})
            [
                ['content' => ['key1' => ['foo' => 'pouet'], 'key2' => 'bar']],
                ['content' => ['key1']],
                ['content' => ['key1' => 'X', 'key2' => 'bar']],
            ],
            // @ObfuscateRequest(content={"key1", "key2"})
            [
                ['content' => ['key1' => ['foo' => 'pouet'], 'key2' => 'bar']],
                ['content' => ['key1', 'key2']],
                ['content' => ['key1' => 'X', 'key2' => 'XXX']],
            ],
            // @ObfuscateRequest(content={"key1[key2]"})
            [
                ['content' => ['key1' => ['key2' => 'pouet'], 'key2' => 'bar']],
                ['content' => ['key1[key2]']],
                ['content' => ['key1' => ['key2' => 'XXXXX'], 'key2' => 'bar']],
            ],
            // @ObfuscateRequest(content={"key1[key2][key3]"})
            [
                ['content' => ['key1' => ['key2' => ['key3' => 'foo']]]],
                ['content' => ['key1[key2][key3]']],
                ['content' => ['key1' => ['key2' => ['key3' => 'XXX']]]],
            ],
            // @ObfuscateRequest(content={"key1[*]"})
            [
                ['content' => ['key1' => ['key2' => ['key3' => 'foo']]]],
                ['content' => ['key1[*]']],
                ['content' => ['key1' => 'X']],
            ],
        ];
    }

    /**
     * @dataProvider dataProviderObfuscate
     */
    public function testObfuscate(array $data, array $patterns, array $expectedData): void
    {
        $this->if($obfuscator = new TestedClass())
            ->array($obfuscator->obfuscate($data, $patterns))
            ->isIdenticalTo($expectedData);
    }
}
