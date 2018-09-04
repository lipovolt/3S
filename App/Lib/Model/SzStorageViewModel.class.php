<?php

Class SzStorageViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'szstorage'=>array('id','position','sku','cinventory','ainventory','csales','remark','_type'=>'LEFT'),
    	'product'=>array('cname'=>'cname','ename'=>'ename', 'weight'=>'weight', 'price'=>'price', '_on'=>'szstorage.sku=product.sku'),
    );


}


?>