<?php

class TodoAction extends CommonAction{

    public function index($keyword=null,$keywordValue=null,$skeywordValue=null){
        $Data=M(C('DB_TODO'));
        import('ORG.Util.Page');
        $count = $Data->count();
        $Page = new Page($count,20);            
        $Page->setConfig('header', '条数据');
        $show = $Page->show();
        if($_POST['keyword']=="" || $_POST['keyword']==null){
            if($keyword!=null){
                $map[$keyword] = array('like','%'.$keywordValue.'%');
                $map[C('DB_TODO_STATUS')] = array('eq',$skeywordValue);
                $task = $Data->order('id desc')->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();
                $this->assign('keyword', $keyword);
                $this->assign('keywordValue', $keywordValue);
                $this->assign('skeywordValue', $skeywordValue);
            }else{
                $task = $Data->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();
            }  
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

    public function newThing(){
        $this->display();
    }

    Public function changeStatus($id,$kw=null,$kwv=null,$skwv=null){
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
            $this->success('保存成功',U('Todo/Todo/index',array('keyword'=>$kw,'keywordValue'=>$kwv,'skeywordValue'=>$skwv)));
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

    public function addZero($temp){
         if($temp<10) 
            return "0".$temp; 
         else 
            return $temp; 
    }

    public function attendance($actualDate){
        $attendaceTable=M(C('DB_ATTENDANCE'));
        $actualDay=substr($actualDate, 8,2);
        $j=0;
        for($i=$actualDay;$i>=1;$i--){
            $map[C('DB_ATTENDANCE_COMETIME')] = array('like',substr($actualDate, 0,7).'-'.$this->addZero($i).'%');
            $map[C('DB_ATTENDANCE_NAME')] = array('eq',$_SESSION['username']);
            $att=$attendaceTable->where($map)->find();
            if($att!=null){
                $data[$j]['id']=$att[C('DB_ATTENDANCE_ID')];
                $data[$j]['name']=$_SESSION['username' ];
                $data[$j]['date']=substr($att[C('DB_ATTENDANCE_COMETIME')], 0,10);
                $data[$j]['come']=substr($att[C('DB_ATTENDANCE_COMETIME')],10,9);
                $data[$j]['leave']=substr($att[C('DB_ATTENDANCE_LEAVETIME')],10,9);
                $data[$j]['rest1_begin']=substr($att[C('DB_ATTENDANCE_REST1_BEGIN')], 10,9);
                $data[$j]['rest1_end']=substr($att[C('DB_ATTENDANCE_REST1_END')], 10,9);
                $data[$j]['rest2_begin']=substr($att[C('DB_ATTENDANCE_REST2_BEGIN')], 10,9);
                $data[$j]['rest2_end']=substr($att[C('DB_ATTENDANCE_REST2_END')], 10,9);
                $rest=((strtotime($data[$j]['rest1_end'])-strtotime($data[$j]['rest1_begin']))+(strtotime($data[$j]['rest2_end'])-strtotime($data[$j]['rest2_begin'])))/3600;
                if($rest==null||$rest==''||$rest==0){
                    $data[$j]['hour']=round((strtotime($data[$j]['leave'])-strtotime($data[$j]['come']))/3600-C('NOON_BREAK')[$_SESSION['username']],2);
                }else{
                    $data[$j]['hour']=round((strtotime($data[$j]['leave'])-strtotime($data[$j]['come']))/3600-$rest,2);
                }
                if($data[$j]['hour']<0){
                    $data[$j]['hour']=0;
                }
                $j++;
            }
        }
        $this->assign('atts',$data);
        $this->display();
    }

    public function attendanceAll(){
        $atts = M(C('DB_ATTENDANCE'))->order('id desc')->select(); 
        $this->assign('atts',$atts);
        $this->display();
    }

    public function attendanceCome($comeTime){
        $map[C('DB_ATTENDANCE_NAME')]=array('eq',$_SESSION['username']);
        $map[C('DB_ATTENDANCE_COMETIME')] = array('like',substr($comeTime, 0,10).'%');
        $result = M(C('DB_ATTENDANCE'))->where($map)->find();
        if($result[C('DB_ATTENDANCE_COMETIME')]!=null){
            $this->error(substr($comeTime, 0,10).'已经开工！');
        }else{
            $result[C('DB_ATTENDANCE_NAME')]=$_SESSION['username'];
            $result[C('DB_ATTENDANCE_COMEIP')]=$_SESSION['loginip'];
            $result[C('DB_ATTENDANCE_COMETIME')]=$comeTime;
            $result=M(C('DB_ATTENDANCE'))->add($result);
            if($result!==false){
                $this->success('开工咯，加油！');
            }else{
                $this->error('开工失败，请重新点击开工按钮！');
            }
        }
        
    }

    public function attendanceLeave($leaveTime){
        $map[C('DB_ATTENDANCE_NAME')]=array('eq',$_SESSION['username']);
        $map[C('DB_ATTENDANCE_COMETIME')] = array('like',substr($leaveTime, 0,10).'%');
        $result = M(C('DB_ATTENDANCE'))->where($map)->find();
        if($result[C('DB_ATTENDANCE_LEAVETIME')]!=null){
            $this->error(substr($leaveTime, 0,10).'已经收工！');
        }else{
            $result[C('DB_ATTENDANCE_LEAVEIP')]=$_SESSION['loginip'];
            $result[C('DB_ATTENDANCE_LEAVETIME')]=$leaveTime;
            $result=M(C('DB_ATTENDANCE'))->save($result);
            if($result!==false){
                $this->success('已收工，好好放松一下！');
            }else{
                $this->error('收工失败，请重新点击收工按钮！');
            }
        }
        
    }

    public function restBegin($restBeginTime){
        $map[C('DB_ATTENDANCE_NAME')]=array('eq',$_SESSION['username']);
        $map[C('DB_ATTENDANCE_COMETIME')] = array('like',substr($restBeginTime, 0,10).'%');
        $result = M(C('DB_ATTENDANCE'))->where($map)->find();
        if($result==null){
            $this->error(substr($restBeginTime, 0,10).' 尚未开工！');
        }else{
            if($result[C('DB_ATTENDANCE_REST1_BEGIN')]==null || $result[C('DB_ATTENDANCE_REST1_BEGIN')]==''){
                $result[C('DB_ATTENDANCE_REST1_BEGIN')] = $restBeginTime;
            }elseif(($result[C('DB_ATTENDANCE_REST1_END')]!=null ||$result[C('DB_ATTENDANCE_REST1_END')]!='') && ($result[C('DB_ATTENDANCE_REST2_BEGIN')]==null || $result[C('DB_ATTENDANCE_REST2_BEGIN')]=='')){
                $result[C('DB_ATTENDANCE_REST2_BEGIN')] = $restBeginTime;
            }else{
                $this->error('间休开始失败，请重新点击间休开始按钮！');
            }
            $result=M(C('DB_ATTENDANCE'))->save($result);
            if($result!==false){
                $this->success('间休开始');
            }else{
                $this->error('间休开始失败，请重新点击间休开始按钮！');
            }
        }
    }

    public function restEnd($restEndTime){
        $map[C('DB_ATTENDANCE_NAME')]=array('eq',$_SESSION['username']);
        $map[C('DB_ATTENDANCE_COMETIME')] = array('like',substr($restEndTime, 0,10).'%');
        $result = M(C('DB_ATTENDANCE'))->where($map)->find();
        if($result==null){
            $this->error(substr($restEndTime, 0,10).' 尚未开工！');
        }else{
            if($result[C('DB_ATTENDANCE_REST1_END')]==null || $result[C('DB_ATTENDANCE_REST1_END')]==''){
                $result[C('DB_ATTENDANCE_REST1_END')] = $restEndTime;
            }elseif(($result[C('DB_ATTENDANCE_REST2_BEGIN')]!=null || $result[C('DB_ATTENDANCE_REST2_BEGIN')]!='')&&($result[C('DB_ATTENDANCE_REST2_END')]==null || $result[C('DB_ATTENDANCE_REST2_END')]=='')){
                $result[C('DB_ATTENDANCE_REST2_END')] = $restEndTime;
            }else{
                $this->error('间休结束失败，请重新点击间休结束按钮！');
            }
            $result=M(C('DB_ATTENDANCE'))->save($result);
            if($result!==false){
                $this->success('间休结束');
            }else{
                $this->error('间休结束失败，请重新点击间休结束按钮！');
            }
        }
    }

    public function editAttendance($id){
        $map[C('DB_ATTENDANCE_ID')]=array('eq',$id);
        $att=M(C('DB_ATTENDANCE'))->where($map)->find();
        $this->assign('names',array_keys(C('NOON_BREAK')));
        $this->assign('name',$_SESSION['username']);
        $this->assign('att',$att);
        $this->display();
    }

    public function editAttendanceHandle(){
        $attendaceTable=M(C('DB_ATTENDANCE'));
        $result=$attendaceTable->save($_POST);
        if($result!=false)
            $this->success('保存成功');
        else
            $this->error('保存失败');
    }
}

?>