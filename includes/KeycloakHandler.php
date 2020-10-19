<?php

class KeycloakHandler {
	
	const KEYCLOAK_MAX_USERS = 10000;
	
	private $host;
	private $realm;
	private $botuser;
	private $botpassword;
		
	private $access_token;
	private $refresh_token;
	
	
	function __construct() {
		$this->host = get_option('digiv_set_host');
		$this->realm = get_option('digiv_set_realm');
		$this->botuser = get_option('digiv_set_botuser');
		$this->botpassword = get_option('digiv_set_botpassword');
	}
	
	function __destruct() {
		$this->logout();
	}
	
	function getToken() {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->host.'/auth/realms/'.$this->realm.'/protocol/openid-connect/token');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'username='.$this->botuser.'&password='.$this->botpassword.'&grant_type=password&client_id=admin-cli');

		$headers = array();
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);
		
		$data = json_decode($result, true);
		if ($data['token_type'] != 'bearer') return false;
		
		$this->access_token = $data['access_token'];
		$this->refresh_token = $data['refresh_token'];
		
		return true;
	}
	
	function logout() {
		if (!isset($this->access_token)) return true;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->host.'/auth/realms/'.$this->realm.'/protocol/openid-connect/logout');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, 'refresh_token='.$this->refresh_token.'&client_id=admin-cli');

		$headers = array();
		$headers[] = 'Authorization: Bearer '.$this->access_token;
		$headers[] = 'Content-Type: application/x-www-form-urlencoded';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		curl_setopt($ch, CURLOPT_HEADER, true);
		
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return $httpcode == 204;
	}
	
	function getUsers() {
		if (!isset($this->access_token)) $this->getToken();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->host.'/auth/admin/realms/'.$this->realm.'/users?max='.self::KEYCLOAK_MAX_USERS);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

		$headers = array();
		$headers[] = 'Authorization: Bearer '.$this->access_token;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);

		return json_decode($result, true);
	}
	
	function getUserByID($id) {
		if (!isset($this->access_token)) $this->getToken();

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->host.'/auth/admin/realms/'.$this->realm.'/users/'.$id);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

		$headers = array();
		$headers[] = 'Authorization: Bearer '.$this->access_token;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		curl_close($ch);

		return json_decode($result, true);
	}
	
	function modifyUser($id, $changes) {
		//if (!isset($this->access_token)) $this->getToken();
		
		//TODO check input
		
		if (is_array($changes)) {
			$changes = json_encode($changes);
		}
		
		$ch = curl_init();
		$url = $this->host.'/auth/admin/realms/'.$this->realm.'/users/'.$id;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_POSTFIELDS, $changes);

		$headers = array();
		$headers[] = 'Authorization: Bearer '.$this->access_token;
		$headers[] = 'Content-Type: application/json';
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		
		curl_setopt($ch, CURLOPT_HEADER, true);
		
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			echo 'Error:' . curl_error($ch);
		}
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return $httpcode == 204;
	}
}