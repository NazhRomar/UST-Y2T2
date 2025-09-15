<?php
require_once "../services/dbaseconnection.php";
session_start();

$is_logged_in = isset($_SESSION['is_logged_in']) ? $_SESSION['is_logged_in'] : false;

// Handle logout
if (isset($_GET['logout'])) {
    if ($is_logged_in && isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
        $user_id = $_SESSION['user_id'];
        $userName = $is_logged_in ? $_SESSION['userName'] : '';
        $account_type = isset($_SESSION['account_type']) ? $_SESSION['account_type'] : '';
        
        $check_user = "SELECT user_id FROM tbl_users WHERE user_id = '$user_id'";
        $SQL_result = $SQL_connection->query($check_user);
        
        if ($SQL_result && $SQL_result->num_rows > 0) {
            $SQL_log = "INSERT INTO tbl_logs (user_id, user_name, account_type, action, datetime)
                        VALUES ('$user_id', '$userName', '$account_type', 'Logged Out', NOW())";
            $SQL_connection->query($SQL_log);
        }
    }

    $toast = [
        'type' => 'success',
        'message' => 'Logged out successfully.',
        'show_progress' => true,
        'duration' => 5000,
    ];

    session_unset();
    session_destroy();

    session_start();
    $_SESSION['toast'] = $toast;

    header("Location: home.php");
    exit();
}

// Handle login form submission
if (isset($_POST['login_button'])) {
    $userName = $_POST['input_userName'];
    $password = md5($_POST['input_password']);

    $SQL_query = "SELECT * FROM tbl_users WHERE user_name = '$userName' AND password = '$password'";
    $SQL_result = $SQL_connection->query($SQL_query);

    if ($SQL_result && $SQL_result->num_rows === 1) {
        $SQL_fields = $SQL_result->fetch_assoc();

        // Check status BEFORE setting session variables
        $status = $SQL_fields['status'];
        
        if ($status === 'active') {
            // Only set session variables for active accounts
            $_SESSION['is_logged_in'] = true;
            $_SESSION['user_id'] = $SQL_fields['user_id'];
            $_SESSION['account_type'] = $SQL_fields['account_type'];
            $_SESSION['status'] = $SQL_fields['status'];
            $_SESSION['fullName'] = $SQL_fields['first_name'] . " " . $SQL_fields['last_name'];
            $_SESSION['userName'] = $SQL_fields['user_name'];

            // Log successful login
            $user_id = $SQL_fields['user_id'];
            $account_type = $SQL_fields['account_type'];
            $SQL_log = "INSERT INTO tbl_logs (user_id, user_name, account_type, action, datetime)
                        VALUES ('$user_id', '$userName', '$account_type', 'Logged In', NOW())";
            $SQL_connection->query($SQL_log);
            
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'Logged in successfully.',
                'show_progress' => true,
                'duration' => 5000,
            ];

            header("Location: home.php");
            exit();
            
        } elseif ($status === 'pending') {
            $_SESSION['is_logged_in'] = true;
            $_SESSION['user_id'] = $SQL_fields['user_id'];
            $_SESSION['account_type'] = $SQL_fields['account_type'];
            $_SESSION['status'] = $SQL_fields['status'];
            $_SESSION['fullName'] = $SQL_fields['first_name'] . " " . $SQL_fields['last_name'];
            $_SESSION['userName'] = $SQL_fields['user_name'];
            
            $_SESSION['toast'] = [
                'type' => 'alert',
                'message' => 'Please verify your account first.',
                'show_progress' => true,
                'duration' => 5000,
            ];
            header("Location: verify.php");
            exit();
            
        } elseif ($status === 'inactive') {
            $_SESSION['toast'] = [
                'type' => 'alert',
                'message' => 'Login failed. This account is currently inactive and cannot be used to log in.',
                'show_progress' => true,
                'duration' => 5000,
            ];
            
        } else {
            // Unknown status
            $_SESSION['toast'] = [
                'type' => 'danger',
                'message' => 'Unable to login. Account status unknown.',
                'show_progress' => true,
                'duration' => 5000,
            ];
        }
    } else {
        // Invalid credentials
        $_SESSION['toast'] = [
            'type' => 'danger',
            'message' => 'Invalid username or password.',
            'show_progress' => true,
            'duration' => 5000,
        ];
    }
}

// prevent user accessing login.php again when they are already logged in
if (isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true) {
    header("Location: home.php");
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Romaré Suites</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../css/style.css">
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark cx-navbar-main sticky-top">
        <div class="container">
            <a class="navbar-brand cx-navbar-title" href="home.php">
                <span class="cx-navbar-font mx-2">Romaré Suites</span>
            </a>

            <ul class="navbar-nav me-auto">
                <li class="nav-item cx-navbar-item">
                    <a class="nav-link" href="home.php">Home</a>
                </li>
                <li class="nav-item cx-navbar-item">
                    <a class="nav-link" href="rooms.php">Rooms</a>
                </li>
                <li class="nav-item cx-navbar-item">
                    <a class="nav-link" href="reservation.php">Reservation</a>
                </li>
                <?php if ($is_logged_in && ($account_type == "admin" || $account_type == "employee")): ?>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="admin-users.php.php"><i class="fa-solid fa-shield me-2"></i>Admin Panel</a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if ($is_logged_in): ?>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="profile.php"><i class="fa-solid fa-user me-2"></i><?= $userName ?></a>
                    </li>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="login.php?logout=true"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link active" href="login.php">
                            <i class="fa-solid fa-right-to-bracket me-2"></i>Login
                        </a>
                    </li>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="register.php">
                            <i class="fa-solid fa-user-plus me-2"></i>Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <section class="d-flex align-items-center justify-content-center cx-poppins" style="min-height: calc(100vh - 100px);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-5">
                    <!-- Login form -->
                    <div class="card shadow rounded-4">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <h3 class="fw-bold"><i class="fa-solid fa-right-to-bracket me-2"></i>Log in</h3>
                                <p>Don't have an account? <a href="register.php">Sign up</a></p>
                            </div>
                            <form action="login.php" method="post">
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="input_userName" id="id_userName" placeholder="Username" required>
                                        <label for="id_userName" class="form-label">Username</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <input type="password" class="form-control" name="input_password" id="password" placeholder="Password" required>
                                        <label for="password" class="form-label">Password</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-grid">
                                        <button class="btn btn-primary btn-lg fs-6" type="submit" name="login_button">Log in now</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous">
    </script>
    <?php
        include_once "../services/toast.php";
    ?>
</body>
</html>
