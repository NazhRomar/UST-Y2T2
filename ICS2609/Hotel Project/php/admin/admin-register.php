<?php
require_once "../services/dbaseconnection.php";
session_start();

$is_logged_in = isset($_SESSION['is_logged_in']) ? $_SESSION['is_logged_in'] : false;
$account_type = $is_logged_in ? $_SESSION['account_type'] : null;
$status = $is_logged_in ? $_SESSION['status'] : null;
$fullName = $is_logged_in ? $_SESSION['fullName'] : null;
$userName = $is_logged_in ? $_SESSION['userName'] : null;

// if session exists and status is 'pending', redirect to verify.php
if ($is_logged_in && $status === 'pending') {
    header("Location: ../main/verify.php");
    exit();
}

// Restrict access to Admin only
if (!$is_logged_in || $account_type !== 'admin') {
    header("Location: ../main/home.php");
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
    $form_accountType = $_POST['input_accountType'];

    $form_status = "active";

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
                        VALUES ('$form_accountType', '$form_userName','$form_email', '$form_password', '$form_firstName', '$form_lastName', '$form_address', '$form_phone', NULL, '$form_status')";
                
        if ($SQL_connection->query($SQL_insert) === TRUE) {
            // get user id for logs
            $user_id = $SQL_connection->insert_id;

            // Log registration
            $SQL_log = "INSERT INTO tbl_logs (user_id, user_name, account_type, action, datetime)
                        VALUES ('$user_id', '$form_userName', '$form_accountType', '(Operator) Account Created', NOW())";
            $SQL_connection->query($SQL_log);
            
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Username registered successfully.',
                'show_progress' => true,
                'duration' => 5000,
            ];

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
        <nav class="navbar navbar-expand-lg navbar-dark cx-navbar-admin">
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
                            <a class="nav-link active" href="<?= $_SESSION['account_type'] == 'admin' ? 'admin-users.php' : 'admin-rooms.php' ?>">
                                <i class="fa-solid fa-shield me-2"></i>Operator Panel
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if ($is_logged_in): ?>
                        <li class="nav-item cx-navbar-item">
                            <a class="nav-link" href="../main/profile.php"><i class="fa-solid fa-user me-2"></i><?= $_SESSION['userName'] ?></a>
                        </li>
                        <li class="nav-item cx-navbar-item">
                            <a class="nav-link" href="../main/login.php?logout=true"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item cx-navbar-item">
                            <a class="nav-link" href="../main/login.php"><i class="fa-solid fa-right-to-bracket me-2"></i>Sign in</h3>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>

        <!-- Admin Navbar -->
        <nav class="navbar navbar-expand-md navbar-dark cx-navbar-admin border-top cx-navbar-admin-sub">
            <div class="container">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item d-flex ms-2 align-items-center">
                        <i class="fa-solid fa-table me-2"></i>
                    </li>
                    <?php if ($is_logged_in &&  $_SESSION['account_type'] == "admin"): ?>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="admin-users.php">Users</a>
                    </li>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="admin-logs.php">Logs</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="admin-rooms.php">Rooms</a>
                    </li>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="admin-reservations.php">Reservations</a>
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <i class="fa-solid fa-pen-to-square ms-3 me-1"></i>
                    </li>
                    <?php if ($is_logged_in &&  $_SESSION['account_type'] == "admin"): ?>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link active" href="admin-register.php">Register</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="admin-reserve.php">Reserve</a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item cx-navbar-item">
                        <a class="cx-navbar-font cx-navbar-button nav-link"><i class="fa-solid fa-image-portrait me-2"></i><?= $_SESSION['account_type'] ?></a>
                    </li>
                    <li class="nav-item cx-navbar-item">
                        <span class="cx-navbar-font cx-navbar-button nav-link" style="white-space: nowrap;">
                            <i class="fa-solid fa-calendar me-2"></i><?= date("Y-m-d") ?>
                        </span>
                    </li>
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
                                <h3 class="fw-bold">Create an Account</h3>
                            </div>
                            <form action="admin-register.php" method="post">
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
                                <div class="mb-2">
                                    <span class="text-muted fs-6">Admin Fields</span>
                                </div>
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" name="input_accountType" id="id_accountType" required>
                                                <option value="" selected disabled hidden>Select an account type</option>
                                                <option value="customer">Customer</option>
                                                <option value="employee">Employee</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                            <label for="id_accountType">Account Type</label>
                                        </div>

                                        <script>
                                            (function () {
                                                const select = document.getElementById('id_accountType');
                                                
                                                select.style.color = '#6c757d';
                                                
                                                const style = document.createElement('style');
                                                style.textContent = `
                                                    #id_accountType option:not([disabled]) {
                                                        color: black !important;
                                                    }
                                                `;
                                                document.head.appendChild(style);
                                                
                                                select.addEventListener('change', function() {
                                                    if (this.selectedIndex !== 0) {
                                                        this.style.color = 'black';
                                                    } else {
                                                        this.style.color = '#6c757d';
                                                    }
                                                });
                                            })();
                                        </script>
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