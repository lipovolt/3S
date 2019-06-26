<?php

Class UsswSalePlan3ViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'ussw_sale_plan3'=>array('id','sku','first_sale_date','last_modify_date','relisting_times','price_note','cost','sale_price','suggested_price','suggest','status','sale_status','_type'=>'LEFT'),
    	'product'=>array('cname'=>'cname', '_on'=>'ussw_sale_plan3.sku=product.sku'),
    );


}


?>