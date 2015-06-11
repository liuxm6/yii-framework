<?php
    #api:/session/confirmTestNumber

    $data = array();
    $dict = new SysDict;

    $deviceId         = $this->getParam('deviceID');
    $testNumber       = $this->getParam('testNumber');
    $reload           = (int)$this->getParam('reload', 0);
    $confirm          = (int)$this->getParam('confirm', 0);
    $platformStr      = substr($deviceId,0,6);
    list($platform)   = sscanf($platformStr,'%x');
    $platform         = (int)$platform;

    if ( $this->getParam('confirm') == 0) {
        return $data;
    }
    $this->checkDb();
    $oTest = OlTest::model()->findByAttributes(array('TestNumber'=>$testNumber));
    if (!$oTest || $oTest->PublishStatus != $dict->getValue('TEST_PUBLISH_STATUS:S1')->value) {
        $this->error($dict->getValue('ERROR_CODE:E01002')->value);
    }
    if ($oTest->CloseStatus == $dict->getValue('TEST_CLOSE_STATUS:YES')->value) {
        $this->error($dict->getValue('ERROR_CODE:E01006')->value);
    }
    $oMember = SysMember::model()->findByPk($oTest->MemberId);
    $forms = OlTestForm::model()->findAllByAttributes(
        array(
            'TestId'=>$oTest->id
        ),
        'Platform & '.$platform
    );
    //考试试卷检测
    if (empty($forms)) {
        $this->error($dict->getValue('ERROR_CODE:E01003')->value); //当前考试机使用的平台下无试卷
    }
    $oForm = current($forms);
    $oSubject = OlTestSubject::model()->findByAttributes(array('TestId'=>$oTest->id, 'SubjectId'=>$oForm->SubjectId));
    $oDevice = OlDevice::model()->findByAttributes(array('DeviceId'=>$this->getParam('deviceID')));
    if (!$oDevice) {//此处需要在登录考试时候正确记录考试机的信息
        $this->error($dict->getValue('ERROR_CODE:E01003')->value);
    }
    $testRule = new MtsTestRule($oTest);

    if ($oTest->CloseStatus == $dict->getValue('TEST_CLOSE_STATUS:YES')->value) {
        $this->error($dict->getValue('ERROR_CODE:E01006')->value);
    }
    //考试已结束
    if ($oTest->Status & 0x100) {
        $this->error($dict->getValue('ERROR_CODE:E08002')->value);
    }
    $data['mtsClientConfig'] = array(
        'stretch'    => $testRule->stretch,
        'minWidth'   => (int)$testRule->minWidth,
        'minHeight'  => (int)$testRule->minHeight,
        'keyboard'   => (int)$testRule->keyboard,
    );

    if ($testRule->skinUrl) {
        $data['skin']['source']['protocol'] = (int)$testRule->skinProtocol;
        $data['skin']['source']['uri']      = $testRule->skinUrl;
        $data['skin']['source']['username'] = (string)$testRule->skinUsername;
        $data['skin']['source']['password'] = (string)$testRule->skinPassword;
        $data['skin']['source']['bucketName']         = DesPKCS5::instance()->encode($testRule->skinBucketName,Yii::app()->getParams()->deskey);
        $data['skin']['source']['downloadPath']       = $testRule->skinDownloadPath;
        $data['skin']['sha1']               = $testRule->skinSha1;
        $data['skin']['expired']            = (int)$testRule->skinZipExpire+time();
    }

    $formProtocol =  (int)$testRule->formProtocol;
    $expired = (int)$testRule->formZipExpire + time();
    $username = '';
    $password = '';
    $ossConfig = Yii::app()->ossConfig['form'];
    $formData = $oForm->getTestForm($platform);
    $formUri = $formProtocol == $dict->getValue('PROTOCOL:OSS')->value?$ossConfig['host']:$formData['download'];
    foreach ($forms as $form) {
        $data['formlist'][] = array(
            'id'=>$form->TestFormGuid,
            'source' => array(
                'protocol' => $formProtocol,
                'uri'      => $formUri,
                'username' => $username,
                'password' => $password,
                'bucketName'=>DesPKCS5::instance()->encode($ossConfig['bucket'],Yii::app()->getParams()->deskey),
                'downloadPath'=>$formData['object']
            ),
            'sha1'  => $formData['sha1'],
            'expired'=>$expired,
        );
    }
    return $data;