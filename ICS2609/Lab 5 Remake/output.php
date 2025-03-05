<?php
include "functions.php";

$firstName = ucwords($_POST["firstName"]) ?: NULL;
$lastName = ucwords($_POST["lastName"]) ?: NULL;
$email = $_POST["email"] ?: NULL;
$contactNum = $_POST["contactNum"] ?: NULL;
$address = ucwords($_POST["address"]) ?: NULL;

$date = $_POST["date"];
$roomInit = $_POST["room"];
$days = $_POST["days"]?: NULL;
$adult = $_POST["adult"]?: NULL;
$children = $_POST["children"]?: NULL;
$additional = $_POST["additional"]?: NULL;
$speReq = $_POST["speReq"];
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Lab 5 Remake</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>
    <body class="text-white">
        <div class="container bg-dark p-5">
            <div class="row bg-info p-5">
                <p class="h1 text-center">Hotel Reservation Form</p>
            </div>
            
            <div class="col px-3">
                <p class="text-info m-0 pt-3 pb-4 fs-5 fw-semibold">Guest Details</p>

                <div class="col">
                    <div class="row border-top border-bottom border-secondary py-3">
                        <div class="col">
                            <b>Guest Name:</b>
                        </div>
                        <div class="col">
                            <?php echo concatName($firstName, $lastName)?>
                        </div>
                    </div>
                    <div class="row border-bottom border-secondary py-3">
                        <div class="col">
                            <b>Email</b>
                        </div>
                        <div class="col">
                            <?php echo $email?>
                        </div>
                    </div>
                    <div class="row border-bottom border-secondary py-3">
                        <div class="col">
                            <b>Contact Number</b>
                        </div>
                        <div class="col">
                            <?php echo $contactNum?>
                        </div>
                    </div>
                    <div class="row border-bottom border-secondary py-3">
                        <div class="col">
                            <b>Address</b>
                        </div>
                        <div class="col">
                            <?php echo $address?>
                        </div>
                    </div>
                </div>
                
                <p class="text-info m-0 pt-3 pb-4 fs-5 fw-semibold">Room Reservation Details</p>
                <div class="col">
                    <div class="row border-top border-bottom border-secondary py-3">
                        <div class="col">
                            <b>Check-In Date</b>
                        </div>
                        <div class="col">
                            <?php echo formatDate($date)?>
                        </div>
                    </div>
                    <div class="row border-bottom border-secondary py-3">
                        <div class="col">
                            <b>No. of Guest</b>
                        </div>
                        <div class="col">
                            <?php echo guestCount($adult, $children, $additional)?>
                        </div>
                    </div>
                    <div class="row border-bottom border-secondary py-3">
                        <div class="col">
                            <b>Special Request</b>
                        </div>
                        <div class="col">
                            <?php echo $speReq?>
                        </div>
                    </div>
                    <div class="row border-bottom border-secondary py-3">
                        <div class="col">
                            <b>Room Type</b>
                        </div>
                        <div class="col">
                            <?php echo roomDetails($roomInit, 0)?>
                        </div>
                    </div>
                    <div class="row border-bottom border-secondary py-3">
                        <div class="col">
                            <b>Room Price</b>
                        </div>
                        <div class="col">
                            <?php echo "P",number_format(roomDetails($roomInit, 1), 2)?>
                        </div>
                    </div>
                    <div class="row border-bottom border-secondary py-3">
                        <div class="col">
                            <b>No. of days</b>
                        </div>
                        <div class="col">
                            <?php echo $days?>
                        </div>
                    </div>
                    <div class="row border-bottom border-secondary py-3">
                        <div class="col">
                            <b>Total Room Price</b>
                        </div>
                        <div class="col">
                            <?php echo "P",number_format(totalRoomPrice(roomDetails($roomInit, 1), $days), 2)?>
                        </div>
                    </div>
                    <div class="row border-bottom border-secondary py-3">
                        <div class="col">
                            <b>Additional Guest Fee(500 per person)</b>
                        </div>
                        <div class="col">
                            <?php echo "P",number_format(addGuestFee($additional), 2)?>
                        </div>
                    </div>
                    <div class="row border-bottom border-secondary py-3">
                        <div class="col">
                            <b>Total Amount</b>
                        </div>
                        <div class="col">
                            <span class="text-success fw-bold fs-5"><?php echo "P",number_format(totalAmt(totalRoomPrice(roomDetails($roomInit, 1), $days), addGuestFee($additional)), 2)?></span>
                        </div>
                    </div>
                </div>
                    
            </div>

            <div class="row bg-info p-4 mt-3">
                <p class="m-0 fs-6 text-center">@NRA2025</p>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>

