<?php

/**
 * Provides credentials to connect to web site.
 */
interface CredentialsProvider {

	/**
	 * Check if this user can connect to site.
	 *
	 * @param string $userName
	 * @param string $password
	 */
	public function checkCredentials( $userName, $password );

	/**
	 * Check if has connection to site of browser (by cookies).
	 */
	public function isConnected();
}

class CredentialsManager implements CredentialsProvider {

	/**
	 * @var array username => password: md5 hash
	 */
	private $users;

	function __construct() {

		$this->users = $this->get_users();
	}


	/**
	 * @return array username => hash passwords
	 */
	private function get_users() {

		return [
			'yaakov'  => '4aa96279f51638364efa149c6a79e2f8',
			'aharon' => 'a31e7b7f49ae4bb4dcc460d51b0bada0',
			'mati'   => 'b31b9b6bfd41ae0e02ad82fc005bfc65',
			'moshe'   => '08f60ae5cf4500ccc86a0601c34855ce',
			'yossi'  => '0ed50bc5986fc64475f03c1e049d33e0',
		];
	}

	/**
	 * Check if this user can connect to site.
	 *
	 * @param string $userName
	 * @param string $password
	 *
	 * @return bool true if has credentials, false if not
	 */
	public function checkCredentials( $userName, $password )
    {
        echo md5( $password );
		return $this->users[ $userName ] == md5( $password );
	}

	/**
	 * Check if has connection to site of browser (by session).
	 *
	 * @return mixed string username if is connected, bool false if not connected
	 */
	public function isConnected() {

		return isset( $_SESSION['MVC_Eample-username'] ) ? $_SESSION['MVC_Eample-username'] : false;
	}
}
