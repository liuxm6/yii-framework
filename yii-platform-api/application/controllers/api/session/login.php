<?php
    #api:/session/login

    $data = array();
    $dict = new SysDict;

    $deviceId         = $this->getParam('deviceID');
    $testNumber       = $this->getParam('testNumber');
    $candidateNumber  = strtoupper($this->getParam('candidateNumber'));
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
    $oCandidate = OlTestCandidate::model()->findByAttributes(array(
        'CandidateNumber' => $this->getParam('candidateNumber'),
        'TestId'          => $oTest->id,
    ));
    if (!$oCandidate) {//没有考生
        $this->error($dict->getValue('ERROR_CODE:E08001')->value);
    }

    //增加行为日志
    $behavior = new OlCandidateBehavior;
    $behavior->MemberId    = $oMember->id;
    $behavior->TestId      = $oTest->id;
    $behavior->CandidateId = $oCandidate->id;
    $behavior->DeviceId    = $deviceId;
    $behavior->ClientIp    = $_SERVER['REMOTE_ADDR'];
    $behavior->Api         = $api;
    $behavior->ApiUrl      = 'http://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']==80?'':':'.$_SERVER['SERVER_PORT']).'/api'.$api.'?'.http_build_query($_REQUEST);
    $behavior->Behavior    = $dict->getValue('CANDIDATE_BEHAVIOR:LOGIN')->value;
    $behavior->BehaviorTime = time();

    if ($oTest->CloseStatus == $dict->getValue('TEST_CLOSE_STATUS:YES')->value) {
        $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:TEST_CLOSE')->value;
        $behavior->save();
        $this->error($dict->getValue('ERROR_CODE:E01006')->value);
    }

    $forms = OlTestForm::model()->findAllByAttributes(
        array(
            'TestId'=>$oTest->id
        ),
        'Platform & '.$platform
    );
    //考试试卷检测
    if (empty($forms)) {
        $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:LOGIN_NO_TESTFORM')->value;
        $behavior->save();
        $this->error($dict->getValue('ERROR_CODE:E01003')->value); //当前考试机使用的平台下无试卷
    }
    $oForm = current($forms);
    $oSubject = OlTestSubject::model()->findByAttributes(array('TestId'=>$oTest->id, 'SubjectId'=>$oForm->SubjectId));
    $oDevice = OlDevice::model()->findByAttributes(array('DeviceId'=>$deviceId));
    if (!$oDevice) {//此处需要在登录考试时候正确记录考试机的信息
        $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:LOGIN_NO_DEVICE')->value;
        $behavior->save();
        $this->error($dict->getValue('ERROR_CODE:E01003')->value);
    }

    $testRule  = new MtsTestRule($oTest);

    //考试已结束
    if ($oTest->Status & 0x100) {
        $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:LOGIN_TEST_END')->value;
        $behavior->save();
        $this->error($dict->getValue('ERROR_CODE:E08002')->value);
    }
    $statusBegin = (int)$dict->getValue('TEST_CLIENT_STATUS:BEGIN')->value;
    $statusConfirm = (int)$dict->getValue('TEST_CLIENT_STATUS:CONFIRM')->value;
    $statusCancel = (int)$dict->getValue('TEST_CLIENT_STATUS:CANCEL')->value;
    $statusEnd = (int)$dict->getValue('TEST_CLIENT_STATUS:END')->value;
    $token = mts_guid();
    $tokenExpire = -1;

    //Type=1正式考，迟到，超过开始+开始有效期的总时间
    if ($oTest->Type == 1) {
        if ($oTest->StartLimit == 0) {
            $checkTime     = $oTest->StartTime;
            $lateTime      = $testRule->lateTime;
            $lateAllow     = $testRule->lateAllow;
            if ($lateAllow) {
                $checkTime += $lateTime;
            }
            if (time() > $checkTime) {
                $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:LOGIN_DELAY')->value;
                $behavior->save();
                $this->error($dict->getValue('ERROR_CODE:E08004')->value);
            }
        }
        else {
            if (time() > ($oTest->StartTime + $oTest->StartLimit)) {
                $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:LOGIN_DELAY')->value;
                $behavior->save();
                $this->error($dict->getValue('ERROR_CODE:E08004')->value);
            }
        }
    }

    //$condition = '(DeviceId='.$oDevice->DeviceId.' and (Status='.$statusBegin .' or Status='.$statusConfirm.'))';
    //$condition .= 'or (CandidateId='.$oCandidate->id.' (Status='.$statusBegin .' or Status='.$statusConfirm.'))';//

    $oTestClient = OlTestClient::model()->findByAttributes(array(
        'TestId'=>$oTest->id,
        'CandidateId'=>$oCandidate->id,
    ), 'Status='.$statusEnd);

    if ($oTestClient) {//已考试完成
        $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:LOGIN_HAS_FINISH')->value;
        $behavior->save();
        $this->error($dict->getValue('ERROR_CODE:E08002')->value); //考试已完成，提示考试已结束
    }

    $oTestClient = OlTestClient::model()->findByAttributes(array(
        'TestId'=>$oTest->id,
        'CandidateId'=>$oCandidate->id,
    ), 'Status='.$statusBegin .' or Status='.$statusConfirm);

    //设备已登录，并且设备号不是当前考生关联设备号
    if ($oTestClient && $oTestClient->DeviceId != $deviceId) {
        $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:LOGIN_OTHER_DEVICE')->value;
        $behavior->save();
        $this->error($dict->getValue('ERROR_CODE:E08003')->value);
    }

    $oTestClient = OlTestClient::model()->findByAttributes(array(
        'TestId'=>$oTest->id,
        'DeviceId'=>$deviceId,
    ), 'Status != '.$statusEnd);
    //设备已登录未确定，强制取消， 设备登录已确认，提示已登录

    if ($oTestClient) {
        if ($oTestClient->CandidateId != $oCandidate->id) {//非当前考生记录
            if ($oTestClient->Status == $statusConfirm) { //已登录
                $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:LOGIN_OTHER_USER_LOGIN')->value;
                $behavior->save();
                $this->error($dict->getValue('ERROR_CODE:E08005')->value);
            }
            else {//未确认，已取消
                if ($oTestClient->Status == $statusBegin) {//未确认，强制取消前一个用户登录
                    $behavior1 = new OlCandidateBehavior;
                    $behavior1->MemberId    = $oMember->id;
                    $behavior1->TestId      = $oTestClient->TestId;
                    $behavior1->CandidateId = $oTestClient->CandidateId;
                    $behavior1->DeviceId    = $deviceId;
                    $behavior1->ClientIp    = $_SERVER['REMOTE_ADDR'];
                    $behavior1->Api         = $api;
                    $behavior1->ApiUrl      = 'http://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']==80?'':':'.$_SERVER['SERVER_PORT']).'/api'.$api.'?'.http_build_query($_REQUEST);
                    $behavior1->Behavior    = $dict->getValue('CANDIDATE_BEHAVIOR:CANCEL_LOGIN')->value;
                    $behavior1->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:CANCEL_LOGIN_OK')->value;
                    $behavior1->BehaviorTime = time();
                    $behavior1->save();
                }
                $oTestClient->candidate->TestStatus = 0;
                $oTestClient->candidate->save();
                $oTestClient->Status = $statusBegin;
                $oTestClient->Token = $token;
                $oTestClient->CandidateId       = $oCandidate->id;
                $oTestClient->CandidateNumber   = $oCandidate->CandidateNumber;
                $oTestClient->save();
            }
        }
        else {//是当前考生
            /*
            if ($oTestClient->Status == $statusConfirm) {//已登录，非法再次请求登录
                $this->error(1);
            }*/
            $oTestClient->Status = $statusBegin;
            $oTestClient->Token = $token;
            $oTestClient->save();
        }
    }


    $oTestClient = OlTestClient::model()->findByAttributes(array(
        'TestId'=>$oTest->id,
        'CandidateId'=>$oCandidate->id,
        'DeviceId'=>$deviceId,
    ));

    if (!$oTestClient) {
        $oTestClient = new OlTestClient;
        $oTestClient->TestId            = $oTest->id;
        $oTestClient->CandidateId       = $oCandidate->id;
        $oTestClient->TestNumber        = $oTest->TestNumber;
        $oTestClient->CandidateNumber   = $oCandidate->CandidateNumber;
        $oTestClient->DeviceId          = $deviceId;
        $oTestClient->Token             = $token;
        $oTestClient->TokenExpire       = $tokenExpire;
        $oTestClient->UserAgent         = $this->getParam('useragent');;
        $oTestClient->Location          = $oDevice->Location;
        $oTestClient->Other             = $oDevice->Other;
        $oTestClient->Status            = (int)$dict->getValue('TEST_CLIENT_STATUS:BEGIN')->value;
        $oTestClient->validate();
        try {
            $oTestClient->save();
        }
        catch (CDbException $e) {
            $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:LOGIN_SERVER_ERROR')->value;
            $behavior->save();
            $this->error($dict->getValue('ERROR_CODE:E00004')->value); //保存出错，sql错误
        }
    }
    else {//取消或者begin状态都是设置begin
        try {
            $oTestClient->Status        = (int)$dict->getValue('TEST_CLIENT_STATUS:BEGIN')->value;
            /** 只更改状态，token保留
            $t = mts_guid_time($token);
            if (time()-$t > 900) {//超过15分钟，重新生成
                $token = mts_guid();
            }
            $oTestClient->Token         = $token; //更新token
            **/
            if (empty($oTestClient->Token)) {
                $oTestClient->Token = $token;
            }
            else {
                $token = $oTestClient->Token;
            }
            $oTestClient->save();
        }
        catch (CDbException $e) {
            $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:LOGIN_SERVER_ERROR')->value;
            $behavior->save();
            $this->error($dict->getValue('ERROR_CODE:E00004')->value); //保存出错，sql错误
        }
    }

    $oCandidate->TestStatus = 1;//
    $oCandidate->save();

    $data = array(
        'uuid'                      => $token,
        'candidateID'               => $oCandidate->CandidateGuid,
        'candidateNumber'           => $oCandidate->CandidateNumber,
        'firstName'                 => $oCandidate->FirstName,
        'lastName'                  => $oCandidate->LastName,
        'displayName'               => $oCandidate->DisplayName,
        'initials'                  => $oCandidate->Initials,
        'gender'                    => $oCandidate->Gender,
        'dob'                       => strtotime($oCandidate->Birthday),
        'identityType'              => $oCandidate->IdentityType,
        'identityNumber'            => $oCandidate->IdentityNumber,
        'seat'                      => $oCandidate->Seat,
        'subject'                   => $oSubject->SubjectName,
        'startTime'                 => $oTest->StartTime,
        'duration'                  => $oTest->TestDuration,
        'field1'                    => $oCandidate->Field1,
        'field2'                    => $oCandidate->Field2,
        'field3'                    => $oCandidate->Field3,
        'photo'                     =>array(
            'photoURI'                  => $oCandidate->PhotoURI,
            'sha1'                      => $oCandidate->PhotoSHA1,
        ),
    );
    $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:LOGIN_OK')->value;
    $behavior->save();
    return $data;