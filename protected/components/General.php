<?php
/**
 * General class to general functions.
 */
class General {

	const DATE_FORMAT = 'd-m-Y H:i:s';

	/**
	 * @return bool|string current date
	 */
	public static function getDate() {

		return date( self::DATE_FORMAT );
	}

	/**
	 * @return string client ip address
	 */
	public static function getIP() {

		$ip = '---';
		if ( ! empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}

		return $ip;
	}
}