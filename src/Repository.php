<?php

namespace MrAtiebatie;

use Illuminate\Support\Str;

/**
 * Repository
 */
trait Repository
{
    /**
     * On method call
     */
    public function __call($method, $arguments)
    {
        // dd($this->model->table);
        if (method_exists($this, $method)) {
            return parent::__call('setModel', [$this->model])->setTable($this->model->table)->$method(...$arguments);
        } else {
            return $this->model->$method(...$arguments);
        }
    }

    /**
     * Get table
     * @return string
     */
    public function getTable()
    {
        if (empty($this->model->table)) {
            return str_replace('\\', '', Str::snake(Str::plural(class_basename($this->model))));
        }

        return $this->model->table;
    }
}
