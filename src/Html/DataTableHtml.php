<?php

namespace Yajra\DataTables\Html;

use BadMethodCallException;
use Yajra\DataTables\Contracts\DataTableHtmlBuilder;

/**
 * @mixin Builder
 */
abstract class DataTableHtml implements DataTableHtmlBuilder
{
    /**
     * @var \Yajra\DataTables\Html\Builder
     */
    protected $htmlBuilder;

    /**
     * @return \Yajra\DataTables\Html\Builder
     */
    public static function make()
    {
        if (func_get_args()) {
            return (new static(...func_get_args()))->handle();
        }

        return app(static::class)->handle();
    }

    /**
     * @param  string  $name
     * @param  mixed  $arguments
     * @return mixed
     *
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        if (method_exists($this->getHtmlBuilder(), $name)) {
            return $this->getHtmlBuilder()->{$name}(...$arguments);
        }

        throw new BadMethodCallException("Method {$name} does not exists");
    }

    /**
     * @return \Yajra\DataTables\Html\Builder
     */
    protected function getHtmlBuilder()
    {
        if ($this->htmlBuilder) {
            return $this->htmlBuilder;
        }

        return $this->htmlBuilder = app(Builder::class);
    }

    /**
     * @param  mixed  $builder
     * @return static
     */
    public function setHtmlBuilder($builder)
    {
        $this->htmlBuilder = $builder;

        return $this;
    }
}
