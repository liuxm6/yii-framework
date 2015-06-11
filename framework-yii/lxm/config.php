<?php

return array(
    'defaultController'=>'index',
    'language'=>'zh_cn',
    'name'=>'Web Application',
    'preload'=>array('log'),
    'import'=>array(
        'mts.components.tables.*',
        'mts.components.*',
        'application.components.*',
        'application.dbmodels.*',
        'application.models.*',
    ),

    'modules'=>array(
        'gii'=>array(
            'class'=>'mts.gii.GiiMtsModule',
            'password'=>'111111',
            'ipFilters'=>array('127.0.0.1','::1'),
        ),
    ),
    'components'=>array(
        'user'=>array(
            'class'=>'WebUser',
            'allowAutoLogin'=>true,
            'loginUrl'=>array('login'),
            'stateKeyPrefix'=>md5(mts_get_domain()),
            'identityCookie'=>array(
                'domain'=>mts_get_domain()
            )
        ),
        'session'=>array(
            'cookieParams'=>array(
                'domain'=>mts_get_domain(),
                'lifetime'=>120
            ),
            'timeout'=>3600
        ),
        'statePersister'=>array(
            'class'=>'CStatePersister',
            'stateFile'=>sys_get_temp_dir().'/state.'.mts_get_domain().'.bin'
        ),
        'errorHandler'=>array(
            'errorAction'=>'error',
        ),
        'urlManager'=>array(
            'urlFormat'=>'path',
            'showScriptName'=>false,
            'rules'=>array(
                '<controller:\w+>/<id:\d+>'=>'<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
                '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
            ),
        ),
        'assetManager'=>array(
            'class'=>'FileAssetManager',
        ),
        'db'=>array(
            'connectionString' => 'mysql:host=localhost;dbname=mysql',
            'emulatePrepare' => true,
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ),
        'cache'=>array(
            'class'=>'CFileCache',
        ),
        'log'=>array(
            'class'=>'CLogRouter',
            'routes'=>array(
                array(
                    'class'=>'FileLogRoute',
                ),
            ),
        ),
    ),
    'params'=>array(
        'adminEmail'=>'webmaster@originseed.com.cn',
        'pageSize'=>20,
    ),
    'mailConfig'=>array(
        'host'=>'XX',
        'port'=>465,
        'secure'=>'ssl',
        'username'=>'XX',
        'password'=>'XXX',
        'from'=>'XX',
        'fromName'=>'XX'
    ),
    'ossConfig'=>array(
        'XX'=>array(
            'accessKey'=>'XX',
            'secureKey'=>'XX',
            'bucket'=>'XX',
            'host'=>'XX',
            'baseurl'=>'XX'
        ),
    ),
);