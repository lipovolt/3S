<?php

Class UsstorageViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'usstorage'=>array('id','position','sku','attribute','cinventory','ainventory','oinventory','iinventory','csales','remark','sale_status'),
    	'product'=>array('cname'=>'cname', '_on'=>'usstorage.sku=product.sku'),
    );


}


?>