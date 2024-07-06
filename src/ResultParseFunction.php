<?php

namespace Inilim\ParseLazyMethod;

final readonly class ResultParseFunction
{
    public function __construct(
        public string $name,
        public ?string $return_type,
        public array $args,
        public array $comments,
        public array $annotations,
    ) {
    }
}
