<?php

class AssetManager extends CApplicationComponent
{
    const ASSET_ROUTE = 'asset';
    protected $_pubmaps;
    private $_basePath;
    private $_published;

    public function init()
    {
        $class = new ReflectionClass('Yii');
        $prefix = str_replace("\\", "/", dirname($class->getFileName()));
        $this->_pubmaps['mts.yii'] = $prefix;
        $this->_pubmaps['mts.prj'] = str_replace("\\", "/", Yii::app()->projectPath);
        parent::init();
    }
    public function getBasePath()
    {
        if($this->_basePath===null)
        {
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

    public function publish($path, $forceCopy=1)
    {
        $pubpath = $this->pubmaps($path);
        if(isset($this->_published[$pubpath]))
            return $this->_published[$pubpath];
        else if(($src=realpath($path))!==false) {
            $dir=$this->hash($pubpath);
            if (!is_dir($basePath=$this->getBasePath())) {
                @mkdir($basePath);
            }
            $dstDir=$basePath.'/'.$dir;
            if(is_file($src)) {
                $fileName=basename($src);
                $dstFile=$dstDir.'/'.$fileName;
                if(!is_file($dstFile)) { //@filemtime($dstFile)<@filemtime($src)) {
                    if(!is_dir($dstDir)) {
                        mkdir($dstDir,0777);
                        chmod($dstDir, 0777);
                    }
                    copy($src,$dstFile);
                    @chmod($dstFile, 0666);
                }
                return $this->_published[$pubpath]=$this->getBaseUrl()."/$dir/$fileName";
            }
            else if(is_dir($src)) {
                if(!is_dir($dstDir) || $forceCopy) {
                    CFileHelper::copyDirectory($src,$dstDir,array(
                        'exclude'=>array('.svn'),
                        'level'=>-1,
                        'newDirMode'=>0777,
                        'newFileMode'=>0666,
                    ));
                }
                return $this->_published[$pubpath]=$this->getBaseUrl().'/'.$dir;
            }
        }
        //throw new CException(Yii::t('yii','The asset "{asset}" to be published does not exist.',array('{asset}'=>$path)));
    }
    public function getPublishedPath($path)
    {
        $pubpath = $this->pubmaps($path);
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
        $pubpath = $this->pubmaps($path);
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


    protected function hash($path)
    {
        return sprintf('%x',crc32($path.Yii::getVersion()));
    }
    protected function pubmaps($path)
    {
        $newpath = str_replace("\\", "/", $path);
        foreach ($this->_pubmaps as $map=>$p) {
            $len = strlen($p);
            if (strncmp($newpath, $p, $len) == 0) {
                return $map.'/'.substr($newpath, $len+1);
            }
        }
        return $newpath;
    }
}
