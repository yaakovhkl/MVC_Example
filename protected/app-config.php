<?php

/*App Config*/

$pureRequestURI = preg_replace( '/\?.*/', '', $_SERVER['REQUEST_URI'] );

define('BASE_URL','http://' . $_SERVER['HTTP_HOST'] . $pureRequestURI);

define('BASE_PATH',$_SERVER['DOCUMENT_ROOT'] . $pureRequestURI);

define('DB_HOST_PATH',BASE_PATH.'protected/data/');

date_default_timezone_set( 'Asia/Jerusalem' );

require BASE_PATH . 'protected/includes.php';
