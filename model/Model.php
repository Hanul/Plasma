<?php

class Plasma_Model extends Plasma_BaseModel
{

    function __construct()
    {
        $this->tableName = '';
        $this->columns = array();
        $this->id = 0;
    }


    public function save()
    {
        // execute query to save

        // generate all column's sql query
        $columnNames = '';
        $columnValues = '';
        foreach ($this->columns as $fieldName => $columnObj)
        {
            if ($columnObj->columnName == null) {
                $columnNames .= $fieldName.', ';
            } else {
                $columnNames .= $columnObj->columnName.', ';
            }
            $columnValues .= $columnObj->generateValueForSQL().', ';
        }
        $columnNames = substr($columnNames, 0, -1);
        $columnValues = substr($columnValues, 0, -1);
        $thisTableName = $this->tableName;
        $thisId = $this->id;

        if ($this->id == 0) {
            // This object is not saved yet.
            $query = 'INSERT INTO $thisTableName ($columnNames) VALUES ($columnValues);';
            // Execute query
        } else {
            // This object is on the DB.
            $query = 'INSERT INTO $thisTableName ($columnNames) VALUES ($columnValues) WHERE id=$thisId;';
            // Execute query
        }

        mysql_run_query($query);

    }

    public function update($fieldName, $_value)
    {
        if ($this->id == 0)
        {
            throw new Plasma_ObjectNotExists;
        }

        try
        {
            /*
             * variable columns forms this structure -
             * fieldName => columnObject(contains type, value, columnName)
             */
            if (array_key_exists($fieldName, $this->columns))
            {
                // make query to update
                $this->columns[$fieldName]->setValue($_value);

                $thisTableName = $this->tableName;
                $columnName = $this->columns[$fieldName]->columnName;
                if ($columnName == null) {
                    $columnName = $fieldName;
                }
                $columnValue = $this->columns[$fieldName]->generateValueForSQL();
                $thisId = $this->id;
                $query = 'UPDATE $thisTableName SET $columnName=$columnValue WHERE id=$thisId';

                // Execute Query
                mysql_run_query($query);

            }
            else
            {
                throw new Plasma_noColumnException;
            }

        }
        catch(Plasma_noColumnException $e)
        {
            // No column.
        }
    }

    public function delete()
    {
        if ($this->id == 0)
        {
            throw new Plasma_ObjectNotExists;
        }
        $thisTableName = $this->tableName;
        $thisId = $this->id;
        $query = 'DELETE FROM $thisTableName WHERE id=$thisId';
        // Execute Query
        mysql_run_query($query);
    }

    public function find($findArray)
    {
        /*
         * 검색 방식 생각중.
         *
         * 1. 단순히 칼럼 => 텍스트
         * 2. 제외, 텍스트 포함, 등 옵션까지 설정
         *
         * 일단 1번부터 만들기로 함.
         * array()를 받아야함.
         *
         * 필드 이름 => 값
         */

        $findQuery = '';
        foreach ($findArray as $findFieldName => $findKey)
        {
            if (!array_key_exists($findFieldName, $this->columns))
            {
                throw new Plasma_NoColumnException;
            }
            $findQuery .= ' '.$findFieldName.'='.$findKey.' AND';
        }
        $findQuery = substr($findQuery, 0, -3);
        $thisTableName = $this->tableName;
        $query = 'SELECT * FROM $thisTableName WHERE $findQuery;';
        return mysql_get_list($query);
        // TODO: 이 값들을 해당 모델에다가 넣어주고, 객체로 리턴해야 함.
    }

    public function findOne($findArray)
    {
        $findQuery = '';
        foreach ($findArray as $findFieldName => $findKey)
        {
            if (!array_key_exists($findFieldName, $this->columns))
            {
                throw new Plasma_NoColumnException;
            }
            $findQuery .= ' '.$findFieldName.'='.$findKey.' AND';
        }
        $findQuery = substr($findQuery, 0, -3);
        $thisTableName = $this->tableName;
        $query = 'SELECT * FROM $thisTableName WHERE $findQuery;';
        return mysql_get_one($query);
        // TODO: 이 값들을 해당 모델에다가 넣어주고, 객체로 리턴해야 함.
    }

}