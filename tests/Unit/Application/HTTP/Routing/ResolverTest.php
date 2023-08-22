<?php

declare(strict_types=1);

namespace Tests\Unit\Application\HTTP\Routing;

use Tests\Unit\UnitTestCase;
use Twilight\Infrastructure\HTTP\Routing\Resolver;

class ResolverTest extends UnitTestCase
{
    /**
     * @testdox test if append generate a correct string
     */
    public function testAppendSuccessfully(): void
    {
        $resolver = new Resolver();
        $resolver->append('get', '/', 1);
        $resolver->append('post', '/pessoas', 2);
        $resolver->append('get', '/pessoas/{id|uuid}', 1);
        $resolver->append('get', '/pessoas', 3);
        $resolver->append('get', '/contagem-pessoas', 4);

        $pattern = '!|' .
            '^(?<routing_1>#):GET:\/?$|' .
            '^(?<routing_2>#):POST:\/pessoas\/?$|' .
            '^(?<routing_1>#):GET:\/pessoas\/(?<id>[^\/]\w{8}-\w{4}-\w{4}-\w{4}-\w{12})\/?$|' .
            '^(?<routing_3>#):GET:\/pessoas\/?$|' .
            '^(?<routing_4>#):GET:\/contagem-pessoas\/?$';
        $this->assertEquals($pattern, $resolver->pattern());
    }
}
