<?php

class Pagination extends CPagination
{
    const DEFAULT_NAV_NUM = 5;
    private $_navNum;
    public $basePageUrl;


    /**
     * @return integer $value nav numbers value
     */
    public function getNavNum()
    {
        return $this->_navNum;
    }

    /**
     * @param integer $value nav numbers value
     */
    public function setNavNum($value)
    {
        if(($this->_navNum=$value)<=0)
            $this->_navNum=self::DEFAULT_NAV_NUM;
    }
    public function isShowPage()
    {
        return $this->getPageCount()>1;
    }
    public function getFirstPageUrl()
    {
        $params[$this->pageVar] = 1;
        $url = url_replace_param($this->basePageUrl, $params);
        return $url;
    }
    public function getLastPageUrl()
    {
        $params[$this->pageVar] = $this->getPageCount();
        $url = url_replace_param($this->basePageUrl, $params);
        return $url;
    }
    public function getPreNavUrl()
    {
        if ($this->getCurrentPage() > 0) {
            $params[$this->pageVar] = $this->getCurrentPage()-1;
            $url = url_replace_param($this->basePageUrl, $params);
        }
        else {
            $url = null;
        }
        return $url;
    }
    public function getNextNavUrl()
    {
        if ($this->getCurrentPage()+1 < $this->getPageCount()) {
            $params[$this->pageVar] = $this->getCurrentPage()+2;
            $url = url_replace_param($this->basePageUrl, $params);
        }
        else {
            $url = null;
        }
        return $url;
    }
    public function getNavList()
    {
        $num = $this->getNavNum();
        $half = intval($num/2);
        $pageCount = $this->getPageCount();
        $page = $this->getCurrentPage()+1;
        $start = $page - $half;
        if ($page+$half>=$pageCount) {
            $start = $pageCount - $num + 1;
        }
        if ($start<1) $start = 1;
        $list = array();
        for ($i=0;$i<$this->getNavNum();$i++) {
            if ($start + $i <= $pageCount) {
                $params[$this->pageVar] = $start+$i;
                $list[$start+$i] = url_replace_param($this->basePageUrl, $params);
            }
        }
        return $list;
    }

}

