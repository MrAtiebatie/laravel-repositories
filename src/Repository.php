<?php

namespace MrAtiebatie;

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
        return $this->model->getTable();
    }
}
