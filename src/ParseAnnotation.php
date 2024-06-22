<?php

namespace Inilim\ParseLazyMethod;

use PhpParser\Comment\Doc;

class ParseAnnotation
{
    public function __invoke(Doc $comment): array
    {
        $t = \_str()->trim($comment->getText());

        // убираем '/**'
        $t = \_str()->replaceFirst('/**', '', $t);
        $t = \_str()->trim($t);

        // убираем '*/'
        $t = \_str()->replaceLast('*/', '', $t);
        $t = \_str()->trim($t);

        // ------------------------------------------------------------------
        // 
        // ------------------------------------------------------------------

        $t = \explode(\PHP_EOL, $t);
        $t = \array_map(static function (string $item) {
            $item = \_str()->trim($item);
            $item = \_str()->ltrim($item, '*');
            $item = \_str()->trim($item);

            return $item;
        }, $t);

        // ------------------------------------------------------------------
        // 
        // ------------------------------------------------------------------

        $t = \array_filter($t, static fn (string $item) => \str_starts_with($item, '@'));
        $t = \array_values($t);

        // ------------------------------------------------------------------
        // 
        // ------------------------------------------------------------------

        if (!$t) return [];

        $res = [];
        foreach ($t as $item) {
            $item = \_str()->squish($item);
            $item = \explode(' ', $item, 2);

            $res[] = [
                'name'  => \_str()->replaceFirst('@', '', $item[0]),
                'value' => $item[1] ?? '',
            ];
        }
        return $res;
    }
}
