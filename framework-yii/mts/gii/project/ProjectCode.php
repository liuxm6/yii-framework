<?php

class ProjectCode extends CCodeModel
{
    public $defaultController='index';
    public $layout='main';
    public $charset='utf-8';
    public $language='zh_cn';
    public $components=array(
        'db'=>array(
            'connectionString'=>'mysql:host=localhost;dbname=mysql',
            'username'=>'root',
            'password'=>'',
            'charset'=>'utf-8',
            'emulatePrepare'=>true
        ),
    );
    public $modules;
    protected $data;
    public function rules()
    {
        return array_merge(parent::rules(), array(
            array('defaultController,layout,charset,language', 'filter', 'filter'=>'trim'),
            array('defaultController,layout,language', 'match', 'pattern'=>'/^\w+[\w+\\.]*$/', 'message'=>'{attribute} should only contain word characters and dots.'),
            array('components,modules', 'safe'),
        ));
    }

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), array(
            'defaultController'=>'默认控制器',
            'layout'=>'布局名',
            'charset'=>'字符集',
            'language'=>'语言',
            'components_db_connectionString'=>"数据库连接",
            'components_db_username'=>"数据库用户",
            'components_db_password'=>"数据库密码",
            'components_db_charset'=>"数据库字符集",
            'modules'=>'模块列表'
        ));
    }

    public function init()
    {
        $loaddata = $this->loaddata();
        $this->attributes = array_merge_recu($this->attributes, $loaddata);
        if (is_array($this->modules)) {
            $this->modules = implode(',', $this->modules);
        }
        parent::init();
    }

    public function prepare()
    {
        $this->files=array();
        $data = $this->attributes;
        $loaddata = $this->loaddata();
        $data = array_merge_recu($loaddata, $data);
        $this->attributes = $data;
        unset($data['answers']);
        unset($data['status']);
        unset($data['files']);
        unset($data['template']);
        ksort($data);
        $this->files[] = new CCodeFile(
                mts_config_file(),
                $this->getFileContent($data)
        );
        $templatePath=$this->templatePath;
        $files=scandir($templatePath);
        foreach($files as $file)
        {
            if(is_file($templatePath.DIRECTORY_SEPARATOR.$file)) {
                $this->files[]=new CCodeFile(
                    Yii::app()->basePath.DIRECTORY_SEPARATOR.$file,
                    $this->render($templatePath.DIRECTORY_SEPARATOR.$file)
                );
            }
            else if (is_dir($templatePath.DIRECTORY_SEPARATOR.$file) && $file != '..' && $file != '.') {
                $subfiles = $this->scandir($templatePath.DIRECTORY_SEPARATOR.$file);
                foreach ($subfiles as $subfile) {
                    $this->files[]=new CCodeFile(
                        Yii::app()->basePath.DIRECTORY_SEPARATOR.substr($subfile, strlen($templatePath)+1),
                        file_get_contents($subfile)
                    );
                }
            }
        }
    }
    protected function scandir($dir)
    {
        $ret = array();
        if (is_dir($dir)) {
            $subfiles = scandir($dir);
            foreach ($subfiles as $file) {
                if ($file == '..' || $file == '.') continue;
                if (is_dir($dir.DIRECTORY_SEPARATOR.$file)) {
                    $ret = array_merge($ret, $this->scandir($dir.DIRECTORY_SEPARATOR.$file));
                }
                else {
                    $ret[] = $dir.DIRECTORY_SEPARATOR.$file;
                }
            }
        }
        return $ret;
    }


    protected function loaddata()
    {
        $file = mts_config_file();
        $data = array();
        if (is_file($file))
            $data = include($file);
        return $data;
    }
    protected function getFileContent($data)
    {
        $str = var_export($data,true);
        $str = preg_replace("/\n[\s]*array/", "array", $str);
        return "<?php\nreturn ".$str.";\n";
    }







}