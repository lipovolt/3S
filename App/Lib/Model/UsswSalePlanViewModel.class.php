<?php

Class UsswSalePlanViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'ussw_sale_plan'=>array('id','sku','first_sale_date','last_modify_date','relisting_times','price_note','cost','sale_price','suggested_price','suggest','status'),
    	'product'=>array('cname'=>'cname', '_on'=>'ussw_sale_plan.sku=product.sku'),
    );


}


?>