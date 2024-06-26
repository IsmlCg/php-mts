<?php
require_once '../config.php';
class Login extends DBConnection {
	private $settings;
	public function __construct(){
		global $_settings;
		$this->settings = $_settings;

		parent::__construct();
		ini_set('display_error', 1);
	}
	public function __destruct(){
		parent::__destruct();
	}
	public function index(){
		echo "<h1>Access Denied</h1> <a href='".base_url."'>Go Back.</a>";
	}
	public function login(){
		extract($_POST);
		$stmt = $this->conn->prepare("SELECT id,password, username from users where username = ? ");
		$stmt->bind_param("s",$username);
		$stmt->execute();
		$result = $stmt->get_result();
		if($result->num_rows > 0){
			$data = $result->fetch_array();
			
			if(password_verify($password, $data['password'])){
				foreach($data as $k => $v){
					if(!is_numeric($k) && $k != 'password'){
						$this->settings->set_userdata($k,$v);
					}
				}
				$this->last_login($data["id"]);
				$resp['status'] = 'success';
			}else{
				$resp['status'] = 'failed';
				$resp['msg'] = 'Incorrect Username or Password';
			}
		}else{
			$resp['status'] = 'failed';
			$resp['msg'] = 'Incorrect Username or Password';
		}
		return json_encode($resp);
	}
	private function last_login($id){
		// Create a new DateTime object
		$dateTime = new DateTime();
		// Set the timezone to Dublin
		$dateTime->setTimezone(new DateTimeZone("Europe/Dublin"));
		// Get the current date and time in Dublin
		$datetime = $dateTime->format('Y-m-d H:i:s');
		$sql = "UPDATE `users` set `last_login` = '{$datetime}' where id = {$id};";
		$save = $this->conn->query( $sql );
		if( $save ){
			return true;
		}else{
			return false;
		}
		
	}
	public function logout(){
		if($this->settings->sess_des()){
			redirect('app/login.php');
		}
	}
}
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$auth = new Login();
switch ($action) {
	case 'login_user':
		echo $auth->login();
		break;
	case 'logout':
		echo $auth->logout();
		break;
	default:
		echo $auth->index();
		break;
}

