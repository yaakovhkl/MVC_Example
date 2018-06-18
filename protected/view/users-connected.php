<div class="container main-container">
	<div class="home-container col-md-8">
		<h1>Hallow <?= $_SESSION['user-name'] ?></h1><a
			<? /**@var Viewer $this */ ?>
			href="<?= BASE_URL ?>?action=Logout">Log out</a>

		<?
		$users_connected = $this->UserConnected->getUsersConnected(  );
		if ( $users_connected ) {
			?>
			<table id="users-connected">
				<tr>
					<td>User Name</td>
					<td>Last Login</td>
					<td>Last Update</td>
					<td>IP</td>
				</tr>
				<?
				/** @var UserConnected $user_connected */
				foreach ( $users_connected as $user_connected ) {
					?>
					<tr>
						<td><?= $user_connected->getUserName() ?></td>
						<td><?= $user_connected->getConnectedDate() ?></td>
						<td><?= $user_connected->getUpdateDate() ?></td>
						<td><?= $user_connected->getIP() ?></td>
					</tr>
				<?
				}
				?>
			</table>
		<?
		} ?>
	</div>
</div>

