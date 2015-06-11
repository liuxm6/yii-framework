<?php
    #api:/client/version

    $data = array();
    $platform = $this->getParam('platform');
    $lang = $this->getParam('lang');
    $reload = (int)$this->getParam('reload', false);

    $cm = new ApiCacheManager(Yii::app()->cache);
    $params = array(
        'Platform'=>$platform,
        'Lang'=>$lang,
    );

    $cacheKey = $cm->getCacheKey($params);
    if ($cacheData = $cm->get($cacheKey, $reload)) {
        $data = $cacheData;
        return $data;
    }
    $this->checkDb();
    $dict = new SysDict;
    $oClientUpdate = SysClientUpdate::model();

    $row = $oClientUpdate->findByAttributes($params);
    if (!$row) {
        $this->error($dict->getValue('ERROR_CODE:E01003')->value);
    }
    else {
        $data = array(
            'isForce'                   => (int)$row->isForce,
            'version'                   => $row->version,
            'releaseDate'               => (int)$row->releaseDate,
            'downloadUri'               => $row->downloadUrl,
            'downloadAlternateUri'      => $row->downloadUrl,
            'sha1'                      => $row->sha1,
            'size'                      => (int)$row->size
        );
    }
    $cm->set($cacheKey, $data);
    return $data;