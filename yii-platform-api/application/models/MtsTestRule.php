<?php
/*
 * 考场规则
 *
 */

class MtsTestRule extends CComponent
{
    /**
     * 考试名称
     * @var string
     */
    public $testName;

    /**
     * 考试类型 @see Dict(TEST_TYPE)
     * @var int
     */
    public $testType = 1;

    public $suspend = 1;

    /**
     * 提早加入考试确认时间，默认4小时
     * @var int
     */
    public $earlyDuration = 14400;

    /**
     * 考试考试时间
     * @var int
     */
    public $startTime;

    /**
     * 考试考试结束
     * @var int
     */
    public $endTime;

    /**
     * 允许迟到时间，默认半小时
     * @var int
     */
    public $startLimit = 1800;

    /**
     * 开考时间呈现方式 @see Dict(TEST_START_RENDER)
     * @var int
     */
    public $startRender = 1;

    /**
     * 考试机考试时间呈现方式 @see Dict(TEST_TIME_RENDER)
     * @var int
     */
    public $testTimeRender = 11;

    /**
     * 考试模式 @see Dict(TEST_MODE)
     * @var int
     */
    public $testMode = 1;

    /**
     * 最早交卷时间，默认开考30分钟内不能交卷
     * @var int
     */
    public $minTime = 0;

    /**
     * 提早交卷显示文字
     * @var string
     */
    public $msgMinTime = '考试 %d 分钟内不允许提早交卷';

    /**
     * 考试时长
     * @var int
     */
    public $testDuration = 7200;

    /**
     * 考试时区
     * @var int
     */
    public $testTimezone = 8;

    public $skinProtocol = 6;

    public $skinUrl;
    public $skinSha1;
    public $skinBucketName;
    public $skinUsername;
    public $skinPassword;
    public $skinDownloadPath;

    public $shuffle = 0;

    public $lateTime = 1200;
    public $lateAllow = 1; //是否允许迟到
    public $lateDeduction = 1; //迟到是否扣时

    public $showScore = 0;

    /**
     * 考试模式 @see Dict(PLATFORM_TYPE)
     * @var int
     */
    public $formProtocol = 6;
    /**
     * 资源包缓存时间
     * @var int
     */
    public $skinZipExpire = 0;
    /**
     * 试卷包缓存时间
     * @var int
     */
    public $formZipExpire = 15552000; //180 * 24 * 3600 180天
    /**
     * 考试数据包缓存时间
     * @var int
     */
    public $dataZipExpire = 864000; //10 * 24 * 3600 10天

    /**
     * 考试机屏幕最小宽度
     * @var int
     */
    public $minWidth = 600;

    /**
     * 考试机屏幕最小高度
     * @var int
     */
    public $minHeight = 600;

    /**
     * 考试模式 @see Dict(TEST_RULE_STRETCH)
     * @var int
     */
    public $stretch = 'fill';

    /**
     * 考试模式 @see Dict(TEST_RULE_KEYBOARD)
     * @var int
     */
    public $keyboard = 1;


    public function __construct(OlTest $test)
    {
        $json = $test->RuleJSON;
        $oJson = json_decode($json);
        if ($test->id) {
            $this->setAttribute('testName',             $test->TestName,true);
            $this->setAttribute('testType',             $test->Type,true);
            $this->setAttribute('testMode',             $test->Mode,true);
            $this->setAttribute('startTime',            $test->StartTime,true);
            $this->setAttribute('endTime',              $test->EndTime,true);
            $this->setAttribute('startLimit',           $test->StartLimit,true);
            $this->setAttribute('testDuration',         $test->TestDuration,true);
            $this->setAttribute('testTimezone',         $test->TimezoneOffset,true);
            $this->setAttribute('startRender',          $test->StartRender,true);
            $this->setAttribute('testTimeRender',       $test->TestTimeRender,true);
        }

        $this->setAttribute('skinUrl',              $oJson->skinUrl);
        $this->setAttribute('skinSha1',             $oJson->skinSha1);
        $this->setAttribute('skinProtocol',         $oJson->skinProtocol);
        $this->setAttribute('formProtocol',         $oJson->formProtocol);
        $this->setAttribute('skinUsername',         $oJson->skinUsername);
        $this->setAttribute('skinPassword',         $oJson->skinPassword);
        $this->setAttribute('skinBucketName',       $oJson->skinBucketName);
        $this->setAttribute('skinDownloadPath',     $oJson->skinDownloadPath);
        $this->setAttribute('skinZipExpire',        $oJson->skinZipExpire);
        $this->setAttribute('formZipExpire',        $oJson->formZipExpire);
        $this->setAttribute('dataZipExpire',        $oJson->dataZipExpire);
        $this->setAttribute('earlyDuration',        $oJson->earlyDuration);
        $this->setAttribute('minWidth',             $oJson->minWidth);
        $this->setAttribute('minHeight',            $oJson->minHeight);
        $this->setAttribute('stretch',              $oJson->stretch);
        $this->setAttribute('keyboard',             $oJson->keyboard);
        $this->setAttribute('minTime',              $oJson->minTime);
        $this->setAttribute('msgMinTime',           $oJson->msgMinTime);
        $this->setAttribute('shuffle',              $oJson->shuffle);
        $this->setAttribute('lateTime',             $oJson->lateTime);
        $this->setAttribute('showScore',            $oJson->showScore);
        $this->setAttribute('suspend',              $oJson->suspend);

    }
    public function setAttribute($key, $value, $write=false)
    {
        if ($value || $write)
            $this->$key = $value;
    }
}