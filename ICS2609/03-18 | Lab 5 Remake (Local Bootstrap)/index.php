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
            <p class="h1 text-center">Hotel Reservation Form</p>
            <p class="m-0 fs-5 text-center">Fill up this form to reserve.</p>
        </div>

        <form action="output.php" method="post">
            <label for="" class="text-info fw-semibold fs-5 py-3">Guest Information</label>
        
            <div class="col px-3">
                <label for="" class="pt-3">Fullname</label>
                <div class="row">
                    <div class="col">
                        <input type="text" name="firstName" id="" class="form-control" placeholder="Firstname">
                    </div>
                    <div class="col">
                        <input type="text" name="lastName" id="" class="form-control" placeholder="Lastname">
                    </div>
                </div>
                <label for="" class="pt-3">Email</label>
                <input type="email" name="email" id="" class="form-control" placeholder="Email">

                <label for="" class="pt-3">Contact Number</label>
                <input type="number" name="contactNum" id="" class="form-control" placeholder="ex: 09001112222">

                <label for="" class="pt-3">Address</label>
                <input type="text" name="address" id="" class="form-control" placeholder="Street, City, Province, Country">
            </div>

            <label for="" class="text-info fw-semibold fs-5 py-4">Room Reservation</label>
            <div class="col px-3">
                <label for="" class="">Check-in Date</label>
                <input type="datetime-local" name="dateInput" id="" class="form-control">

                <label for="" class="pt-3 d-block">Room Preference</label>
                <div class="form-check form-check-inline">
                    <label for="">Standard (P1500.00)</label>
                    <input type="radio" name="roomPref" id="" value="Standard%1500" class="form-check-input">
                </div>
                <div class="form-check form-check-inline">
                    <label for="">Deluxe (P3000.00)</label>
                    <input type="radio" name="roomPref" id="" value="Deluxe%3000" class="form-check-input">
                </div>
                <div class="form-check form-check-inline">
                    <label for="">Suite (P4500.00)</label>
                    <input type="radio" name="roomPref" id="" value="Suite%4500" class="form-check-input">
                </div>

                <label for="" class="pt-3 d-block">No. of days</label>
                <input type="number" name="daysTotal" id="" class="form-control" placeholder="ex: 5">

                <label for="" class="pt-3">No. of Guest</label>
                <div class="row">
                    <div class="col">
                        <label for="">Adult</label>
                        <input type="number" name="guestAdult" id="" class="form-control" placeholder="ex: 5">
                    </div>
                    <div class="col">
                        <label for="">Children</label>
                        <input type="number" name="guestChildren" id="" class="form-control" placeholder="ex: 5">
                    </div>
                </div>
                
                <label for="" class="pt-3">Additional No. of Guest</label>
                <input type="number" name="guestAdditional" id="" class="form-control">

                <label for="" class="pt-3">Special Request</label>
                <textarea name="specialRequest" id="" class="form-control" rows="5" style="resize: none"></textarea>
                
                <div class="col text-center pt-5">
                    <button type="submit" class="btn btn-info px-5 py-3 w-25">Reserve</button>
                </div>
            </div>
        </form>
    </div>
    <script src="bootstrap\bootstrap-5.3.3-dist\js\bootstrap.js"></script>
</body>
</html>
