<?php
/**
 * UserConnected
 *
 * Contain the CsvDataBase instance to connection to table in DB, and properties for user connected.
 *
 */
class UserConnected {

	const DB_NAME = 'MVC_Eample';

	const TABLE_NAME = 'users-connected';

	const UPDATE_USER_TIME = 30;

	private $userName;
	private $connectedDate;
	private $updateDate;
	private $IP;
	/**
	 * @var CsvDataBase $CsvDataBase
	 */
	private $CsvDataBase;

	/**
	 * @param array $details details for user connection (optional)
	 *
	 * Create database ans table if not exist and create CsvDataBase object.
	 * If has $details set the details (used to make users from database do not connection).
	 */
	function __construct( $details = null ) {

		if ( ! CsvDataBase::dataBaseExist( self::DB_NAME ) ) {
			CsvDataBase::createDataBase( self::DB_NAME );
		}
		if ( ! CsvDataBase::tableExist( self::DB_NAME, self::TABLE_NAME ) ) {
			CsvDataBase::createTable( self::DB_NAME, self::TABLE_NAME, [
				'name',
				'connection-date',
				'update-date',
				'IP'
			] );
		}

		$this->CsvDataBase = new CsvDataBase( self::DB_NAME, self::TABLE_NAME );
		if ( $details ) {
			foreach ( $details as $key => $value ) {
				switch ( $key ) {
					case 'name':
						$this->setUserName( $value );
						break;
					case 'connection-date':
						$this->setConnectedDate( $value );
						break;
					case 'update-date':
						$this->setUpdateDate( $value );
						break;
					case 'IP':
						$this->setIP( $value );
						break;
				}
			}
		}
	}


	/**
	 * @return mixed
	 */
	public function getUserName() {

		return $this->userName;
	}

	/**
	 * @param mixed $userName
	 */
	public function setUserName( $userName ) {

		$this->userName = $userName;
	}

	/**
	 * @return mixed
	 */
	public function getUpdateDate() {

		return $this->updateDate;
	}

	/**
	 * @param mixed $updateDate
	 */
	public function setUpdateDate( $updateDate ) {

		$this->updateDate = $updateDate;
	}

	/**
	 * @return mixed
	 */
	public function getConnectedDate() {

		return $this->connectedDate;
	}

	/**
	 * @param mixed $connectedDate
	 */
	public function setConnectedDate( $connectedDate ) {

		$this->connectedDate = $connectedDate;
	}

	/**
	 * @return mixed
	 */
	public function getIP() {

		return $this->IP;
	}

	/**
	 * @param mixed $IP
	 */
	public function setIP( $IP ) {

		$this->IP = $IP;
	}

	/**
	 * Set the details for user connected.
	 */
	public function setDetails() {

		$this->setUserName( $_SESSION['user-name'] );
		$this->setConnectedDate( $_SESSION['connection-date'] );
		$this->setUpdateDate( General::getDate() );
		$this->setIP( General::getIP() );
	}


	/**
	 * Save the user connected to DB.
	 */
	public function save() {

		$this->CsvDataBase->update( [
			'name'            => $this->userName,
			'connection-date' => $this->connectedDate,
			'update-date'     => $this->updateDate,
			'IP'              => $this->IP
		], [ 'name' => $this->userName ], true );
	}

	/**
	 * Update connection date in order not be old user.
	 *
	 * @param $userName
	 */
	public function updateUserConnected( $userName ) {

		$this->CsvDataBase->update( [ 'update-date' => General::getDate() ], [ 'name' => $userName ] );
	}

	/**
	 * Delete user from DB.
	 *
	 * @param $userName
	 */
	public function deleteUserConnected( $userName ) {

		$this->CsvDataBase->delete( [ 'name' => $userName ] );
	}

	/**
	 * Return list of all users connected (provided they do not old users).
	 *
	 * @return array|mixed
	 */
	public function getUsersConnected() {

		if ( $users = $this->CsvDataBase->select() ) {
			$users = self::filterOldUsers( $users );
			if ( $users ) {
				$users_connected = [ ];
				foreach ( $users as $user_connected ) {
					$users_connected[] = new UserConnected( $user_connected );
				}

				return $users_connected;
			} else {
				return $users;
			}
		} else {
			return [ ];
		}
	}

	/**
	 * Filter all users last update time is before the UPDATE_USER_TIME.
	 * Used to know if user logout by close browser, the update-date updated by ajax request timed by UPDATE_USER_TIME.
	 *
	 * @param array $users
	 *
	 * @return array
	 */
	public function filterOldUsers( array $users ) {

		$only_new_users = [ ];
		foreach ( $users as $key => $user ) {
			if ( strtotime( $user['update-date'] ) < time() - self::UPDATE_USER_TIME ) {
				continue;
			}
			$only_new_users[ $key ] = $user;
		}

		return $only_new_users;
	}
}