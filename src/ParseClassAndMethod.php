<?php

namespace Inilim\ParseLazyMethod;

use Inilim\ParseLazyMethod\TypeAsString;
use Inilim\ParseLazyMethod\ParamAsString;
use Inilim\ParseLazyMethod\DefaultValueAsString;
use Inilim\ParseLazyMethod\ParseAnnotation;
use Inilim\ParseLazyMethod\ParseComment;
use Inilim\ParseLazyMethod\ResultParseClass;
// PHParser
use PhpParser\Comment\Doc;
use PhpParser\NodeFinder;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Class_;

class ParseClassAndMethod
{
    protected ?ParseComment $parse_comment = null;
    protected ?ParseAnnotation $parse_annotation = null;

    public function __invoke(Class_ $class, string $name_method): ResultParseClass
    {
        // ------------------------------------------------------------------
        // парсим комментарии класса
        // ------------------------------------------------------------------

        $comments       = $class->getComments();

        $comments_class = $this->parseComments($comments);

        // ------------------------------------------------------------------
        // парсим аннотации класса
        // ------------------------------------------------------------------

        $annotations_class = $this->parseAnnotations($comments);
        $comments = [];

        // ------------------------------------------------------------------
        // ищем метод
        // ------------------------------------------------------------------

        $node_finder = new NodeFinder;
        $methods = $node_finder->findInstanceOf($class, ClassMethod::class);

        $method = null;
        foreach ($methods as $m) {
            if ($m->name->toString() === $name_method) {
                $method = $m;
                break;
            }
        }
        unset($methods);

        if ($method === null) {
            throw new \Exception(
                \sprintf('Method no found: %s', $name_method)
            );
        }
        /** @var ClassMethod $method */

        // ------------------------------------------------------------------
        // парсим комментарии метода
        // ------------------------------------------------------------------

        $comments        = $method->getComments();
        $comments_method = $this->parseComments($comments);

        // ------------------------------------------------------------------
        // парсим аннотации метода
        // ------------------------------------------------------------------

        $annotation_method = $this->parseAnnotations($comments);

        // ------------------------------------------------------------------
        // 
        // ------------------------------------------------------------------

        $type_to_str    = new TypeAsString;
        $param_to_str   = new ParamAsString;
        $default_to_str = new DefaultValueAsString;

        // ------------------------------------------------------------------
        // парсим агрументы метода
        // ------------------------------------------------------------------

        $return_type = $type_to_str->__invoke($method->getReturnType());

        $params = $method->getParams();

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

        // return [
        //     'method'      => \lcfirst($class->name->name),
        //     'return_type' => $return_type,
        //     'args'        => $args,
        //     'annotations_class' => $annotations_class,
        //     'comments_class'    => $comments_class,
        //     'comments_method'   => $comments_method,
        //     'annotations_method' => $annotation_method,
        // ];

        return new ResultParseClass(
            method: \lcfirst($class->name->name),
            return_type: $return_type,
            args: $args,
            annotations_class: $annotations_class,
            annotations_method: $annotation_method,
            comments_class: $comments_class,
            comments_method: $comments_method,
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
