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
     * @var \Yajra\DataTables\Html\Builder|null
     */
    protected ?Builder $htmlBuilder = null;

    /**
     * @return \Yajra\DataTables\Html\Builder
     *
     * @throws \Exception
     */
    public static function make(): Builder
    {
        if (func_get_args()) {
            return (new static(...func_get_args()))->handle();
        }

        return app(static::class)->handle();
    }

    /**
     * @param  string  $method
     * @param  mixed  $parameters
     * @return mixed
     *
     * @throws \Exception
     */
    public function __call(string $method, $parameters)
    {
        if (method_exists($this->getHtmlBuilder(), $method)) {
            return $this->getHtmlBuilder()->{$method}(...$parameters);
        }

        throw new BadMethodCallException("Method {$method} does not exists");
    }

    /**
     * @return \Yajra\DataTables\Html\Builder
     */
    protected function getHtmlBuilder(): Builder
    {
        if ($this->htmlBuilder) {
            return $this->htmlBuilder;
        }

        return $this->htmlBuilder = app(Builder::class);
    }

    /**
     * @param  \Yajra\DataTables\Html\Builder  $builder
     * @return static
     */
    public function setHtmlBuilder(Builder $builder): static
    {
        $this->htmlBuilder = $builder;

        return $this;
    }
}
