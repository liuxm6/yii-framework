<?php
/**
 * client    方法，用于 获取 MTS 考试机最新版本信息
 * signin    方法，用于 加入考试（登录到 Test Number）
 * confirm   方法，用于 考试确认（确认 Test Number）
 * slogin    方法，用于 登录准考证
 * candidate 方法，用于 确认考生信息
 * testpwd   方法，用于 获取试卷密码（暂时不用）
 * starts    方法，用于 获取指定场次是否可以开考(暂时不用)
 * cstat     方法，用于 提交考生状态（暂时不用）（返回通用错误）
 * ansstat   方法，用于 请求交卷
 * upanswer  方法，用于 上传答案     （返回通用错误）
 * gansstat  方法，用于 交卷是否完成
 * resume    方法，用于 恢复考试
 * quitapp   方法，用于 跳出应用程序 （返回通用错误）
 */

class ApitestController extends Controller
{
    public $host   = '';//设置请求的域名,如'http://mtsonlineapi.atamts.com';


    public function actionIndex()
    {
    }

    /**
     * 将返回的值放入数组中
     * @param  int   $arr_suc
     * @param  array $arr_con
     * @return array $data
     */
    public function jsonData($arr_con)
    {
        $json = json_encode($arr_con);
        $data = array('data' => $json);

        return $data;
    }

    /**
     * 模拟 POST 请求
     * @param  string $url
     * @param  array  $data
     * @return string
     */
    public function virtualPost($url,$data)
    {
        $postdata = http_build_query($data);

        $opts = array('http' =>
            array(
                'method'  => 'POST',
                'header'  => 'Content-type: application/x-www-form-urlencoded',
                'content' => $postdata
            )
        );

        $context = stream_context_create($opts);

        $result = file_get_contents($url, false, $context);
        return $result;
    }

    //获取 MTS 考试机具体参数
    public function clientInfo()
    {
        if($this->host != ''){
            $url = $this->host.'/client/version';
            $data = array(
                'platform' => 512,
                'lang'     => 'zh-CN'
            );
            $arr_con = $this->virtualPost($url,$data);
        }else{
            //模拟数据
            $arr_tmp = array(
                'success'    => 1,
                'data'       => array(
                    'isForce'              => 1,
                    'version'              => '1.1.1',                                                                 //MTS Online考试机最新版本号
                    'releaseDate'          => 1425259196,                                                              //版本发布日期
                    'downloadUri'          => 'https://www.atamts.com/testdelivery/windows/MtsTestClient.application', //MTS Online考试机下载地址
                    'downloadAlternateUri' => 'https://us.atamts.com/testdelivery/windows/MtsTestClient.application',  //MTS Online考试机备用下载地址
                    'sha1'                 => '52d4304bd2180b245fdd3d0f0a8565d9eba586f8',                              //sha1码
                    'size'                 => 102002                                                                   //考试机在下载完成时，应做sha1和文件大小的双重校验
                )
            );
            $arr_con = $this->jsonData($arr_tmp);
        }
        return($arr_con);
    }

    //加入考试的信息
    public function signinCon()
    {
        if($this->host != ''){
            $url = $this->host.'/session/signinTestCode';
            $data = array(
                'deviceID'    => '0001xxxxxx',    //platform(4位)+Identify(设备唯一标识)
                'testNumber'  => '01234567',      //考试识别码
                'deviceInfo'  => '',              //考试机相关信息
                'appVersion'  => '',              //考试机程序版本号
                'osVersion'   => '',              //操作系统版本号
                'sdkVersion'  => 'IOS 8.1',       //系统版本
                'modelName'   => 'Galaxy Note 2', //品牌型号
                'resolution'  => '1024x768',      //分辨率
                'useragent'   => 'Mozilla\/5.0',  //浏览器相关信息
                'location'    => '23,33',         //经纬度
                'other'       => 'density',       //其它
                'lang'        => 'zh-CN'          //考试机当前使用语言
            );
            $arr_con = $this->virtualPost($url,$data);
        }else{
            //模拟数据
            $arr_tmp = array(
                'success'    => 1,
                'data'       => array(
                    'serverName'  => 'China server 01',                         //服务器名称
                    'serverURL'   => 'www.atamts.com',                          //后续API接口访问主路径
                    'testName'    => '证券从业人员考试',                        //考试名称
                    'sessionID'   => '25c66323-4fcb-a48e-196e-52903fa7864c',    //场次ID
                    'type'        => 1,                                         //场次类型
                    'mode'        => 1,                                         //场次模式
                    'suspend'     => 1,                                         //程序挂起模式处理类型
                    'sponsor'     => 'ATA',                                     //考试主办方名称
                    'subjectID'   => 'xxxxx-xxxx-xxxx',                         //科目ID
                    'subjectName' => '证券基础知识',                            //科目名称
                    'startTime'   => 1427490000,                                //考试开考日期时间
                    'startLimit'  => 3600,                                      //考试开考时间有效期
                    'startRender' => 1,                                         //考试开考日期时间呈现方式
                    'endtime'     => 1427506111                                 //强制交卷时间
                )
            );
            $arr_con = $this->jsonData($arr_tmp);
        }
        return($arr_con);
    }

    //考试确认的信息
    public function confirmCon()
    {
        if($this->host != ''){
            $url = $this->host.'/session/confirmTestNumber';
            $data = array(
                'deviceID'    => '0001xxxxxx',    //platform(4位)+Identify(设备唯一标识)
                'testNumber'  => '01234567',      //考试识别码
                'confirm'     => 1,               //确认是否进入场次 0代表否，1代表是
                'lang'        => 'zh-CN'          //考试机当前使用语言
            );
            $arr_con = $this->virtualPost($url,$data);
        }else{
            //模拟数据
            $arr_tmp = array(
                'success'    => 1,
                'data'       => array(
                    //'skinConfig' => '待定',
                    'skin' => array(                                                                                                //皮肤包文件
                        'source' => array(                                                                                          //皮肤包下载相关
                            'protocol' => 1,                                                                                        //传输协议
                            'uri'      => 'http://forms.atamts.com/PROJECT/skin.zip',                                               //试卷包文件完整下载地址
                            'username' => 'username1',                                                                              //访问服务器的用户名
                            'password' => 'password1'                                                                               //访问服务器的密码
                        ),
                        'size'   => 10202,                                                                                          //试卷包文件大小
                        'sha1'   => '52d4304bd2180b245fdd3d0f0a8565d9eba586f8'                                                      //试卷包文件 SHA1 校验码
                    ),
                    'formlist' => array(                                                                                            //试卷组
                        array(
                            'id'       => '602cc109-f5f19da759b0-9da759b0abb608',                                                   //考试实际用试卷 GUID
                            'platform' => 1,                                                                                        //试卷适用的 MTS 考试机平台
                            'subject'  => 'Starters',                                                                               //科目名称
                            'source'   => array(                                                                                    //试卷包文件下载地址
                                'protocol' => 3,                                                                                    //传输协议
                                'uri'      => 'ftp://forms.atamts.com/PROJECT/windows/602cc109-f5f19da759b0-9da759b0abb608.zip',    //试卷包文件完整下载地址
                                'username' => 'username1',                                                                          //访问服务器的用户名
                                'password' => 'password1'                                                                           //访问服务器的密码
                            ),
                            'size'     => 10202,                                                                                    //试卷包文件 SHA1 校验码
                            'sha1'     => '52d4304bd2180b245fdd3d0f0a8565d9eba586f8'                                                //试卷包文件大小
                        ),
                        array(
                            'id' => '602cc109-f5f19da759b0-9da759b0abb605',
                            'platform' => 112,
                            'subject'  => 'Movers',
                            'source'   => array(
                                'protocol' => 3,
                                'uri'      => 'ftp://forms.atamts.com/PROJECT/windows/602cc109-f5f19da759b0-9da759b0abb608.zip',
                                'username' => 'username1',
                                'password' => 'password1'
                            ),
                            'size'     => 10202,
                            'sha1'     => 'a00e4a8829176dd4117fb7380e61366c0306dbfb'
                        )
                    )
                )
            );
            $arr_con = $this->jsonData($arr_tmp);
        }

        return($arr_con);
    }

    //登录准考证的信息
    public function sloginCon()
    {
        if($this->host != ''){
            $url = $this->host.'/session/login';
            $data = array(
                'deviceID'        => '0001xxxxxx',    //platform(4位)+Identify(设备唯一标识)
                'testNumber'      => '01234567',      //考试识别码
                'candidateNumber' => '',              //准考证号
                'lang'            => 'zh-CN'          //考试机当前使用语言
            );
            $arr_con = $this->virtualPost($url,$data);
        }else{
            //模拟数据
            $arr_tmp = array(
                'success'    => 1,
                'data'       => array(
                    'uuid'            => '19b7a6990cddbb210f28bd57ac502638',                //请求 MTS Cloud 服务器的令牌（token）
                    'candidateNumber' => 'CN20140001',                                      //准考证号
                    'firstName'       => 'nan',                                             //名字
                    'lastName'        => 'zhong',                                           //姓氏
                    'initials'        => 'xiao',                                            //中间字
                    'displayName'     => 'xiao zhong nan',                                  //姓名
                    'gender'          => 'F',                                               //性别
                    'dob'             => 686851200,                                         //生日
                    'identityType'    => 1,                                                 //证件类型
                    'identityNumber'  => '210312199110082121',                              //证件号码
                    'seat'            => '01',                                              //座位号
                    'subject'         => '证券投资基金',                                    //科目名称
                    'startTime'       => 1425302525,                                        //开始考试时间
                    'duration'        => 1800,                                              //考试时长 单位：（秒）
                    'candidateID'     => '00B6532E-0A0D-4C45-A1D6-79465F59F1D0',            //考生ID
                    'field1'          => '备选参数',                                        //备选参数
                    'field2'          => '备选参数',                                        //备选参数
                    'field3'          => '备选参数',                                        //备选参数
                    'photo'           => array(                                             //考生照片信息
                        'photoURI'    => 'http://www.atamts.com/photos/CN20140001.png',     //考生照片下载地址
                        'sha1'        => 'a00e4a8829176dd4117fb7380e61366c0306dbfb',        //照片文件sha1值
                        'size'        => 10101                                              //照片文件大小
                    )
                )
            );
            $arr_con = $this->jsonData($arr_tmp);
        }
        return($arr_con);
    }

    //确认考生的相关信息
    public function candidateCon()
    {
        if($this->host != ''){
            $url = $this->host.'/session/confirmCandidate';
            $data = array(
                'uuid'       => '503312',         //请求 MTS Cloud 服务器的令牌（token）
                'confirm'    => 1,                //确认值 1代表确认，0代表取消
                'lang'       => 'zh-CN'           //考试机当前使用语言
            );
            $arr_con = $this->virtualPost($url,$data);
        }else{
            //模拟数据
            $arr_tmp = array(
                'success'    => 1,
                'data'       => array(
                    'shuffle'     => 882344485,                                          //随机因子
                    'msg_mintime' => '亲，你交卷太早哦。',                               //早于minTime交卷时在考试机上显示的提示文字
                    'min_time'    => 3600,                                               //提早交卷的最小时间
                    'latetime'    => 0,                                                  //迟到扣时
                    'form'        => array(                                              //返回考生所考试卷
                        'id'      => '25c66323-4fcb-a48e-196e-52903fa7864c',             //考试实际用试卷GUID
                        'formName'=> '25c66323-4fcb-a48e-196e-52903fa7864c-Ipad.zip',    //考试实际用试卷文件名
                        'password'=> 'password1'                                         //试卷解压密码
                    ),
                    'session'     => array(
                        'status'  => 11,                                                 //场次状态
                        'diff'    => 600                                                 //距离可开考的时间差
                    )
                )
            );
            $arr_con = $this->jsonData($arr_tmp);
        }
        return($arr_con);
    }

    //试卷密码的信息
    public function testpwdCon()
    {
        if($this->host != ''){
            $url = $this->host.'/session/getTestFormPassword';
            $data = array(
                'uuid'     => '503312',          //请求 MTS Cloud 服务器的令牌（token）
                'lang'     => 'zh-CN'            //考试机当前使用语言
            );
            $arr_con = $this->virtualPost($url,$data);
        }else{
            //模拟数据
            $arr_tmp = array(
                'success'    => 1,
                'data'       => array(
                    'form' => array(                                                      //返回考生所考试卷
                        'id'       => '25c66323-4fcb-a48e-196e-52903fa7864c',             //考试实际用试卷GUID
                        'formname' => '25c66323-4fcb-a48e-196e-52903fa7864c-Ipad.zip',    //考试实际用试卷文件名
                        'password' => 'password1'                                         //试卷解压密码
                    )
                )
            );
            $arr_con = $this->jsonData($arr_tmp);
        }
        return($arr_con);
    }

    //指定场次的信息
    public function startsCon()
    {
        if($this->host != ''){
            $url = $this->host.'/session/getStartTestStatus';
            $data = array(
                'uuid'     => '503312',           //请求 MTS Cloud 服务器的令牌（token）
                'lang'     => 'zh-CN'             //考试机当前使用语言
            );
            $arr_con = $this->virtualPost($url,$data);
        }else{
            //模拟数据
            $arr_tmp = array(
                'success'    => 1,
                'data'       => array(
                    'status' => 11,          //开考状态
                    'diff'   => 600          //当前时间还未到开考时间，返回距离开考时间的时间差
                )
            );
            $arr_con = $this->jsonData($arr_tmp);
        }
        return($arr_con);
    }

    //提交考生状态的信息
    public function cstatCon()
    {
        if($this->host != ''){
            $url = $this->host.'/postCandidateStatus';
            $data = array(
                'uuid'     => '503312',           //请求 MTS Cloud 服务器的令牌（token）
                'status'   => 1,                  //考生状态
                'lang'     => 'zh-CN'             //考试机当前使用语言
            );
            $arr_con = $this->virtualPost($url,$data);
        }else{
            //模拟数据
            $arr_tmp = array(
                'success'    => 1,
                //返回错误结果为：通用错误
                'data'       => array(

                )
            );
            $arr_con = $this->jsonData($arr_tmp);
        }
        return($arr_con);
    }

    //请求交卷的信息
    public function ansstatCon()
    {
        if($this->host != ''){
            $url = $this->host.'/session/getPostAnswerStatus';
            $data = array(
                'uuid'     => '503312',           //请求 MTS Cloud 服务器的令牌（token）
                'type'     => 1                   //上传的考试数据包类型
            );
            $arr_con = $this->virtualPost($url,$data);
        }else{
            //模拟数据
            $arr_tmp = array(
                'success'    => 1,
                'data'       => array(
                    'uploadURI'     => 'http://mtsonline/postAnswer',    //上传地址
                    'showScore'     => 1,                                //是否显示分数
                    'uploadPackage' => array(                            //考试上传答案包
                        'zipName'      => 'Response.zip',                //上传的文件名称
                        'packageFiles' => 1,                             //需要打包的文件
                        'zipPassword'  => '09Db[7U.]?^,130r',            //上传打包的压缩密码
                        'expired'      => 1427708326                     //上传数据过期日期时间
                    )
                )
            );
            $arr_con = $this->jsonData($arr_tmp);
        }
        return($arr_con);
    }

    //上传答案的信息
    public function upanswerCon()
    {
        if($this->host != ''){
            //获得请求交卷接口返回的 uploadURI
            $url_ansstat = $this->host.'/session/getPostAnswerStatus';
            $data_ansstat = array(
                'uuid'     => '503312',           //请求 MTS Cloud 服务器的令牌（token）
                'type'     => 1                   //上传的考试数据包类型
            );
            $arr_ansstat = $this->virtualPost($url_ansstat,$data_ansstat);

            $arr_ans = json_decode($arr_ansstat);
            $url = $arr_ans->data->uploadURI;

            $data = array(

            );
            $arr_con = $this->virtualPost($url,$data);
        }else{
            //模拟数据
            $arr_tmp = array(
                'success'    => 1,
                //返回错误结果为：通用错误
                'data'       => array(

                )
            );
            $arr_con = $this->jsonData($arr_tmp);
        }
        return($arr_con);
    }

    //交卷信息
    public function gansstatCon()
    {
        if($this->host != ''){
            $url = $this->host.'/session/getAnswerStatus';
            $data = array(
                'uuid'        => '503312',           //请求 MTS Cloud 服务器的令牌（token）
                'xmlSha1'     => '1a2abe',           //xml格式答案内容的sha1校验码
                'size'        => 10201,              //上传的考试数据包文件大小(单位：字节)
                'sha1'        => '0a2abe'            //上传的考试数据包文件 SHA1 校验码
            );
            $arr_con = $this->virtualPost($url,$data);
        }else{
            //模拟数据
            $arr_tmp = array(
                'success'    => 1,
                'data'       => array(
                    'answerStatus' => array(   //答案状态
                        'uploaded' => true,    //答案上传状态
                        'verified' => true     //答案校验状态
                    ),
                    'zipStatus'    => array(   //考试数据包状态
                        'uploaded' => true,    //考试数据包上传状态
                        'verified' => true     //考试数据包校验状态
                    )
                )
            );
            $arr_con = $this->jsonData($arr_tmp);
        }
        return($arr_con);
    }

    //恢复考试的状态信息
    public function resumeCon()
    {
       if($this->host != ''){
            $url = $this->host.'/session/resumeTest';
            $data = array(
                'uuid'     => '51131512',      //请求 MTS Cloud 服务器的令牌（token）
                'lang'     => 'zh-CN'          //考试机当前使用语言
            );
            $arr_con = $this->virtualPost($url,$data);
        }else{
            //模拟数据
            $arr_tmp = array(
                'success'    => 1,
                'data'       => array(
                    'status' => 11   //场次状态
                )
            );
            $arr_con = $this->jsonData($arr_tmp);
        }
        return($arr_con);
    }

    //跳出应用程序的信息
    public function quitappCon()
    {
        if($this->host != ''){
            $url = $this->host.'/session/quitApplication';
            $data = array(
                'uuid'        => '51131512',        //请求 MTS Cloud 服务器的令牌（token）
                'localtime'   => 1427941638         //本地时间戳
            );
            $arr_con = $this->virtualPost($url,$data);
        }else{
            //模拟数据
            $arr_tmp = array(
                'success'    => 1,

                //返回错误结果为：通用错误
                'data'       => array(

                )
            );
            $arr_con = $this->jsonData($arr_tmp);
        }
        return($arr_con);
    }

}