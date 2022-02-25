<?php
include_once ('tools/htpasswd.php');
include_once ('tools/util.php');
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

include_once ('includes/head.php');
include_once ('includes/nav.php');


?>

<div class="container box">
	<div class="row">
		<div class="col-xs-12">
<?php

echo "<h2>" . $ini ['app_title'] . "</h2>";


if (isset ( $_POST ['user'] )) {
	$username = $_POST ['user'];
	$passwd = $_POST ['pwd'];

	if (! check_username ( $username ) || ! check_password_quality ( $passwd )) {
		?>
			<div class="alert alert-danger">
			<?php
		echo "<p>User <em>" . htmlspecialchars ( $username ) . "</em> is invalid!.</p>";
	} else {
		?>
			<div class="alert alert-info">
			<?php
		if (! $htpasswd->user_exists ( $username )) {
			$htpasswd->user_add ( $username, $passwd );
			echo "<p>User <em>" . htmlspecialchars ( $username ) . "</em> created.</p>";
		} else {
			$htpasswd->user_update ( $username, $passwd );
			echo "<p>User <em>" . htmlspecialchars ( $username ) . "</em> changed.</p>";
		}
	}

	?>
		</div>
    <?php
}
?>
<div class="result alert alert-info" style="display: none;"></div>

			</div>
		</div>
		<div class=row>
			<div class="col-xs-12 col-md-4">
				<h3>Create or update user:</h3>
				<form class="navbar-form navbar-left" action="index.php"
					method="post">
					<div class="form-group">
						<p>
							<input type="text" class="userfield form-control"
								placeholder="Username" name="user">
						</p>
					<p>
							<input class="passwordfield form-control" type="password"
								name="pwd" placeholder="Password" />
						</p>
						<button type="submit" class="btn btn-default">Submit</button>
					</div>
				</form>

			</div>

			<div class="col-xs-12 col-md-6">
				<h3>Users:</h3>
			<?php
			$users = $htpasswd->get_users ();
			include_once ("includes/user_table.php");
			?>
		</div>
		</div>
		<div class=row>
			<br /> <br />
			<div class="col-xs-12 col-md-10 well">
				<p>
					Create new users for the htpasswd file here.
					<?php if ($htpasswd->getmsg()) {
						echo('<pre>'. $htpasswd->getmsg().'</pre>');
					} ?>
				</p>
			</div>
		</div>
	</div>

<?php
include_once ('includes/footer.php');
?>
