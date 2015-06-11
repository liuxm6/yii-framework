<?php
    #api:getPostAnswerStatus

    $data = array();
    $this->checkDb();
    $dict = new SysDict;
    require 'inc-token.php';
    $behavior->Behavior    = $dict->getValue('CANDIDATE_BEHAVIOR:FINISH')->value;

    if ($oTestClient->Status == (int)$dict->getValue('TEST_CLIENT_STATUS:CONFIRM')) {
        try {
            $oTestClient->Status = (int)$dict->getValue('TEST_CLIENT_STATUS:END')->value;
            $oTestClient->save();
        }
        catch (CDbException $e) {
            $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:FINISH_SERVER_ERROR')->value;
            $behavior->save();
            $this->error($dict->getValue('ERROR_CODE:E00004')->value);
        }
    }
    $ossName = 'response';
    $ossConfig = Yii::app()->ossConfig[$ossName];
    if (!$ossConfig) {
        $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:FINISH_SERVER_ERROR_NO_OSS')->value;
        $behavior->save();
        $this->error($dict->getValue('ERROR_CODE:E00003')->value);
    }
    if ($oTest->CloseStatus == $dict->getValue('TEST_CLOSE_STATUS:YES')->value) {
        $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:FINISH_TEST_CLOSE')->value;
        $behavior->save();
        $this->error($dict->getValue('ERROR_CODE:E02501')->value);
    }
    if ($oCandidate->UploadStatus == $dict->getValue('DATA_UPLOADED_STATUS:YES')->value) {
        $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:FINISH_HAS_UPLOAD')->value;
        $behavior->save();
        $this->error($dict->getValue('ERROR_CODE:E02502')->value);
    }
    $oCandidate->ServerGuid = $ossName;
    $oCandidate->UploadType = $this->getParam('type');
    $oCandidate->TestStatus = 0x111;
    $oCandidate->UploadTime = time();
    if (!$oCandidate->DataPassword)
        $oCandidate->DataPassword = mts_password();
    if (!$oCandidate->ResponsePassword)
        $oCandidate->ResponsePassword = mts_password();

    try {
        $oCandidate->save();
    }
    catch (CDbException $e) {
        $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:FINISH_SERVER_ERROR')->value;
        $behavior->save();
        $this->error($dict->getValue('ERROR_CODE:E00004')->value); //保存出错，sql错误
    }

    $oQueue = OlQueue::model()->findByAttributes(array(
        'MemberId'=>$oTest->MemberId,
        'TestId'=>$oTest->id,
        'CandidateId'=>$oCandidate->id
    ));
    if (!$oQueue) {
        $oQueue = new OlQueue;
        $oQueue->attributes = array(
            'MemberId'=>$oTest->MemberId,
            'TestId'=>$oTest->id,
            'CandidateId'=>$oCandidate->id,
            'TestNumber'=>$oTest->TestNumber,
            'CandidateNumber'=>$oCandidate->CandidateNumber,
        );
        $oQueue->CreateTime = time();
        try {
            $oQueue->save();
        }
        catch (CDbException $e) {
            $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:FINISH_SERVER_ERROR')->value;
            $behavior->save();
            $this->error($dict->getValue('ERROR_CODE:E00004')->value); //保存出错，sql错误
        }
    }

    $testRule = new MtsTestRule($oTest);

    $data['mode']              = $dict->getValue('PROTOCOL:OSS')->value;
    $data['uploadURI']         = $ossConfig['host'];
    $data['bucketName']        = DesPKCS5::instance()->encode($ossConfig['bucket'], Yii::app()->getParams()->deskey);
    $data['isForceVerify']     = isset($testRule->isForceVerify)?$testRule->isForceVerify:0;
    $data['uploadPath']        = 'response/'.$oMember->MemberGuid.'-'.$oMember->id.'/'.$oTest->TestGuid.'-'.$oTest->id.'/'.$oCandidate->CandidateGuid.'-'.$oCandidate->id.'/';
    $data['showScore']         = isset($testRule->showScore)? $testRule->showScore:0;
    $data['dataPassword']      = DesPKCS5::instance()->encode($oCandidate->DataPassword, Yii::app()->getParams()->deskey);
    $data['responsePassword']  = DesPKCS5::instance()->encode($oCandidate->ResponsePassword, Yii::app()->getParams()->deskey);
    $data['expired']           = $testRule->dataZipExpire+time();

    $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:FINISH_DOING')->value;
    $behavior->save();
    return $data;
