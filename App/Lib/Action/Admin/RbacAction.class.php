<?php

class RbacAction extends CommonAction{

	public function index(){
		$this->user = D('UserRelation')->field(C('DB_3S_USER_PASSWORD'),true)->relation(true)->select();
		$this->display();
	}

	//角色列表
	public function role(){
		$this->role=M(C('DB_ROLE'))->select();
		$this->display();
	}

	//节点列表
	public function node(){
		$field=array(C('DB_NODE_ID'),C('DB_NODE_NAME'),C('DB_NODE_TITLE'),C('DB_NODE_PID'));
		$node=M(C('DB_NODE'))->field($field)->order(C('DB_NODE_SORT'))->select();
		$this->node=node_merge($node);
		$this->display();
	}

	//添加用户
	public function addUser(){
		$this->role=M(C('DB_ROLE'))->select();
		$this->display();
	}

	//删除用户
	public function deleteUser($uid){
		$role = M(C('DB_3S_USER'));
		$role->where(array(C('DB_3S_USER_ID')=>$uid))->delete();
		$this->success('已删除',U('Admin/Rbac/index'));
	}

	//添加用户表单处理
	public function addUserHandle(){
		$user = array(
			C('DB_3S_USER_USERNAME') => I(C('DB_3S_USER_USERNAME')),
			C('DB_3S_USER_PASSWORD') => I(C('DB_3S_USER_PASSWORD'),'','md5'),
			C('DB_3S_USER_LOGINTIME') => time(),
			C('DB_3S_USER_LOGINIP') => get_client_ip()
			);

		$role = array();
		if($uid = M(C('DB_3S_USER'))->add($user)){
			foreach ($_POST[C('DB_ROLE_ID')] as $key => $value) {
				$role[] = array(
					C('DB_ROLE_USER_ROLE_ID') => $value,
					C('DB_ROLE_USER_USER_ID') => $uid
					);
			}
			M(C('DB_ROLE_USER'))->addAll($role);
			$this->success('保存成功',U('Admin/Rbac/index'));
		}
	}

	//添加角色
	public function addRole(){
		$this->display();
	}

	//添加角色表单处理
	public function addRoleHandle(){
		if(M(C('DB_ROLE'))->add($_POST)){
			$this->success('添加成功',U('Admin/Rbac/role'));
		}else{
			$this->error('添加失败');
		}
	}

	//添加节点
	public function addNode(){
		$this->pid=I(C('DB_NODE_PID'),0,'intval');
		$this->level=I(C('DB_NODE_LEVEL'),1,'intval');
		Switch($this->level){
			case 1:
				$this->type='应用';
				break;
			case 2:
				$this->type='控制器';
				break;
			case 3:
				$this->type='方法';
				break;
		}
		$this->display();
	}

	//添加节点表单处理
	public function addNodeHandle(){
		if(M(C('DB_NODE'))->add($_POST)){
			$this->success('添加成功',U('Admin/Rbac/node'));
		}else{
			$this->error('添加失败');
		}
	}

	//权限配置列表
	public function access(){
		$rid = I(C('DB_ROLE_ID'),0,'intval');
		$field = array(C('DB_NODE_ID'),C('DB_NODE_NAME'),C('DB_NODE_TITLE'),C('DB_NODE_PID'));
		$node=M(C('DB_NODE'))->field($field)->order(C('DB_NODE_SORT'))->select();
		$access = M(C('DB_ACCESS'))->where(array(C('DB_ACCESS_ROLE_ID')=>$rid))->getField(C('DB_ACCESS_NODE_ID'),true);

		$this->node=node_merge($node,$access);
		$this->rid=$rid;
		$this->display();
	}

	//保存配置的权限
	public function setAccess(){
		$rid = I('rid',0,'intval');
		$access = M(C('DB_ACCESS'));
		$access->starttrans();
		$access->where(array(C('DB_ROLE_ID')=>$rid))->delete();
		$data = array();
		foreach ($_POST[C('DB_ACCESS')] as $key => $value) {
			$tmp = explode('_',$value);
			$data[] = array(
				C('DB_ACCESS_ROLE_ID')=>$rid,
				C('DB_ACCESS_NODE_ID')=>$tmp[0],
				C('DB_ACCESS_LEVEL')=>$tmp[1],
				);
		}
		$result = $access->addAll($data);
		if($result>0 || $result==false){
			$this->success('修改成功', U('Admin/Rbac/role'));
		}else{
			$this->error('修改失败');
		}
		$access->commit();
	}

	//锁定用户
	public function lockUser($uid){
		M(C('DB_3S_USER'))->where(array(C('DB_3S_USER_ID')=>$uid))->setField(C('DB_3S_USER_LOCK'),1);
		$this->success('用户已锁定');
	}

	//解锁用户
	public function unlockUser($uid){
		M(C('DB_3S_USER'))->where(array(C('DB_3S_USER_ID')=>$uid))->setField(C('DB_3S_USER_LOCK'),0);
		$this->success('用户已解锁');
	}
}

?>