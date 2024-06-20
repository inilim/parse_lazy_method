<?php

namespace Inilim\ParseLazyMethod;

use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\UnionType;


class TestClass
{
    const MY_C = 123;

    public function __invoke($var1 = -100.1)
    {
        return [];
    }
}
