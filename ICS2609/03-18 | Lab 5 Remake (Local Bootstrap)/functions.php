<?php
function nameConcat($firstName, $lastName){
    return ucwords($firstName)." ".ucwords($lastName);
}

function dateFormatting($dateInput){
    return date_format(date_create($dateInput), "l, F d, Y h:i A");
}

function guestTotal($guestAdult, $guestChildren, $guestAdditional){
    return $guestAdult + $guestChildren + $guestAdditional;

}

function roomSplit($roomPref, $index){
    $roomSplit = explode("%", $roomPref);
    return $roomSplit[$index];
}

function addFee($guestAdditional){
    return $guestAdditional * 500;
}

function totalAmt($roomPrice, $guestAdditional){
    return $roomPrice + $guestAdditional;
}
?>
