<?php
require_once('../config.php');
Class Master extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;
		parent::__construct();
	}
	public function __destruct(){
		parent::__destruct();
	}
	function capture_err(){
		if(!$this->conn->error)
			return false;
		else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;
			return json_encode($resp);
			exit;
		}
	}
	function save_medicine(){
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k,['id'])){
				$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .= ", ";
				$data .= "`{$k}` = '{$v}'";
			}
		}
		if(empty($id)){
			if(!empty($data)) $data .= ", ";
				$data .= "`user_id` = '{$this->settings->userdata('id')}'";
			$sql = "INSERT INTO `medicine_list` set {$data}";
		}else{
			$sql = "UPDATE `medicine_list` set {$data} where id = '{$id}'";
		}
		$save = $this->conn->query($sql);
		if($save){
			$cid= empty($id) ? $this->conn->insert_id : $id ;
			$resp['status'] = 'success';
				if(empty($id))
					$this->settings->set_flashdata('success',"Medicine has been added successfully.");
				else
					$this->settings->set_flashdata('success',"Medicine has been updated.");
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = 'Saving medicine failed';
			$resp['sql'] = $sql;
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function delete_medicine(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `medicine_list` where id = '{$id}' ");
		if($delete){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Medicine has been deleted successfully");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;

		}
		return json_encode($resp);
	}
	function dt_medicines(){
		extract($_POST);
		$totalCount = $this->conn->query("SELECT * FROM `medicine_list` where user_id = '{$this->settings->userdata('id')}' ")->num_rows;
		$search_where = "";
		if(!empty($search['value'])){
			$search_where .= " name LIKE '%{$search['value']}%' ";
			$search_where .= " OR description LIKE '%{$search['value']}%' ";
			$search_where .= " OR date_format(created_at,'%M %d, %Y') LIKE '%{$search['value']}%' ";
			$search_where = " and ({$search_where}) ";
		}
		$columns_arr = array("unix_timestamp(created_at)",
							"unix_timestamp(created_at)",
							"name",
							"description",
							"");
		$query = $this->conn->query("SELECT * FROM `medicine_list`  where user_id = '{$this->settings->userdata('id')}'  {$search_where} ORDER BY {$columns_arr[$order[0]['column']]} {$order[0]['dir']} limit {$length} offset {$start} ");
		$recordsFilterCount = $this->conn->query("SELECT * FROM `medicine_list`  where user_id = '{$this->settings->userdata('id')}'  {$search_where} ")->num_rows;
		
		$recordsTotal= $totalCount;
		$recordsFiltered= $recordsFilterCount;
		$data = array();
		$i= 1 + $start;
		while($row = $query->fetch_assoc()){
			$row['no'] = $i++;
			$row['created_at'] = date("F d, Y H:i",strtotime($row['created_at']));
			$data[] = $row;
		}
		echo json_encode(array('draw'=>$draw,
							'recordsTotal'=>$recordsTotal,
							'recordsFiltered'=>$recordsFiltered,
							'data'=>$data
							)
		);
	}
	function save_schedule(){
		$_POST['day'] = implode(', ',$_POST['day']);
		extract($_POST);
		$data = "";
		foreach($_POST as $k => $v){
			if(!in_array($k,['id','lifetime_schedule', 'until']) && !is_array($_POST[$k])){
				$v = $this->conn->real_escape_string($v);
				if(!empty($data)) $data .= ", ";
				$data .= "`{$k}` = '{$v}'";
			}
		}
		if(isset($lifetime_schedule)){
			if(!empty($data)) $data .= ", ";
			$data .= "`until` = NULL ";
		}else{
			if(isset($until) && !empty($until)){
				if(!empty($data)) $data .= ", ";
				$data .= "`until` = NULL ";
			}
		}
		if(empty($id)){
			$sql = "INSERT INTO `schedule_list` set {$data}";
		}else{
			$sql = "UPDATE `schedule_list` set {$data} where id = '{$id}'";
		}
		$save = $this->conn->query($sql);
		if($save){
			$sid= empty($id) ? $this->conn->insert_id : $id ;
			$resp['sid'] = $sid;
			if(empty($id))
				$this->settings->set_flashdata('success',"Schedule has been added successfully.");
			else
				$this->settings->set_flashdata('success',"Schedule has been updated.");
			$resp['status'] = 'success';
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = 'Saving Schedule failed';
			$resp['error'] = $this->conn->error;
		}
		return json_encode($resp);
	}
	function delete_schedule(){
		extract($_POST);
		$delete = $this->conn->query("DELETE FROM `schedule_list` where id = '{$id}' ");
		if($delete){
			$resp['status'] = 'success';
			$this->settings->set_flashdata('success'," Schedule has been deleted successfully");
		}else{
			$resp['status'] = 'failed';
			$resp['error'] = $this->conn->error;

		}
		return json_encode($resp);
	}
	function dt_schedules(){
		extract($_POST);
 
		$totalCount = $this->conn->query("SELECT * FROM `schedule_list` where  user_id = '{$this->settings->userdata('id')}' ")->num_rows;
		$search_where = "";
		$columns_arr = array("unix_timestamp(created_at)",
							"unix_timestamp(date_start)",
							"day",
							"medicine_name",
							"");
		if(!empty($search['value'])){
			$search_where .= "day LIKE '%{$search['value']}%' ";
			$search_where .= " OR date_format(date_start,'%M %d, %Y') LIKE '%{$search['value']}%' ";
			$search_where .= " OR medicine_id in (SELECT id FROM `medicine_list` where user_id = '{$this->settings->userdata('id')}' and name LIKE '%{$search['value']}%') ) ";
			$search_where = " and ({$search_where}) ";
		}
		$query = $this->conn->query("SELECT *, COALESCE((SELECT `name` FROM `medicine_list` where `medicine_list`.`id` = `schedule_list`.`medicine_id`),'') as medicine_name FROM `schedule_list`  where  user_id = '{$this->settings->userdata('id')}'  {$search_where} ORDER BY {$columns_arr[$order[0]['column']]} {$order[0]['dir']} limit {$length} offset {$start} ");
		$recordsFilterCount = $this->conn->query("SELECT * FROM `schedule_list`  where  user_id = '{$this->settings->userdata('id')}'  {$search_where} ")->num_rows;
		
		$recordsTotal= $totalCount;
		$recordsFiltered= $recordsFilterCount;
		$data = array();
		$i= 1 + $start;
		while($row = $query->fetch_assoc()){
			$row['no'] = $i++;
			$row['date_start'] = date("F d, Y",strtotime($row['date_start']));
			$row['date_start'] = date("F d, Y",strtotime($row['date_start']));
			if(is_null($row['until'])){
				$row['status'] = '1';
			}elseif(strtotime(date('Y-m-d')) < strtotime($row['until'])){
				$row['status'] = '0';
			}else{
				$row['status'] = '1';
			}
			$data[] = $row;
		}
		echo json_encode(array('draw'=>$draw,
							'recordsTotal'=>$recordsTotal,
							'recordsFiltered'=>$recordsFiltered,
							'data'=>$data
							)
		);
	}
	function dt_schedules_public(){
		extract($_POST);
 
		$totalCount = $this->conn->query("SELECT * FROM `schedule_list` where  delete_flag = 0 ")->num_rows;
		$search_where = "";
		$columns_arr = array("unix_timestamp(date_updated)",
							"unix_timestamp(date_updated)",
							"doctors",
							"status",
							"unix_timestamp(birthdate)");
		if(!empty($search['value'])){
			$search_where .= "location LIKE '%{$search['value']}%' ";
			$search_where .= " OR id in (SELECT schedule_id FROM schedule_medicine where medicine_id in (SELECT id FROM medicine_list where name LIKE '%{$search['value']}%') ) ";
			$search_where = " and ({$search_where}) ";
		}
		$query = $this->conn->query("SELECT * FROM `schedule_list`  where  delete_flag = 0  {$search_where} ORDER BY {$columns_arr[$order[0]['column']]} {$order[0]['dir']} limit {$length} offset {$start} ");
		$recordsFilterCount = $this->conn->query("SELECT * FROM `schedule_list`  where  delete_flag = 0  {$search_where} ")->num_rows;
		
		$recordsTotal= $totalCount;
		$recordsFiltered= $recordsFilterCount;
		$data = array();
		$i= 1 + $start;
		while($row = $query->fetch_assoc()){
			$row['no'] = $i++;
			$medicines = $this->conn->query("SELECT cc.name as medicine FROM schedule_medicine c inner join medicine_list cc on c.medicine_id = cc.id where c.schedule_id = '{$row['id']}' ")->fetch_all(MYSQLI_ASSOC);
			$cats = array_column($medicines,'medicine');
			$cats = implode(", ", $cats);
			$row['medicine'] = $cats;
			$row['doctors'] = str_replace("||"," & ",$row['doctors']);
			$row['date_updated'] = date("F d, Y H:i",strtotime($row['date_updated']));
			$data[] = $row;
		}
		echo json_encode(array('draw'=>$draw,
							'recordsTotal'=>$recordsTotal,
							'recordsFiltered'=>$recordsFiltered,
							'data'=>$data
							)
		);
	}
	
}

$Master = new Master();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'save_medicine':
		echo $Master->save_medicine();
	break;
	case 'delete_medicine':
		echo $Master->delete_medicine();
	break;
	case 'dt_medicines':
		echo $Master->dt_medicines();
	break;
	case 'save_schedule':
		echo $Master->save_schedule();
	break;
	case 'delete_schedule':
		echo $Master->delete_schedule();
	break;
	case 'dt_schedules':
		echo $Master->dt_schedules();
	break;
	case 'dt_schedules_public':
		echo $Master->dt_schedules_public();
	break;
	default:
		// echo $sysset->index();
		break;
}