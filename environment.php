<?php

/**
 * 
 * Create table for Keys if it already doesn't exist
 * 
 */
require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
global $wpdb;

$main_sql_create = 'CREATE TABLE contactFormKeys (
	LogId integer UNIQUE default(1), 
	siteKey TEXT NOT NULL,
    secretKey TEXT NOT NULL,
    emailAddress TEXT NOT NULL,
    emailPassword TEXT NOT NULL,
    hostName TEXT NOT NULL,
    portNumber TEXT NOT NULL,
    contactName TEXT NOT NULL,
	Constraint CHK_Logging_singlerow CHECK (LogId = 1)
);';

maybe_create_table( 'contactFormKeys', $main_sql_create );
$wpdb->insert('contactFormKeys', array('LogId' => 1, 'siteKey' => '', 'secretKey' => '', 'emailAddress' => '', 'emailPassword' => '', 'hostName' => '', 'portNumber' => '', 'contactName' => ''));


$result = $wpdb->get_results( "SELECT * FROM contactFormKeys WHERE LogId = 1" );

$siteKey = $result->siteKey;
$secretKey = $result->secretKey;
$emailAddress = $result->emailAddress;
$emailPassword = $result->emailPassword;
$hostName = $result->hostName;
$portNumber = $result->portNumber;
$contactName = $result->contactName;

if (isset($_POST['submit'])) {
	$siteKey2 = '';
	if (isset($_POST['siteKey'])){
		$siteKey2 = filter_var(strval($_POST['siteKey']), FILTER_SANITIZE_STRING);
	}
	$secretKey2 = '';
	if (isset($_POST['secretKey'])){
		$secretKey2 = filter_var(strval($_POST['secretKey']), FILTER_SANITIZE_STRING);
	}
	$emailAddress2 = '';
	if (isset($_POST['emailAddress'])){
		$emailAddress2 = sanitize_email(strval($_POST['emailAddress']));
	}
	$emailPassword2 = '';
	if (isset($_POST['emailPassword'])){
		$emailPassword2 = filter_var(strval($_POST['emailPassword']), FILTER_SANITIZE_STRING);
	}
	$hostName2 = '';
	if (isset($_POST['hostName'])){
		$hostName2 = filter_var(strval($_POST['hostName']), FILTER_SANITIZE_STRING);
	}
	$portNumber2 = '';
	if (isset($_POST['portNumber'])){
		$portNumber2 = filter_var(strval($_POST['portNumber']), FILTER_SANITIZE_STRING);
	}
	$contactName2 = '';
	if (isset($_POST['contactName'])){
		$contactName2 = sanitize_title(strval($_POST['contactName']));
	} 
	
	$data = [ 'siteKey' => $siteKey2, 'secretKey' => $secretKey2, 'emailAddress' => $emailAddress2, 'emailPassword' => $emailPassword2, 'hostName' => $hostName2, 'portNumber' => 	 $portNumber2, 'contactName' => $contactName2 ]; 
	$where = [ 'LogId' => 1 ];
	$wpdb->update( 'contactFormKeys', $data, $where ); // Also works in this case.
}

$_ENV["testKey"] = $result[0];
$_ENV["SITE_KEY"] = $result[0]->siteKey;
$_ENV["SECRET_KEY"] = $result[0]->secretKey;
$_ENV["MY_EMAIL"] = $result[0]->emailAddress;
$_ENV["MY_PASSWORD"] = $result[0]->emailPassword;
$_ENV["MY_HOST"] = $result[0]->hostName;
$_ENV["MY_PORT"] = $result[0]->portNumber;
$_ENV["CONTACT_NAME"] = $result[0]->contactName;

?>