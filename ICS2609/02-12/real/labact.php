<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Arcedo 2-12</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>
    <div class="container p-5 mt-3">
    <form action="output.php" method="post">
        <div class="row">
            <div class="col-4 bg-warning d-flex justify-content-center align-items-center p-5">
                <div class="h1 text-light text-center">Meralco Biling Form</div>
            </div>
            
            <div class="col-8 bg-light p-5">
                <div class="row mb-2">
                    <label for="">Customer Name</label>
                    <div class="row">
                        <div class="col-5">
                            <input type="text" name="LN" id="LN" class="form-control">
                            <label for="LN" class="text-secondary">Last Name</label>
                        </div>
                        <div class="col-5">
                            <input type="text" name="FN" id="FN" class="form-control">
                            <label for="FN" class="text-secondary">First Name</label>
                        </div>
                        <div class="col-2">
                            <input type="text" name="MI" id="MI" class="form-control">
                            <label for="MI" class="text-secondary">Middle Initial</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-2">
                    <label for="">Address</label>
                    <div class="row">
                        <div class="col-8">
                            <input type="text" name="add1" id="add1" class="form-control">
                            <label for="add1" class="text-secondary">Building number, Street, and Barangay</label>
                        </div>
                        <div class="col-4">
                            <input type="text" name="add2" id="add2" class="form-control">
                            <label for="add2" class="text-secondary">City</label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-5">
                            <input type="text" name="add3" id="add3" class="form-control">
                            <label for="add3" class="text-secondary">Province</label>
                        </div>
                        <div class="col-5">
                            <input type="text" name="add4" id="add4" class="form-control">
                            <label for="add4" class="text-secondary">Country</label>
                        </div>
                        <div class="col">
                            <input type="text" name="add5" id="add5" class="form-control">
                            <label for="add5" class="text-secondary">Zip</label>
                        </div>
                    </div>
                </div>

                <div class="row mb-2">
                    <label for="">No. of Kilowatts</label>
                    <div class="row">
                        <div class="col">
                            <input type="text" name="kw" id="kw" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="container col-6 bg-warning rounded p-3">
                        <p class="h3 text-light text-center">Subscription Type</p>
                        <div class="col">
                            <div class="form-check">
                                <input type="radio" name="size" value="Residential" class="form-check-input" id="">
                                <label for="" class="text-light">Residential (Php 2.75 per KW)</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="size" value="Industrial" class="form-check-input" id="">
                                <label for="" class="text-light">Industrial (Php 3.75 per KW)</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="size" value="Commercial" class="form-check-input" id="">
                                <label for="" class="text-light">Commercial (Php 4.25 per KW)</label>
                            </div>
                        </div>


                    </div>
                    <div class="container col-5 bg-warning rounded p-3">
                        <p class="h3 text-light text-center">Other Charges</p>
                        <div class="row">
                            <div class="col">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" value="Disconnection (Php 500.00)" name="discon" id="">
                                    <label for="" class="text-light">Disconnection (Php 500.00)</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" value="Reconnection (Php 600.00)" name="recon" id="">
                                    <label for="" class="text-light">Reconnection (Php 600.00)</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" value="Late Payment (30% of the Energy Charge)" name="latePay" id="">
                                    <label for="" class="text-light">Late Payment (30% of the Energy Charge)</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" value="Additional Electricity Meter (Php 750.00)" name="MeterAdd" id="">
                                    <label for="" class="text-light">Additional Electricity Meter (Php 750.00)</label>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" value="Electricity Meter Transfer (Php 1,500.00)" name="MeterTrans" id="">
                                    <label for="" class="text-light">Electricity Meter Transfer (Php 1,500.00)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col text-center mt-3">
                        <input type="submit" value="Compute" name="compute" class="text-light btn btn-warning w-50">
                    </div>
                </div>

            </div>
        </div>
    </div>
        </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>

