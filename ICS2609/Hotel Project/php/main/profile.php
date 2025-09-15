<?php
require_once "../services/dbaseconnection.php";
session_start();

$is_logged_in = isset($_SESSION['is_logged_in']) ? $_SESSION['is_logged_in'] : false;
$account_type = $is_logged_in ? $_SESSION['account_type'] : null;
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
$status = $is_logged_in ? $_SESSION['status'] : null;
$fullName = $is_logged_in ? $_SESSION['fullName'] : null;
$userName = $is_logged_in ? $_SESSION['userName'] : null;

// Handle cancel request
if (isset($_POST['cancel_reservation'])) {
    $reservation_id = $_POST['reservation_id'];
    
    // Update reservation status to cancelled
    $cancel_query = "UPDATE tbl_reservations SET reservation_status = 'cancelled' WHERE reservation_id = '$reservation_id' AND user_id = '$user_id'";
    $SQL_connection->query($cancel_query);
    
    // Redirect to refresh the page
    $_SESSION['toast'] = [
        'type' => 'info',
        'message' => 'Your reservation has been cancelled successfully.',
        'show_progress' => true,
        'duration' => 5000,
    ];
    
    header("Location: profile.php");
    exit();
}

// Redirect if not logged in
if (!$is_logged_in) {
    header("Location: login.php");
    exit();
}

// if session exists and status is 'pending', redirect to verify.php
if ($is_logged_in && $status === 'pending') {
    header("Location: verify.php");
    exit();
}

$SQL_query = "SELECT * FROM tbl_users WHERE user_id = '$user_id'";
$SQL_result = $SQL_connection->query($SQL_query);
$SQL_row = $SQL_result->fetch_assoc();

$SQL_query = "SELECT r.*, rm.room_number, rm.room_type, rm.price as room_price
                        FROM tbl_reservations r
                        JOIN tbl_rooms rm ON r.room_id = rm.room_id
                        WHERE r.user_id = '$user_id'
                        ORDER BY
                            CASE r.reservation_status
                                WHEN 'confirmed' THEN 1
                                WHEN 'checked_in' THEN 2
                                WHEN 'checked_out' THEN 3
                                WHEN 'cancelled' THEN 4
                                WHEN 'no_show' THEN 5
                            END,
                            CASE
                                WHEN r.check_in_date >= CURDATE() THEN 1
                                ELSE 2
                            END,
                            r.check_in_date DESC";
$SQL_result = $SQL_connection->query($SQL_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Romaré Suites</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="../../css/style.css" rel="stylesheet" />
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark cx-navbar-main sticky-top">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <span class="cx-navbar-title mx-2">Romaré Suites</span>
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
                <?php if ($is_logged_in): ?>
                    <?php if ($account_type == "admin"): ?>
                        <li class="nav-item cx-navbar-item">
                            <a class="nav-link" href="../admin/admin-users.php"><i class="fa-solid fa-shield me-2"></i>Operator Panel</a>
                        </li>
                    <?php elseif ($account_type == "employee"): ?>
                        <li class="nav-item cx-navbar-item">
                            <a class="nav-link" href="../admin/admin-rooms.php"><i class="fa-solid fa-shield me-2"></i>Operator Panel</a>
                        </li>
                    <?php endif; ?>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if ($is_logged_in): ?>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link active" href="profile.php"><i class="fa-solid fa-user me-2"></i><?= $userName ?></a>
                    </li>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="login.php?logout=true"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="login.php">
                            <i class="fa-solid fa-user me-2"></i>Login/Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
    
    <!-- Content -->
    <section class="container cx-poppins p-3">

        <!-- Profile Info -->
        <div class="border rounded p-4">
            <div class="row mb-5">
                <div class="col d-flex align-items-center">
                    <span class="fs-4 fw-bold text-muted">
                        <i class="fa-solid fa-user me-2"></i>Profile Information
                    </span>
                        <div class="ms-auto d-flex gap-2">
                            <?php
                                if ($SQL_row['account_type'] === 'admin') {
                                    echo '<span class="cx-badge cx-badge-admin">admin</span>';
                                } elseif ($SQL_row['account_type'] === 'employee') {
                                    echo '<span class="cx-badge cx-badge-employee">employee</span>';
                                } else {
                                    echo '<span class="cx-badge cx-badge-customer">customer</span>';
                                }

                                if ($SQL_row['status'] === 'active') {
                                    echo '<span class="cx-badge cx-badge-active">active</span>';
                                } else if ($SQL_row['status'] === 'inactive') {
                                    echo '<span class="cx-badge cx-badge-inactive">inactive</span>';
                                } else if ($SQL_row['status'] === 'pending') {
                                    echo '<span class="cx-badge cx-badge-pending">pending</span>';
                                }
                            ?>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-4">
                        <label for="id_userName" class="fw-semibold small mb-0">Username</label>
                        <p id="id_userName"><?= $SQL_row['user_name'] ?></p>
                    </div>
                    <div class="col-4">
                        <label for="id_fullName" class="fw-semibold small mb-0">Full Name</label>
                        <p id="id_fullName"><?= $SQL_row['first_name'] . ' ' . $SQL_row['last_name'] ?></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <label for="id_email" class="fw-semibold small mb-0">Email</label>
                        <p id="id_email"><?= $SQL_row['email'] ?></p>
                    </div>
                    <div class="col-4">
                        <label for="id_phoneNumber" class="fw-semibold small mb-0">Phone Number</label>
                        <p id="id_phoneNumber"><?= $SQL_row['phone_number'] ?></p>
                    </div>
                    <div class="col-4">
                        <label for="id_address" class="fw-semibold small mb-0">Address</label>
                        <p id="id_address"><?= $SQL_row['address'] ?></p>
                    </div>
                </div>
        </div>

        <!-- Reservations -->
        <div class="border rounded p-4 mt-3">
            <div class="row mb-5">
                <div class="col d-flex align-items-center">
                    <span class="fs-4 fw-bold text-muted">
                        <i class="fa-solid fa-calendar-check me-2"></i>My Reservations
                    </span>
                </div>
            </div>

            <?php if ($SQL_result && $SQL_result->num_rows > 0): ?>
                <div class="row">
                    <?php while ($reservation = $SQL_result->fetch_assoc()): ?>
                        <div class="col-4 mb-3">
                            <div class="card" id="reservation-<?= $reservation['reservation_id'] ?>">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <p class="fw-semibold mb-0"><?= ucfirst($reservation['room_type']) ?></p>
                                    <span>
                                        <?php
                                            if ($reservation['reservation_status'] === "confirmed") {
                                                echo '<span class="cx-badge-profile-reservation cx-badge-confirmed">Confirmed</span>'; // Green
                                            } elseif ($reservation['reservation_status'] === "checked_in") {
                                                echo '<span class="cx-badge-profile-reservation cx-badge-checked-in">Checked In</span>';
                                            } elseif ($reservation['reservation_status'] === "checked_out") {
                                                echo '<span class="cx-badge-profile-reservation cx-badge-checked-out">Checked Out</span>';
                                            } elseif ($reservation['reservation_status'] === "cancelled") {
                                                echo '<span class="cx-badge-profile-reservation cx-badge-cancelled">Cancelled</span>';
                                            }
                                        ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="id_roomNumber" class="fw-semibold small mb-0">Room Number</label>
                                            <p class="id_roomNumber"><?= $reservation['room_number'] ?></p>
                                        </div>
                                        <div class="col-6">
                                            <label for="id_totalGuests" class="fw-semibold small mb-0">Total Guests</label>
                                            <p class="id_totalGuests"><i class="fa-solid fa-users me-2"></i><?= $reservation['total_guests'] ?></p>
                                        </div>
                                    </div>
                                    

                                    <div class="row">
                                        <div class="col-6">
                                            <label for="id_check_in_date" class="fw-semibold small mb-0">Check-in Date</label>
                                            <p class="id_check_in_date">
                                                <?= date('M d, Y', strtotime($reservation['check_in_date'])) ?>
                                            </p>
                                        </div>
                                        <div class="col-6">
                                            <label for="check_out_date" class="fw-semibold small mb-0">Check-out Date</label>
                                            <p class="check_out_date">
                                                <?= date('M d, Y', strtotime($reservation['check_out_date'])) ?>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-6">
                                            <label for="id_totalPrice" class="fw-semibold small mb-0">Total Price</label>
                                            <p class="id_totalPrice text-success fw-bold mb-0">₱<?= number_format($reservation['total_price'], 2) ?></p>
                                        </div>
                                        <div class="col-6">
                                            <?php if ($reservation['reservation_status'] === 'confirmed'): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="reservation_id" value="<?= $reservation['reservation_id'] ?>">
                                                    <button type="submit" name="cancel_reservation" class="btn btn-outline-danger btn-sm w-100 mt-3">
                                                        <i class="fa-solid fa-times me-2"></i>Cancel Booking
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                </div>

                                
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center text-muted py-5">
                    <i class="fa-solid fa-calendar-xmark fa-3x mb-3"></i>
                    <p class="fs-5">No reservations found</p>
                    <p>Start planning your stay with us!</p>
                    <a href="reservation.php" class="btn btn-primary">Make a Reservation</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous">
    </script>
    <?php
        include_once "../services/toast.php";
    ?>
</body>
</html>