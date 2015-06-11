<?php
    #api:resumeTest

    $data = array();
    $this->checkDb();
    $dict = new SysDict;
    require 'inc-token.php';
    $behavior->Behavior    = $dict->getValue('CANDIDATE_BEHAVIOR:CONTINUE')->value;

    if ($oTest->CloseStatus == $dict->getValue('TEST_CLOSE_STATUS:YES')->value) {
        $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:CONTINUE_TEST_CLOSE')->value;
        $behavior->save();
        $this->error($dict->getValue('ERROR_CODE:E01006')->value);
    }

    $records = $this->getParam('records');

    $testRule = new MtsTestRule($oTest);

    $time = time();
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
    else if ($time > $endTime || $oTest->Status & 0x100) {
        $status = (int)$dict->getValue('TEST_START_CHECK:FINISH')->value;
    }
    $oCandidate->QuitTimes += count($records);
    $oCandidate->save();

    $data = array(
        'status'=>(int)$status,
        'diff'=>(int)$diff
    );
    $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:CONTINUE_OK')->value;
    $behavior->save();
    return $data;