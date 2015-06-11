<body style="background:#CCC;">
    <table cellpadding="0" cellspacing="0" style="background-color: #2882A9; border: 1px solid #CCC; padding: 0; margin: 0 auto; outline: 0; width: 550px;font:13px/1.231 arial,helvetica,clean,sans-serif;">
        <tr>
            <td style="padding:0;">
                <img src="<?php echo $url?>/img/mail-title.jpg" width="550" height="58" alt="<?php echo $title?>" />
            </td>
        </tr>
        <tr>
            <td style="margin:0;padding:0;">
                <table cellpadding="0" cellspacing="0" style="font-family: tahoma, verdana, arial, 'Microsoft YaHei', sans-serif, simsun; font-size: 14px; background-color: #fff; border: 0; padding: 0; outline: 0; width:100%;">
                    <tr>
                        <td style="color: #333; line-height: 2;  padding:0;font-family:inherit">
                            <p style="text-align:center;font-size:18px;font-weight:100;padding:10px 0 0;">
                            智测制卷申请反馈
                            </p>
                            <p style="line-height:1.9; margin:8px 0;padding-left:20px;">
                            亲爱的用户:<br />
                                您的智测制卷申请被智测管理员拒绝了。<br />
                                拒绝理由：【<?php echo $info;?>】<br />
                                如有任何问题，可联系客服热线，谢谢
                            </p>
                            <table style=" border: 0; padding: 0; margin: 40px auto 20px; outline: 0;font:inherit;width:100%;">
                                <tr>
                                    <td style="color:#AAA;text-align:right;padding-right:20px;">
                                        —— <?php echo $title?>团队
                                    </td>
                                </tr>
                            </table>
                            <table style="width:100%;font-size:12px;line-height:1.5em; margin:30px 0 10px;color:#999; text-align:center;border-top:1px solid #EEE;">
                                <tr>
                                    <td style="text-align:center;padding-top:15px;line-height:1.7;">
                                        注：此邮件为系统邮件，请勿直接回复。<br/>
                                        如您有任何问题，可拨打客服热线：021-61821999-6891<br/>
                                        Copyright &copy; 1999-2015 ATA Inc. All Rights Reserved.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>