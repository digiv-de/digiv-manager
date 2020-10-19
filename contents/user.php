<?php

require_once(plugin_dir_path( __FILE__ ).'../includes/KeycloakHandler.php');
$kh = new KeycloakHandler();

/**
 * list all users
 */

if (!isset($_REQUEST['id']) || empty($_REQUEST['id'])) {
	
	$users = $kh->getUsers();
	
	echo '<br><form action="?page=digiv&tab=user" method="post">';
	echo '<input type="text" name="id" list="userlist" />';
	echo '<datalist id="userlist">';
		foreach ($users as $row) echo '<option value="'.$row['id'].'">'.$row['username'].'</option>';
	echo '</datalist>';
	echo '<input type="submit" value="weiter">';
	echo '</form>';
	
} else {

/**
 * Edit User	
 */
 
	if ($_POST['change'] == 'core') {
		
		$data = $kh->getUserByID($_REQUEST['id']);
		$valid_fields = array('username','firstName','lastName','email');
		
		$unchanged = 0;
		$changes = array();
		
		foreach ($valid_fields as $field) {
			if ($_POST[$field] == $data[$field]) {
				$unchanged++;
				continue;
			}
			
			// value was changed
			
			// sanity check
			if ($field == 'username') {
				$POST[$field] = trim($_POST[$field]);
				if (preg_match('/\s|@|\./', $_POST['field'])) {
					echo '<b style="color:red">Benutzernamen dürfen keine Leerzeichen, Punkt oder @ enthalten</b>';
					return;
				}
			}
			
			$changes[$field] = $_POST[$field];
		}
		
		if (count($changes) > 0) {
			if (!$kh->modifyUser($_REQUEST['id'], $changes)) {
				echo '<b style="color:red">Konnte Account nicht bearbeiten</b>';
				return;
			}
		}
		
		echo '<div style="padding:5px;border:2px #0F0 solid;background:#9F9"><b>'.count($changes).'</b> verändert, <b>'.$unchanged.'</b> unverändert</div>';
	}
	
/**
 * show user
 */

	$data = $kh->getUserByID($_REQUEST['id']);
	
	echo '<h2>bearbeite Benutzer &#0187;'.$_REQUEST['id'].'&#0171;</h2>';
	
	echo '<table>';
	echo '<form action="?page=digiv&tab=user&id='.$_REQUEST['id'].'" method="post">';
	echo '<input type="hidden" name="change" value="core">';
	echo '<tr><th>Benutzer</th><td><input type="text" name="username" value="'.$data['username'].'"></td></tr>';
	echo '<tr><th>Vorname</th><td><input type="text" name="firstName" value="'.$data['firstName'].'"></td></tr>';
	echo '<tr><th>Nachname</th><td><input type="text" name="lastName" value="'.$data['lastName'].'"></td></tr>';
	echo '<tr><th>E-Mail</th><td><input type="email" name="email" value="'.$data['email'].'"></td></tr>';
	echo '<tr><td></td><td><input type="submit" value="speichern"></td><tr>';
	echo '</form></table>';

	echo '<br><hr><br>';
	
	echo '<table>';
	echo '<form action="?page=digiv&tab=user&id='.$user.'" method="post">';
	echo '<input type="hidden" name="change" value="attr">';
	foreach ($data['attributes'] as $key=>$attr) {
		if (substr($key, 0, 15) == 'saml.persistent') continue;
		if (substr($key, 0, 3) == '_dm') continue;
		
		echo '<tr>';
		echo '<th>'.$key.'</th>';
		if (count($attr) == 1) echo '<td><input type="text" name="'.$key.'" value="'.$attr[0].'"></td>';
		else echo '<td>'.implode('; ',$attr).'</td>';
		echo '</tr>';
	}
	echo '<tr><td></td><td><input type="submit" value="speichern"></td><tr>';
	echo '</form></table>';

	echo '<br><hr><br>';
	
	echo '<pre>'.print_r($data, true).'</pre>';
}
