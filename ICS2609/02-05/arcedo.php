<!doctype html>
<html lang="en">
    <head>
        <!-- February 5, 2025 -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Feb 4</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <style>
            .ctm-bg-dark{
                background-color: #FFFFFF;
            }
        </style>
    </head>

    <body class="bg-white">
        <div class="container mt-5 p-3 bg-white border border-3 border-success rounded">
            <p class="h1 text-success text-center">Student Registration</p>
            <p class="text-center">Thank you for applying to our college. Please fill in the form below to complete the registration process for admission.</p>

            <div class="container p-4">
                <form action="output.php" method="post">
                    <div class="row">
                        <div class="col"> 
                            <label for="studNum" class="form-label w-100">Student Number</label>
                            <input type="number" name="getStudNum" id="studNum" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-5">
                            <label for="FN" class="form-label w-100">Firstname</label>
                            <input type="text" name="getFN" id="FN" class="form-control">
                        </div> 
                        <div class="col-5">
                            <label for="LN" class="form-label w-100">Lastname</label>
                            <input type="text" name="getLN" id="LN" class="form-control">
                        </div>
                        <div class="col-2">
                            <label for="MI" class="form-label w-100">MI</label>
                            <input type="text" name="getMI" id="MI" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="" class="d-block">Gender</label>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="getGender" value="Female" id="" class="form-check-input">
                                <label for="" class="form-check-label">Female</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="getGender" value="Male" id="" class="form-check-input">
                                <label for="" class="form-check-label">Male</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input type="radio" name="getGender" value="Other" id="" class="form-check-input">
                                <label for="" class="form-check-label">Other</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="col"> 
                                <label for="bDay" class="form-label w-100">Birthday</label>
                                <input type="date" name="getBDay" id="bDay" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="email" class="form-label">Email</label>
                            <input type="text" name="getEmail" id="email" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="contact" class="form-label">Contact Number</label>
                            <input type="number" name="getContact" id="contact" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label class="form-label" for="">Course</label>
                                <select class="form-control" name="getCourse" id="">
                                    <option selected disabled>-- Choose Course --</option>
                                    <option>BS in Information Technology</option>
                                    <option>BS in Computer Science</option>
                                    <option>BS in Information Systems</option>
                                    <option>BS in Industrial Technology</option>
                                </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                        <label class="form-label" for="">Year Level</label>
                                <select class="form-control" name="getYearLevel" id="">
                                    <option selected disabled>-- Choose Year Level --</option>
                                    <option>1st Year</option>
                                    <option>2nd Year</option>
                                    <option>3rd Year</option>
                                    <option>4th Year</option>
                                    <option>Irregular</option>
                                </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="info" class="form-label">Additional Information</label>
                            <textarea class="form-control" name="getInfo" id="info" rows="5"></textarea>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col text-center mt-3">
                            <input type="submit" name="register" value="Register" class="btn btn-success w-75"></input>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>
