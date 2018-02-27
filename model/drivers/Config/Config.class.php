<?php

class Config
{
    public $drivers;
    public $result;
    public $patternregx = '/(?<={)(.*)(?=})/';

    public function GetDrivers()
    {
        $path = __DIR__ . '/../';
        $this->drivers = scandir($path);
        $this->drivers = array_diff($this->drivers, array('.', '..', 'Config'));
        foreach ($this->drivers as $item) {
            $result[] = $item;
        }
        $this->drivers = $result;
        return $this->drivers;
    }

    public function config()
    {
        return json_decode(file_get_contents(__DIR__ . '/driverconfig.json'), true);
    }

    public function cleanFunction($function)
    {
        return str_replace('{', '', str_replace('}', '', $function));
    }

    public function select($result)
    {
        foreach ($result as $key => $item) {
            if (preg_match($this->patternregx, $item)) {
                $result[$key] = call_user_func([$this, $this->cleanFunction($item)]);
            }
        }
        return $result;
    }
}