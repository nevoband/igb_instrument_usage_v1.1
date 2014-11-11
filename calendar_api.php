<?php


include('includes/initializer.php');
//error_log($_POST['action']." ".$_POST['user_id']." ".$_POST['key'],0);
if (isset($_POST['action']) && isset($_POST['user_id']) && isset($_POST['key'])) {
    //Load pages index
    $page = new Pages($sqlDataBase);

    //Load User information for user_id
    $user = new User ($sqlDataBase);
    $user->LoadUser($_POST['user_id']);

    //Verify the user is who is is saying he is by comparing the user key from the database to key given to the api
    if ($user->GetSecureKey() == $_POST ['key']) {

        //Create reservation object and load reservation info if we are given a reservation id
        $reservation = new Reservation ($sqlDataBase);
        if (isset($_POST['id'])) {
            $reservation->LoadReservation($_POST ['id']);
        }

        //For debugging purposes
        $POST_ARRAY = print_r($_POST, true);
        //Verify the user has permission to perform the operation
        $userAccessLevel = $accessControl->GetPermissionLevel($user->GetUserId(), AccessControl::RESOURCE_PAGE, $page->GetPageId("Calendar"));
        if ($userAccessLevel == AccessControl::PERM_ADMIN
            || $user->GetUserId() == $reservation->getUserId()
            || ($_POST['action']='get_events'
                && $userAccessLevel == AccessControl::PERM_ALLOW ))
        {
            switch ($_POST['action']) {
                case 'get_events':
                    error_log("get events: " . $POST_ARRAY, 0);
                    echo $reservation->JsonEventsRange($_POST['start'], $_POST['end'], $_POST ['user_id'], $_POST ['device_id']);
                    break;
                case 'add_event':
                    error_log("add switch statement: " . $POST_ARRAY, 0);
                    $reservation->CreateReservation($_POST['device_id'], $_POST ['user_id'], $_POST ['start'], $_POST ['end'], $_POST['description'], 0);
                    break;
                case 'delete_event':
                    error_log("delete events: " . $POST_ARRAY, 0);
                    $reservation->DeleteReservation();
                    break;
                case 'update_event_time':
                    error_log("update events time: " . $POST_ARRAY, 0);
                    $reservation->setDeviceId($_POST ['device_id']);
                    $reservation->setStart($_POST ['start']);
                    $reservation->setStop($_POST ['end']);
                    $reservation->UpdateReservation();
                    break;
                case 'update_event_info':
                    error_log("update events info: " . $POST_ARRAY, 0);
                    if ($reservation->getReservationId() == 0) {
                        $reservation->CreateReservation($_POST['device_id'], $_POST ['user_id'], $_POST ['start'], $_POST ['end'], $_POST['description'], 0);
                    }
                    $reservation->setDescription($_POST['description']);
                    $reservation->UpdateReservation();
                    break;
            }
        }
    }
}


?>