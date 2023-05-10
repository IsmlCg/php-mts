<?php
require_once('../config.php');
Class Users extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function save_users(){
		if(empty($_POST['password']))
			unset($_POST['password']);
		else
			$_POST['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
		extract($_POST);
		$id = $id ?? '';
		$oid = $id;
		$data = '';
		$chk = $this->conn->query("SELECT * FROM `users` where username ='{$username}' ".($id>0? " and id!= '{$id}' " : ""))->num_rows;
		if($chk > 0){
			return 3;
			exit;
		}
		foreach($_POST as $k => $v){
			if(!in_array($k,['id']) && !is_array($_POST[$k])){
				if(!empty($data)) $data .=" , ";
				$v = $this->conn->real_escape_string($v);
				$data .= " {$k} = '{$v}' ";
			}
		}
		
		if(empty($id)){
			$sql = "INSERT INTO users set {$data}";
		}else{
			$sql = "UPDATE users set $data where id = {$id}";
		}
		$save = $this->conn->query($sql);
		if($save){
			$uid = empty($id) ? $this->conn->insert_id : $id;
			if(empty($id))
				$this->settings->set_flashdata('success','Your Account has been created successfully.');
			else
				$this->settings->set_flashdata('success','Your Account has been updated successfully.');
			$resp['status'] = 'success';
			if($this->settings->userdata('id') == $uid){
				foreach($_POST as $k => $v){
					if(!in_array($k,['id']) && !is_array($_POST[$k])){
						$this->settings->set_userdata($k,$v);
					}
				}
			}
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = 'Saving account failed.';
			$resp['error'] = $this->conn->error;
		}
		
		return  json_encode($resp);
	}
	public function delete_users(){
		extract($_POST);
		$avatar = $this->conn->query("SELECT avatar FROM users where id = '{$id}'")->fetch_array()['avatar'];
		$qry = $this->conn->query("DELETE FROM users where id = $id");
		if($qry){
			$this->settings->set_flashdata('success','User Details successfully deleted.');
			$avatar = explode("?", $avatar)[0];
			if(is_file(base_app.$avatar))
				unlink(base_app.$avatar);
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
		}
		return json_encode($resp);
	}
}

$users = new users();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
switch ($action) {
	case 'save_user':
		echo $users->save_users();
	break;
	case 'delete_user':
		echo $users->delete_users();
	break;
	case 'register_user':
		echo $users->save_users();
	break;
	default:
		// echo $sysset->index();
		break;
}