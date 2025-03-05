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
                <p class="m-0 fs-5 text-center">Fill up this form to reserve</p>
            </div>

            <form action="output.php" method="post">
                <p class="text-info m-0 pt-3 pb-4 fs-5 fw-semibold">Guest Information</p>
                
                <div class="col px-3">
                    <label for="" class="">Fullname</label>
                        <div class="row">
                            <div class="col">
                                <input type="text" name="firstName" id="" class="form-control" placeholder="Firstname">
                            </div>
                            <div class="col">
                                <input type="text" name="lastName" id="" class="form-control" placeholder="Lastname">
                            </div>
                        </div>
                    <label for="" class="pt-4">Email Address</label>
                    <input type="email" name="email" id="" class="form-control" placeholder="Email">

                    <label for="" class="pt-4">Contact Number</label>
                    <input type="number" name="contactNum" id="" class="form-control" placeholder="ex. 09348430123">

                    <label for="" class="pt-4">Address</label>
                    <input type="text" name="address" id="" class="form-control" placeholder="Street, City, Province, Country">
                </div>

                <p class="text-info m-0 pt-4 fs-5 fw-semibold">Room Reservation</p>
                <div class="col px-3">
                    <label for="" class="pt-4">Check-in date</label>
                    <input type="datetime-local" name="date" id="" class="form-control">

                    <label for="" class="pt-4 d-block">Room Preferences</label>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="room" id="" class="form-check-input" value="Standard|1500">
                        <label for="">Standard (P1,500)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="room" id="" class="form-check-input" value="Deluxe|3000">
                        <label for="">Deluxe (P3,000)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="room" id="" class="form-check-input" value="Suite|4500">
                        <label for="">Suite (P4,500)</label>
                    </div>
                    <label for="" class="d-block pt-4">No. of days</label>
                    <input type="number" name="days" id="" class="form-control" placeholder="ex. 09348430123">
                    <label for="" class="pt-4">No.of Guest</label>
                    <div class="row">
                        <div class="col">
                            <label for="">Adult</label>
                            <input type="number" name="adult" id="" class="form-control" min="0" max="3" placeholder="Max: 3">
                        </div>
                        <div class="col">
                            <label for="">Children</label>
                            <input type="number" name="children" id="" class="form-control" min="0" max="2" placeholder="Max: 2">
                        </div>
                    </div>
                    <label for="" class="pt-4">Additional No. of Guest</label>
                    <input type="number" name="additional" id="" class="form-control" min="0">
                    <label for="" class="pt-4">Special Request</label>
                    <textarea name="speReq" id="" class="form-control" rows="5" style="resize:none"></textarea>
                    <div class="col text-center pt-5 pb-4">
                        <button type="submit" class="btn btn-info p-3 w-25">Reserve</button>
                    </div>
                </div>
            </form>


            <div class="row bg-info p-4">
                <p class="m-0 fs-6 text-center">@NRA2025</p>
            </div>
        </div>


        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>
