<?php
    #api:/session/signinTestCode

    $data = array();
    $dict = new SysDict;

    $deviceId         = $this->getParam('deviceID');
    $testNumber       = $this->getParam('testNumber');
    $reload           = (int)$this->getParam('reload', 0);
    $platformStr      = substr($deviceId,0,6);
    list($platform)   = sscanf($platformStr,'%x');
    $platform         = (int)$platform;

    $this->checkDb();
    $oTest = OlTest::model()->findByAttributes(array('TestNumber'=>$testNumber));
    if (!$oTest || $oTest->PublishStatus != $dict->getValue('TEST_PUBLISH_STATUS:S1')->value) {
        $this->error($dict->getValue('ERROR_CODE:E01002')->value);
    }
    $oMember = SysMember::model()->findByPk($oTest->MemberId);
    $forms = OlTestForm::model()->findAllByAttributes(
        array(
            'TestId'=>$oTest->id
        ),
        'Platform & '.$platform
    );

    $oForm = current($forms);
    $oSubject = OlTestSubject::model()->findByAttributes(array('TestId'=>$oTest->id, 'SubjectId'=>$oForm->SubjectId));
    $oDevice = OlDevice::model()->findByAttributes(array('DeviceId'=>$this->getParam('deviceID')));
    $testRule   = new MtsTestRule($oTest);

    if (!$oTest || $oTest->PublishStatus != $dict->getValue('TEST_PUBLISH_STATUS:S1')->value) {
        $this->error($dict->getValue('ERROR_CODE:E01002')->value);
    }
    if ($oTest->CloseStatus == $dict->getValue('TEST_CLOSE_STATUS:YES')->value) {
        $this->error($dict->getValue('ERROR_CODE:E01006')->value);
    }
    //考试试卷检测
    if (empty($forms)) {
        $this->error($dict->getValue('ERROR_CODE:E01004')->value); //当前考试机使用的平台下无试卷
    }

    if (!$oDevice) { //再次检查是否有设备信息，没有就读取数据库信息
        $oDevice = OlDevice::model()->findByAttributes(array('DeviceId'=>$this->getParam('deviceID')));
    }
    if (!$oDevice) { //还没有就生成
        if (!$dict) {
            $this->checkDb();
            $dict = new SysDict;
        }
        $oDevice = new OlDevice;
        $oDevice->DeviceId   = $this->getParam('deviceID');
        $oDevice->createTime = time();
        $oDevice->validate();
        try {
            $oDevice->save();
        }
        catch (CDbException $e) {
            $this->error($dict->getValue('ERROR_CODE:E00004')->value); //保存出错，sql错误
        }
    }
    if (!$oDevice) {
        $this->error(2);
    }

    //正式赋值
    $diff = $testRule->startTime - $testRule->earlyDuration - time();
    if ($diff < 0) $diff = 0;
    $serverURL = Yii::app()->getParams()->serverUrl;
    $actDuration = (int)$oForm->TestFormDuration;
    $currDuration = $testRule->endTime - time();

    if ($currDuration > 0) {
        if ($actDuration > $currDuration)
            $actDuration = $currDuration;
    }
    else {
        $actDuration = 0;
    }
    $data = array(
        'serverURL'  => $serverURL,
        'testName'          => $oTest->TestName,
        'sessionID'         => $oTest->TestGuid,
        'type'              => (int)$oTest->Type,
        'mode'              => (int)$oTest->Mode,
        'sponsor'           => $oMember->Name,
        'subjectName'       => $oSubject->SubjectName,
        'startTime'         => $testRule->startTime,
        'testTimeRender'    => $testRule->testTimeRender,
        'duration'          => $testRule->testDuration,
        'actDuration'       => $actDuration,
        'suspend'           => $testRule->suspend,
        'endTime'           => $testRule->endTime,
        'diff'              => (int)$diff,
    );
    return $data;