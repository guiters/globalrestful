<?php
/**
 * Created by PhpStorm.
 * User: guilherme
 * Date: 11/02/18
 * Time: 13:58
 */

class pattern
{
    public $route;
    public $path;
    protected $pattern;
    protected $patternfile;
    private $whereType = ['require', 'optional'];
    private $columns = ['columns'];
    private $connection = 'local';

    function __construct($route, $path)
    {
        $this->route = $route;
        $this->path = $path;
        $this->patternfile = $this->getPattern();

        if (is_file($this->patternfile)) {
            $this->patternfile = file_get_contents($this->patternfile);
            if (is_json($this->patternfile)) {
                $this->pattern = json_decode($this->patternfile, true);
                $this->pageControl();
                $this->getHeaders();
                $this->checkAuth();
                $this->getMethod();
                $this->whereValidate();
                $this->arrayToWhere();

            }
        } else {
            $this->error('This route does not exist', 404);
        }
    }

    public function start()
    {
        return $this->pattern;
    }

    public function connectionControl()
    {
        if (!isset($this->pattern['connection'])) {
            $this->pattern['connection'] = $this->connection;
        }
    }


    private function arrayToWhere()
    {

        if (isset($this->pattern['where'])) {
            $this->pattern['whereString'] = '';
            foreach ($this->whereType as $where) {
                if (isset($this->pattern['where'][$where])) {
                    foreach ($this->pattern['where'][$where] as $key => $value) {
                        if (isset($this->pattern['database'])) {
                            $this->pattern['whereString'] .= $key . '="' . $value . '" AND ';
                        } else {
                            $this->pattern['whereString'][$key] = $value;
                        }
                    }
                }
            }
            if (!is_array($this->pattern['whereString'])) {
                $this->pattern['whereString'] = substr($this->pattern['whereString'], 0, -5);
            }
        }
    }


    private
    function whereValidate()
    {
        if (isset($this->pattern['where'])) {
            foreach ($this->whereType as $where) {
                if (isset($this->pattern['where'][$where])) {
                    foreach ($this->pattern['where'][$where] as $key => $value) {
                        if (isRegex($this->pattern['where'][$where][$key])) {
                            unset($this->pattern['where'][$where][$key]);
                        }
                    }
                }
            }
        }
    }


    private
    function matchGET()
    {
        $get = $_GET;
        if (isset($this->pattern['REQUEST_DATA'])) {
            $get = $this->pattern['REQUEST_DATA'];
            if (isset($this->pattern['where']['require'])) {
                if (!keys_are_equal($get, $this->pattern['where']['require'],$this->pattern['where']['optional'])) {
                    $this->error('The request is invalid');
                }
            }
        }
        foreach ($get as $key => $value) {
            foreach ($this->whereType as $where) {
                if (isset($this->pattern['where'][$where][$key]) && isRegex($this->pattern['where'][$where][$key])) {
                    if (preg_match($this->pattern['where'][$where][$key], $value)) {
                        $this->pattern['where'][$where][$key] = $value;
                    }
                }
            }
        }
        if (isset($this->pattern['REQUEST_DATA'])) {
            unset($this->pattern['REQUEST_DATA']);
        }
    }

    private
    function matchPOST()
    {
        foreach ($_POST as $key => $value) {
            foreach ($this->columns as $column) {
                if (in_array($key, $this->pattern[$column])) {
                    $this->pattern['data'][$key] = $value;
                } else {

                    $this->pattern['data'][$key] = null;

                }
            }
        }
    }

    private
    function isWebFormKit($form)
    {
        foreach ($form as $key => $item) {
            if (strpos($key, 'form-data') !== false) {
                return true;
                break;
            }
        }
        return false;
    }

    private
    function matchDELETE()
    {
    }

    private
    function matchPUT()
    {
        $this->pattern['data'] = null;
        $putData = file_get_contents("php://input");
        if (is_json($putData)) {
            $this->pattern['data'] = json_decode($putData, true);
        } else {
            $this->pattern['data'] = $this->webkitform($putData, $this->pattern);
        }
    }

    private
    function webkitform($putData)
    {
        $putData = str_replace('_', ' ', $putData);
        $putData = explode(' ', $putData);
        $i = 0;
        foreach ($putData as $item) {
            if ($i > 0 && numbertype($i)) {
                $put[$i] = preg_replace('/------(.*)/', '', $item);
                $put[$i] = preg_replace('/\n\n/', '', $put[$i]);
                $put[$i] = preg_replace('/\n/', '', $put[$i]);
                $put[$i] = preg_replace('/\r/', '', $put[$i]);
                $put[$i] = str_replace('Content-Disposition:', '', $put[$i]);
                $put[$i] = preg_replace('/\v\v/', '', $put[$i]);
                $put[$i] = str_replace('name="', '', $put[$i]);
                $put[$i] = explode('"', $put[$i]);
                $si = 0;
                foreach ($put[$i] as $subitem) {
                    if (numbertype($si)) {
                        $pattern[$subitem] = $put[$i][$si + 1];
                    }
                    $si++;
                }
            }
            $i++;
        }
        return $pattern;
    }


    private
    function pageControl()
    {
        if (isset($_GET['pg'])) {
            $this->pattern['limitpage'] = (($this->pattern['limitpage'] * $_GET['pg'])) . ', ' . $this->pattern['limitpage'];
        }
    }

    public
    function getMethod()
    {
        $this->pattern['REQUEST'] = "GET";
        if (!isAssoc($this->pattern['requires']['REQUEST'])) {
            if (in_array($_SERVER['REQUEST_METHOD'], $this->pattern['requires']['REQUEST'])) {
                $this->pattern['REQUEST'] = $_SERVER['REQUEST_METHOD'];
            } else {
                $this->error('This method is not valid for this request');
            }
            if (isset($_GET)) {
                $this->matchGET();
            }
        } else {
            if (in_array_r_like_key($_SERVER['REQUEST_METHOD'], $this->pattern['requires']['REQUEST'])) {
                $this->pattern['REQUEST'] = $this->pattern['requires']['REQUEST'][$_SERVER['REQUEST_METHOD']];
                eval('$this->pattern["REQUEST_DATA"] = $_' . $_SERVER['REQUEST_METHOD'] . ';');
            } else {
                $this->error('This method is not valid for this request');
            }
        }
        //toconsole('$this->match' . $this->pattern['REQUEST'] . '();');
        eval('$this->match' . $this->pattern['REQUEST'] . '();');
    }

    public
    function getPattern()
    {
        foreach ($this->route as $page) {
            if (is_file($this->path . '/' . $page . '.json')) {
                return $this->path . '/' . $page . '.json';
                break;
            }
        }
    }

    public function getHeaders()
    {
        $this->pattern['header'] = getallheaders();
    }


    public function checkAuth()
    {
        if (isset($this->pattern['requires']['AUTH'])) {
            $result = false;
            foreach ($this->pattern['requires']['AUTH'] as $key => $value) {
                $this->pattern[$key] = $this->processAuth($value, $key);
            };
            if (isset($this->pattern[key($this->pattern['requires']['AUTH'])])) {
                $call = $this->pattern[key($this->pattern['requires']['AUTH'])];
                eval($call['class']);
                eval('$result = ' . $call['func']);
                if (!$result) {
                    $this->error('Token invalido', 401);
                }
            } else {
                $this->error('Erro de configuração');
            }
        }
    }


    private function processAuth($auth, $loc)
    {
        foreach ($auth as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $fkey => $fvalue) {
                    $call['method'] = createCall($fvalue, $fkey, "/{(.*?)}/", "/\((.*?)\)/", 'checkAuth');
                }
            } else {
                $this->pattern[$value] = $this->pattern[$loc][$value];
                $search = $value;
                $replace = $this->pattern[$loc][$value];
            }
        }
        return $this->replace_array_like("'" . $search . "'", "'" . $replace . "'", $call['method']);
    }

    private function replace_array_like($search, $replace, $array)
    {
        $result = $array;
        foreach ($array as $key => $value) {
            if (!is_array($value)) {
                $result[$this->replace_like($search, $replace, $key)] = $this->replace_like($search, $replace, $value);
            } else {
                $result[$this->replace_like($search, $replace, $key)] = $this->replace_array_like($search, $replace, $value);
            }
        }
        return $result;
    }

    private function replace_like($search, $replace, $value)
    {
        $res = $value;
        if (strpos($value, $search) !== false) {
            $res = str_replace($search, $replace, $value);
        }
        return $res;
    }


    public function error($message, $error = 400)
    {
        header('Content-Type: application/json');
        http_set_code($error);
        echo json_encode(['error' => $message]);
        die();
    }
}