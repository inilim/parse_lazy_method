<?php

namespace Inilim\ParseLazyMethod;

use Inilim\ParseLazyMethod\ResultParseFunction;

class FormationFunctionAnnotation
{
    protected ResultParseFunction $result;

    /**
     * @param boolean $static ставить в аннотации метода "static"
     */
    public function __invoke(
        ResultParseFunction $result,
        bool $static = false,
    ): string {
        $this->result = $result;
        // @method ?string getSegmentPath(string $path)
        // de($this->result);

        return \sprintf(
            '@method %s %s %s(%s) %s',
            ($static ? ' static ' : ''),
            $this->getReturn(), // type return
            $this->result->name, // name
            $this->getArgs(), // args
            '', // comment
        );
    }

    // ------------------------------------------------------------------
    // 
    // ------------------------------------------------------------------

    protected function getArgs(): string
    {
        if (!$this->result->args) {
            return '';
        }

        $ann_params = \array_filter($this->result->annotations, static fn ($item) => $item['name'] === 'param');
        $ann_params = \array_column($ann_params, 'value');
        /** @var string[] $ann_params */

        // ------------------------------------------------------------------
        // 
        // ------------------------------------------------------------------

        $args = $this->result->args;
        foreach ($args as $idx => $arg) {

            foreach ($ann_params as $param) {
                if (\str_ends_with($param, $arg['var'])) {
                    $param = \replaceLast($arg['var'], '', $param);
                    $param = \cus_trim($param);
                    $args[$idx]['annotation_type'] = $param;
                }
            }
        }

        // ------------------------------------------------------------------
        // 
        // ------------------------------------------------------------------
        // dde($args);

        foreach ($args as $idx => $arg) {
            $args[$idx] = \sprintf(
                '%s %s%s',
                $arg['annotation_type'] ?? $arg['type'],
                $arg['var'],
                ($arg['value'] === '' ? '' : ' = ' . $arg['value']),
            );
        }

        return \implode(', ', $args);
    }

    protected function getReturn(): string
    {
        if (!$this->result->annotations) {
            return $this->result->return_type ?? '';
        }

        foreach ($this->result->annotations as $item) {
            if ($item['name'] === 'return') {
                return $item['value'];
            }
        }

        return $this->result->return_type ?? '';
    }
}
