<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>
    <div class="container bg-dark text-white p-5 w-75">
        <form action="condition.php" method="post">
            <div class="row">
                <div class="col">
                    <input type="text" name="var1" id="" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-check">
                        <input type="checkbox" name="pizza" id="">
                        <label for=""> Pizza</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="spag" id="">
                        <label for=""> Spaghetti</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="donut" id="">
                        <label for=""> Donut</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col text-center mt-3">
                    <input type="submit" value="Save" name="smb" class="btn btn-primary w-50">
                </div>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>

<?php
//button function
if (isset($_POST['smb'])) {
    
    $age = $_POST['var1'];



    if ($age >= 18) {
        echo "Valid to vote";
    }else{
        echo "Not Valid to vote";
    }


    //pizza
    if (isset($_POST['pizza'])) {
        $food1 = "Pizza";
    } else {
        $food1 = NULL;
    }

       //spag
    if (isset($_POST['spag'])) {
        $food2 = "Spaghetti";
    } else {
        $food2 = NULL;
    }



    echo "<br>Order:</br>".$food1."<br>".$food2;
    


    


}


?>
