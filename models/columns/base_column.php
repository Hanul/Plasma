<?php

abstract class Plasma_baseColumn {

    protected $value;

    protected $columnName;

    protected $columnType;

    abstract protected function plsm_getColumnType();

    abstract protected function plsm_getColumnName();

}