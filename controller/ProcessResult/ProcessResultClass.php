<?php
/**
 * Created by Guilherme Camacho.
 * User: guilherme
 * Date: 07/03/18
 * Time: 14:46
 */

class ProcessResultClass
{

    private $result = false;

    public function __construct($result)
    {
        $this->result = $result;
    }

    public function json($options = JSON_PRETTY_PRINT)
    {
        header('Content-Type: application/json');
        return json_encode($this->result, $options);
    }

    public function xml()
    {
        header("Content-type: text/xml");
        $xml = new SimpleXMLElement("<?xml version=\"1.0\"?><Request></Request>");
        $node = $xml->addChild('result');
        array_to_xml($this->result, $node);
        return $xml->asXML();
    }

}