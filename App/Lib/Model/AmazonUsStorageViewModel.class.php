<?php

Class AmazonUsStorageViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'amazon_us_storage'=>array('id','sku','cinventory','ainventory','oinventory','iinventory','csales','_type'=>'LEFT'),
    	'product'=>array('cname'=>'cname', '_on'=>'amazon_us_storage.sku=product.sku'),
    );


}


?>