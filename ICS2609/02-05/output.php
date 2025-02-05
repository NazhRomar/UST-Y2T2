<?php
if (isset($_POST['register'])) {
    $nrastudNum = $_POST['getStudNum'];
    $nraFN = $_POST['getFN'];
    $nraLN = $_POST['getLN'];
    $nraMI = $_POST['getMI'];
    $nragender = $_POST['getGender'];
    $nrabDay = $_POST['getBDay'];
    $nraemail = $_POST['getEmail'];
    $nracontact = $_POST['getContact'];
    $nracourse = $_POST['getCourse'];
    $nrayearLevel = $_POST['getYearLevel'];
    $nrainfo = $_POST['getInfo'];
}

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body class="bg-white">
    
    <div class="container mt-5 p-3 bg-white border border-3 border-success rounded w-25 p-3">
      <div class="container p-4">
        <p class="h1 text-success text-center">Student Registration</p>
        <p class="text-center">Thank you for applying to our college. Here's the summary of your details.</p>
      
        <div class="row">
          <div class="col">
            <b>Student Number</b><br><?php echo $nrastudNum ?>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <b>Fullname</b><br><?php echo $nraLN.", ".$nraFN." ".$nraMI ?>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <b>Gender</b><br><?php echo $nragender ?>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <b>Birthday</b><br><?php echo $nrabDay ?>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <b>Email Address</b><br><?php echo $nraemail ?>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <b>Contact Number</b><br><?php echo $nracontact ?>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <b>Course</b><br><?php echo $nracourse ?>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <b>Year Level</b><br><?php echo $nrayearLevel ?>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <b>Additional Information</b><br><?php echo $nrainfo ?>
          </div>
        </div>
      </div>
    </div>
    
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
