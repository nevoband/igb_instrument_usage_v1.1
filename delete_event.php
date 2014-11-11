<?php
error_reporting ( E_ALL );
ini_set ( 'display_errors', '1' );

//Intiialize common stuff
include ('includes/initializer.php');

// Load POST data and check mysql injection attacks
$id = $_POST ['id'];
$userId = $_POST ['user_id'];
$key = $_POST ['key'];

// Load User information
$user = new User ( $sqlDataBase );
$user->LoadUser ( $userId );

// Verify the userId is who he really is checking keys
if ($user->GetSecureKey () ==$_POST ['key']) {
    $reservation = new Reservation ( $sqlDataBase );
    $reservation->LoadReservation (  $_POST ['id'] );
    $reservation->DeleteReservation();

}

?>