<?php

/**
 * ConnectionManager
 *
 * It is a controller of the site, this site is only simple connection system, this class checker and directs the user.
 *
 */
class ConnectionManager {

	/* @var UserConnected $UserConnected */
	private $UserConnected;

	function __construct() {

		$this->UserConnected = new UserConnected();
		/* Checked if is specific request or default request*/
		if ( isset( $_REQUEST['action'] ) ) {
			$this->actions();
		} else {
			$this->defaultAction();
		}
	}

	/**
	 * In default request check if is user connected to view the right page.
	 */
	private function defaultAction() {

		$Viewer = new Viewer( $this->UserConnected );

		if ( $this->isConnected() ) {
			$Viewer->viewPart( 'users-connected', 'Users Connected' );
		} else {
			$Viewer->viewPart( 'login', 'Sign In' );
		}
	}

	/**
	 * In specific request check if has such action function, this checked done for security, after check if this request is ajax to check before the execute if the user has connected.
	 */
	private function actions() {

		if ( method_exists( $this, 'action' . $_REQUEST['action'] ) ) {
			if ( isset( $_POST['requestType'] ) && $_POST['requestType'] == 'ajax' ) {
				$this->ajaxActions();
			} else {
				$func = 'action' . $_REQUEST['action'];
				$this->$func( $_REQUEST );
			}
		} else {
			return;
		}
	}

	/**
	 * Checker if the user has connected if connected calling to function, if not add alert and redirect to login page.
	 */
	private function ajaxActions() {

		session_start();
		if ( isset($_SESSION['user-name']) ) {
			$func = 'action' . $_REQUEST['action'];
			$this->$func( $_REQUEST );
		} else {
			$_SESSION['alert'] = 'Your connection finish please login again';
			echo json_encode(['redirect'=>true]);
		}
	}

	/**
	 * Check by session if has connection, if has connection, updating in DB.
	 *
	 * @return bool
	 */
	private function isConnected() {

		session_start();

		if ( isset( $_SESSION['user-name'] ) ) {

			$this->UserConnected->updateUserConnected( $_SESSION['user-name'] );

			return true;
		}

		return false;
	}

	/**
	 * Steps to login:
	 *
	 * checkCredentials.
	 * Set sessions to saved the username and date connected.
	 * Redirect to view users connected.
	 * If login failed redirect to login form which alert message.
	 *
	 * @param array $params the $_SERVER array
	 */
	private function  actionLogin( array $params ) {

		$CredentialsManager = new CredentialsManager();

        if ( $CredentialsManager->checkCredentials( $params['username'], $params['password'] ) ) {
			session_start();

			$_SESSION['user-name']       = $params['username'];
			$_SESSION['connection-date'] = General::getDate();

			$this->UserConnected->setDetails();
			$this->UserConnected->save();
		} else {
			session_start();
			$_SESSION['alert'] = 'Failed to connect !';
			$this->redirect();
		}
		$this->redirect();
	}

	/**
	 * Cancels the connect by destroy session and delete from DB
	 */
	private function actionLogout() {

		session_start();
		if ( $_SESSION['user-name'] ) {
			$this->UserConnected->deleteUserConnected( $_SESSION['user-name'] );
			session_destroy();
		}
		$this->redirect( '?logout' );
	}

	/**
	 * Return json with all users connected.
	 */
	private function actionGetUsersConnected() {

		$response        = [ ];
		$users_connected = $this->UserConnected->getUsersConnected();
		/** @var UserConnected $user_connected */
		foreach ( $users_connected as $user_connected ) {
			$response['users'][] = [
				'username'       => $user_connected->getUserName(),
				'connected_date' => $user_connected->getConnectedDate(),
				'update_date'    => $user_connected->getUpdateDate(),
				'IP'             => $user_connected->getIP()
			];
		}

		echo json_encode( $response );
	}


	/**
	 * Update connection date at the specified time in order not  be old user.
	 */
	private function actionUpdateUserConnected() {

		$this->UserConnected->updateUserConnected( $_SESSION['user-name'] );
	}

	private function redirect( $params = '' ) {

		header( 'Location: ' . BASE_URL . $params );
	}
}

