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
}

?>