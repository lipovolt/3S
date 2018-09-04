<?php

class KpiAction extends Action {
    
    public function index(){
        if($_SESSION['username']=='admin'){
            $names = M(C('DB_3S_USER'))->getField(C('DB_3S_USER_USERNAME'),true);
            $names['all'] = '全部';
            $this->assign('names',$names);
            $this->assign('kpi_statistic',M(C('DB_KPI_STATISTIC'))->order('month desc')->where(array(array(C('DB_KPI_STATISTIC_NAME')=>$_POST['name']),array(C('DB_KPI_STATISTIC_MONTH')=>$_POST['month'])))->select());
        }else{
            $this->assign('names',array($_SESSION['username']));
            $this->assign('kpi_statistic',M(C('DB_KPI_STATISTIC'))->order('month desc')->where(array(array(C('DB_KPI_STATISTIC_NAME')=>$_SESSION['username']),array(C('DB_KPI_STATISTIC_MONTH')=>$_POST['month'])))->select());
        }
        
        $this->assign('month',$_POST['month']);
        $this->assign('name',$_POST['name']);
        $this->display();
    }

    public function statistic(){
        if($_POST['name']=='all'){
            $exist = M(C('DB_KPI_STATISTIC'))->where(array(C('DB_KPI_STATISTIC_MONTH')=>$_POST['month']))->getField(C('DB_KPI_STATISTIC_NAME'),true);
            if($exist!==null){
                $this->error($_POST['month'].' 已经统计以下人员的绩效考核。 '.$exist);
            }

            $names = M(C('DB_3S_USER'))->getField(C('DB_3S_USER_USERNAME'),true);
            foreach ($names as $key => $name) {
                $this->calKpiStatistic($_POST['month'],$name,false);
            }
            $this->assign('month',$_POST['month']);
            $this->assign('names',M(C('DB_3S_USER'))->getField(C('DB_3S_USER_USERNAME'),true));
            $this->assign('name','all');
            $this->assign('kpi_statistic',M(C('DB_KPI_STATISTIC'))->order('month desc')->where(array(C('DB_KPI_STATISTIC_MONTH')=>$_POST['month']))->select());
            $this->display('index');
        }else{
            $exist = M(C('DB_KPI_STATISTIC'))->where(array(array(C('DB_KPI_STATISTIC_NAME')=>$_POST['name']),array(C('DB_KPI_STATISTIC_MONTH')=>$_POST['month'])))->getField(C('DB_KPI_STATISTIC_NAME'),true);
            if($exist!==null){
                $this->error($_POST['month'].' 已经统计绩效考核。 '.$_POST['name']);
            }
            $this->calKpiStatistic($_POST['month'],$_POST['name'],false);
            $this->assign('month',$_POST['month']);
            $this->assign('names',M(C('DB_3S_USER'))->getField(C('DB_3S_USER_USERNAME'),true));
            $this->assign('name',$_POST['name']);
            $this->assign('kpi_statistic',M(C('DB_KPI_STATISTIC'))->order('month desc')->where(array(array(C('DB_KPI_STATISTIC_NAME')=>$_POST['name']),array(C('DB_KPI_STATISTIC_MONTH')=>$_POST['month'])))->select());
            $this->display('index');
        }
    }

    public function reStatistic(){
        $this->calKpiStatistic($_POST['month'],$_POST['name'],true);
        $this->assign('month',$month);
        $this->assign('names',array($name));
        $this->assign('name',$name);
        $this->assign('kpi_statistic',M(C('DB_KPI_STATISTIC'))->order('month desc')->where(array(array(C('DB_KPI_STATISTIC_NAME')=>$name),array(C('DB_KPI_STATISTIC_MONTH')=>$month)))->select());
        $this->display('index');
    }

    private function calKpiStatistic($month,$name,$resta){       
        $user = D('UserRelation')->where(array(C('DB_3S_USER_USERNAME')=>$name))->relation(true)->find();
        if($user['role'][0][C('DB_ROLE_NAME')]==C('DEPARTMENT')['sale']){
            $this->calKpiSaleStatistic($month,$name,$resta);
        }elseif($user['role'][0][C('DB_ROLE_NAME')]==C('DEPARTMENT')['product']){
            $this->calKpiProductStatistic($month,$name,$resta);
        }elseif($user['role'][0][C('DB_ROLE_NAME')]==C('DEPARTMENT')['storage']){
            $this->calKpiStorageStatistic($month,$name,$resta);
        }elseif($user['role'][0][C('DB_ROLE_NAME')]==C('DEPARTMENT')['customer']){
            $this->calKpiCustomerServiceStatistic($month,$name,$resta);
        }
    }

    //统计销售人员某月绩效
    private function calKpiSaleStatistic($month,$name,$resta){
        //获取起始库存段1的销售绩效表清货记录
        $map[C('DB_KPI_SALE_NAME')] = array('eq',$name);
        $map[C('DB_KPI_SALE_BEGIN_DATE')] = array('egt',strtotime($month.'-01 00:00:00')-60*60*24*C('kpi_sc1_day'));
        $map[C('DB_KPI_SALE_TYPE')] = array('eq',C('USSW_SALE_PLAN_CLEAR'));
        $map[C('DB_KPI_SALE_BEGIN_SQUANTITY')] = array('elt',C('kpi_sc1_squantity'));
        $kpiSales = $this->calKpiSale(M(C('DB_KPI_SALE'))->where($map)->select());
        foreach ($kpiSales as $key => $value) {
            if($value[C('DB_KPI_SALE_SALE_QUANTITY')]>=$value[C('DB_KPI_SALE_BEGIN_SQUANTITY')]){
                $validClear=$validClear+1;
            }
        }

        //获取起始库存段2的销售绩效表清货记录
        $map[C('DB_KPI_SALE_NAME')] = array('eq',$name);
        $map[C('DB_KPI_SALE_BEGIN_DATE')] = array('egt',strtotime($month.'-01 00:00:00')-60*60*24*C('kpi_sc2_day'));
        $map[C('DB_KPI_SALE_TYPE')] = array('eq',C('USSW_SALE_PLAN_CLEAR'));
        $map[C('DB_KPI_SALE_BEGIN_SQUANTITY')] = array('elt',C('kpi_sc2_squantity'));
        $kpiSales = $this->calKpiSale(M(C('DB_KPI_SALE'))->where($map)->select());
        foreach ($kpiSales as $key => $value) {
            if($value[C('DB_KPI_SALE_SALE_QUANTITY')]>=$value[C('DB_KPI_SALE_BEGIN_SQUANTITY')]){
                $validClear=$validClear+1;
            }
        }

        //获取起始库存段3的销售绩效表清货记录
        $map[C('DB_KPI_SALE_NAME')] = array('eq',$name);
        $map[C('DB_KPI_SALE_BEGIN_DATE')] = array('egt',strtotime($month.'-01 00:00:00')-60*60*24*C('kpi_sc3_day'));
        $map[C('DB_KPI_SALE_TYPE')] = array('eq',C('USSW_SALE_PLAN_CLEAR'));
        $map[C('DB_KPI_SALE_BEGIN_SQUANTITY')] = array('gt',C('kpi_sc2_squantity'));
        $kpiSales = $this->calKpiSale(M(C('DB_KPI_SALE'))->where($map)->select());
        foreach ($kpiSales as $key => $value) {
            if($value[C('DB_KPI_SALE_SALE_QUANTITY')]>=$value[C('DB_KPI_SALE_BEGIN_SQUANTITY')]){
                $validClear=$validClear+1;
            }
        }

        //写入销售有效清货记录
        $newStatistic[C('DB_KPI_STATISTIC_NAME')] = $name;
        $newStatistic[C('DB_KPI_STATISTIC_MONTH')] = $month;
        $newStatistic[C('DB_KPI_STATISTIC_TYPE')] = 'clear';
        $newStatistic[C('DB_KPI_STATISTIC_SCORE')] = $validClear;
        if($validClear<C('kpi_scqm')){
            $newStatistic[C('DB_KPI_STATISTIC_PASS')] = 0;
        }else{
            $newStatistic[C('DB_KPI_STATISTIC_PASS')] = 1;
        }
        if($resta==false){
            M(C('DB_KPI_STATISTIC'))->add($newStatistic);
        }else{
            $oldStatistic = M(C('DB_KPI_STATISTIC'))->where(array(array(C('DB_KPI_STATISTIC_MONTH')=>$month),array(C('DB_KPI_STATISTIC_NAME')=>$name),array(C('DB_KPI_STATISTIC_TYPE')=>'clear')))->find();
            if($oldStatistic!==null && $oldStatistic!==false){
                $oldStatistic[C('DB_KPI_STATISTIC_SCORE')] = $validClear;
                $oldStatistic[C('DB_KPI_STATISTIC_PASS')] = $newStatistic[C('DB_KPI_STATISTIC_PASS')];
                M(C('DB_KPI_STATISTIC'))->save($oldStatistic);
            }
        }
        

        //获取销售绩效表重新刊登记录
        $usswSaleRelistingPeriod = M(C('DB_USSW_SALE_PLAN_METADATA'))->where(array(C('DB_USSW_SALE_PLAN_METADATA_ID')=>1))->getField(C('DB_USSW_SALE_PLAN_METADATA_RELISTING_NOD'));
        $szswSaleRelistingPeriod = M(C('DB_SZ_SALE_PLAN_METADATA'))->where(array(C('DB_SZ_SALE_PLAN_METADATA_ID')=>1))->getField(C('DB_SZ_SALE_PLAN_METADATA_ADJUST_PERIOD'));
        if($usswSaleRelistingPeriod>$szswSaleRelistingPeriod){
            $relistingPeriod = $usswSaleRelistingPeriod;
        }else{
            $relistingPeriod = $szswSaleRelistingPeriod;
        }
        $map[C('DB_KPI_SALE_NAME')] = array('eq',$name);
        $map[C('DB_KPI_SALE_BEGIN_DATE')] = array('egt',strtotime($month.'-01 00:00:00')-60*60*24*$relistingPeriod);
        $map[C('DB_KPI_SALE_TYPE')] = array('eq',C('USSW_SALE_PLAN_RELISTING'));
        $kpiSales = $this->calKpiSale(M(C('DB_KPI_SALE'))->where($map)->select());
        foreach ($kpiSales as $key => $value) {
            if($value[C('DB_KPI_SALE_SALE_QUANTITY')]>=C('kpi_srmsq') && $value[C('DB_KPI_SALE_AVERAGE_PROFIT')]>=C('kpi_srsap')){
                $validRelisting = $validRelisting+1;
            }
        }

        //写入销售有效重新刊登记录
        $newStatistic[C('DB_KPI_STATISTIC_NAME')] = $name;
        $newStatistic[C('DB_KPI_STATISTIC_MONTH')] = $month;
        $newStatistic[C('DB_KPI_STATISTIC_TYPE')] = 'relisting';
        $newStatistic[C('DB_KPI_STATISTIC_SCORE')] = $validRelisting;
        if($validRelisting<C('kpi_srqm')){
            $newStatistic[C('DB_KPI_STATISTIC_PASS')] = 0;
        }else{
            $newStatistic[C('DB_KPI_STATISTIC_PASS')] = 1;
        }
        if($resta==false){
            M(C('DB_KPI_STATISTIC'))->add($newStatistic);
        }else{
            $oldStatistic = M(C('DB_KPI_STATISTIC'))->where(array(array(C('DB_KPI_STATISTIC_MONTH')=>$month),array(C('DB_KPI_STATISTIC_NAME')=>$name),array(C('DB_KPI_STATISTIC_TYPE')=>'relisting')))->find();
            if($oldStatistic!==null && $oldStatistic!==false){
                $oldStatistic[C('DB_KPI_STATISTIC_SCORE')] = $validRelisting;
                $oldStatistic[C('DB_KPI_STATISTIC_PASS')] = $newStatistic[C('DB_KPI_STATISTIC_PASS')];
                M(C('DB_KPI_STATISTIC'))->save($oldStatistic);
            }
        }
        

    }

    //统计产品开发人员某月绩效
    private function calKpiProductStatistic($month,$name,$resta){
        $map[C('DB_PURCHASE_PURCHASED_DATE')] = array('like',$month.'%');
        $map[C('DB_PURCHASE_MANAGER')] = array('eq',C('PRODUCT_MANAGER_ENAME')[$name]);
        $porders = D('PurchaseView')->where($map)->select();
        $nindex = 0;
        foreach ($porders as $key => $po) {
            $firstPurchase = D('PurchaseView')->where(array(C('DB_PURCHASE_ITEM_SKU')=>$po[C('DB_PURCHASE_ITEM_SKU')]))->order(C('DB_PURCHASE_PURCHASED_DATE'))->limit(1)->getField(C('DB_PURCHASE_PURCHASED_DATE'));
            if(substr($firstPurchase, 0,7)==$month){
                if(!in_array(substr($po[C('DB_PURCHASE_ITEM_SKU')], 0,4), $newItem)){
                    $newItem[$nindex] = substr($po[C('DB_PURCHASE_ITEM_SKU')], 0,4);
                    $nindex = $nindex+1;
                }                
            }
        }
        if(count($newItem)>0){
            $newStatistic[C('DB_KPI_STATISTIC_NAME')] = $name;
            $newStatistic[C('DB_KPI_STATISTIC_MONTH')] = $month;
            $newStatistic[C('DB_KPI_STATISTIC_TYPE')] = 'new_item_quantity';
            $newStatistic[C('DB_KPI_STATISTIC_SCORE')] = count($newItem);
            if(count($newItem)<C('kpi_pqm')){
                $newStatistic[C('DB_KPI_STATISTIC_PASS')] = 0;
            }else{
                $newStatistic[C('DB_KPI_STATISTIC_PASS')] = 1;
            }
            if($resta==false){
                M(C('DB_KPI_STATISTIC'))->add($newStatistic);
            }else{
                $oldStatistic = M(C('DB_KPI_STATISTIC'))->where(array(array(C('DB_KPI_STATISTIC_MONTH')=>$month),array(C('DB_KPI_STATISTIC_NAME')=>$name),array(C('DB_KPI_STATISTIC_TYPE')=>'new_item_quantity')))->find();
                if($oldStatistic!==null && $oldStatistic!==false){
                    $oldStatistic[C('DB_KPI_STATISTIC_SCORE')] = count($newItem);
                    $oldStatistic[C('DB_KPI_STATISTIC_PASS')] = $newStatistic[C('DB_KPI_STATISTIC_PASS')];
                    M(C('DB_KPI_STATISTIC'))->save($oldStatistic);
                }
            }
            
        }
    }

    //统计仓库人员某月绩效
    private function calKpiStorageStatistic($month,$name,$resta){
        $mistakes = M(C('DB_KPI_STORAGE_MISTAKE'))->where(array(array(C('DB_KPI_STORAGE_MISTAKE_MONTH')=>$month),array(C('DB_KPI_STORAGE_MISTAKE_NAME')=>$name)))->sum(C('DB_KPI_STORAGE_MISTAKE_SCORE'));
        $newStatistic[C('DB_KPI_STATISTIC_NAME')] = $name;
        $newStatistic[C('DB_KPI_STATISTIC_MONTH')] = $month;
        $newStatistic[C('DB_KPI_STATISTIC_TYPE')] = 'mistake';
        $newStatistic[C('DB_KPI_STATISTIC_SCORE')] = $mistakes;
        if( $mistakes>C('kpi_smq')){
             $newStatistic[C('DB_KPI_STATISTIC_PASS')] = 0;
        }else{
            $newStatistic[C('DB_KPI_STATISTIC_PASS')] = 1;
        }
        if($resta==false){
            M(C('DB_KPI_STATISTIC'))->add($newStatistic);
        }else{
            $oldStatistic = M(C('DB_KPI_STATISTIC'))->where(array(array(C('DB_KPI_STATISTIC_MONTH')=>$month),array(C('DB_KPI_STATISTIC_NAME')=>$name),array(C('DB_KPI_STATISTIC_TYPE')=>'mistake')))->find();
            if($oldStatistic!==null && $oldStatistic!==false){
                $oldStatistic[C('DB_KPI_STATISTIC_SCORE')] = $mistakes;
                $oldStatistic[C('DB_KPI_STATISTIC_PASS')] = $newStatistic[C('DB_KPI_STATISTIC_PASS')];
                M(C('DB_KPI_STATISTIC'))->save($oldStatistic);
            }
        }
        
    }

    //统计客服人员某月绩效
    private function calKpiCustomerServiceStatistic($month,$name,$resta){
        $performances = M(C('DB_KPI_CUSTOMER'))->where(array(array(C('DB_KPI_CUSTOMER_MONTH')=>$month),array(C('DB_KPI_CUSTOMER_NAME')=>$name)))->sum(C('DB_KPI_CUSTOMER_SCORE'));
        $newStatistic[C('DB_KPI_STATISTIC_NAME')] = $name;
        $newStatistic[C('DB_KPI_STATISTIC_MONTH')] = $month;
        $newStatistic[C('DB_KPI_STATISTIC_MONTH')] = 'customer_performance';
        $newStatistic[C('DB_KPI_STATISTIC_SCORE')] = $performances;
        $newStatistic[C('DB_KPI_STATISTIC_PASS')] = 1;
        if($resta==false){
            M(C('DB_KPI_STATISTIC'))->add($newStatistic);
        }else{
            $oldStatistic = M(C('DB_KPI_STATISTIC'))->where(array(array(C('DB_KPI_STATISTIC_MONTH')=>$month),array(C('DB_KPI_STATISTIC_NAME')=>$name),array(C('DB_KPI_STATISTIC_TYPE')=>'customer_performance')))->find();
            if($oldStatistic!==null && $oldStatistic!==false){
                $oldStatistic[C('DB_KPI_STATISTIC_SCORE')] = $performances;
                $oldStatistic[C('DB_KPI_STATISTIC_PASS')] = $newStatistic[C('DB_KPI_STATISTIC_PASS')];
                M(C('DB_KPI_STATISTIC'))->save($oldStatistic);
            }
        }
        
    }

    public function kpiStorage(){
        if($_SESSION['username']=='admin'){
            $this->assign('mistakes',M(C('DB_KPI_STORAGE_MISTAKE'))->order('month desc')->select());
        }else{
            $this->assign('mistakes',M(C('DB_KPI_STORAGE_MISTAKE'))->where(array(C('DB_KPI_STATISTIC_NAME')=>$_SESSION['username']))->order('month desc')->select());
        }        
        $storageRoleId = M(C('DB_ROLE'))->where(array(C('DB_ROLE_NAME')=>'仓库'))->getField(C('DB_ROLE_ID'));
        $storageUserIds = M(C('DB_ROLE_USER'))->where(array(C('DB_ROLE_USER_ROLE_ID')=>$storageRoleId))->getField(C('DB_ROLE_USER_USER_ID'),true);
        $map[C('DB_3S_USER_ID')] = array('in', $storageUserIds);
        $storageUserNames = M(C('DB_3S_USER'))->where($map)->getField(C('DB_3S_USER_USERNAME'),true);
        $this->assign('names',$storageUserNames);
        $this->display();
    }

    public function newStoMistake(){
        if(M(C('DB_KPI_STORAGE_MISTAKE'))->add($_POST)!==false){
            $this->success('保存成功');
        }else{
            $this->error('保存失败');
        }
        
    }

    public function deleteStoMistake($id){
        if(M(C('DB_KPI_STORAGE_MISTAKE'))->where(array(C('DB_KPI_STORAGE_MISTAKE_ID')=>$id))->delete()!==false){
            $this->success('已删除');
        }else{
            $this->error('删除失败'); 
        }
        
    }

    public function kpiCustomer(){
        if($_SESSION['username']=='admin'){
            $this->assign('performances',M(C('DB_KPI_CUSTOMER'))->order('month desc')->select());
        }else{
            $this->assign('performances',M(C('DB_KPI_CUSTOMER'))->where(array(C('DB_KPI_CUSTOMER_NAME')=>$_SESSION['username']))->order('month desc')->select());
        }        
        $this->assign('names',M(C('DB_3S_USER'))->where(array(C('DB_3S_USER_DEPARTMENT')=>'客服'))->select());
        $this->display();
    }

    public function newCustomerPerformance(){
        if(M(C('DB_KPI_CUSTOMER'))->add($_POST)!==false){
            $this->success('保存成功');
        }else{
            $this->error('保存失败');
        }
        
    }

    public function deleteCustomerPerformance($id){
        if(M(C('DB_KPI_CUSTOMER'))->where(array(C('DB_KPI_CUSTOMER_ID')=>$id))->delete()!==false){
            $this->success('已删除');
        }else{
            $this->error('删除失败'); 
        }        
    }

    public function kpiSale(){
        if($_SESSION['username']=='admin'){
            $this->assign('sales', $this->calKpiSale(M(C('DB_KPI_SALE'))->order('id desc')->select()));
        }else{
            $this->assign('sales', $this->calKpiSale(M(C('DB_KPI_SALE'))->where(array(C('DB_KPI_SALE_NAME')=>$_SESSION['username']))->order('id desc')->select()));
        }
        $this->display();
    }

    public function kpiSaleSearch(){
        if($_SESSION['username']=='admin'){
            $this->assign('sales',$this->calKpiSale(M(C('DB_KPI_SALE'))->order('id desc')->where(array($_POST['keyword']=>$_POST['keywordValue']))->select()));
        }else{
            $this->assign('sales',$this->calKpiSale(M(C('DB_KPI_SALE'))->where(array(array(C('DB_KPI_SALE_NAME')=>$_SESSION['username']),array($_POST['keyword']=>$_POST['keywordValue'])))->order('id desc')->select()));
        }
        $this->assign('keyword',$_POST['keyword']);
        $this->assign('keywordValue',$_POST['keywordValue']);
        $this->display('kpiSale');
    }

    private function calKpiSale($sales){
        $usswSaleRelistingPeriod = M(C('DB_USSW_SALE_PLAN_METADATA'))->where(array(C('DB_USSW_SALE_PLAN_METADATA_ID')=>1))->getField(C('DB_USSW_SALE_PLAN_METADATA_RELISTING_NOD'));
        $szswSaleRelistingPeriod = M(C('DB_SZ_SALE_PLAN_METADATA'))->where(array(C('DB_SZ_SALE_PLAN_METADATA_ID')=>1))->getField(C('DB_SZ_SALE_PLAN_METADATA_ADJUST_PERIOD'));
        $kpiSaleTable = M(C('DB_KPI_SALE'));
        foreach ($sales as $key => $value) {
            if($value[C('DB_KPI_SALE_WAREHOUSE')]==C('USSW')){
                $data[$key]=$this->getKpiSaleQuantityAProfit($value,$usswSaleRelistingPeriod,R('Sale/GgsUsswSale/getSalePlanViewModel',array('greatgoodshop')));
            }elseif($value[C('DB_KPI_SALE_WAREHOUSE')]==C('SZSW')){
                $data[$key] = $this->getKpiSaleQuantityAProfit($value,$szswSaleRelistingPeriod,R('Sale/SzSale/getSalePlanViewModelName',array('vtkg5755','us')));
            }elseif($value[C('DB_KPI_SALE_WAREHOUSE')]==C('winit_de_warehouse')){
                $data[$key] = $this->getKpiSaleQuantityAProfit($value,$usswSaleRelistingPeriod,R('Sale/WinitDeSale/getSalePlanViewModel',array('rc-helicar')));
            }elseif($value[C('DB_KPI_SALE_WAREHOUSE')]==C('winit_uswc_warehouse')){
                $data[$key] = $this->getKpiSaleQuantityAProfit($value,$usswSaleRelistingPeriod,R('Sale/GgsUsswSale/getSalePlanViewModel',array('greatgoodshop')));
            }
        }
        return $data;
    }

    private function getKpiSaleQuantityAProfit($kpiSale,$relistingPeriod,$salePlan){
        $kpiSaleRecordTable = M(C('DB_KPI_SALE_RECORD'));
        if($kpiSale[C('DB_KPI_SALE_TYPE')]==C('USSW_SALE_PLAN_RELISTING')){
            $map[C('DB_KPI_SALE_RECORD_SALE_ID')] = $kpiSale[C('DB_KPI_SALE_ID')];
            $map[C('DB_KPI_SALE_RECORD_SOLD_DATE')] = array(array('egt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]),array('lt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]+60*60*24*$relistingPeriod));
            $kpiSale[C('DB_KPI_SALE_SALE_QUANTITY')] = $kpiSaleRecordTable->where($map)->sum(C('DB_KPI_SALE_RECORD_QUANTITY'));
            
            $sumPrice = $kpiSaleRecordTable->where($map)->sum(C('DB_KPI_SALE_RECORD_QUANTITY').'*'.C('DB_KPI_SALE_RECORD_PRICE').'+'.C('DB_KPI_SALE_RECORD_SHIPPING_FEE'));
            $avaragePrice = $sumPrice/$kpiSale[C('DB_KPI_SALE_SALE_QUANTITY')];
            $cost = D($salePlan)->where(array('sku'=>$kpiSale[C('DB_KPI_SALE_SKU')]))->getField('cost');
            $kpiSale[C('DB_KPI_SALE_AVERAGE_PROFIT')] = ($avaragePrice-$cost)/$avaragePrice;            
        }elseif($kpiSale[C('DB_KPI_SALE_TYPE')]==C('USSW_SALE_PLAN_CLEAR')){
            $map[C('DB_KPI_SALE_RECORD_SALE_ID')] = $kpiSale[C('DB_KPI_SALE_ID')];
            if($kpiSale[C('DB_KPI_SALE_BEGIN_SQUANTITY')]<=C('kpi_sc1_squantity')){
                $map[C('DB_KPI_SALE_RECORD_SOLD_DATE')] = array(array('egt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]),array('lt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]+60*60*24*C('kpi_sc1_day')));
            }elseif($kpiSale[C('DB_KPI_SALE_BEGIN_SQUANTITY')]>C('kpi_sc1_squantity') && $kpiSale[C('DB_KPI_SALE_BEGIN_SQUANTITY')]<=C('kpi_sc2_squantity')){
                $map[C('DB_KPI_SALE_RECORD_SOLD_DATE')] = array(array('egt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]),array('lt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]+60*60*24*C('kpi_sc2_day')));
            }else{
                $map[C('DB_KPI_SALE_RECORD_SOLD_DATE')] = array(array('egt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]),array('lt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]+60*60*24*C('kpi_sc3_day')));
            }
            $kpiSale[C('DB_KPI_SALE_SALE_QUANTITY')] = $kpiSaleRecordTable->where($map)->sum(C('DB_KPI_SALE_RECORD_QUANTITY'));
            $sumPrice = $kpiSaleRecordTable->where($map)->sum(C('DB_KPI_SALE_RECORD_QUANTITY').'*'.C('DB_KPI_SALE_RECORD_PRICE').'+'.C('DB_KPI_SALE_RECORD_SHIPPING_FEE'));
            $avaragePrice = $sumPrice/$kpiSale[C('DB_KPI_SALE_SALE_QUANTITY')];
            $cost = D($salePlan)->where(array('sku'=>$kpiSale[C('DB_KPI_SALE_SKU')]))->getField('cost');
            $kpiSale[C('DB_KPI_SALE_AVERAGE_PROFIT')] = ($avaragePrice-$cost)/$avaragePrice;            
        }
        return $kpiSale;
    }

    public function kpiSaleEdit($id){
        $sellerRoleId = M(C('DB_ROLE'))->where(array(C('DB_ROLE_NAME')=>'销售'))->getField(C('DB_ROLE_ID'));
        $sellerId = M(C('DB_ROLE_USER'))->where(array(C('DB_ROLE_USER_ROLE_ID')=>$sellerRoleId))->getField(C('DB_ROLE_USER_USER_ID'),true);

        $map[C('DB_3S_USER_ID')] = array('in',$sellerId);
        $sellerName = M(C('DB_3S_USER'))->where($map)->getField(C('DB_3S_USER_USERNAME'),true);
        $this->assign('sellerName',$sellerName);
        $this->assign('saleRecorde',M(C('DB_KPI_SALE'))->where(array(C('DB_KPI_SALE_ID')=>$id))->find());
        $this->display();
    }

    public function kpiSaleDetail($id){
        $kpiSale = M(C('DB_KPI_SALE'))->where(array(C('DB_KPI_SALE_ID')=>$id))->find();
        if($kpiSale[C('DB_KPI_SALE_TYPE')]==C('USSW_SALE_PLAN_CLEAR')){
            $map[C('DB_KPI_SALE_RECORD_SALE_ID')] = array('eq', $id);
            if($kpiSale[C('DB_KPI_SALE_BEGIN_SQUANTITY')]<=C('kpi_sc1_squantity')){
                $map[C('DB_KPI_SALE_RECORD_SOLD_DATE')] = array(array('egt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]),array('lt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]+60*60*24*C('kpi_sc1_day')));
            }elseif($kpiSale[C('DB_KPI_SALE_BEGIN_SQUANTITY')]>C('kpi_sc1_squantity') && $kpiSale[C('DB_KPI_SALE_BEGIN_SQUANTITY')]<=C('kpi_sc2_squantity')){
                $map[C('DB_KPI_SALE_RECORD_SOLD_DATE')] = array(array('egt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]),array('lt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]+60*60*24*C('kpi_sc2_day')));
            }else{
                $map[C('DB_KPI_SALE_RECORD_SOLD_DATE')] = array(array('egt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]),array('lt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]+60*60*24*C('kpi_sc3_day')));
            }
        }elseif($kpiSale[C('DB_KPI_SALE_TYPE')]==C('USSW_SALE_PLAN_RELISTING')){
            if($kpiSale[C('DB_KPI_SALE_WAREHOUSE')]==C('SZSW')){
                $szswSaleRelistingPeriod = M(C('DB_SZ_SALE_PLAN_METADATA'))->where(array(C('DB_SZ_SALE_PLAN_METADATA_ID')=>1))->getField(C('DB_SZ_SALE_PLAN_METADATA_ADJUST_PERIOD'));
                $map[C('DB_KPI_SALE_RECORD_SALE_ID')] = array('eq', $id);
                $map[C('DB_KPI_SALE_RECORD_SOLD_DATE')] = array(array('egt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]),array('lt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]+60*60*24*$szswSaleRelistingPeriod));
            }else{
                $usswSaleRelistingPeriod = M(C('DB_USSW_SALE_PLAN_METADATA'))->where(array(C('DB_USSW_SALE_PLAN_METADATA_ID')=>1))->getField(C('DB_USSW_SALE_PLAN_METADATA_RELISTING_NOD'));
                $map[C('DB_KPI_SALE_RECORD_SALE_ID')] = array('eq', $id);
                $map[C('DB_KPI_SALE_RECORD_SOLD_DATE')] = array(array('egt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]),array('lt', $kpiSale[C('DB_KPI_SALE_BEGIN_DATE')]+60*60*24*$usswSaleRelistingPeriod));
            }            
        }
        $this->assign('records',M(C('DB_KPI_SALE_RECORD'))->where($map)->select());
        $this->display();
    }

    public function kpiSaleSave(){
        if(M(C('DB_KPI_SALE'))->save($_POST)!=false){
            $this->success('保存成功');
        }else{
            $this->error('保存失败');
        }
    }

    public function kpiSaleDelete($id){
        if(M(C('DB_KPI_SALE'))->where(array(C('DB_KPI_SALE_ID')=>$id))->delete()!=false){
            $this->success('删除成功');
        }else{
            $this->error('删除失败');
        }        
    }

    /**  
     * 判断某年的某月有多少天  
     * @return [type] [description]  
     */    
    private function daysInMonth($year='',$month=''){  
        if(empty($year)) $year = date('Y');    
        if(empty($month)) $month = date('m');  
        $day = '01';  
          
        //检测日期是否合法  
        if(!checkdate($month,$day,$year)) return $this->error('输入的时间有误');  
          
        //获取当年当月第一天的时间戳(时,分,秒,月,日,年)  
        $timestamp = mktime(0,0,0,$month,$day,$year);  
        $result = date('t',$timestamp);  
        return $result;  
    }
}

?>