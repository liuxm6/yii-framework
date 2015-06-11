<?php
    #api:getAnswerStatus

    $data = array();
    $this->checkDb();
    $dict = new SysDict;
    require 'inc-token.php';
    $behavior->Behavior    = $dict->getValue('CANDIDATE_BEHAVIOR:FINISH')->value;

    $attrs = array(
        'MemberId'   => $oMember->id,
        'TestId'     => $oTest->id,
        'CandidateId'=> $oCandidate->id
    );
    $oQueue = OlQueue::model()->findByAttributes($attrs);

    if (!$oQueue) {
        $oQueue = new OlQueue;
        $oQueue->attributes = $attrs;
        $oQueue->CreateTime = time();
        try {
            $oCandidate->save();
        }
        catch (CDbException $e) {
            $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:FINISH_SERVER_ERROR')->value;
            $behavior->save();
            $this->error($dict->getValue('ERROR_CODE:E00004')->value); //保存出错，sql错误
        }
    }
    $oQueue->UploadStatus = 0x111;
    $oQueue->save();

    $answerStatus = $oQueue->UploadStatus & 0x1;
    $zipStatus    = ($oQueue->UploadStatus & 0x10) /0x10;
    $jsonStatus   = ($oQueue->UploadStatus & 0x100)/0x100;

    $data = array(
        'answerStatus'=>array(
            'uploaded' =>$answerStatus,
            'verified' =>$answerStatus,
        ),
        'zipStatus'=>array(
            'uploaded' =>$zipStatus,
            'verified' =>$zipStatus,

        ),
        'jsonStatus'=>array(
            'uploaded' =>$jsonStatus,
            'verified' =>$jsonStatus,
        ),
    );
    /*
    if ($answerStatus && $zipStatus && $jsonStatus) {
        $attrs = $behavior->attributes;
        unset($attrs['ClientIp'], $attrs['BehaviorTime']);
        $attrs['BehaviorResult'] = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:FINISH_OK')->value;
        $finishBehavior = OlCandidateBehavior::model()->findByAttributes($attrs);
        if (!$finishBehavior) {
            $finishBehavior = new OlCandidateBehavior;
            $finishBehavior->attributes = $attrs;
            $finishBehavior->ClientIp = $_SERVER['REMOTE_ADDR'];
            $finishBehavior->BehaviorTime = time();
            $finishBehavior->save();
        }
    }*/
    return $data;