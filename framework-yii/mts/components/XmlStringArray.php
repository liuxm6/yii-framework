<?php

class XmlStringArray
{
    public $doc;
    protected $_data;
    public function __construct($xmlString)
    {
        libxml_use_internal_errors(true);
        $this->doc = new DOMDocument(); //params '1.0', 'UTF-8'
        $this->doc->formatOutput = TRUE;
        $this->doc->preserveWhiteSpace = FALSE;
        $this->doc->loadXML($xmlString);
    }
    public function __call($method, $args)
    {
        if (method_exists($this->doc, $method)) {
            return call_user_func_array($this->$doc, $args);
        }
        else {
            throw new CException('no this method '.__CLASS__.':'.$method, 404);
        }
    }

    public function valid($schema){
        return $this->doc->schemaValidate($schema) ? array('success'=>1) : array('success'=>0, 'errors'=>$this->libxml_display_errors());
    }

    public function libxml_display_error($error) {
        $return = "<br/>\n";
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "<b>Warning $error->code</b>: ";
                break;
            case LIBXML_ERR_ERROR:
                $return .= "<b>Error $error->code</b>: ";
                break;
            case LIBXML_ERR_FATAL:
                $return .= "<b>Fatal Error $error->code</b>: ";
                break;
        }
        $return .= trim($error->message);
        if ($error->file) {
            $return .= " in <b>$error->file</b>";
        }
        $return .= " on line <b>$error->line</b>\n";

        return $return;
    }

    public function libxml_display_errors()
    {
        $errors    = libxml_get_errors();
        $getErrors = array();
        foreach ($errors as $error) {
            $getErrors[] = $this->libxml_display_error($error);
        }
        libxml_clear_errors();
        return $getErrors;
    }

    public function toArray()
    {
        return $this->getNodeArray($this->doc->documentElement);
    }
    public function getNodeArray($node) {
        $array = false;
        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $array['#attribute'][$attr->nodeName] = $attr->nodeValue;
            }
        }
        if ($node->hasChildNodes()) {
            if ($node->childNodes->length == 1) {
                if ($node->firstChild->nodeType == XML_CDATA_SECTION_NODE) {
                    $array['#value'] = $node->firstChild->textContent;
                }
                else {
                    $ar = $this->getNodeArray($node->firstChild);
                    $array[$node->firstChild->nodeName] = $ar;
                }
            }
            else {
                foreach ($node->childNodes as $childNode) {
                    if ($childNode->nodeType != XML_TEXT_NODE) {
                        $array[$childNode->nodeName][] = $this->getNodeArray($childNode);
                    }
                }
                foreach ($array as $k=>$v) {
                    if (count($v) == 1 && $k != '#attribute') {
                        $array[$k] = current($v);
                    }
                }
            }
        } else {
            $array['#value'] = $node->nodeValue;
        }
        return $array;
    }
}