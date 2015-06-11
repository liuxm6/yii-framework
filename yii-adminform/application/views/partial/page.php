<?php

if (isset($criteria, $count, $linkUrl)
    && $criteria instanceof CDbCriteria):
    $pages = new Pagination($count);
    $pages->basePageUrl = $linkUrl;
    if (isset($pageVar)) {
        $pages->pageVar = $pageVar;
    }
    if (isset($pageSize) && $pageSize>0) {
        $pages->pageSize = $pageSize;
    }
    if (isset($navNum) && $navNum>0) {
        $pages->navNum = $navNum;
    }
    $pages->applyLimit($criteria);
    $url = url_replace_param($linkUrl, array(), array($pages->pageVar));
    if (strstr($url, '?')) {
        if (substr($url,-1) != '&')
            $url .= '&'.$pages->pageVar.'=';
        else
            $url .= $pages->pageVar.'=';
    }
    else {
        $url .= '?'.$pages->pageVar.'=';
    }
    $list = $pages->getNavList();
    $currPage = $pages->getCurrentPage()+1;
    if ($pages->isShowPage()):
?>

                    <div class="row pagination-custom">
                        <div class="col-xs-6">
                            <div class="info">
                                <span>
                                    共<?php echo $pages->getPageCount();?>页
                                </span>
                                <span>
                                    <input type="text" class="form-control input-code">
                                    <input type="button" value="Go" class="btn btn-default btn-sm" onclick="location.href='<?php echo $url;?>'+$(this).prev().val()">
                                </span>
                            </div>
                        </div>
                        <div class="col-xs-6 text-right">
                            <ul class="pagination">
<?php if ($pages->getPreNavUrl()):?>
                                <li><a href="<?php echo $pages->getPreNavUrl();?>" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
<?php else:?>
                                <li class="disabled"><a href="javascript:void(0)" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
<?php endif;?>
<?php foreach ($list as $k=>$v):?>
<?php if ($k == $currPage): ?>
                                <li class="active"><a href="<?php echo $v;?>"><?php echo $k; ?> <span class="sr-only">(current)</span></a></li>
<?php else:?>
                                <li><a href="<?php echo $v;?>"><?php echo $k; ?></a></li>
<?php endif;?>
<?php endforeach;?>
<?php if ($pages->getNextNavUrl()):?>
                                <li><a href="<?php echo $pages->getNextNavUrl();?>" aria-label="Next"><span aria-hidden="true">»</span></a></li>
<?php else:?>
                                <li class="disabled"><a href="javascript:void(0)" aria-label="Next"><span aria-hidden="true">»</span></a></li>
<?php endif;?>
                            </ul>

                        </div>
                    </div>

<?php
    endif;
endif;
