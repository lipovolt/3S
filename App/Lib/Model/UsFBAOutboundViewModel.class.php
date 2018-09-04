<?php

Class UsFBAOutboundViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'amazon_us_fba_outbound'=>array('id','create_time'),
    	'amazon_us_fba_outbound_item'=>array('sku'=>'sku','quantity'=>'quantity','_on'=>'amazon_us_fba_outbound.id=amazon_us_fba_outbound_item.outbound_id'),
    );


}


?>