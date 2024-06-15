<?php

namespace Inilim\ParseLazyMethod;

use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\UnionType;

class TestClass
{
    public function __invoke(string $var1)
    {
        return [];
    }
}
