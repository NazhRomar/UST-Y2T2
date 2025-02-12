<?php
    if (isset($_POST['compute'])) {
        $nraln =  $_POST['LN'];
        $nrafn = $_POST['FN'];
        $nrami = $_POST['MI'];
        $nraadd1 = $_POST['add1'];
        $nraadd2 = $_POST['add2'];
        $nraadd3 = $_POST['add3'];
        $nraadd4 = $_POST['add4'];
        $nraadd5 = $_POST['add5'];
        $nrakw = $_POST['kw'];
        $nrasubRate = 0;
        $nracharges1 = NULL;
        $nracharges2 = NULL;
        $nracharges3 = NULL;
        $nracharges4 = NULL;
        $nracharges5 = NULL;
        
        $nrachargesMath = NULL;
        $nrachargesList = NULL;
    
        if (isset($_POST['size'])) {
        $nrasize = $_POST['size'];

        switch ($nrasize) {
            case 'Residential':
                $nrasubType = "Residential";
                $nrasubRate = "2.75";
                break;
            case 'Industrial':
                $nrasubType = "Industrial";
                $nrasubRate = "3.75";
                break;
            case 'Commercial':
                $nrasubType = "Commercial";
                $nrasubRate = "4.25";
                break;
            default:
                $nrasubType = "NULL";
                $nrasubRate = "0";
                break;
            }
        }
        $nraenergyChargeInit = $nrakw * $nrasubRate;
        $nraenergyCharge = $nraenergyChargeInit." kw";
    
        if (isset($_POST["recon"])) {
            $nracharges1 = "Disconnection (Php 500.00)<br>";
            $nrachargesMath += 500;
        }
        if (isset($_POST["discon"])) {
            $nracharges2 = 'Reconnection (Php 600.00)<br>';
            $nrachargesMath += 600;
        }
        if (isset($_POST["latePay"])) {
            $nracharges3 = 'Late Payment (30% of the Energy Charge)<br>';
            $nrachargesMath += $nraenergyCharge * (10/3);
        }
        if (isset($_POST["meterAdd"])) {
            $nracharges4 ='Additional Electricity Meter (Php 750.00)<br>';
            $nrachargesMath += 750;
        }
        if (isset($_POST["meterTrans"])) {
            $nracharges5 = 'Electricity Meter Transfer (Php 1,500.00)<br>';
            $nrachargesMath += 1500;
        }

    



       $nrafullN = $nraln.", ".$nrafn." ".$nrami.".";
       $nrafullAdd = $nraadd1.", ".$nraadd2.", ".$nraadd3.", ".$nraadd4.", ".$nraadd5;
       
       
       $nratotalEnergy = $nrachargesMath + $nraenergyChargeInit;

    }
?>

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
            <div class="col-4 bg-warning align-items-center text-center d-flex p-5">
                <div class="h1 text-light text-center">Meralco Biling Form</div>
            </div>
            
            <div class="col-8 bg-light p-5">
                <p>Customer Name: <?php echo $nrafullN ?></p>
                <p>Address: <?php echo $nrafullAdd?></p>
                <p>No. of Kilowatt <?php echo $nrakw ?></p>
                <p>Subscription Type: <?php echo $nrasubType?></p>
                <p>Rate of Subscription: <?php echo $nrasubRate?></p>
                <p>Energy Charge: Php<?php echo $nraenergyCharge?></p>
                <p>Other Charges: <?php echo $nracharges1.$nracharges2.$nracharges3.$nracharges4.$nracharges5?></p>
                <p>Total Other Charges/Fees<?php $nrachargesMath?></p>
                <p>Total Electricity Bill<?php $nratotalEnergy?></p>
            </div>
        </div>
    </div>
        </form>
        



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
