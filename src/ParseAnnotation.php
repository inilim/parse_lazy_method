<?php

namespace Inilim\ParseLazyMethod;

use PhpParser\Comment\Doc;

class ParseAnnotation
{
    public function __invoke(Doc $comment): array
    {
        $t = \cus_trim($comment->getText());

        // убираем '/**'
        $t = \replaceFirst('/**', '', $t);
        $t = \cus_trim($t);

        // убираем '*/'
        $t = \replaceLast('*/', '', $t);
        $t = \cus_trim($t);

        // ------------------------------------------------------------------
        // 
        // ------------------------------------------------------------------

        $t = \explode(\PHP_EOL, $t);
        $t = \array_map(static function (string $item) {
            $item = \cus_trim($item);
            $item = \cus_ltrim($item, '*');
            $item = \cus_trim($item);

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
            $item = \squish($item);
            $item = \explode(' ', $item, 2);

            $res[] = [
                'name'  => \replaceFirst('@', '', $item[0]),
                'value' => $item[1] ?? '',
            ];
        }
        return $res;
    }
}
