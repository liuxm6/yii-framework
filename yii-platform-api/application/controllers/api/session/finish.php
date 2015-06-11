<?php
    #api:getOSSToken

    $data = array();
    $this->checkDb();
    $dict = new SysDict;
    require 'inc-token.php';
    $behavior->Behavior    = $dict->getValue('CANDIDATE_BEHAVIOR:FINISH')->value;

    if ($oTest->Type == (int)$dict->getValue('TEST_TYPE:DEMO')->value) {
        if ($oTestClient->Status == (int)$dict->getValue('TEST_CLIENT_STATUS:CONFIRM')) {
            try {
                $oTestClient->Status = (int)$dict->getValue('TEST_CLIENT_STATUS:END')->value;
                $oTestClient->save();
                $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:FINISH_OK')->value;
                $behavior->save();
            }
            catch (CDbException $e) {
                $behavior->BehaviorResult = $dict->getValue('CANDIDATE_BEHAVIOR_MESSAGE:FINISH_SERVER_ERROR')->value;
                $behavior->save();
                $this->error($dict->getValue('ERROR_CODE:E00004')->value);
            }
        }
    }
    else {
        $this->error($dict->getValue('ERROR_CODE:E01007')->value);
    }

    return $data;
