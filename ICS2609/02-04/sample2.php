<!doctype html>
<html lang="en">
    <head>
        <!-- February 4, 2025 -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Feb 4</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    </head>

    <body class="bg-primary">
        <div class="container bg-dark text-white w-75 p-4">
            <form action="sample2.php" method="post">
                <!-- <div class="row">
                    <div class="col">
                        <label for="fn" class="form-label">Fullname</label>
                        <input type="text" name="fullname" id="fn" class="form-control">
                    </div>
                </div> -->
                <div class="row">
                    <div class="col">
                        <label for="email" class="form-label">Email</label>
                        <input type="text" name="getEmail" id="email" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="getPass" id="password" class="form-control">
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control" name="getComment" id="comment" rows="3"></textarea>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="">Gender</label>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="r1" value="Male" id="" class="form-check-input">
                            <label for="" class="form-check-label">Male</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="r1" value="Female" id="" class="form-check-input">
                            <label for="" class="form-check-label">Female</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input type="radio" name="r1" value="Other" id="" class="form-check-input">
                            <label for="" class="form-check-label">Other</label>
                        </div>
                        <div class="row">
                            <div class="col">
                                <!-- Checkbox -->
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label class="form-label" for=""></label>
                                <select class="form-control" name="sel" id="">
                                    <option selected disabled>Choose Number</option>
                                    <option>1</option>
                                    <option>2</option>
                                    <option>3</option>
                                    <option>4</option>
                                    <option value="">5</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col text-center mt-3">
                        <input type="submit" name="save" value="Save" class="btn btn-warning w-75"></input>
                    </div>
                </div>
            </form>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>

<?php
//GET - puts input in ur;
//POST - doesnt;

//Button Function if clicked

if (isset($_POST['save'])) {
    $email = $_POST['getEmail'];
    $pass = $_POST['getPass'];
    $comment = $_POST['getComment'];
    $radio = $_POST['r1'];
    $drop = $_POST['sel'];

    echo "Email: ".$email;
    echo "<br>Your Password is: ".$pass;
    echo "<br>Comment: ".$comment;
    echo "<br>Gender: ".$radio;
    echo "<br>Drop: ".$drop;
}



// $var1 = $_GET['fullname'];
// echo "<br>Fullname:".$var1;


?>
