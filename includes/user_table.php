<?php
/**
 * Page model:
 * $users as an array of strings
 */
?>
<?php

if (count ( $users ) == 0) {
	echo "<p>No users found!</p>";
} else {
	?>
<div class="panel panel-default">

	<table class="table">
		<thead>
			<tr>
				<th>Username</th>
			<th>&nbsp;</th>
			</tr>
		</thead>
		<tbody>
	<?php
	
	foreach ( $users as $user ) {
		$fieldjs = "onclick=\"setUserField('" . htmlspecialchars ( $user ) . "','','');\"";
		
		echo "<tr class='id-" . htmlspecialchars ( $user ) . "' >";
		echo "<td scope='row' " . $fieldjs . ">" . htmlspecialchars ( $user ) . " </td>";
		echo "<td scope='row'><a class='btn btn-danger pull-right' " . "onclick=\"deleteUser('" . htmlspecialchars ( $user ) . "');\"" . "href='#' >Delete</a>" . "</li></td>";
	}
	?>
	</tbody>
	</table>

</div>
<p>Click on a user to edit.</p>
<?php
}
?>
