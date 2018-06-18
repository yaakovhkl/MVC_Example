<footer>
	<script>
		window.miniSite =
		<?= json_encode([
					'baseURL'=>BASE_URL,
					'updateUserTime'=>UserConnected::UPDATE_USER_TIME * 1000
					])?>;
	</script>
	<script src="<?= BASE_URL ?>assets/js/jquery-1.10.2.min.js"></script>
	<?
	/**
	 *@var Viewer $this
	 *@var string $partName
	 */
	$this->printScripts( $partName );
	?>
</footer>
</body>
</html>