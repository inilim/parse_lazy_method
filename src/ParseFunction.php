<?php

namespace Inilim\ParseLazyMethod;

use Inilim\ParseLazyMethod\TypeAsString;
use Inilim\ParseLazyMethod\ParamAsString;
use Inilim\ParseLazyMethod\DefaultValueAsString;
use Inilim\ParseLazyMethod\ParseAnnotation;
use Inilim\ParseLazyMethod\ParseComment;
use Inilim\ParseLazyMethod\ResultParseFunction;
// PHParser
use PhpParser\Comment\Doc;
use PhpParser\Node\Stmt\Function_;

class ParseFunction
{
    protected ?ParseComment $parse_comment = null;
    protected ?ParseAnnotation $parse_annotation = null;

    public function __invoke(Function_ $func): ResultParseFunction
    {

        // ------------------------------------------------------------------
        // парсим комментарии функции
        // ------------------------------------------------------------------

        $comments       = $func->getComments();
        $comments_res = $this->parseComments($comments);

        // ------------------------------------------------------------------
        // парсим аннотации функции
        // ------------------------------------------------------------------

        $annotations = $this->parseAnnotations($comments);
        unset($comments);

        // ------------------------------------------------------------------
        // 
        // ------------------------------------------------------------------

        $type_to_str    = new TypeAsString;
        $param_to_str   = new ParamAsString;
        $default_to_str = new DefaultValueAsString;

        // ------------------------------------------------------------------
        // парсим агрументы метода
        // ------------------------------------------------------------------

        $return_type = $type_to_str->__invoke($func->getReturnType());
        $name_func = $func->name->name;
        $params = $func->getParams();

        $args = [];
        foreach ($params as $param) {
            $args[] = [
                'type'  => $type_to_str->__invoke($param->type),
                'var'   => $param_to_str->__invoke($param),
                'value' => $default_to_str->__invoke($param->default),
            ];
        }

        // ------------------------------------------------------------------
        // 
        // ------------------------------------------------------------------

        return new ResultParseFunction(
            name: $name_func,
            return_type: $return_type,
            args: $args,
            comments: $comments_res,
            annotations: $annotations
        );
    }

    // ------------------------------------------------------------------
    // 
    // ------------------------------------------------------------------

    protected function parseComments(array $comments): array
    {
        if (!$comments) return [];
        $this->parse_comment ??= new ParseComment;

        $res = [];
        foreach ($comments as $item_comment) {
            $res = \array_merge(
                $res,
                $this->parse_comment->__invoke($item_comment)
            );
        }

        return $res;
    }

    protected function parseAnnotations(array $comments): array
    {
        if (!$comments) return [];
        $this->parse_annotation ??= new ParseAnnotation;

        $items = \array_filter(
            $comments,
            fn ($c) => $c instanceof Doc
        );

        if (!$items) return [];

        $res = [];
        foreach ($items as $item) {
            $res = \array_merge(
                $res,
                $this->parse_annotation->__invoke($item)
            );
        }

        return $res;
    }
}
