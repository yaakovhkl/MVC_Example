<div class="container main-container">
	<div class="login-container col-md-4">
		<div class="page-header">
			<h1>Sign in</h1>
		</div>
		<? /* if has alert view it. */ ?>
		<? if ( isset( $_SESSION['alert'] ) ) { ?>
			<div class="alert alert-danger" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				<span class="sr-only">Error:</span>
				<?= $_SESSION['alert'] ?>
				<? unset( $_SESSION['alert'] ) ?>
			</div>
		<? } ?>
		<form method="post">
			<div class="form-group">
				<input name="username" type="text" class="form-control input-lg" placeholder="Name" required>
			</div>
			<div class="form-group">
				<input name="password" type="password" class="form-control input-lg" placeholder="Password" required>
			</div>
			<input name="action" type="hidden" value="Login">

			<div class="form-group">
				<button class="btn btn-primary btn-lg btn-block">Sign in</button>
			</div>
		</form>
	</div>
</div>
<footer>