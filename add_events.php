<?php
error_reporting ( E_ALL );
ini_set ( 'display_errors', '1' );

//Initialize common stuff
include ('includes/initializer.php');

// Load POST data and check mysql injection attacks
$userId = $_POST ['user_id'];
$deviceId = $_POST['device_id'];
$key = $_POST ['key'];
$start = $_POST ['start'];
$end = $_POST ['end'];
$description = $_POST ['description'];
$train = 0;

$start = strtotime($start);
$end = strtotime($end);

// Load User information
$user = new User ( $sqlDataBase );
$user->LoadUser ( $_POST ['user_id']);

// Verify the user_id is who he really is, checking keys
if ($user->GetSecureKey () == $key) {
	//Add event to calendar
    $reservation = new Reservation ( $sqlDataBase );
    $reservation->CreateReservation( $_POST['device_id'],$_POST ['user_id'],strtotime($_POST ['start']),strtotime($_POST ['end']),$_POST['description'],0);

}

?>