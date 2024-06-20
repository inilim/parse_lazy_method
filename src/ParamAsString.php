<?php

namespace Inilim\ParseLazyMethod;

use PhpParser\Node\Param;

class ParamAsString
{
    public function __invoke(Param $node): string
    {
        return '$' . $node->var->name;
    }
}
