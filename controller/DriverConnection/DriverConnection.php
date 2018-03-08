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
    public $base = 'default';
    private $connection = 'local';
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
            $custom = new CustomResponse($this->pattern, $this->result);
            $process = $this->makeCustomResponse();
            if ($this->result) {
                eval('$this->result = $process["Found"];');
            } else {
                //eval('$this->result = $this->pattern["customResponse"]["REQUEST"][$this->pattern["REQUEST"]]["NotFound"]');
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

    function setconnetion()
    {
        if (isset($this->pattern['connection'])) {
            $this->connection = $this->pattern['connection'];
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
            eval('$this->drive = new ' . $drive . '($this->base, $this->connection);');
            $this->driveMethod = $this->drive->config();
        } else {
            die('sem Drive Valido');
        }
    }

    public function makeCustomResponse()
    {
        $result = false;
        $customResponse = $this->pattern['customResponse']['REQUEST'][$this->pattern['REQUEST']];
        if (isset($customResponse)) {
            foreach ($customResponse as $key => $value) {
                if (is_array($value)) {
                    $result[$key] = $this->ProcessCustomResponse($value);
                }
            }
        }
        return $result;
    }

    function ProcessCustomResponse($value)
    {
        $result = false;
        foreach ($value as $skey => $svalue) {
            if (is_array($svalue)) {
                foreach ($svalue as $fkey => $fvalue) {
                    $call = createCall($fvalue, $fkey, "/{(.*?)}/", "/\((.*?)\)/", 'CustomResponse');
                    eval($call['class'] . ';');
                    eval('$result[$skey] = ' . $call['func'] . ';');
                }
            } else {
                $result[$skey] = $svalue;
                //TODO fazer aqui para funcao unica!!!
            }
        }
        return $result;
    }
}