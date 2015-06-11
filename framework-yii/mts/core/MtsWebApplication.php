<?php

class LxmWebApplication extends CWebApplication
{
    public $defaultController='index';
    public $mailConfig = array();
    public $ossConfig = array();
    protected $_runtimePath;

    public function processRequest()
    {
        if(is_array($this->catchAllRequest) && isset($this->catchAllRequest[0]))
        {
            $route=$this->catchAllRequest[0];
            foreach(array_splice($this->catchAllRequest,1) as $name=>$value)
                $_GET[$name]=$value;
        }
        else
            $route=$this->getUrlManager()->parseUrl($this->getRequest());
        if ($this->assetManager instanceof FileAssetManager) {//增加文件发布路由管理
            $asset = FileAssetManager::ASSET_ROUTE;
            $len = strlen($asset);
            if ($route == $asset || substr($route,0, $len+1) == $asset.'/') {
                $path = substr($route, $len);
                $this->assetManager->run($path);
                $this->end();
            }
        }

        $this->runController($route);
    }
    public function setSaveDir($dir, $target='root')
    {
        return SaveManager::instance()->setSaveDir($dir, $target);
    }
    public function getSaveDir($target='root')
    {
        return SaveManager::instance()->getSaveDir($target);
    }

    public function getProjectPath()
    {
        return dirname($this->basePath);
    }

    public function getRuntimePath()
    {
        if($this->_runtimePath!==null)
            return $this->_runtimePath;
        else {
            $this->setRuntimePath(SaveManager::instance()->getSaveDir('runtime'));
            return $this->_runtimePath;
        }
    }
    public function setRuntimePath($path)
    {
        if(($runtimePath=realpath($path))===false || !is_dir($runtimePath) || !is_writable($runtimePath))
            throw new CException(Yii::t('yii','MtsApplication runtime path "{path}" is not valid. Please make sure it is a directory writable by the Web server process.',
                array('{path}'=>$path)));
        $this->_runtimePath=$runtimePath;
    }
    public function publishCommon()
    {

    }
    public function registerCssFile($cssfile)
    {
        $script = $this->publishCommon().'/css/'.$cssfile;
        return $this->getClientScript()->registerCssFile($script);
    }
    public function registerJEasyUI()
    {
        $cs = Yii::app()->getClientScript();
        Yii::import('mts.extensions.jeasyui.JEasyUI');
        $easyuipath = JEasyUI::publish();
        $cs->registerScriptFile($easyuipath.'/jquery.easyui.min.js',CClientScript::POS_END);
        $cs->registerCssFile($easyuipath.'/themes/default/easyui.css');
        $cs->registerCssFile($easyuipath.'/themes/icon.css');
        $cs->registerScriptFile($easyuipath.'/easyloader.js',CClientScript::POS_END);
    }

    public function getCaptchaCode($route)
    {
        $ca = $this->createController($route);
        list($controller,$actionID) = $ca;
        if ($controller && $actionID) {
            $captcha=$controller->createAction($actionID);
            if ($captcha instanceof CCaptchaAction) {
                return $captcha->getVerifyCode(false);
            }
        }
        return null;
    }
    public function getAllActions($m='modules',$c='controllers')
    {
        $ret = array();
        $mdir = $this->getBasePath().'/'.$m;
        $mfiles = scandir($mdir);
        foreach ($mfiles as $mfile) {
            if ($mfile != '.' && $mfile != '..') {
                $cdir = $mdir.'/'.$mfile.'/'.$c;
                $cfiles = scandir($cdir);
                foreach ($cfiles as $cfile) {
                    if (preg_match('/([\w]+)Controller.php/', $cfile, $match)) {
                        $controller = $match[1];
                        $actions = $this->getFileActions($cdir.'/'.$cfile, $controller, $mfile);
                        $ret = array_merge($ret, $actions);
                    }
                }
            }
        }
        return $ret;

    }
    protected function getFileActions($file, $c, $m=null)
    {
        $content = file_get_contents($file);
        preg_match_all('/public[\s]+function[\s]+action([\w]+)/', $content, $matches);
        $ret = array();
        foreach ($matches[1] as $action) {
            $one['url'] = strtolower(($m?'/'.$m:'').'/'.$c.'/'.$action);
            $one['module'] = strtolower($m);
            $one['controller'] = strtolower($c);
            $one['action'] = strtolower($action);
            $ret[] = $one;
        }
        return $ret;
    }
    public function mail($to, $toName, $subject, $message, $options=array())
    {
        Yii::import('mts.extensions.mailer.EMailer');
        $c = $this->mailConfig;
        if (!isset($c['host'],$c['username'],$c['password'],$c['from'])) {
            return false;
        }
        $mail = new EMailer;
        $mail->IsSMTP();
        $mail->SMTPAuth    = true;
        $mail->CharSet     = isset($options['charset'])?$options['charset']:'UTF-8';
        $mail->ContentType = isset($options['contentType'])?$options['contentType']:'text/html';
        $mail->SMTPDebug   = isset($options['debug'])?$options['debug']:false;
        $mail->SMTPSecure  = isset($c['secure'])?$c['secure']:'';
        $mail->Host        = $c['host'];
        $mail->Port        = isset($c['port'])?$c['port']:25;
        $mail->Username    = $c['username'];
        $mail->Password    = $c['password'];
        $mail->From        = $c['from'];
        $mail->FromName    = isset($c['fromName'])?$c['fromName']:$mail->From;
        $mail->Subject     = $subject;
        $mail->Body        = $message;
        $mail->addAddress($to, $toName);

        if (is_array($options['cc'])) {
            foreach  ($options['cc'] as $k=>$v) {
                $mail->AddCC($k, $v);
            }
        }
        if (is_array($options['bcc'])) {
            foreach  ($options['bcc'] as $k=>$v) {
                $mail->AddBCC($k, $v);
            }
        }
        return $mail->Send();
    }
    public function getOssService($name)
    {
        $ret = false;
        if (isset($this->ossConfig[$name])) {
            $accessKey = $this->ossConfig[$name]['accessKey'];
            $secureKey = $this->ossConfig[$name]['secureKey'];
            $bucket    = $this->ossConfig[$name]['bucket'];
            $host      = $this->ossConfig[$name]['host'];
            $baseurl   = $this->ossConfig[$name]['baseurl'];
            $oss = new OssService($host, $accessKey,$secureKey,$bucket, $baseurl);
            $oss->debug(false);
            return $oss;
        }
        return $ret;
    }
}