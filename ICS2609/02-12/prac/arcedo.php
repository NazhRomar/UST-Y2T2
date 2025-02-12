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
        <form action="arcedo.php" method="post">
            <div class="row">
                <div class="col">
                    <input type="text" name="var1" id="" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="pizza" id="">
                        <label for="">Pizza</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="spag" id="">
                        <label for="">Spaghetti</label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="donut" id="">
                        <label for="">Donut</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <label for="" class="d-block">Drink Size:</label>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="size" value="Small" class="form-check-input" id="">
                        <label for="">Small (+10)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="size" value="Medium" class="form-check-input" id="">
                        <label for="">Medium (+20)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input type="radio" name="size" value="Large" class="form-check-input" id="">
                        <label for="">Large (+30)</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <label for="">Extras</label>
                    <select name="extra" id="" class="form-control">
                        <option disabled selected>- Choose an Option -</option>
                        <option value="Ketchup">Ketchup (+10)</option>
                        <option value="MUSTAAARD">MUSTAAARD (+10)</option>
                        <option value="Mayo">Mayo (+10)</option>
                        <option value="MUSTAAAAAAAAARD">MUSTAAAAAAAAARD (+20)</option>
                    </select>
                </div>
            </div>

            <div class="rol">
                <div class="col">
                    <label for="">Payment</label>
                    <input type="text" name="payment" id="" class="form-control">
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
    $total = 0;
    $payment = $_POST['payment'] ?: 0;

    if ($age >= 18) {
        echo "Valid to vote";
    }else{
        echo "Not Valid to vote";
    }


    //pizza
    if (isset($_POST['pizza'])) {
        $price1 = 200;
        $food1 = "<br>Pizza - P".$price1;
        $total += $price1;
    } else {
        $food1 = NULL;

    }

    //spag
    if (isset($_POST['spag'])) {
        $price2 = 400;
        $food2 = "<br>Spaghetti - P".$price2;
        $total += $price2;
    } else {
        $food2 = NULL;
    }


    //donut
    if (isset($_POST['donut'])) {
        $price3 = 600;
        $food3 = "<br>Donut - P".$price3;
        $total += $price3;
    } else {
        $food3 = NULL;
    }

    $change = $payment - $total;
    
    if (isset($_POST["size"])) {
        $size = $_POST['size'];
        
        if ($size == "Small") {
            $sizeprice = 10;
            $total += $sizeprice;
        }elseif ($size == "Medium") {
            $sizeprice =  20;
            $total += $sizeprice;
        }elseif ($size == "Large") {
            $sizeprice = 30;
            $total += $sizeprice;
        } else {
            $sizeprice = 0;
        }

    } else {
        $size = NULL;
        $sizeprice = 0;
    }

    if (isset($_POST["extra"])) {
        $extra = $_POST["extra"];

        switch ($extra) {
            case 'Ketchup':
                $extraprice = 10;
                $total += $extraprice;
                break;
            case 'MUSTAAARD':
                $extraprice = 10;
                $total += $extraprice;
                break;
            case 'Mayo':
                $extraprice = 10;
                $total += $extraprice;
                break;
            case 'MUSTAAAAAAAAARD':
                $extraprice = 10;
                $total += $extraprice;
                break;
            default:
                $extraprice = 0;
                break;
        }
        // if ($extra == "Ketchup") {
        //     $extraprice = 10;
        //     $total += $extraprice;
        // } elseif ($extra == "MUSTAAARD") {
        //     $extraprice = 10;
        //     $total += $extraprice;
        // } elseif ($extra == "Mayo") {
        //     $extraprice = 10;
        //     $total += $extraprice;
        // } elseif ($extra == "MUSTAAAAAAAAARD") {
        //     $extraprice = 20;
        //     $total += $extraprice;
        // } else {
        //     $extraprice = 0;
        // }
        
    } else {
        $extraprice = NULL;
    }
    
    
    
    
    

    echo "<br>Order:".$food1.$food2.$food3;
    if ($sizeprice != 0) {
        echo "<br>Drink Size: ".$size." - P".$sizeprice;
    }
    if ($extraprice != 0) {
        echo "<br>Extra: ".$extra." - P".$extraprice;
    }
    echo "<br>-----------------<br>";
    echo "<span class = 'fw-bold'>Total: P".$total;
    echo "<br> Payment: P".$payment;
    echo "<br> Change: P".$change;
}
?>
