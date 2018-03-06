<?php

class urlService
{
    private $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function getPages()
    {
        $pages = array_map(array($this, 'removeGet'), explode('/', $this->url));
        return array_values(array_filter($pages));
    }

    public function removeGet($route)
    {
        if (strpos($route, '?') !== false) {
            $res = explode('?', $route);
            return $res[0];
        }else{
            return $route;
        }
    }

    public function getProto()
    {
        return strtolower(preg_replace('/[^a-zA-Z]/', '', $_SERVER['SERVER_PROTOCOL'])) . '://';
    }

    public function getDomain()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public function getComplete()
    {
        return $this->getProto() . $this->getDomain() . $this->url;
    }

    public function getBreadcumps()
    {
        $pages = $this->getPages();
        for ($i = 0; $i < count($pages); $i++) {
            $res['url'][$i] = $this->getProto() . $this->getDomain();
            for ($i2 = 0; $i2 < $i + 1; $i2++) {
                $res['url'][$i] .= $pages[$i2] . '/';
            }
        }
        return $res;
    }

    public function getRequest()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public function getGET()
    {
        $bget = explode('?', $this->url);
        $pages = $this->getPages();
        if (isset($bget[1])) {
            $var['get'] = $_GET;
            for ($i = 0; $i < count($pages); $i++) {
                $var['url'][$i] = $var['domain'];
                for ($i2 = 0; $i2 < count($i + 1); $i2++) {
                    $var['url'][$i] .= $pages[$i2] . '/';
                }
                $rem = explode('?', $pages[$i]);
                if (isset($rem[1])) {
                    $pages[$i] = $rem[0];
                } else {
                    $pages[$i] = $pages[$i];
                }
            }
        }
        return $var;
    }
}