<?php

namespace Inilim\ParseLazyMethod;

final readonly class ResultParseClass
{
    public function __construct(
        public string $method,
        public string $method_original,
        public ?string $return_type,
        public array $args,
        public array $annotations_class,
        public array $comments_class,
        public array $comments_method,
        public array $annotations_method,
    ) {
    }
}
