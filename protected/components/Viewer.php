<?php

/**
 * Viewer
 *
 * View parts by partName name.
 */
class Viewer {

	/* @var UserConnected $UserConnected */
	private $UserConnected;
	private $viewPath;
	private $scripts = [ ];

	function __construct( $UserConnected ) {

		$this->UserConnected = $UserConnected;
		$this->viewPath      = BASE_PATH . 'protected/view/';
	}

	/**
	 * This function require file from view folder.
	 *
	 * @param string $partName name of view file
	 * @param string $title to use in header file
	 */
	public function viewPart( $partName, $title ) {

		require $this->viewPath . 'header.php';
		require $this->viewPath . $partName . '.php';
		require $this->viewPath . 'footer.php';
	}

	/**
	 * Add script to load in specific part.
	 *
	 * @param string $partName part name to add script
	 * @param string $href link to script
	 */
	public function addScript( $partName, $href ) {

		$this->scripts[ $partName ][] = $href;
	}

	/**
	 * Print all scripts added to part.
	 *
	 * @param string $partName part name to print scripts
	 */
	public function printScripts( $partName ) {

		if ( isset( $this->scripts[ $partName ] ) ) {
			foreach ( $this->scripts[ $partName ] as $href ) {
				?>
				<script src="<?= $href ?>"></script>
			<?
			}
		}
	}
}