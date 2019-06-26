<?php

Class WinitDeAmazonSalePlanViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'winit_de_amazon_sale_plan'=>array('id','sku','first_sale_date','last_modify_date','relisting_times','price_note','cost','sale_price','suggested_price','suggest','status','sale_status','upc','_type'=>'LEFT'),
    	'product'=>array('cname'=>'cname', '_on'=>'winit_de_amazon_sale_plan.sku=product.sku'),
    );
}


?>