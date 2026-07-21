<?php 

Class Settings
{

	public function get_settings($id)
	{
		$DB = new Database();
		$sql = "select * from users where userid = '$id' limit 1";
		$row = $DB->read($sql);

		if(is_array($row)){

			return $row[0];
		}
	}

	public function save_settings($data,$id){

		$DB = new Database();

		$password = isset($data['password']) ? $data['password'] : "";

		if(strlen($password) < 30 && isset($data['password2'])){

			if($data['password'] == $data['password2']){
				$data['password'] = hash("sha1", $password);
			}else{

				unset($data['password']);
			}
		}

		unset($data['password2']);

		$allowed_keys = ['first_name','last_name','gender','email','about','tag_name','password','url_address'];

		$sql = "update users set ";
		$set_parts = [];
		foreach ($data as $key => $value) {

			if(in_array($key, $allowed_keys)){
				$set_parts[] = $key . "='" . addslashes($value) . "'";
			}
		}

		if(empty($set_parts)){
			return;
		}

		$sql .= implode(",", $set_parts);
		$sql .= " where userid = '$id' limit 1";
		$DB->save($sql);
	}
}