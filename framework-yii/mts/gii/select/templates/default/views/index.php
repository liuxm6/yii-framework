<<?php echo '?';?>php

    $data = array(
        'name'=>'abc',
        'value'=>'1',
        'htmlOptions'=>array()
    );
    $this->renderPartial('application.modules.<?php echo $this->module;?>.views.<?php echo $this->controller; ?>.select', $data);

    $data = array(
        'name'=>'abcbd',
        'value'=>'1',
        'htmlOptions'=>array()
    );
    $this->renderPartial('application.modules.<?php echo $this->module;?>.views.<?php echo $this->controller; ?>.select', $data);
