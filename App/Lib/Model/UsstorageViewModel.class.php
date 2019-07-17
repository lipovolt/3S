<?php

Class UsstorageViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'usstorage'=>array('id','position','sku','attribute','cinventory','ainventory','oinventory','iinventory','csales','remark','sale_status','_type'=>'LEFT'),
    	'product'=>array('cname'=>'cname', 'weight'=>'weight', 'price'=>'price','tous'=>'tous', '_on'=>'usstorage.sku=product.sku'),
    );


}


?>