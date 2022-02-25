<?php
function read_config() {
  if (!is_file('config/config.ini')) {
    echo 'Missing "config/config.ini"'.PHP_EOL;
    exit;
  }
  $ini =  parse_ini_file('config/config.ini');
  if (!$ini) {
    echo '<pre>';
    $ec = error_get_last();
    foreach ($ec as $k => $v) {
      echo $k . ': ' . $v . PHP_EOL;
    }
    echo '</pre>';
    exit;
  }
  return $ini;
}

function check_password_quality($pwd) {
	if (!isset($pwd)||strlen($pwd)<4) {
		return false;
	}
	return true;
}

function check_username($username) {
	if (!isset($username)||strlen($username)>20 || strlen($username)<3) {
		return false;
	}
	return preg_match('/^[-\._a-zA-Z0-9]+$/', $username);
}

function random_password($length) {
	$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ1234567890';
	$pass = array(); //remember to declare $pass as an array
	$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
	for ($i = 0; $i < $length; $i++) {
		$n = rand(0, $alphaLength);
		$pass[] = $alphabet[$n];
	}
	return implode($pass); //turn the array into a string
}
