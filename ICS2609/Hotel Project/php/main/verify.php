<?php
require_once "../services/dbaseconnection.php";
include_once "../services/email.php";
session_start();

echo "<script>console.log('Session Toast Debug:', " . json_encode($_SESSION['toast'] ?? null) . ");</script>";

$is_logged_in = isset($_SESSION['is_logged_in']) ? $_SESSION['is_logged_in'] : false;
$account_type = $is_logged_in ? $_SESSION['account_type'] : null;
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
$status = $is_logged_in ? $_SESSION['status'] : null;
$fullName = $is_logged_in ? $_SESSION['fullName'] : null;
$userName = $is_logged_in ? $_SESSION['userName'] : null;

// Having session brings back to home.php
if (!$is_logged_in || $status !== 'pending') {
    header("Location: home.php");
    exit();
}

// Handle resend verification code
if (isset($_POST['button_resend'])) {
    if ($is_logged_in && $user_id) {
        $SQL_get_user_data = "SELECT email, auth_code FROM tbl_users WHERE user_id = '".$user_id."'";
        $user_result = $SQL_connection->query($SQL_get_user_data);

        if ($user_result && $user_result->num_rows == 1) {
            $user_data = $user_result->fetch_assoc();
            $email = $user_data['email'];
            $authCode = $user_data['auth_code'];

            send_verification($fullName, $email, $authCode);
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => 'We’ve resent the authentication code to your email.',
                'show_progress' => true,
                'duration' => 5000,
            ];
            
            $SQL_log = "INSERT INTO tbl_logs (user_id, user_name, account_type, action, datetime)
                VALUES ('$user_id', '$userName', '$account_type', 'Resent Verification Code', NOW())";
            $SQL_connection->query($SQL_log);
        }
    }
}

// OTP Verification
if (isset($_POST['button_verify'])) {
    $input_authCode = $_POST['input_authCode'];
    $SQL_authCode = "SELECT * FROM tbl_users WHERE auth_code = '".$input_authCode."'";
    $SQL_result = $SQL_connection->query($SQL_authCode);

    if ($SQL_result->num_rows == 1) {
        // OTP Valid
        $SQL_update = "UPDATE tbl_users SET status = 'Active', auth_code = NULL
                       WHERE auth_code = '".$input_authCode."'";
        $SQL_connection->query($SQL_update);

        // Get user data for logging
        $SQL_fields = $SQL_result->fetch_assoc();
        $user_id = $SQL_fields['user_id'];
        $userName = $SQL_fields['user_name'];
        $account_type = $SQL_fields['account_type'];

        $_SESSION['is_logged_in'] = true;
        $_SESSION['user_id'] = $SQL_fields['user_id'];
        $_SESSION['account_type'] = $SQL_fields['account_type'];
        $_SESSION['status'] = 'active';
        $_SESSION['fullName'] = $SQL_fields['first_name'] . " " . $SQL_fields['last_name'];
        $_SESSION['userName'] = $SQL_fields['user_name'];

        $SQL_log = "INSERT INTO tbl_logs (user_id, user_name, account_type, action, datetime)
                VALUES ('$user_id', '$userName', '$account_type', 'Status changed to Active', NOW())";
        $SQL_connection->query($SQL_log);

        $_SESSION['toast'] = [
            'type' => 'success',
            'message' => 'Your account has been successfully verified. Logged in successfully.',
            'show_progress' => true,
            'duration' => 5000,
        ];
        
        header("Location: home.php");
        exit();

    } else {
        // OTP Invalid
        $_SESSION['toast'] = [
            'type' => 'danger',
            'message' => 'The verification code you entered is incorrect. Please try again.',
            'show_progress' => true,
            'duration' => 5000,
        ];
    }
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Romaré Suites</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="../../css/style.css" rel="stylesheet" />
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark cx-navbar-main sticky-top">
        <div class="container">
            <a class="navbar-brand" style="cursor: not-allowed">
                <span class="cx-navbar-title mx-2">Romaré Suites</span>
            </a>

            <ul class="navbar-nav me-auto">
                <li class="nav-item cx-navbar-item" style="cursor: not-allowed">
                    <a class="nav-link">Home</a>
                </li>
                <li class="nav-item cx-navbar-item" style="cursor: not-allowed">
                    <a class="nav-link">Rooms</a>
                </li>
                <li class="nav-item cx-navbar-item" style="cursor: not-allowed">
                    <a class="nav-link">Reservation</a>
                </li>
                <?php if ($is_logged_in && ($account_type == "admin" || $account_type == "employee")): ?>
                    <li class="nav-item cx-navbar-item" style="cursor: not-allowed">
                        <a class="nav-link"><i class="fa-solid fa-shield me-2"></i>Operator Panel</a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if ($is_logged_in): ?>
                    <li class="nav-item cx-navbar-item">
                        <span class=" nav-link active">
                            <i class="fa-solid fa-user-check me-2"></i><?= $userName ?> - Verification
                        </span>
                    </li>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="login.php?logout=true"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="login.php">
                            <i class="fa-solid fa-right-to-bracket me-2"></i>Login/Register
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
                    <div class="card shadow rounded-4">
                        <div class="card-body p-5">
                            <div class="mb-4">
                                <h3 class="fw-bold"><i class="fa-solid fa-user-check me-2"></i>Account Verification</h3>
                                <p class="text-muted">For your security, we've emailed a code to verify your account.</p>
                            </div>
                            
                            <!-- Verification Form -->
                            <form action="verify.php" method="post" class="mb-3">
                                <div class="mb-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="input_authCode" id="id_authCode" placeholder="Verification Code" required maxlength="6" pattern="[0-9]{6}" title="Please enter a 6-digit verification code">
                                        <label for="id_authCode" class="form-label">Verification Code</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="d-grid">
                                        <button class="btn btn-primary btn-lg fs-6" type="submit" name="button_verify">
                                            <i class="fa-solid fa-check me-2"></i>Verify
                                        </button>
                                    </div>
                                </div>
                            </form>
                            
                            <!-- Resend Code Section -->
                            <div class="text-center">
                                <p class="text-muted small mb-2">Didn't receive the code?</p>
                                <form action="verify.php" method="post" style="display: inline;">
                                    <button type="submit" name="button_resend" class="btn btn-outline-secondary btn-sm">
                                        <i class="fa-solid fa-refresh me-2"></i>Resend Code
                                    </button>
                                </form>
                            </div>
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
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('id_authCode').focus();
            
            document.getElementById('id_authCode').addEventListener('input', function(e) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });
        });


    </script>
</body>
</html>

