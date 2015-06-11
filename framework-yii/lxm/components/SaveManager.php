<?php

class SaveManager
{

    protected $_saveTargets = array('root', 'runtime', 'cache', 'upload', 'asset', 'state');
    protected $_saveDir = array();
    protected static $_instance;

    private function __construct()
    {
    }
    public static function instance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function setSaveDir($dir, $target='root')
    {
        if ($target=='root') {
            if (!is_writable($dir)) {
                throw new CException(Yii::t('yii','Application set save directory "{path}" is not valid. Please make sure it is a directory writable by the Web server process.',
                    array('{path}'=>$dir)));
            }
            $this->_saveDir['root'] = $dir;
        }
        else if (in_array($target, $this->_saveTargets)) {
            if (is_dir($dir) && is_writable($dir)) {
                $this->_saveDir[$target] = $dir;
            }
        }
        else {
            throw new CException(Yii::t('yii','Application set save directory target "{target}" is not valid.',
                array('{target}'=>$target)));
        }
    }
    public function getSaveDir($target='root')
    {
        if (!in_array($target, $this->_saveTargets)) {
            throw new CException(Yii::t('yii','Application get save directory target "{target}" is not valid.',
                array('{target}'=>$target)));
        }
        if ($target == 'root') {
            if (!isset($this->_saveDir[$target])) {
                if (defined('YII_PROJECT_PATH')) {
                    $this->_saveDir[$target] = YII_PROJECT_PATH.DIRECTORY_SEPARATOR.'data';
                }
                else {
                    $this->_saveDir[$target] = dirname(getcwd()).DIRECTORY_SEPARATOR.'data';
                }
            }
            return $this->_saveDir[$target];
        }
        else if (isset($this->_saveDir[$target])) {
            return $this->_saveDir[$target];
        }
        else {
            $targetDir = $this->getSaveDir().DIRECTORY_SEPARATOR.$target;
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
                chmod($targetDir, 0777);
            }
            return $targetDir;
        }
    }
}