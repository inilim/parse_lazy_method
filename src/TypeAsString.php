<?php

namespace Inilim\ParseLazyMethod;

use PhpParser\NodeAbstract;
// типы
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\UnionType;
use PhpParser\Node\NullableType;
use PhpParser\Node\Identifier;

class TypeAsString
{
    public function __invoke(?NodeAbstract $node): string
    {
        if ($node === null) return '';

        if ($this->isPrimitive($node)) {
            return $this->getNamePrimitive($node);
        }

        // если типы через разделитель "|"
        if ($node instanceof UnionType) {
            $t = [];
            foreach ($node->types as $type) {
                if ($this->isPrimitive($type)) {
                    $t[] = $this->getNamePrimitive($type);
                } else {
                    $t[] = '(' . $this->getNameIntersection($type) . ')';
                }
            }

            return \implode('|', $t);
        }
        // если ?string, ?int, ?float ...
        elseif ($node instanceof NullableType) {
            return 'null|' . $this->getNamePrimitive($node->type);
        }
        // обьединение обьектов через "&"
        elseif ($node instanceof IntersectionType) {
            return $this->getNameIntersection($node);
        }

        throw new \Exception('Node не определен: ' . $node::class);
    }

    protected function getNameIntersection(IntersectionType $node): string
    {
        $t = [];
        foreach ($node->types as $type) {
            $t[] = $this->getNamePrimitive($type);
        }

        return \implode('|', $t);
    }

    protected function isPrimitive(NodeAbstract $node): bool
    {
        return $node instanceof Identifier || $node instanceof FullyQualified || $node instanceof Name;
    }

    protected function getNamePrimitive(NodeAbstract $node): string
    {
        // одиночный примитивный тип (int,string,float,object,mixed...)
        if ($node instanceof Identifier) {
            return $node->name;
        }
        // именованные обьекты
        elseif ($node instanceof FullyQualified) {
            return '\\' . $node->name;
        }
        // self static parent
        elseif ($node instanceof Name) {
            return $node->name;
        }

        throw new \Exception('Primitive не определен: ' . $node::class);
    }
}
