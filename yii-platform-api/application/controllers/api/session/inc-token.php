<?php

    $oTestClient = OlTestClient::model()->findByAttributes(array(
        'Token'=>$this->getParam('uuid'),
    ));
    if (!$oTestClient) {
        $this->error($dict->getValue('ERROR_CODE:E01005')->value);
    }
    $oCandidate = OlTestCandidate::model()->findByPk($oTestClient->CandidateId);
    $oTest = OlTest::model()->findByPk($oTestClient->TestId);
    if (!$oTest || $oTest->PublishStatus != $dict->getValue('TEST_PUBLISH_STATUS:S1')->value) {
        $this->error($dict->getValue('ERROR_CODE:E01002')->value);
    }
    $oMember = SysMember::model()->findByPk($oTest->MemberId);

    $behavior = new OlCandidateBehavior;
    $behavior->MemberId    = $oMember->id;
    $behavior->TestId      = $oTest->id;
    $behavior->CandidateId = $oCandidate->id;
    $behavior->DeviceId    = $oTestClient->DeviceId;
    $behavior->ClientIp    = $_SERVER['REMOTE_ADDR'];
    $behavior->Api         = $api;
    $behavior->ApiUrl      = 'http://'.$_SERVER['SERVER_NAME'].($_SERVER['SERVER_PORT']==80?'':':'.$_SERVER['SERVER_PORT']).'/api'.$api.'?'.http_build_query($_REQUEST);
    $behavior->BehaviorTime = time();

    $oDevice = OlDevice::model()->findByAttributes(array('DeviceId'=>$oTestClient->DeviceId));
    if ($oDevice) {
        $oDevice->appVersion = $this->getParam('appVersion');
        $oDevice->osVersion  = $this->getParam('osVersion');
        $oDevice->sdkVersion = $this->getParam('sdkVersion');
        $oDevice->modelName  = $this->getParam('modelName');
        $oDevice->resolution = $this->getParam('resolution');
        $oDevice->location   = $this->getParam('location');
        $oDevice->other      = $this->getParam('other');
        $oDevice->save();
    }

    $form = OlTestForm::model()->findByAttributes(array('TestId'=>$oTest->id));
    $platformStr      = substr($oDevice->deviceId,0,6);
    list($platform)   = sscanf($platformStr,'%x');
    $platform         = (int)$platform;

