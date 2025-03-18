<?php
include_once "functions.php";

$firstName = $_POST['firstName'] ?: NULL;
$lastName = $_POST['lastName'] ?: NULL;
$email = $_POST['email'] ?: NULL;
$contactNum = $_POST['contactNum'] ?: NULL;
$address = $_POST['address'] ?: NULL;

$dateInput = $_POST['dateInput'];
$roomPref = $_POST['roomPref'];
$daysTotal = $_POST['daysTotal'];
$guestAdult = $_POST['guestAdult'];
$guestChildren = $_POST['guestChildren'];
$guestAdditional = $_POST['guestAdditional'];
$specialRequest = $_POST['specialRequest'];



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="bootstrap\bootstrap-5.3.3-dist\css\bootstrap.css">
</head>
<body>
    <div class="text-white container bg-dark p-5">
        <div class="row bg-info p-5">
            <p class="h1 text-center">Hotel Reservation Details</p>
        </div>
    
        <div class="px-3">
            <label for="" class="text-info fw-semibold fs-5 py-3">Guest Information</label>
            <hr class="mt-0">
            <div class="row">
                <div class="col"><p class="fw-bold">Guest Name:</p></div>
                <div class="col"><?php echo nameConcat($firstName, $lastName)?></div>
            </div>
            <hr class="mt-0">
            <div class="row">
                <div class="col"><p class="fw-bold">Email</p></div>
                <div class="col"><?php echo $email?></div>
            </div>
            <hr class="mt-0">
            <div class="row">
                <div class="col"><p class="fw-bold">Contact Number</p></div>
                <div class="col"><?php echo $contactNum?></div>
            </div>
            <hr class="mt-0">
            <div class="row">
                <div class="col"><p class="fw-bold">Address</p></div>
                <div class="col"><?php echo ucwords($address)?></div>
            </div>
            <hr class="m-0">
            <label for="" class="text-info fw-semibold fs-5 py-3">Room Reservation Details</label>
            <hr class="mt-0">
            <div class="row">
                <div class="col"><p class="fw-bold">Check-in Date</p></div>
                <div class="col"><?php echo dateFormatting($dateInput)?></div>
            </div>
            <hr class="mt-0">
            <div class="row">
                <div class="col"><p class="fw-bold">No. of Guest</p></div>
                <div class="col"><b><?php echo guestTotal($guestAdult, $guestChildren, $guestAdditional)?></b><br>
                                        Adult: <?php echo $guestAdult?><br>
                                        Children: <?php echo $guestChildren?><br>
                                        Additional: <?php echo $guestAdditional?><br>
                </div>
            </div>
            <hr class="mt-3">
            <div class="row">
                <div class="col"><p class="fw-bold">Special Request</p></div>
                <div class="col"><?php echo $specialRequest?></div>
            </div>
            <hr class="mt-0">
            <div class="row">
                <div class="col"><p class="fw-bold">Room Type</p></div>
                <div class="col"><?php echo roomSplit($roomPref, 0)?></div>
            </div>
            <hr class="mt-0">
            <div class="row">
                <div class="col"><p class="fw-bold">Room Price</p></div>
                <div class="col"><?php echo "P ".number_format(roomSplit($roomPref, 1), 2)?></div>
            </div>
            <hr class="mt-0">
            <div class="row">
                <div class="col"><p class="fw-bold">Additional Guest Fee</p></div>
                <div class="col"><?php echo "P ".number_format(addFee($guestAdditional), 2)?></div>
            </div>
            <hr class="mt-0">
            <div class="row">
                <div class="col"><p class="fw-bold">Total Amount</p></div>
                <div class="col"><span class="text-success fs-5 fw-bold"><?php echo "P ".number_format(totalAmt(roomSplit($roomPref, 1), addFee($guestAdditional)), 2)?></span></div>
            </div>
        </div>
    </div>

    <script src="bootstrap\bootstrap-5.3.3-dist\js\bootstrap.js"></script>
</body>
</html>
