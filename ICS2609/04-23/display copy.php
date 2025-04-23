<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body class="">

  <div class="bg-info container w-100 p-4">
  
  <form action="display.php" method="post">
    <div class="row">
        <div class="col">
        <input type="search" name="searchInput" id="" placeholder="Search" class="form-control w-25">
        </div>
    </div>

    <div class="row">
        <div class="col">
        <input type="submit" value="Search" name="search" class="btn btn-primary">
        </div>
    </div>
  </form>

<?php
    // database
    require_once "dbaseconnection.php";

    // debug
    if (!$nraconn->connect_error) {
        echo "Connection Successful";
    }


    // filter
    if (isset($_POST['search'])) {
        $nrainput = $_POST['searchInput'];
        $nraselectsql = "SELECT * from tbl_accountdetails where fname like '%$nrainput%' OR lname like '%$nrainput%' "; 
    } 

    // default
    else{
    $nraselectsql = "SELECT * from tbl_accountdetails"; 
    $nrasearchInput = NULL;
}

    // convert query string to sql syntax and return array values
    $nraresult = $nraconn ->query($nraselectsql);

    // Display if a table has any value
    // num_rows = number of records inside the table
    if ($nraresult->num_rows > 0) {
        ?>
        <div class="row">
         <?php
         foreach ($nraresult as $nraindex => $nrafielddata) {
         ?>
             <div class="col border border-dark rounded p-1 m-2 text-center bg-white">
                 <div class="row">
                     <div class="col">
                         <img src="<?php echo $nrafielddata['img_path'] ?>" width=200 height=200>
                     </div>
                 </div>
                 <div class="row">
                    <div class="col">
                        <h3><?php echo $nrafielddata ['fname']?></h3>
                    </div>
                 </div>
                 <div class="row">
                    <div class="col">
                        <p><?php echo $nrafielddata ['email']?></p>
                    </div>
                 </div>
             </div>
        
         <?php
        //  create new row
        if (($nraindex+1) % 2 == 0) {
            echo "</div><div class=row>";
        }
         }
         
         
         ?>
        </div>
 
         <?php

    } else {    
        ?>   
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>No records found!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
        <?php
    }
    
?>

  </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
