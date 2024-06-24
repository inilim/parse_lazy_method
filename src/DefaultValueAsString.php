<?php

namespace Inilim\ParseLazyMethod;

use PhpParser\NodeAbstract;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr;

use PhpParser\Node\Expr\New_;
use PhpParser\Node\Scalar\Float_;
use PhpParser\Node\Scalar\Int_;
use PhpParser\Node\Expr\UnaryMinus;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\Expr\Array_;

class DefaultValueAsString
{
    public function __invoke(?Expr $default_node): string
    {
        if ($default_node === null) return '';

        if ($default_node instanceof New_) {
            return 'new \\' . $default_node->class->name;
        }
        // ------------------------------------------------------------------
        // парсер думает что null|true|false это константа
        // ------------------------------------------------------------------
        elseif ($default_node instanceof ConstFetch) {
            $t = \strtolower($default_node->name->name);
            if (\in_array($t, ['null', 'true', 'false', true])) {
                return $t;
            }
            // тут глобальные константы
            return $default_node->name->name;
        }
        // ------------------------------------------------------------------
        // положительные числа
        // ------------------------------------------------------------------
        elseif ($default_node instanceof Float_) {
            return $default_node->value;
        } elseif ($default_node instanceof Int_) {
            return $default_node->value;
        }
        // ------------------------------------------------------------------
        // отрицательные числа
        // ------------------------------------------------------------------
        elseif ($default_node instanceof UnaryMinus) {
            if ($default_node->expr instanceof Int_) {
                return '-' . $default_node->expr->value;
            } elseif ($default_node->expr instanceof Float_) {
                return '-' . $default_node->expr->value;
            }
            throw new \Exception($default_node->expr::class);
        }
        // ------------------------------------------------------------------
        // строка
        // ------------------------------------------------------------------
        elseif ($default_node instanceof String_) {
            return $default_node->getAttribute('rawValue');
        }
        // ------------------------------------------------------------------
        // константы классов
        // ------------------------------------------------------------------
        elseif ($default_node instanceof ClassConstFetch) {
            $c = $default_node->class->name;
            $n = $default_node->name->name;
            if (\in_array(\strtolower($c), ['self', 'static', 'parent'], true)) {
                return \sprintf('%s::%s', $c, $n);
            }
            return \sprintf('\%s::%s', $c, $n);
        }
        // 
        elseif ($default_node instanceof Array_) {
            return '[]';
        }

        print_r($default_node);

        exit();

        throw new \Exception($default_node::class);
    }
}
