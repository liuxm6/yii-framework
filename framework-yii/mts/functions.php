<?php


function error_t($message, $lang, $params=array())
{
    return Yii::t('error',$message,$params,null,$lang);
}
function _t($message,$params=array(), $lang=null,$source=null, $category='global')
{
    return $message;
}
if (!function_exists('t')) {
    function t($message,$params=array(), $lang=null,$source=null, $category='global')
    {
        if ($params === null) $params = array();
        return Yii::t($category,$message,$params,$source,$lang);
    }
}
/**
 * @to array
 * @from array
 * @message string
 * @return boolean
 * @example:
 *
 */
function mail_send($to, $title, $message, $type="text/html", $options=array())
{
    include_once dirname(__FILE__).'/extensions/mailer/EMailer.php';
    $mail = new EMailer;
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->CharSet = "utf-8";
    $mail->ContentType = $type;
    $mail->SMTPDebug = true;
    $mail->Host = 'secure.emailsrvr.com';
    $mail->SMTPSecure = 'ssl';//isset($options['ssl'])?$options['ssl']:'';
    $mail->Port = '465';
    $mail->Username = 'admin@cambridgeyounglearners.org';
    $mail->Password = 'y1eAdm1n';
    $mail->From = 'admin@cambridgeyounglearners.org';
    $mail->FromName = "zhitestadmin";
    $mail->Subject = $title;
    $mail->Body = $message;
    $cc = isset($options['cc'])?$options['cc']:array();
    $bcc = isset($options['bcc'])?$options['bcc']:array();
    foreach ($to as $k=>$v) {
        $mail->AddAddress($k, $v);
    }
    foreach ($cc as $k=>$v) {
        $mail->AddCC($k, $v);
    }
    foreach ($bcc as $k=>$v) {
        $mail->AddBCC($k, $v);
    }
    return $mail->Send();
}
function include_string($string){
    eval ("?>$string");
 }
function utf8($type="text/html") {
    header("Content-type:{$type};charset=utf-8");
}
function get_excel_column($i)
{
    $list = array();
    for ($j=1;$j<=26;$j++) $list[$j] = chr(64+$j);
    $first = ($i-1) % 26+1;
    $ret = $list[$first];
    $k = $i;
    while ($k > 26) {
        $k = intval($k/26);
        $v = ($k-1) % 26+1;
        $ret = $list[$v].$ret;
    }
    return $ret;
}
function make_object_id($hash=null, $counter=null) {
    $time = dechex(time());
    if (strlen($hash) != 3)
        $hash = substr(md5(__FILE__), 0, 3);
    $hash = bin2hex($hash);
    $pid = sprintf("%04s", dechex(getmypid()%65536));
    if (is_int($counter)) {
        $counter %= 1<<24;
    }
    else {
        $counter = mt_rand(0, 1<<24);
    }

    $count = sprintf("%06s", dechex($counter));
    return $time.$hash.$pid.$count;
}
/**
 * 模拟xcopy命令
 *
 * @source  mixed 源文件或者目录
 * @target  string 目标文件或者目录
 *
 */
function xcopy($source, $target, $quiet=true)
{
    if (is_file($source)) {
        if (is_dir($target)) {
            if (!is_writable($target)) {
                trigger_error($target." can't write", E_USER_ERROR);
                return false;
            }
            if (!$quiet) echo $source.' => '.$target."\n";
            return copy($source, $target.DIRECTORY_SEPARATOR.basename($source));
        }
        elseif (is_file($target)) {
            if (!is_writable($target)) {
                trigger_error($target." can't write", E_USER_ERROR);
                return false;
            }
            if (!$quiet) echo $source.' => '.$target."\n";
            return copy($source, $target);
        }
        else {
            $tardir = dirname($target);
            if (is_dir($tardir)) {
                if (!is_writable($tardir)) {
                    trigger_error($target." can't write", E_USER_ERROR);
                    return false;
                }
                if (!$quiet) echo $source.' => '.$target."\n";
                return copy($source, $target);
            }
            else {
                if (!mkdir($tardir, 0777, true)) {
                    trigger_error($tardir." can't write", E_USER_ERROR);
                    return false;
                }
                chmod($tardir, 0777);
                if (!$quiet) echo $source.' => '.$target."\n";
                return copy($source, $target);
            }
        }
    }
    else if (is_dir($source)) {
        $tardir = dirname($target);
        if (!is_dir($tardir)) {
            if (!mkdir($target, 0777, true)) {
                trigger_error($target." can't write", E_USER_ERROR);
                return false;
            }
            chmod($target, 0777);
        }
        else {
            if (is_file($target)) {
                trigger_error($target." is file", E_USER_ERROR);
                return false;
            }
            elseif (is_dir($target)) {
                if (!is_writable($target)) {
                    trigger_error($target." can't write", E_USER_ERROR);
                    return false;
                }
            }
            else {
                if (!mkdir($target, 0777, true)) {
                    trigger_error($target." can't write", E_USER_ERROR);
                    return false;
                }
                chmod($target, 0777);
            }
        }
        //$source = realpath($source);
        //$target = realpath($target);
        $base = $source;
        $files = scandir($source);
        while (!empty($files)) {
            $file = array_shift($files);
            if ($file == '.' || $file == '..' || $file == '.svn') continue;
            $source_file = $base.DIRECTORY_SEPARATOR.$file;
            if (is_file($source_file)) {
                $target_file = $target.DIRECTORY_SEPARATOR.$file;
                xcopy($source_file, $target_file, $quiet);
            }
            else if (is_dir($source_file)) {
                $sfiles = scandir($source_file);
                foreach ($sfiles as $sf) {
                    if ($sf == '.' || $sf == '..' || $sf == '.svn') continue;
                    $files[] = substr($source_file, strlen($base)+1).DIRECTORY_SEPARATOR.$sf;
                }
            }
        }

    }
    else if (is_array($source)) {
        $ok = true;
        foreach ($source as $file) {
            $ok &= xcopy($file, $target, $quiet);
        }
        return $ok;
    }
    else {
        trigger_error($source." is not a file or directory", E_USER_ERROR);
        return false;
    }
}
function xdelete($file)
{
    if (is_file($file)) {
        return unlink($file);
    }
    elseif (is_dir($file)) {
        $dir = $file;
        $files = scandir($dir);
        $ok = true;
        foreach ($files as $f) {
            if ($f == '.' || $f == '..') continue;
            $ok &= xdelete($dir.DIRECTORY_SEPARATOR.$f);
        }
        if ($ok) $ok &= rmdir($file);
        return $ok;
    }
    else {
        return false;
    }
}
function echo_r($var)
{
    $iscli = php_sapi_name() == 'cli';
    if (!$iscli) echo '<pre>';
    print_r($var);
    if (!$iscli) echo '</pre>';
}
function curl(array $opts, $debug=false)
{
    $ret = array();
    $ch = curl_init();
    if (!$ch) {
        return $ret;
    }
    curl_setopt_array($ch, $opts);
    curl_setopt($ch, CURLOPT_HEADER, true);
    if ($debug) {
        curl_setopt($ch, CURLINFO_HEADER_OUT, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
    }
    ob_start();
    $response = curl_exec($ch);
    $curl_contents = ob_get_contents();
    ob_end_clean();
    if (curl_errno($ch)) {
        $errno = curl_errno($ch);
        $errmsg = curl_error($ch);
        curl_close($ch);
        return $ret;
    }
    $info = curl_getinfo($ch);
    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $header_size);
    $body = substr($response, $header_size);
    curl_close($ch);
    array_push($ret, $info, $header, $body);
    return $ret;
}
function curl_post($url, $post_body='', $port=80, $headers=array(), $debug=false)
{
    $ret = array();
    $curl_opts = array(
        CURLOPT_URL => $url,
        CURLOPT_PORT => $port,
        CURLOPT_POST => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS => $post_body,
        CURLOPT_RETURNTRANSFER => true
    );
    if (!empty($headers)) {
        $curl_opts[CURLOPT_HTTPHEADER] = $headers;
    }
    $ret = curl($curl_opts, $debug);
    return $ret;
}
function curl_get($url, $port=80, $headers=array(), $debug=false)
{
    $ret = array();
    $curl_opts = array(
        CURLOPT_URL => $url,
        CURLOPT_PORT => $port, //sina sae can't use this options
        CURLOPT_POST => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_RETURNTRANSFER => true
    );
    if (!empty($headers)) {
        $curl_opts[CURLOPT_HTTPHEADER] = $headers;
    }
    $ret = curl($curl_opts, $debug);
    return $ret;
}
function url_replace_param($url, $params, $delparams=array())
{
    $pos = strpos($url, '?');
    if ($pos !== false) {
        $query_string = substr($url, $pos+1);
        $base_url = substr($url, 0, $pos);
        parse_str($query_string, $oldparams);
        $params = array_merge_recu($oldparams, $params);
        foreach ($params as $k=>$v)
            if (in_array($k, $delparams)) unset($params[$k]);
        return $base_url."?".http_build_query($params);
    }
    else {
        if (!empty($params)) {
            foreach ($params as $k=>$v)
                if (in_array($k, $delparams)) unset($params[$k]);
            return $url."?".http_build_query($params);
        }
        else
            return $url;
    }
}
function array_merge_recu($data, $merge)
{
    foreach ($merge as $k=>$v) {
        if (is_array($v) && isset($data[$k]) && is_array($data[$k])) {
            $data[$k] = array_merge_recu($data[$k], $v);
        }
        else {
            if ($v === null) { //客户端设置属性为null,删除该配置
                unset($data[$k]);
            }
            else {
                $data[$k] = $v;
            }
        }
    }
    return $data;
}
function db_exec($sql, $params=array(), $db=null)
{
    if (!($db instanceof CDbConnection)) {
        $db = Yii::app()->db;
    }
    return $db->getCommandBuilder()->createSqlCommand($sql, $params)->execute();
}
function db_get_all($sql, $params=array(), $db=null)
{
    if (!($db instanceof CDbConnection)) {
        $db = Yii::app()->db;
    }
    $ret = $db->getCommandBuilder()->createSqlCommand($sql, $params)->queryAll();
    return $ret;
}
function db_get_columns($sql, $params=array(), $db=null)
{
    if (!($db instanceof CDbConnection)) {
        $db = Yii::app()->db;
    }
    $ret = $db->getCommandBuilder()->createSqlCommand($sql, $params)->queryColumn();
    return $ret;
}
function db_get_one($sql, $params=array(), $db=null)
{
    if (!($db instanceof CDbConnection)) {
        $db = Yii::app()->db;
    }
    $ret = $db->getCommandBuilder()->createSqlCommand($sql, $params)->queryRow();
    return $ret;
}
function db_insert($table, $data, $db=null)
{
    if (!($db instanceof CDbConnection)) {
        $db = Yii::app()->db;
    }
    $ret = $db->getCommandBuilder()->createInsertCommand($table, $data)->execute();
    return $ret;
}
function db_update($table, $data, $where="1", $db=null)
{
    if (!($db instanceof CDbConnection)) {
        $db = Yii::app()->db;
    }
    $criteria=new CDbCriteria;
    $criteria->condition = $where;
    $ret = $db->getCommandBuilder()->createUpdateCommand($table, $data, $criteria)->execute();
    return $ret;
}

/**
 * 导航浏览内容,需要样式支持
 * @page       当前页
 * @pagecount  分页数
 * @datacount  总记录数
 * $params     参数
 *
 */
function get_page_nav_html($url, $page, $pagecount, $datacount, $params=array(
    'class'=>'page-nav', //导航层css class
    'label_prev' => '上一页',
    'label_next' => '下一页',
    'label_stat' => '共 %s 页  记录%d条 第 %s 页',
    'label_submit' => '确定',
    'navnum'=>5,//导航按钮数
    'page_arg'=>'page', //分页参数名
))
{
    $curlist = array();
    $pre = $next = false;
    $nopre = $nonext = true;
    $half = (int)($params['navnum'] / 2); //计算一半的数量
    if ($page <= $half+1) {
        for ($i=0;$i<$params['navnum'];$i++) {  //向后补直到补满navnum为止
            if ($i < $pagecount) $curlist[] = $i+1; //序号从1开始计算
        }
    }
    else if ($page > $pagecount - $half) { //向前补直到补满navnum,遇到第一页停止
        for ($i= $pagecount-1;$i>=$pagecount-$params['navnum'];$i--)
            if ($i >= 0) array_unshift($curlist, $i+1);
    }
    else {
        for ($i=$page-1-$half;$i<=$page-1+$half;$i++)
            $curlist[] = $i+1;
    }
    if ($page - $params['navnum'] < 1) $pre = 1;
    else $pre = $page - $params['navnum'];
    if ($page + $params['navnum'] > $pagecount) $next = $pagecount;
    else $next = $page + $params['navnum'];
    if ($curlist[0] > 1) $nopre = false;
    if ($curlist[count($curlist)-1] < $pagecount) $nonext = false;

    $args = array();
    if (($pos = strpos($url, '?')) !== false) {
        $baseuri = substr($url, 0, $pos);
        $qstr = substr($url, $pos+1);
        parse_str($qstr, $args);
    }
    else {
        $baseuri = $url;
    }
    $content = '<div class="clear"></div><div class="'.$params['class'].'">';
    if ($nopre) {
        $content .= '<a href="#" onclick="return false" class="btn-left btn-left-end">'.$params['label_prev'].'</a>';
    }
    else {
        $args[$params['page_arg']] = $pre;
        $newurl = $baseuri."?".http_build_query($args);
        $content .= '<a href="'.$newurl.'" class="btn-left">'.$params['label_prev'].'</a>';
    }
    foreach ($curlist as $idx) {
        $args['page'] = $idx;
        $newurl = $baseuri."?".http_build_query($args);
        if ($idx == $page)
            $content .= '<a href="'.$newurl.'" class="cur">'.$idx.'</a>';
        else
            $content .= '<a href="'.$newurl.'" >'.$idx.'</a>';
    }
    if ($nonext) {
        $content .= '<a href="#" onclick="return false" class="btn-right btn-right-end">'.$params['label_next'].'</a>';
    }
    else {
        $args['page'] = $next;
        $newurl = $baseuri."?".http_build_query($args);
        $content .= '<a href="'.$newurl.'" class="btn-right">'.$params['label_next'].'</a>';
    }
    unset($args['page']);
    $newurl = $baseuri."?".http_build_query($args);
    $content .= sprintf($params['label_stat'], $pagecount, $datacount,'<input type="text" name="'.$params['page_arg'].'" class="inp-page"> ');
    $content .= '<button onclick="location.href=\''.$newurl.'&page=\'+$(this).prev().val()">'.$params['label_submit'].'</button>';
    $content .= '</div>';
    return $content;
}

function sms_conn($host, $port, $username, $password)
{
    include_once dirname(__FILE__).'/extensions/sms/XMPP.php';
    return new XMPPHP_XMPP($host, $port, $username, $password, 'xmpphp');
}
function sms_system_send($touser,$msg)
{
    $username = 'system';
    $password = 'origin';
    sms_send($username, $password, $touser, $msg);
}
function sms_add_user($user, $pass)
{

}
function sms_add_friend($user, $friend)
{
}
function sms_send($mobile, $message)
{
    $OperID = "";
    $OperPass = "";
    $SendTime = "";
    $ValidTime = "";
    $AppendID = "";
    $DesMobile = $mobile;
    $ContentType = 15;
    $url = "http://221.179.180.158:9007/QxtSms/QxtFirewall";
    $Content = iconv("UTF-8", "GB2312//IGNORE", $message) ;
    $post_data = "OperID=".$OperID."&OperPass=".$OperPass."&SendTime=".$SendTime."&ValidTime=".$ValidTime."&AppendID=".$AppendID."&DesMobile=".$DesMobile."&Content=".$Content."&ContentType=".$ContentType;
    return sms_post($url, $post_data);
}
function sms_post($url, $postData)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_NOBODY, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);
    $return_str = curl_exec($curl);
    curl_close($curl);
    return $return_str;
}
function process_message($username, $password)
{
    $host = '192.168.2.50';
    $port = 5222;
    $domain = 'im-agrit';
    $conn = sms_conn($host, $port, $username, $password);
    try {
        $conn->useEncryption(false);
        $conn->connect();
        while(!$conn->isDisconnected()) {
            $payloads = $conn->processUntil(array('message', 'presence', 'end_stream', 'session_start', 'vcard'));
            foreach($payloads as $event) {
                $pl = $event[1];
                switch($event[0]) {
                    case 'message':
                        $subject = $pl['subject'];
                        $body = $pl['body'];
                        $from = $pl['from'];
                        echo $body;
                    break;
                    case 'presence':
                        print "Presence: {$pl['from']} [{$pl['show']}] {$pl['status']}\n";
                    break;
                    case 'session_start':
                        print "Session Start\n";
                        $conn->getRoster();
                        $conn->presence($status="Cheese!");
                    break;
                }
            }
        }
    } catch(XMPPHP_Exception $e) {
        die($e->getMessage());
    }
}


function pinyin($str, $charset='UTF8')
{
    $pin = new CPinyin;
    return $pin->Pinyin($str, $charset);
}
function get_url_param($str,$type=1)
{
    $find = strpos($str,'?');
    if ($find) {
        $str = substr($str,0,$find);
    }
    $pathArr = explode('/',$str);
    $fileName = explode('-',$pathArr['3']);
    if ($type == 1) {
        $urlParam = $fileName['0'];
    }
    else if ($type == 2) {
        $urlParam = $fileName['0'].'-'.$fileName['1'];
    }
    else if ($type == 3){
        $urlParam = $fileName['2'];
    }
    return $urlParam;
}


/**
 * 小型数据库操作类
 */
class DbMysql
{
    protected $db;
    public function __construct($host, $user, $pass, $dbname)
    {
        $this->db = mysql_connect($host, $user, $pass);
        if (!$this->db) {
            die('Could not connect: ' . mysql_error());
        }
        mysql_select_db($dbname, $this->db) or die ('Can\'t use '.$dbname.' : ' . mysql_error());
        mysql_query("SET NAMES 'utf8'", $this->db);
        mysql_query("SET CHARACTER SET utf8", $this->db);
        mysql_query("SET CHARACTER_SET_CONNECTION=utf8", $this->db);
        mysql_query("SET SQL_MODE = ''", $this->db);

    }
    public function changeDb($dbname)
    {
        if (!$this->db)
            die('Could not connect: ' . mysql_error());
        mysql_select_db($dbname, $this->db) or die ('Can\'t use '.$dbname.' : ' . mysql_error());
    }
    public function exec($sql)
    {
        return $this->query($sql);
    }
    public function query($sql)
    {
        $resource = mysql_query($sql, $this->db);
        $err = mysql_error();
        if ($err) {
            echo $err;
        }
        if ($resource) {
            if (is_resource($resource)) {
                $i = 0;
                $data = array();
                while ($result = mysql_fetch_assoc($resource)) {
                    $data[$i++] = $result;
                }
                mysql_free_result($resource);
                $query = new stdClass();
                $query->row = isset($data[0]) ? $data[0] : array();
                $query->rows = $data;
                $query->num_rows = $i;
                unset($data);
                return $query;
            }
            else {
                return $resource;
            }
        }
        return false;
    }
    public function fetchRow($sql)
    {
        $query = $this->query($sql);
        if (is_object($query)) {
            return $query->row;
        }
    }
    public function fetchAll($sql)
    {
        $query = $this->query($sql);
        if (is_object($query)) {
            return $query->rows;
        }
    }
    public function insert($table, $data)
    {
        $table = $this->getTableStr($table);
        foreach ($data as $k=>$v) {
            $data[$k] = $this->escape($v);
        }
        $sql = "INSERT INTO ".$table." (`".implode("`,`", array_keys($data))."`) VALUES ('".implode("','", array_values($data))."');";
        return $this->query($sql);
    }
    public function update($table, $data, $where)
    {
        $table = $this->getTableStr($table);
        $sql = "UPDATE ".$table." SET ";
        if (!empty($data)) {
            foreach ($data as $k=>$v) {
                $sql .= $k."='".$this->escape($v)."',";
            }
            $sql = rtrim($sql, ",");
            if (empty($where)) $where = '0';
            $sql .= " WHERE ".$where;
            return $this->query($sql);
        }
        return false;
    }
    public function delete($table, $where)
    {
        $table = $this->getTableStr($table);
        if (empty($where)) $where = '0';
        return $this->query("DELETE FROM ".$table." WHERE ".$where);
    }
    public function getTableStr($table)
    {
        $arr = explode('.', $table);
        foreach ($arr as $k=>$v) {
            $arr[$k] = "`".$v."`";
        }
        return implode(".", $arr);
    }
    public function escape($value)
    {
        return mysql_real_escape_string($value, $this->db);
    }
    public function __destruct()
    {
        if ($this->db) {
            //mysql_close($this->db);
            $this->db = null;
        }
    }
}

class CPinyin{
    function Pinyin($_String, $_Code='gb2312'){

        $_DataKey = "a|ai|an|ang|ao|ba|bai|ban|bang|bao|bei|ben|beng|bi|bian|biao|bie|bin|bing|bo|bu|ca|cai|can|cang|cao|ce|ceng|cha".
                    "|chai|chan|chang|chao|che|chen|cheng|chi|chong|chou|chu|chuai|chuan|chuang|chui|chun|chuo|ci|cong|cou|cu|".
                    "cuan|cui|cun|cuo|da|dai|dan|dang|dao|de|deng|di|dian|diao|die|ding|diu|dong|dou|du|duan|dui|dun|duo|e|en|er".
                    "|fa|fan|fang|fei|fen|feng|fo|fou|fu|ga|gai|gan|gang|gao|ge|gei|gen|geng|gong|gou|gu|gua|guai|guan|guang|gui".
                    "|gun|guo|ha|hai|han|hang|hao|he|hei|hen|heng|hong|hou|hu|hua|huai|huan|huang|hui|hun|huo|ji|jia|jian|jiang".
                    "|jiao|jie|jin|jing|jiong|jiu|ju|juan|jue|jun|ka|kai|kan|kang|kao|ke|ken|keng|kong|kou|ku|kua|kuai|kuan|kuang".
                    "|kui|kun|kuo|la|lai|lan|lang|lao|le|lei|leng|li|lia|lian|liang|liao|lie|lin|ling|liu|long|lou|lu|lv|luan|lue".
                    "|lun|luo|ma|mai|man|mang|mao|me|mei|men|meng|mi|mian|miao|mie|min|ming|miu|mo|mou|mu|na|nai|nan|nang|nao|ne".
                    "|nei|nen|neng|ni|nian|niang|niao|nie|nin|ning|niu|nong|nu|nv|nuan|nue|nuo|o|ou|pa|pai|pan|pang|pao|pei|pen".
                    "|peng|pi|pian|piao|pie|pin|ping|po|pu|qi|qia|qian|qiang|qiao|qie|qin|qing|qiong|qiu|qu|quan|que|qun|ran|rang".
                    "|rao|re|ren|reng|ri|rong|rou|ru|ruan|rui|run|ruo|sa|sai|san|sang|sao|se|sen|seng|sha|shai|shan|shang|shao|".
                    "she|shen|sheng|shi|shou|shu|shua|shuai|shuan|shuang|shui|shun|shuo|si|song|sou|su|suan|sui|sun|suo|ta|tai|".
                    "tan|tang|tao|te|teng|ti|tian|tiao|tie|ting|tong|tou|tu|tuan|tui|tun|tuo|wa|wai|wan|wang|wei|wen|weng|wo|wu".
                    "|xi|xia|xian|xiang|xiao|xie|xin|xing|xiong|xiu|xu|xuan|xue|xun|ya|yan|yang|yao|ye|yi|yin|ying|yo|yong|you".
                    "|yu|yuan|yue|yun|za|zai|zan|zang|zao|ze|zei|zen|zeng|zha|zhai|zhan|zhang|zhao|zhe|zhen|zheng|zhi|zhong|".
                    "zhou|zhu|zhua|zhuai|zhuan|zhuang|zhui|zhun|zhuo|zi|zong|zou|zu|zuan|zui|zun|zuo";

        $_DataValue = "-20319|-20317|-20304|-20295|-20292|-20283|-20265|-20257|-20242|-20230|-20051|-20036|-20032|-20026|-20002|-19990".
                    "|-19986|-19982|-19976|-19805|-19784|-19775|-19774|-19763|-19756|-19751|-19746|-19741|-19739|-19728|-19725".
                    "|-19715|-19540|-19531|-19525|-19515|-19500|-19484|-19479|-19467|-19289|-19288|-19281|-19275|-19270|-19263".
                    "|-19261|-19249|-19243|-19242|-19238|-19235|-19227|-19224|-19218|-19212|-19038|-19023|-19018|-19006|-19003".
                    "|-18996|-18977|-18961|-18952|-18783|-18774|-18773|-18763|-18756|-18741|-18735|-18731|-18722|-18710|-18697".
                    "|-18696|-18526|-18518|-18501|-18490|-18478|-18463|-18448|-18447|-18446|-18239|-18237|-18231|-18220|-18211".
                    "|-18201|-18184|-18183|-18181|-18012|-17997|-17988|-17970|-17964|-17961|-17950|-17947|-17931|-17928|-17922".
                    "|-17759|-17752|-17733|-17730|-17721|-17703|-17701|-17697|-17692|-17683|-17676|-17496|-17487|-17482|-17468".
                    "|-17454|-17433|-17427|-17417|-17202|-17185|-16983|-16970|-16942|-16915|-16733|-16708|-16706|-16689|-16664".
                    "|-16657|-16647|-16474|-16470|-16465|-16459|-16452|-16448|-16433|-16429|-16427|-16423|-16419|-16412|-16407".
                    "|-16403|-16401|-16393|-16220|-16216|-16212|-16205|-16202|-16187|-16180|-16171|-16169|-16158|-16155|-15959".
                    "|-15958|-15944|-15933|-15920|-15915|-15903|-15889|-15878|-15707|-15701|-15681|-15667|-15661|-15659|-15652".
                    "|-15640|-15631|-15625|-15454|-15448|-15436|-15435|-15419|-15416|-15408|-15394|-15385|-15377|-15375|-15369".
                    "|-15363|-15362|-15183|-15180|-15165|-15158|-15153|-15150|-15149|-15144|-15143|-15141|-15140|-15139|-15128".
                    "|-15121|-15119|-15117|-15110|-15109|-14941|-14937|-14933|-14930|-14929|-14928|-14926|-14922|-14921|-14914".
                    "|-14908|-14902|-14894|-14889|-14882|-14873|-14871|-14857|-14678|-14674|-14670|-14668|-14663|-14654|-14645".
                    "|-14630|-14594|-14429|-14407|-14399|-14384|-14379|-14368|-14355|-14353|-14345|-14170|-14159|-14151|-14149".
                    "|-14145|-14140|-14137|-14135|-14125|-14123|-14122|-14112|-14109|-14099|-14097|-14094|-14092|-14090|-14087".
                    "|-14083|-13917|-13914|-13910|-13907|-13906|-13905|-13896|-13894|-13878|-13870|-13859|-13847|-13831|-13658".
                    "|-13611|-13601|-13406|-13404|-13400|-13398|-13395|-13391|-13387|-13383|-13367|-13359|-13356|-13343|-13340".
                    "|-13329|-13326|-13318|-13147|-13138|-13120|-13107|-13096|-13095|-13091|-13076|-13068|-13063|-13060|-12888".
                    "|-12875|-12871|-12860|-12858|-12852|-12849|-12838|-12831|-12829|-12812|-12802|-12607|-12597|-12594|-12585".
                    "|-12556|-12359|-12346|-12320|-12300|-12120|-12099|-12089|-12074|-12067|-12058|-12039|-11867|-11861|-11847".
                    "|-11831|-11798|-11781|-11604|-11589|-11536|-11358|-11340|-11339|-11324|-11303|-11097|-11077|-11067|-11055".
                    "|-11052|-11045|-11041|-11038|-11024|-11020|-11019|-11018|-11014|-10838|-10832|-10815|-10800|-10790|-10780".
                    "|-10764|-10587|-10544|-10533|-10519|-10331|-10329|-10328|-10322|-10315|-10309|-10307|-10296|-10281|-10274".
                    "|-10270|-10262|-10260|-10256|-10254";

        $_TDataKey = explode('|', $_DataKey);
        $_TDataValue = explode('|', $_DataValue);
        $_Data = (PHP_VERSION>='5.0') ? array_combine($_TDataKey, $_TDataValue) : $this->Arr_Combine($_TDataKey, $_TDataValue);
        arsort($_Data);
        reset($_Data);
        if($_Code != 'gb2312') $_String = $this->U2_Utf8_Gb($_String);
        $_Res = '';
        for($i=0; $i<strlen($_String); $i++){
            $_P = ord(substr($_String, $i, 1));
            if($_P>160) { $_Q = ord(substr($_String, ++$i, 1)); $_P = $_P*256 + $_Q - 65536; }
            $_Res .= $this->Pinyins($_P, $_Data);
        }
        return $_Res;
        //return preg_replace("/[^a-z0-9]*/", '', $_Res);
    }

    function Pinyins($_Num, $_Data){
        if ($_Num>0 && $_Num<160 ) return chr($_Num);
            elseif($_Num<-20319 || $_Num>-10247) return '';
        else {
            foreach($_Data as $k=>$v){ if($v<=$_Num) break; }
            return $k;
        }
    }
    function U2_Utf8_Gb($_C){
        $_String = '';
        if($_C < 0x80){
            $_String .= $_C;
        }elseif($_C < 0x800){
            $_String .= chr(0xC0 | $_C>>6);
            $_String .= chr(0x80 | $_C & 0x3F);
        }elseif($_C < 0x10000){
            $_String .= chr(0xE0 | $_C>>12);
            $_String .= chr(0x80 | $_C>>6 & 0x3F);
            $_String .= chr(0x80 | $_C & 0x3F);
        }elseif($_C < 0x200000) {
            $_String .= chr(0xF0 | $_C>>18);
            $_String .= chr(0x80 | $_C>>12 & 0x3F);
            $_String .= chr(0x80 | $_C>>6 & 0x3F);
            $_String .= chr(0x80 | $_C & 0x3F);
        }
            return iconv('UTF-8', 'GB2312', $_String);
        }
    function Arr_Combine($_Arr1, $_Arr2){
        for($i=0; $i<count($_Arr1); $i++) $_Res[$_Arr1[$i]] = $_Arr2[$i];
        return $_Res;
    }
}
class DesPKCS5 {
    private static $_instance = NULL;
    /**
     * @return DesPKCS5
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new DesPKCS5();
        }
        return self::$_instance;
    }

    /**
     * 加密
     * @param string $str 要处理的字符串
     * @param string $key 加密Key，为8个字节长度
     * @return string
     */
    public function encode($str, $key) {
        $size = mcrypt_get_block_size(MCRYPT_DES, MCRYPT_MODE_CBC);
        $str = $this->pkcs5Pad($str, $size);
        $aaa = mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_CBC, $key);
        $ret = base64_encode($aaa);
        return $ret;
    }
    /**
     * 解密
     * @param string $str 要处理的字符串
     * @param string $key 解密Key，为8个字节长度
     * @return string
     */
    public function decode($str, $key) {
        $strBin = base64_decode($str);
        $str = mcrypt_decrypt(MCRYPT_DES, $key, $strBin, MCRYPT_MODE_CBC, $key);
        $str = $this->pkcs5Unpad($str);
        return $str;
    }

    function hex2bin($hexData) {
        $binData = "";
        for ($i = 0; $i < strlen($hexData); $i += 2) {
            $binData .= chr(hexdec(substr($hexData, $i, 2)));
        }
        return $binData;
    }

    function pkcs5Pad($text, $blocksize) {
        $pad = $blocksize - (strlen($text) % $blocksize);
        return $text . str_repeat(chr($pad), $pad);
    }

    function pkcs5Unpad($text) {
        $pad = ord($text {strlen($text) - 1});
        if ($pad > strlen($text))
            return false;

        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad)
            return false;

        return substr($text, 0, - 1 * $pad);
    }
}
//获得考试使用的平台
function formsPlat($param){
    $param = intval($param);
    $dict = new SysDict();
    $dictArr = $dict->getList('PLATFORM_TYPE');
    $str = array();
    foreach($dictArr as $key => $value){
        if ($key & $param) {
            switch ($key) {
                case $dict->getValue('WINDOWS','PLATFORM_TYPE')->value:
                    $str[] = '电脑（Windows）';
                    continue;
                case $dict->getValue('IPAD','PLATFORM_TYPE')->value:
                    $str[] = 'IPAD';
                    continue;
                case $dict->getValue('APAD','PLATFORM_TYPE')->value:
                    $str[] = 'APAD';
                    continue;
                case $dict->getValue('MPAD','PLATFORM_TYPE')->value:
                    $str[] = 'MPAD';
                    continue;
                case $dict->getValue('IPHONE','PLATFORM_TYPE')->value:
                    $str[] = 'Iphone手机';
                    continue;
                case $dict->getValue('ANDROID','PLATFORM_TYPE')->value:
                    $str[] = 'Android手机';
                    continue;
            }
        }
    }
    $platform = trim(implode(array_unique($str),'、'),'、');
    return $platform;
}