<?php

class StorageAction extends CommonAction{

    public function index(){
    	if($_POST['keyword']==""){
            $Data = M(C('DB_USSTORAGE'));
            import('ORG.Util.Page');
            $count = $Data->count();
            $Page = new Page($count,20);            
            $Page->setConfig('header', '条数据');
            $show = $Page->show();
            $usstorage = $Data->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('usstorage',$usstorage);
            $this->assign('page',$show);
        }
        else{
            $where[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
            $this->usstorage = M(C('DB_USSTORAGE'))->where($where)->select();
        }
        $this->display();
    }

    Public function edit($sku){
        $this->usstorage = M(C('DB_USSTORAGE'))->where(array(C('DB_USSTORAGE_SKU')=>$sku))->select();
        $this->display();

    }

    public function update(){
        $data[C('DB_USSTORAGE_ID')] = I('post.'.C('DB_USSTORAGE_ID'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_POSITION')] = I('post.'.C('DB_USSTORAGE_POSITION'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_SKU')] = I('post.'.C('DB_USSTORAGE_SKU'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_CNAME')] = I('post.'.C('DB_USSTORAGE_CNAME'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_ENAME')] = I('post.'.C('DB_USSTORAGE_ENAME'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_ATTRIBUTE')] = I('post.'.C('DB_USSTORAGE_ATTRIBUTE'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_CINVENTORY')] = I('post.'.C('DB_USSTORAGE_CINVENTORY'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_AINVENTORY')] = I('post.'.C('DB_USSTORAGE_AINVENTORY'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_OINVENTORY')] = I('post.'.C('DB_USSTORAGE_OINVENTORY'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_IINVENTORY')] = I('post.'.C('DB_USSTORAGE_IINVENTORY'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_CSALES')] = I('post.'.C('DB_USSTORAGE_CSALES'),'','htmlspecialchars');
        $data[C('DB_USSTORAGE_REMARK')] = I('post.'.C('DB_USSTORAGE_REMARK'),'','htmlspecialchars');
        $usstorage = M(C('DB_USSTORAGE'));
    	$result =  $usstorage->save($data);
	    if(false !== $result || 0 !== $result) {
	    	$this->success('操作成功！');}
	    else{
	        $this->error('写入错误！');
	     }
    }

    public function insertSingleItem(){
        if(IS_POST){
            if($this->checkSku(I('post.sku','','htmlspecialchars'))){
                $usstorage = M(C('DB_USSTORAGE'));
                $usstorage->starttrans();
                if(I('post.position','','htmlspecialchars') == ''){
                    $where[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                    $row = $usstorage->where($where)->find();
                    if($row!=''){
                        $data[C('DB_USSTORAGE_CINVENTORY')] = $row[C('DB_USSTORAGE_CINVENTORY')]+1;
                        $data[C('DB_USSTORAGE_AINVENTORY')] = $row[C('DB_USSTORAGE_AINVENTORY')]+1;

                        $result = $usstorage->where($where)->save($data);
                        $usstorage->commit();
                        if(false !== $result and 0!== $result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }else{
                        $data[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_CINVENTORY')] = 1;
                        $data[C('DB_USSTORAGE_AINVENTORY')] = 1;
                        $result = $usstorage->add($data);
                        $usstorage->commit();
                        if($result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }
                    
                }else{
                    $where[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                    $where[C('DB_USSTORAGE_POSITION')] = I('post.position','','htmlspecialchars');
                    $row = $usstorage->where($where)->find();
                    if($row!=''){
                        $data[C('DB_USSTORAGE_CINVENTORY')] = $row[C('DB_USSTORAGE_CINVENTORY')]+1;
                        $data[C('DB_USSTORAGE_AINVENTORY')] = $row[C('DB_USSTORAGE_AINVENTORY')]+1;
                        $result = $usstorage->where($where)->save($data);
                        $usstorage->commit();
                        if(false !== $result and 0!== $result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }else{
                        $data[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_POSITION')] = I('post.position','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_CINVENTORY')] = 1;
                        $data[C('DB_USSTORAGE_AINVENTORY')] = 1;
                        $result = $usstorage->add($data);
                        $usstorage->commit();
                        if($result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }
                    
                }
            }else{
                $this->error('产品编码不在产品列表，请检查');
            }
            
        } 
    }

    public function insertMultiItem(){
        if(IS_POST){
            if($this->checkSku(I('post.sku','','htmlspecialchars'))){
                $usstorage = M(C('DB_USSTORAGE'));
                $usstorage->starttrans();
                if(I('post.position','','htmlspecialchars') == ''){
                    $where[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                    $row = $usstorage->where($where)->find();
                    if($row!=''){
                        $data[C('DB_USSTORAGE_CINVENTORY')] = $row[C('DB_USSTORAGE_CINVENTORY')]+I('post.quantity','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_AINVENTORY')] = $row[C('DB_USSTORAGE_AINVENTORY')]+I('post.quantity','','htmlspecialchars');

                        $result = $usstorage->where($where)->save($data);
                        $usstorage->commit();
                        if(false !== $result and 0!== $result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }else{
                        $data[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_CINVENTORY')] = I('post.quantity','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_AINVENTORY')] = I('post.quantity','','htmlspecialchars');
                        $result = $usstorage->add($data);
                        $usstorage->commit();
                        if($result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }
                    
                }else{
                    $where[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                    $where[C('DB_USSTORAGE_POSITION')] = I('post.position','','htmlspecialchars');
                    $row = $usstorage->where($where)->find();
                    if($row!=''){
                        $data[C('DB_USSTORAGE_CINVENTORY')] = $row[C('DB_USSTORAGE_CINVENTORY')]+I('post.quantity','','htmlspecialchars');;
                        $data[C('DB_USSTORAGE_AINVENTORY')] = $row[C('DB_USSTORAGE_AINVENTORY')]+I('post.quantity','','htmlspecialchars');;
                        $result = $usstorage->where($where)->save($data);
                        $usstorage->commit();
                        if(false !== $result and 0!== $result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }else{
                        $data[C('DB_USSTORAGE_SKU')] = I('post.sku','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_POSITION')] = I('post.position','','htmlspecialchars');
                        $data[C('DB_USSTORAGE_CINVENTORY')] = I('post.quantity','','htmlspecialchars');;
                        $data[C('DB_USSTORAGE_AINVENTORY')] = I('post.quantity','','htmlspecialchars');;
                        $result = $usstorage->add($data);
                        $usstorage->commit();
                        if($result){
                            $this->success('入库成功！');
                        }
                        else{
                            $this->error('入库失败！');
                        }
                    }
                    
                }
            }else{
                $this->error('产品编码不在产品列表，请检查');
            }
            
        } 
    }

    private function checkSku($sku){
        $result = M(C('DB_PRODUCT'))->where(array(C('DB_PRODUCT_SKU')=>$sku))->find();
        if($result != ''){
            return true;
        }
        return false;
    }
}

?>