<?php

/*
CREATE TABLE IF NOT EXISTS `3s_usstorage` (
  `position` varchar(10) primary key NOT NULL,
  `sku` varchar(10) NOT NULL,
  `cname` varchar(50) DEFAULT NULL,
  `ename` varchar(50) DEFAULT NULL,
  `attribute` varchar(50) DEFAULT NULL,
  `cinventory` smallint(6) DEFAULT 0,
  `ainventory` smallint(6) DEFAULT 0,
  `oinventory` smallint(6) DEFAULT 0,
  `iinventory` smallint(6) DEFAULT 0,
  `csales` smallint(6) DEFAULT 0,
  `remark` varchar(255) DEFAULT NULL
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
*/

class StorageAction extends CommonAction{

	public function usstorage(){
		if($_POST['keyword']==""){
            $Data = M('usstorage');
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $usstorage = $Data->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('usstorage',$usstorage);
            $this->assign('page',$show);
        }
        else{
            $this->usstorage = M('usstorage')->where(array($_POST['keyword']=>$_POST['keywordValue']))->select();
        }
        $this->display();
	}

  /**
      * 系统邮件发送函数
      * @param string $to    接收邮件者邮箱
      * @param string $name  接收邮件者名称
      * @param string $subject 邮件主题 
      * @param string $body    邮件内容
      * @param string $attachment 附件列表
      * @return boolean 
      */
    private function sendInventoryMail($to, $name, $subject = '', $body = '', $attachment = null){

         $config = C('3S_EMAIL');
         vendor('PHPMailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
         $mail             = new PHPMailer(); //PHPMailer对象
         $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
         $mail->IsSMTP();  // 设定使用SMTP服务
         $mail->SMTPDebug  = true;                     // 关闭SMTP调试功能
                                                    // 1 = errors and messages
                                                    // 2 = messages only
         $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
         //$mail->SMTPSecure = 'ssl';                 // 使用安全协议
         $mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
         //$mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
         $mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
         $mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
         $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
         $replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
         $replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
         $mail->AddReplyTo($replyEmail, $replyName);
         $mail->Subject    = $subject;
         $mail->MsgHTML($body);
         $mail->AddAddress($to, $name);
         if(is_array($attachment)){ // 添加附件
             foreach ($attachment as $file){
                 is_file($file) && $mail->AddAttachment($file);
             }
         }
         return $mail->Send() ? true : $mail->ErrorInfo;
    }

    private function inventoryWarning(){
        $data = M(C('DB_USSTORAGE'))->select();
        $warning = null;
        $indexOfWarning = 0;
        $product = M(C('DB_PRODUCT'));
        $user = M(C('DB_USER'));
        foreach ($data as $key => $value) {
            if($value[C('DB_USSTORAGE_AINVENTORY')]<=1){
                $manager = $product->where(array(C('DB_PRODUCT_SKU')=>$value[C('DB_USSTORAGE_SKU')]))->getField(C('DB_PRODUCT_MANAGER'));
                $warning[$indexOfWarning][C('DB_PRODUCT_MANAGER')] = $manager;
                $warning[$indexOfWarning][C('DB_PRODUCT_SKU')] = $value[C('DB_USSTORAGE_SKU')];
                $indexOfWarning = $indexOfWarning+1;
            }
        }

        /*if($warning != null){
            foreach ($warning as $key => $w) {
                $email = $user->where(array(C('DB_3S_USER_USERNAME')=>$key))->getField(C('DB_3s_USER_EMAIL'));
                if($email != null){
                    $body = $w."美国自建仓库存数量不足，请求改在线刊登的ebay,amazon数量";
                    $this->sendInventoryMail($email,$manager,'缺货提醒',$body);
                }
            }
            
        }*/
        return $warning;
    }

    public function checkAinventory(){
      $usstorage=$this->inventoryWarning();
      $this->assign('usstorage',$usstorage);
      $this->display();
    }
}

?>