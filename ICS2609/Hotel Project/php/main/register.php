<?php
    require_once "../services/dbaseconnection.php";
    include_once "../services/email.php";
    session_start();

    $is_logged_in = isset($_SESSION['is_logged_in']) ? $_SESSION['is_logged_in'] : false;
    $account_type = $is_logged_in ? $_SESSION['account_type'] : null;
    $status = $is_logged_in ? $_SESSION['status'] : null;
    $fullName = $is_logged_in ? $_SESSION['fullName'] : null;
    $userName = $is_logged_in ? $_SESSION['userName'] : null;

    // if session exists and status is 'pending', redirect to verify.php
    if (isset($_SESSION['is_logged_in']) && $_SESSION['status'] === 'pending') {
        header("Location: ../main/verify.php");
        exit();
    }

    if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true && $_SESSION['status'] === 'active') {
        header("Location: home.php");
        exit();
    }


    // Process form submission
    if (isset($_POST["button_submit"])) {
        $form_userName = $_POST['input_userName'];
        $form_email = $_POST['input_email'];
        $form_password = md5($_POST['input_password']);
        $form_firstName = $_POST['input_firstName'];
        $form_lastName = $_POST['input_lastName'];
        $form_address = $_POST['input_address'];
        $form_phone = $_POST['input_phone'];

        $form_authCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $form_accountType = "customer";
        $form_status = "pending";

        // check if username is unique
        $SQL_check = "SELECT * FROM tbl_users WHERE user_name = '$form_userName'";
        $SQL_result = $SQL_connection->query($SQL_check);

        if ($SQL_result->num_rows > 0) {
            // username already exists
            $_SESSION['toast'] = [
                'type' => 'danger',
                'message' => 'Username already exists.',
                'show_progress' => true,
                'duration' => 5000,
            ];

        } else {
            // insert new user
            $SQL_insert = "INSERT INTO tbl_users (account_type, user_name, email, password, first_name, last_name, address, phone_number, auth_code, status)
                            VALUES ('$form_accountType', '$form_userName','$form_email', '$form_password', '$form_firstName', '$form_lastName', '$form_address', '$form_phone', '$form_authCode', '$form_status')";
                    
            if ($SQL_connection->query($SQL_insert) === TRUE) {
                // get user id for logs
                $user_id = $SQL_connection->insert_id;

                // Log registration
                $SQL_log = "INSERT INTO tbl_logs (user_id, user_name, account_type, action, datetime)
                            VALUES ('$user_id', '$form_userName', '$form_accountType', 'Account Created', NOW())";
                $SQL_connection->query($SQL_log);

                $_SESSION['is_logged_in'] = true;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['account_type'] = $form_accountType;
                $_SESSION['status'] = $form_status;
                $_SESSION['fullName'] = $form_firstName . " " . $form_lastName;
                $_SESSION['userName'] = $form_userName;

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => 'Authentication code has been successfully sent to your email.',
                    'show_progress' => true,
                    'duration' => 5000,
                ];
                
                send_verification($fullName, $form_email, $form_authCode);
                header("Location: verify.php");
                exit();

            } else {
                echo "Error: " . $SQL_connection->error;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Romaré Suites</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />  
    <link rel="stylesheet" href="../../css/style.css" />
</head>
<body>
    <!-- Main Navbar -->
    <section class="cx-navbar-container">
        <!-- Main Navbar -->
        <nav class="navbar navbar-expand-lg navbar-dark cx-navbar-main">
            <div class="container">
                <a class="navbar-brand cx-navbar-title" href="home.php"><span class="cx-navbar-font mx-2">Romaré Suites</span></a>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="../main/home.php">Home</a>
                    </li>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="../main/rooms.php">Rooms</a>
                    </li>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="../main/reservation.php">Reservation</a>
                    </li>
                    <?php if ($is_logged_in && ( $_SESSION['account_type'] == "admin" || $_SESSION['account_type'] == "employee")): ?>
                        <li class="nav-item cx-navbar-item">
                            <a class="nav-link" href="<?= $_SESSION['account_type'] == 'admin' ? 'admin-users.php' : 'admin-rooms.php' ?>">
                                <i class="fa-solid fa-shield me-2"></i>Operator Panel
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if ($is_logged_in): ?>
                        <li class="nav-item cx-navbar-item">
                            <a class="nav-link" href="profile.php"><i class="fa-solid fa-user me-2"></i><?= $_SESSION['userName'] ?></a>
                        </li>
                        <li class="nav-item cx-navbar-item">
                            <a class="nav-link" href="login.php?logout=true"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item cx-navbar-item">
                            <a class="nav-link" href="login.php">
                                <i class="fa-solid fa-right-to-bracket me-2"></i>Login
                            </a>
                        </li>
                        <li class="nav-item cx-navbar-item">
                            <a class="nav-link active" href="register.php">
                                <i class="fa-solid fa-user-plus me-2"></i>Register
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </section>

    <!-- Register - Admin -->
    <section class="d-flex align-items-center justify-content-center cx-poppins" style="min-height: calc(100vh - 56px);">
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <div class="card shadow rounded-4">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <h3 class="fw-bold"><i class="fa-solid fa-user-plus me-2"></i>Create an Account</h3>
                                <p>Already have an account? <a href="login.php">Log in</a></p>
                            </div>
                            <form action="register.php" method="post">
                                <div class="mb-2">
                                    <span class="text-muted fs-6">Account Credentials</span>
                                </div>
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="input_userName" id="id_userName" placeholder="Username" required>
                                        <label for="id_userName" class="form-label">Username</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" name="input_email" id="id_email" placeholder="Email" required>
                                        <label for="id_email" class="form-label">Email</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" name="input_password" id="id_password" placeholder="Password" required>
                                        <label for="id_password" class="form-label">Password</label>
                                    </div>
                                </div>
                                <div class="mb-2">
                                    <span class="text-muted fs-6">Personal Information</span>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="input_firstName" id="id_firstName" placeholder="First Name" required>
                                            <label for="id_firstName" class="form-label">First Name</label>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="input_lastName" id="id_lastName"
                                                placeholder="id_Last Name" required>
                                            <label for="lastName" class="form-label">Last Name</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="input_address" id="id_address" placeholder="Address" required>
                                        <label for="id_address" class="form-label">Address</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="input_phone" id="id_phone" placeholder="Phone Number" required>
                                        <label for="id_phone" class="form-label">Phone Number</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-grid">
                                        <button class="btn btn-primary btn-lg fs-6" type="submit" name="button_submit">Register Now</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<?php
    include_once "../services/toast.php";
?>
</body>
</html>