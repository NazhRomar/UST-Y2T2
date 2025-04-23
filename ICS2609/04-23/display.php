<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body class="">

  <div class="container w-100 p-4">
  
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
    // database connections
    require_once "dbaseconnection.php";

    // debug connection
    if (!$nraconn->connect_error) {
        echo "Connection Successful";
    }


    // FILTER SEARCH
    if (isset($_POST['search'])) {
        $nrainput = $_POST['searchInput'];
        $nraselectsql = "SELECT * from tbl_accountdetails where fname like '%$nrainput%' OR lname like '%$nrainput%' "; 
    } 

    // DISPLAY ALL FIRST
    else{
    $nraselectsql = "SELECT * from tbl_accountdetails"; // eto gagalawin pag search (order by account_id etc.)
    $nrasearchInput = NULL;
}

    // convert query string to sql syntax and return array values
    $nraresult = $nraconn ->query($nraselectsql);

    // Display if a table has any value
    // num_rows - return the number of records inside the table
    if ($nraresult->num_rows > 0) {
        ?>
        <table class="table table-primary">
            <tr>
                <th>Account ID</th>
                <th>Image</th>
                <th>First Name</th>
                <th>Last Name </th>
                <th>Gender </th>
                <th>Address </th>
                <th>Email</th>
                <th>Phone number</th>
                <th>Additional Informations</th>
                <th>Account Type</th>
                <th>Username</th>
                <th>Password</th>
            </tr>

            <?php
                // ACCOUNT ID
                foreach ($nraresult as $nrafieldata) {
                    echo "<tr>";
                    echo "<td>".$nrafieldata['account_id']."</td>";
                    echo "<td><img src='".$nrafieldata['img_path']."' width=100 height=100></td>";
                    echo "<td>".$nrafieldata['fname']."</td>";
                    echo "<td>".$nrafieldata['lname']."</td>";
                    echo "<td>".$nrafieldata['gender']."</td>";
                    echo "<td>".$nrafieldata['address']."</td>";
                    echo "<td>".$nrafieldata['email']."</td>";
                    echo "<td>".$nrafieldata['phone_num']."</td>";
                    echo "<td>".$nrafieldata['addinfo']."</td>";
                    echo "<td>".$nrafieldata['account_type']."</td>";
                    echo "<td>".$nrafieldata['username']."</td>";
                    echo "<td>".$nrafieldata['password']."</td>";
                    echo "</tr>";
                }
            ?>

        </table>

        <?php

    } else {    
        ?>   
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>Holy guacamole!</strong> No records found!
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
        <?php
    }
    
?>

  </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
