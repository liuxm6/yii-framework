<!--空白页结构 begin-->
<div class="holder-wrapper">
    <img src="<?php echo $this->mts->rootUrl;?>img/holder-empty.jpg" alt="空白条目">
    <h4>
        <?php
        if ($title) {
            echo $title;
        }
        else {
            echo "暂无相关数据";
        }
        ?>
    </h4>
    <p>
        <?php
        if ($content) {
            echo $content;
        }
        else {
            echo "您可以通过前台添加数据";
        }
        ?>
    </p>
</div>
<!--空白页结构 end-->