<?php

function concatName($firstName, $lastName){
    return $firstName." ".$lastName;
}

function formatDate($date){
    if (!empty($date)) {
        return date_format(date_create($date), "l, F d, Y h:i A");
    } else {
        return "Not Provided";
    }
}

function roomDetails($roomInit, $index){
    if (isset($roomInit)) {
        $room = explode("|", $roomInit);
        return $room[$index];
    }
}

function guestCount($adult, $children, $additional){
    return "<b>".$adult + $children + $additional."</b><br>Adult: ".$adult."<br>Children: ".$children."<br>Additional Guest: ".$additional;
}

function totalRoomPrice($roomPrice, $days){
    return $roomPrice * $days;
}

function addGuestFee($additional){
    return $additional * 500;
}

function totalAmt($totalRoomPrice, $addGuestFee){
    return $totalRoomPrice + $addGuestFee;

}
?>
