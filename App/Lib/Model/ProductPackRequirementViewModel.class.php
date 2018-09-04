<?php

Class ProductPackRequirementViewModel extends ViewModel{

	//定义关联关系
	public $viewFields = array(
	    'product_pack_requirement'=>array('id','product_id','warehouse','requirement'),
    	'product'=>array('sku'=>'sku','cname'=>'cname','_on'=>'product_pack_requirement.product_id=product.id'),
    );


}


?>