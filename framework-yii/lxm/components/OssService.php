<?php
/**
 * Oss上传 下载 检测服务
 *
 */
class OssService
{
    protected $_oss;
    protected $_bucket;
    protected $_baseurl;

    public function __construct($host, $accessKey, $secureKey, $bucket, $baseurl)
    {
        Yii::import('mts.extensions.oss.ALIOSS');
        $this->_oss = new ALIOSS($accessKey, $secureKey, $host);
        $this->_bucket = $bucket;
        $this->_baseurl = $baseurl;
    }
    public function upload($file, $object)
    {
        $object = ltrim($object, '/');
        $options = array(
            ALIOSS::OSS_FILE_UPLOAD => $file,
            'partSize' => 5242880,
        );
        try  {
            $this->_oss->create_mpu_object($this->_bucket, $object, $options);
        }
        catch (Exception $e) {
        }
        return $this;
    }
    public function download($object, $file, $contentType = 'application/octet-stream')
    {
        $object = ltrim($object, '/');
        $options = array(
            ALIOSS::OSS_FILE_DOWNLOAD => $file,
            ALIOSS::OSS_CONTENT_TYPE => $contentType,
        );
        try  {
            $this->_oss->get_object($this->_bucket, $object, $options);
        }
        catch (Exception $e) {
        }
        return $this;
    }
    public function status($object)
    {
        $status = $this->_oss->is_object_exist($this->_bucket,$object);
        return $status;
    }
    public function exist($object)
    {
        $status = $this->status($object);
        return $status->status == 200;
    }
    public function debug($debug=false)
    {
        $this->_oss->set_debug_mode($debug);
    }
}