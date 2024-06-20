<?php

namespace Inilim\ParseLazyMethod;

use PhpParser\Comment\Doc;

class ParseAnnotation
{
    public function __invoke(Doc $comment): array
    {
        \preg_match_all('#\@([a-z\_]+)[\s\t]+(.+)\n#i', $comment->getText(), $matches);

        $names = $matches[1] ?? [];
        $values = $matches[2] ?? [];
        unset($matches);

        $res = [];
        foreach ($names as $idx => $name) {
            $res[] = [
                'name' => \trim($name),
                'value' => \trim($values[$idx]),
            ];
        }
        unset($names, $values);

        return $res;
    }
}
