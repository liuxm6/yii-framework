<?php
    #api:confirmCandidate
    #未优化

    $data = array();

    $this->checkDb();
    $dict = new SysDict;

    require 'inc-token.php';
    $behavior->Behavior    = $dict->getValue('CANDIDATE_BEHAVIOR:CONFIRM_LOGIN')->value;

    if ($oTest->CloseStatus == $dict->getValue('TEST_CLOSE_STATUS:YES')->value) {
        $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:CONFIRM_LOGIN_TEST_CLOSE')->value;
        $behavior->save();
        $this->error($dict->getValue('ERROR_CODE:E01006')->value);
    }

    if ( $this->getParam('confirm') == 0) {
        try {
            $oTestClient->Status = (int)$dict->getValue('TEST_CLIENT_STATUS:CANCEL')->value;
            $oTestClient->save();
            $oCandidate->TestStatus = 0;//
            $oCandidate->save();
            $behavior->Behavior    = $dict->getValue('CANDIDATE_BEHAVIOR:CANCEL_LOGIN')->value;
            $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:CANCEL_LOGIN_OK')->value;
            $behavior->save();
        }
        catch (CDbException $e) {
            $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:CONFIRM_LOGI_SERVER_ERROR')->value;
            $behavior->save();
            $this->error($dict->getValue('ERROR_CODE:E00004')->value); //保存出错，sql错误
        }
        return $data;
    }

    //考试已结束
    if ($oTest->Status & 0x100) {
        $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:CONFIRM_LOGIN_TEST_END')->value;
        $behavior->save();
        $this->error($dict->getValue('ERROR_CODE:E08002')->value);
    }
    $testRule = new MtsTestRule($oTest);

    $serverURL     = Yii::app()->getParams()->serverUrl;//$_SERVER['SERVER_NAME'];
    $shuffle       = isset($testRule->shuffle)? $testRule->shuffle :0;
    $msgMinTime    = isset($testRule->msgMinTime)? $testRule->msgMinTime :'';
    $minTime       = isset($testRule->minTime)? $testRule->minTime :0;
    $userLateTime = 0; //用户迟到扣时时间
    $time = time();

    //$this->error($dict->getValue('ERROR_CODE:E08004')->value);
    if ($oTest->Type == 1) {
        if ($oTest->StartLimit == 0) {
            $checkTime     = $oTest->StartTime;
            $lateTime      = $testRule->lateTime;
            $lateAllow     = $testRule->lateAllow;
            $lateDeduction = $testRule->lateDeduction;
            if ($lateAllow) {
                $checkTime += $lateTime;
            }
            if ($time > $checkTime) {
                $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:LOGIN_DELAY')->value;
                $behavior->save();
                $this->error($dict->getValue('ERROR_CODE:E08011')->value);
            }
            if ($lateDeduction) {
                $userLateTime = $time - $oTest->StartTime;
                if ($userLateTime < 0) $userLateTime = 0;
            }
        }
        else {
            if ($time > ($oTest->StartTime + $oTest->StartLimit)) {
                $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:LOGIN_DELAY')->value;
                $behavior->save();
                $this->error($dict->getValue('ERROR_CODE:E08011')->value);
            }
        }
    }


    $startTime = $testRule->startTime;
    $limitTime = $testRule->startLimit;
    $endTime   = $testRule->endTime;
    $showScore = $testRule->showScore;
    $diff = 0;
    $status = (int)$dict->getValue('TEST_START_CHECK:ALLOW')->value;
    if ($time < $startTime) {
        $status = (int)$dict->getValue('TEST_START_CHECK:BEFORE')->value;
        $diff = $startTime - $time;
    }
    $oCandidate->IsJoinTest = 1;
    $oCandidate->JoinTime = time();
    $oCandidate->TestStatus = (int)$dict->getValue('CANDIDATE_STATUS:CONFIRM')->value;
    $oCandidate->save();
    $oTestClient->Status = (int)$dict->getValue('TEST_CLIENT_STATUS:CONFIRM')->value;
    $oTestClient->save();

    $formData = $form->getTestForm($platform);
    $data = array(
        'serverURL'  => $serverURL,
        'shuffle'    => $shuffle,
        'msgMinTime' => $minTime>0?sprintf($msgMinTime, ceil($minTime/60)):'',
        'showScore'  => $showScore,
        'minTime'    => $minTime,
        'lateTime'   => $userLateTime,
        'form'       => array(
            'id'        => $form->TestFormGuid,
            'fileName'  => $formData['file'],
            'password'  => $formData['password'],
        ),
        'session'    => array(
            'status'    => $status,
            'diff'      => $diff,
        ),
    );
    $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:CONFIRM_LOGIN_OK')->value;
    $behavior->save();
    return $data;