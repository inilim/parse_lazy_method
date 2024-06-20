<?php

namespace Inilim\ParseLazyMethod;

use PhpParser\Comment;

class ParseComment
{
    /**
     * @return string[]|array{}
     */
    public function __invoke(Comment $comment): array
    {
        $c = $comment->getText();
        $c = \explode('*', $c);
        $c = \array_map(function (string $item) {
            $item = \trim($item);
            return \trim($item, '/');
        }, $c);

        // убираем пустоты
        $c = \array_filter($c, fn (string $item) => $item !== '');
        // убираем аннотации
        $c = \array_filter($c, fn (string $item) => !\str_contains($item, '@'));
        $c = \array_values($c);

        return $c;
    }
}
