<?php

class TodoAction extends CommonAction{

    public function index(){
        $Data=M(C('DB_TODO'));
        import('ORG.Util.Page');
        $count = $Data->count();
        $Page = new Page($count,20);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        if($_POST['keyword']==""){
            $task = $Data->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();    
        }
        else{
            $map[I('post.keyword','','htmlspecialchars')] = array('like','%'.I('post.keywordValue','','htmlspecialchars').'%');
            $map[I('post.skeyword','','htmlspecialchars')] = array('eq',I('post.skeywordValue','','htmlspecialchars'));
            $task = $Data->order('id desc')->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();
            $this->assign('keyword', I('post.keyword','','htmlspecialchars'));
            $this->assign('keywordValue', I('post.keywordValue','','htmlspecialchars'));
            $this->assign('skeywordValue', I('post.skeywordValue','','htmlspecialchars'));
        }
        $this->assign('task',$task);
        $this->assign('page',$show);
        $this->display();
    }

    public function details($id){
        $task = M(C('DB_TODO'))->where(array(C('DB_TODO_ID')=>$id))->find();
        $this->assign('task',$task);
        $this->display();
    }

    public function update(){
        if(IS_POST){
            $data[C('DB_TODO_ID')] = I('post.'.C('DB_TODO_ID'),'','htmlspecialchars');
            $data[C('DB_TODO_CREATER')] = I('post.'.C('DB_TODO_CREATER'),'','htmlspecialchars');
            $data[C('DB_TODO_PERSON')] = I('post.'.C('DB_TODO_PERSON'),'','htmlspecialchars');
            $data[C('DB_TODO_STATUS')] = I('post.'.C('DB_TODO_STATUS'),'','htmlspecialchars');
            if(I('post.'.C('DB_TODO_STATUS'),'','htmlspecialchars')==1){
                $data[C('DB_TODO_DTIME')]=date("Y-m-d H:i:s" ,time());
            }else{
                $data[C('DB_TODO_DTIME')]=null;
            }
            $data[C('DB_TODO_TASK')] = I('post.'.C('DB_TODO_TASK'),'','htmlspecialchars');
            $data[C('DB_TODO_REMARK')] = I('post.'.C('DB_TODO_REMARK'),'','htmlspecialchars');
            if(M(C('DB_TODO'))->save($data)!==false){
                $this->success('保存成功');
            }else{
                $this->error('保存失败');
            }
            
        }
    }

    public function newTodo(){
        if(IS_POST){
            $data[C('DB_TODO_CREATER')] = I('post.'.C('DB_TODO_CREATER'),'','htmlspecialchars');
            $data[C('DB_TODO_PERSON')] = I('post.'.C('DB_TODO_PERSON'),'','htmlspecialchars');
            $data[C('DB_TODO_STATUS')] = 0;
            $data[C('DB_TODO_TASK')] = I('post.'.C('DB_TODO_TASK'),'','htmlspecialchars');
            $data[C('DB_TODO_REMARK')] = I('post.'.C('DB_TODO_REMARK'),'','htmlspecialchars');
            if(M(C('DB_TODO'))->add($data)!==false){
                $this->redirect('Todo/Todo/index','新增成功');
            }else{
                $this->error('无法新增');
            }
            
        }
    }

    Public function changeStatus($id){
        $status = M(C('DB_TODO'))->where(array(C('DB_TODO_ID')=>$id))->getField(C('DB_TODO_STATUS'));
        if($status==0){
            $data[C('DB_TODO_STATUS')]=1;
            $data[C('DB_TODO_DTIME')]=date("Y-m-d H:i:s" ,time());
        }else{
            $data[C('DB_TODO_STATUS')]=0;
            $data[C('DB_TODO_DTIME')]=null;
        }
        $data[C('DB_TODO_ID')]=$id;
        if(M(C('DB_TODO'))->save($data)!==false){
            $this->success('保存成功');
        }else{
            $this->error('保存失败');
        }
    }

    Public function remove($id){
        if(M(C('DB_TODO'))->where(array(C('DB_TODO_ID')=>$id))->delete()!==false){
            $this->success('已删除');
        }else{
            $this->error('删除失败');
        }
    }
}

?>