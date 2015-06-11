<?php

class FileAssetManager extends CApplicationComponent
{
    const ASSET_ROUTE = 'asset';
    protected $_publishMap;
    private $_basePath;
    private $_published;

    public function init()
    {
        $class = new ReflectionClass('Yii');
        $prefix = str_replace("\\", "/", dirname($class->getFileName()));
        $this->_publishMap['mts.yii'] = $prefix;
        $this->_publishMap['mts.prj'] = str_replace("\\", "/", Yii::app()->projectPath);
        parent::init();
    }
    public function run($path)
    {
        $filePath = $this->getBasePath().'/'.$path;
        if(!is_file($filePath)) {
            throw new CHttpException(404,Yii::t('yii','The asset "{asset}" to be published does not exist.',
                array('{asset}'=>$asset)));
        }
        $etag = md5($path + date('d'));
        header("ETag: {$etag}");
        $offset = 60 * 60 * 24;
        $expire = "expires: " . gmdate ("D, d M Y H:i:s", time() + $offset) . " GMT";
        header ($expire);
        $type = CFileHelper::getMimeType($path);
        header ("content-type: {$type}; charset: UTF-8");
        #header ("cache-control: max-age=$offset,must-revalidate");
        header ("cache-control: max-age=$offset");
        #header ("Pragma:");
        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) AND $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
            #header('HTTP/1.1 304 Not Modified');
            header('Etag:'.$etag,true,304);
        }
        else {
            $fp = fopen($filePath,"r");
            while(!feof($fp)) {
                 echo fgets($fp, 4096);
            }
            fclose($fp);
        }
    }
    public function getBasePath()
    {
        if($this->_basePath===null) {
            $this->setBasePath(str_replace("\\", "/", Yii::app()->getSaveDir('asset')));
        }
        return $this->_basePath;
    }
    public function setBasePath($value)
    {
        $parentPath = dirname($value);
        $baseName = basename($value);
        if(($basePath=realpath($parentPath))!==false) {
            if (is_dir($basePath.'/'.$baseName) && is_writable($basePath.'/'.$baseName)) {
                $this->_basePath=str_replace("\\", "/", $basePath).'/'.$baseName;
            }
            else if (is_dir($basePath) && is_writable($basePath)) {
                $this->_basePath=str_replace("\\", "/", $basePath).'/'.$baseName;
            }
            else {
                throw new CException(Yii::t('yii','CAssetManager.basePath "{path}" is invalid. Please make sure the directory exists and is writable by the Web server process.',
                    array('{path}'=>$value)));
            }
        }
        else
            throw new CException(Yii::t('yii','CAssetManager.basePath "{path}" is invalid. Please make sure the directory exists and is writable by the Web server process.',
                array('{path}'=>$value)));
    }

    public function publish($path)
    {
        $pubpath = $this->publishMap($path);
        if(isset($this->_published[$pubpath]))
            return $this->_published[$pubpath];
        else if(($src=realpath($path))!==false) {
            $basePath=$this->getBasePath();
            $dir=$this->hash($pubpath);
            $dstDir=$basePath.'/'.$dir;
            xcopy($path, $dstDir);
            if (is_file($src)) {
                $fileName=basename($src);
                return $this->_published[$pubpath] = $this->getBaseUrl().'/'.$dir.'/'.$fileName;
            }
            else {
                return $this->_published[$pubpath] = $this->getBaseUrl().'/'.$dir;
            }
        }
        else {
            throw new CException(Yii::t('yii','The asset "{asset}" to be published does not exist.',array('{asset}'=>$path)));
        }
    }
    public function getPublishedPath($path)
    {
        $pubpath = $this->publishMap($path);
        if(($src=realpath($path))!==false) {
            $dir=$this->hash($pubpath);
            $dstDir=$this->getBasePath().'/'.$dir;
            if(is_file($src)) {
                $fileName=basename($src);
                $dstFile=$dstDir.'/'.$fileName;
                return $dstFile;
            }
            else {
                return $dstDir;
            }
        }
        else
            return false;
    }

    public function getPublishedUrl($path)
    {
        $pubpath = $this->publishMap($path);
        if(($src=realpath($path))!==false) {
            $dir=$this->hash($pubpath);
            $dstDir=$this->getBaseUrl().'/'.$dir;
            if(is_file($src)) {
                $fileName=basename($src);
                $dstFile=$dstDir.'/'.$fileName;
                return $dstFile;
            }
            else {
                return $dstDir;
            }
        }
        else
            return false;
    }
    public function getBaseUrl()
    {
        return Yii::app()->getRequest()->getBaseUrl().'/'.self::ASSET_ROUTE;
    }

    protected function hash($path)
    {
        return sprintf('%x',crc32($path.Yii::getVersion()));
    }
    protected function publishMap($path)
    {
        $newpath = rtrim(str_replace("\\", "/", $path), '/');
        foreach ($this->_publishMap as $map=>$p) {
            $len = strlen($p);
            if (strncmp($newpath, $p, $len) == 0) {
                return $map.'/'.substr($newpath, $len+1);
            }
        }
        return $newpath;
    }
}
