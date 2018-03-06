<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 11/02/18
 * Time: 14:32
 */

class DriverConnection
{
    private $pattern;
    private $drive;
    public $result;
    private $dataBaseParams = '';
    private $method = ['get' => 'select',
        'post' => "insert",
        "put" => "update",
        "delete" => "delete",
        'showdatabases' => 'showdatabases',
        'show_table' => 'show_table',
        'show_table_columns' => 'show_table_columns'];
    private $driveMethod;

    function __construct($pattern)
    {
        $this->pattern = $pattern;
    }

    function custom_response()
    {
        if (isset($this->pattern['customResponse'])) {
            if ($this->result) {
                $this->result = $this->pattern['customResponse']['REQUEST'][$this->pattern['REQUEST']]['Found'];
            } else {
                $this->result = $this->pattern['customResponse']['REQUEST'][$this->pattern['REQUEST']]['NotFound'];
            }
        }
        return $this->result;
    }

    function start()
    {
        $this->setdriver($this->pattern['drive']);
        if (isset($this->pattern['function'])) {
            $this->result = $this->method($this->method[$this->pattern['function']]);
        } else {
            $this->result = $this->method(strtolower($this->pattern['REQUEST']));
        }
        return $this->custom_response();
    }

    function getDataBase()
    {
        if (isset($this->pattern['database'])) {
            $this->drive->base = $this->pattern['database'];
        }
    }

    function setParams($method)
    {
        if (isset($this->driveMethod[$method])) {
            foreach ($this->driveMethod[$method] as $item) {
                if (isset($this->pattern[$item])) {
                    $this->dataBaseParams .= ', $this->pattern["' . $item . '"]';
                } else if (isset($this->pattern['function'])) {
                    $this->dataBaseParams .= ', $this->pattern["whereString"]["' . $item . '"]';
                }
            }
            $this->dataBaseParams = substr($this->dataBaseParams, 2);
        }
    }

    function method($method)
    {
        $this->getDataBase();
        $this->setParams($method);
        eval('$this->result = $this->drive->' . $this->method[$method] . '(' . $this->dataBaseParams . ');');
        return $this->result;
    }

    function setdriver($drive)
    {
        if ($drive) {
            require 'model/drivers/' . $drive . '/' . $drive . '.class.php';
            eval('$this->drive = new ' . $drive . '();');
            $this->driveMethod = $this->drive->config();
        } else {
            die('sem Drive Valido');
        }
    }
}