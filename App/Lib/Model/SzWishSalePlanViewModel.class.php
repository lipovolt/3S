<?php

Class SzWishSalePlanViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'sz_wish_sale_plan'=>array('id','sku','first_sale_date','last_modify_date','relisting_times','price_note','cost','sale_price','suggested_price','suggest','status','sale_status','register','_type'=>'LEFT'),
    	'product'=>array('cname'=>'cname', '_on'=>'sz_wish_sale_plan.sku=product.sku'),
    );


}


?>