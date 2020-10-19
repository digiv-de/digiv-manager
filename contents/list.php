<?php

require_once(plugin_dir_path( __FILE__ ).'../includes/KeycloakHandler.php');
$kh = new KeycloakHandler();

echo '<table>';
echo '<tr><th>Benutzer</th><th>Vorname</th><th>Name</th><th></th></tr>';
foreach ($kh->getUsers() as $row) {
	echo '<tr><td>'.$row['username'].'</td>';
	echo '<td>'.$row['firstName'].'</td>';
	echo '<td>'.$row['lastName'].'</td>';
	echo '<td><a href="?page=digiv&tab=user&id='.$row['id'].'">Bearbeiten</a></td></tr>';
}
echo '</table>';