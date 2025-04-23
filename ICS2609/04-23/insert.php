<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body class="">
    <form action="insert.php" method="post" enctype="multipart/form-data">
    <div class="container p-5 w-50 border border-primary rounded mt-5">
    <div class="row">
        <div class="col text-center">
            <h1 class="display-1 text-primary">
                Register
            </h1>
        </div>
    </div>

    <!-- IMAGE -->
    <div class="row ">
        <div class="col">
            <img src="" src="" alt="" id="preview_img" width=200 height=200  class="img-thumbnail mx-auto d-block">
        </div>
    </div>
    <div class="row my-3">
        <div class="col">
            <input type="file" name="upload_img" id="" class="form-control w-25 mx-auto d-block" onchange="previewImage(event)">
        </div>
    </div>

    



    <div class="row">
        <div class="col">
                <div class="form-outline">
                    <input type="text" id="firstname" name="first" class="form-control" >
                    <label class="form-label" id="firstname-label" for="firstname">First name</label>
                </div>
            </div>
            <div class="col">
            <div class="form-outline">
                <input type="text" id="lastname" name="last" class="form-control " />
                <label class="form-label" for="lastname">Last name</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col">
        <input type="text" id="form6Example4"  name="add" class="form-control" />
        <label class="form-label" for="form6Example4">Address</label>
        </div>
    </div>

    <div class="row">
        <div class="col">
        <span class="form-label ">Gender</span>
        <div class="btn-group mx-5" id="btn-group-3" >
            <div class="form-check form-check-inline ">
                <input class="form-check-input" type="radio" name="Gender" id="inlineRadio1" value="Female" />
                <label class="form-check-label" for="inlineRadio1">Female</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="Gender" id="inlineRadio2" value="Male" />
                <label class="form-check-label" for="inlineRadio2">Male</label>
            </div>

            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="Gender" id="inlineRadio3" value="Others"/>
                <label class="form-check-label" for="inlineRadio3">Others</label>
            </div>
        </div>
        </div>
    </div>

    <div class="row">
        <div class="col">
        <input type="text" id="email"  name="email" class="form-control" />
        <label class="form-label" for="email">Email</label>
        </div>
    </div>

    <div class="row">
        <div class="col">
        <input type="text" id="form6Example6" name="contact" class="form-control" />
        <label class="form-label" for="form6Example6">Phone</label>
        </div>
    </div>

    <div class="row">
        <div class="col">
        <textarea class="form-control" name="addinfo" id="form6Example7" rows="4"></textarea>
        <label class="form-label" for="form6Example7">Additional information</label>
        </div>
    </div>


    <div class="row">
        <div class="col">
        <input type="text" id="form6Example6" name="username" class="form-control" />
        <label class="form-label" for="form6Example6">Username</label>
        </div>
    </div>
    <div class="row">
        <div class="col">
        <input type="password" id="form6Example6" name="password" class="form-control" />
        <label class="form-label" for="form6Example6">Password</label>
        </div>
    </div>
    <div class="row">
        <div class="col">
        <label class="form-label" for="form6Example6">Role</label>
        <select name="role" class="form-control" id="">
            <option> Admin</option>
            <option> Employee</option>
        </select>
        </div>
    </div>
    <div class="row">
        <div class="col mt-3">   
            <input type="submit"  name="sub" class="btn btn-primary btn-block w-100" value="Save Details" id=sub>
        </div>
    </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function previewImage(event) {
        // variable that will handle img tag
        var displayimg = document.getElementById("preview_img");
        
        // debug
        //console.log(displayimg);

        displayimg.src = URL.createObjectURL(event.target.files[0]);
    }
</script>
</body>
</html>

<?php
require_once "dbaseconnection.php";
// database connection
// $nraservername = "localhost"; // offline default server name
// $nrausername = "root"; // username from MySQL
// $nrapassword = "";
// $nradbase = "db_hotel";

// $nraconn = new mysqli($nraservername, $nrausername, $nrapassword, $nradbase);

// Check connection
if (!$nraconn->connect_error) {
    echo "Connection Successful";
}

if (isset($_POST['sub'])) {
    // user inputs
    $nrafirst = $_POST['first'];
    $nralast = $_POST['last'];
    $nraadd = $_POST['add'];
    $nragender = $_POST['Gender'];
    $nraemail = $_POST['email'];
    $nracontact = $_POST['contact'];
    $nraaddinfo = $_POST['addinfo'];
    $nrausername = $_POST['username'];
    $nrapassword =  md5($_POST['password']);
    $nrarole = $_POST['role'];
    $imagepath = "nra_images/".basename($_FILES['upload_img']['name']);
    move_uploaded_file($_FILES['upload_img']['tmp_name'],$imagepath);
    // var_dump($_FILES['upload_img']['name']);
// query to insert
$nrainsertsql = "INSERT INTO tbl_accountdetails(fname, lname, gender, address, email, phone_num, addinfo, username, password, account_type, img_path)
              VALUES('$nrafirst','$nralast','$nragender','$nraadd','$nraemail', $nracontact, '$nraaddinfo', '$nrausername', '$nrapassword', '$nrarole', '$imagepath')";

// converts string to sql query
$nraresult = $nraconn ->query($nrainsertsql);

if ($nraresult == TRUE) {
    //echo "Registered Successfully";
    ?>
    <script>
        Swal.fire({
            position: "center",
            icon: "success",
            title: "Registered Successfully!",
            showConfirmButton: false,
            timer: 1500
            });

    </script>
    <?php
} else {
    echo $nraconn->error;
}

// debug
// echo $conn->error;
}
?>
