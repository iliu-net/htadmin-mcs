<?php
include_once('tools/util.php');
include_once ('tools/htpasswd.php');
if (!isset($ini)) $ini = read_config ();
$htpasswd = new htpasswd ( $ini ['secure_path']);

if (isset($ini['admin_users'])) {
  // First check if a username was provided.
  if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
    // If no username provided, present the auth challenge.
    header('WWW-Authenticate: Basic realm="My Website"');
    header('HTTP/1.0 401 Unauthorized');
    // User will be presented with the username/password prompt
    // If they hit cancel, they will see this access denied message.
    echo '<p>Access denied. You did not enter a password.</p>';
    exit; // Be safe and ensure no other content is returned.
  }
  // If we get here, username was provided. Check password.
  if (!$htpasswd->user_check($_SERVER['PHP_AUTH_USER'],$_SERVER['PHP_AUTH_PW'])) {
    // If no username provided, present the auth challenge.
    header('WWW-Authenticate: Basic realm="My Website"');
    header('HTTP/1.0 401 Unauthorized');
    // User will be presented with the username/password prompt
    // If they hit cancel, they will see this access denied message.

    echo '<p>Access denied. You did not enter a valid password.</p>';
    exit; // Be safe and ensure no other content is returned.
  }
  if ($ini['admin_users']) {
    $admins = preg_split('/\s*,\s*/', $ini['admin_users']);
    if (count($admins) > 0) {
      if (!in_array($_SERVER['PHP_AUTH_USER'],$admins)) {
	header('HTTP/1.0 403 Forbidden');
	include_once ('includes/head.php');
	include_once ('includes/nav.php');

	echo '<p>Access denied. You are not allowed here.</p>';
	exit; // Be safe and ensure no other content is returned.
      }
    }
  }
}

$ini = read_config();

$htpasswd = new htpasswd ( $ini ['secure_path']);

if (isset ( $_POST['user'] )) {
	$user = $_POST['user'];
	if ($htpasswd->user_delete($user)) {
		echo "success";
	} else {
		echo "error";
	}

} else {
	echo "error";
}
?>
