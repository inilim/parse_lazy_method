<?php

namespace Inilim\ParseLazyMethod;

class ResultParseClass
{
    public function __construct(
        public readonly string $method,
        public readonly string $method_original,
        public readonly ?string $return_type,
        public readonly array $args,
        public readonly array $annotations_class,
        public readonly array $comments_class,
        public readonly array $comments_method,
        public readonly array $annotations_method,
    ) {
    }
}
