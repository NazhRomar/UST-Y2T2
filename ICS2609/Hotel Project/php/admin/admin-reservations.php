<?php
require_once "../services/dbaseconnection.php";
session_start();

$is_logged_in = isset($_SESSION['is_logged_in']) ? $_SESSION['is_logged_in'] : false;
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
$account_type = $is_logged_in ? $_SESSION['account_type'] : null;
$status = $is_logged_in ? $_SESSION['status'] : null;
$fullName = $is_logged_in ? $_SESSION['fullName'] : null;
$userName = $is_logged_in ? $_SESSION['userName'] : null;

// if session exists and status is 'pending', redirect to verify.php
if ($is_logged_in && $status === 'pending') {
    header("Location: ../main/verify.php");
    exit();
}

// Restrict access to admin and employee only
if (!$is_logged_in || !in_array($account_type, ['admin', 'employee'])) {
    header("Location: ../main/home.php");
    exit();
}

// Status Change
if (isset($_POST['status_change']) && isset($_POST['reservation_id'])) {
    $reservation_id = (int)$_POST['reservation_id'];

    // Get the reservation's current status and guest name
    $reservation_result = $SQL_connection->query("SELECT r.reservation_status, r.room_id, u.first_name, u.last_name, rm.room_number 
                                                  FROM tbl_reservations r
                                                  JOIN tbl_users u ON r.user_id = u.user_id
                                                  JOIN tbl_rooms rm ON r.room_id = rm.room_id
                                                  WHERE r.reservation_id = $reservation_id");
    
    if ($reservation_result && $reservation_result->num_rows > 0) {
        $reservation_row = $reservation_result->fetch_assoc();
        $current_status = $reservation_row['reservation_status'];
        $room_id = $reservation_row['room_id'];
        $guest_name = $reservation_row['first_name'] . ' ' . $reservation_row['last_name'];
        $room_number = $reservation_row['room_number'];

        // Handle status transitions
        if ($current_status === 'confirmed') {
            // Change from confirmed to checked_in
            $update_query = "UPDATE tbl_reservations SET reservation_status = 'checked_in' WHERE reservation_id = $reservation_id";
            $room_update_query = "UPDATE tbl_rooms SET status = 'occupied' WHERE room_id = $room_id";
            $success_message = "Guest $guest_name (Room $room_number) has been checked in successfully";
            $action_type = "checked in";
        } elseif ($current_status === 'checked_in') {
            // Change from checked_in to checked_out
            $update_query = "UPDATE tbl_reservations SET reservation_status = 'checked_out' WHERE reservation_id = $reservation_id";
            $room_update_query = "UPDATE tbl_rooms SET status = 'maintenance' WHERE room_id = $room_id";
            $success_message = "Guest $guest_name (Room $room_number) has been checked out successfully";
            $action_type = "checked out";
        }

        if (isset($update_query)) {
            if ($SQL_connection->query($update_query)) {
                $SQL_connection->query($room_update_query);
                $SQL_log = "INSERT INTO tbl_logs (user_id, user_name, account_type, action, datetime)
                    VALUES ('$user_id', '$userName', '$account_type', '(Operator) $success_message" . "', NOW())";
                $SQL_connection->query($SQL_log);
                
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => $success_message,
                    'show_progress' => true,
                    'duration' => 5000,
                ];
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => "Error occurred while updating reservation status.",
                    'show_progress' => true,
                    'duration' => 5000,
                ];
            }
        } else {
            $_SESSION['toast'] = [
                'type' => 'warning',
                'message' => "Invalid status transition for reservation.",
                'show_progress' => true,
                'duration' => 5000,
            ];
        }
        
        header("Location: admin-reservations.php");
        exit();
    } else {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => "Reservation not found.",
            'show_progress' => true,
            'duration' => 5000,
        ];
        header("Location: admin-reservations.php");
        exit();
    }
}

// Cancel Reservation
if (isset($_POST['cancel_reservation']) && isset($_POST['reservation_id'])) {
    $reservation_id = (int)$_POST['reservation_id'];

    $reservation_result = $SQL_connection->query("SELECT r.reservation_status, r.room_id, u.first_name, u.last_name, rm.room_number 
                                                  FROM tbl_reservations r
                                                  JOIN tbl_users u ON r.user_id = u.user_id
                                                  JOIN tbl_rooms rm ON r.room_id = rm.room_id
                                                  WHERE r.reservation_id = $reservation_id");
    
    if ($reservation_result && $reservation_result->num_rows > 0) {
        $reservation_row = $reservation_result->fetch_assoc();
        $current_status = $reservation_row['reservation_status'];
        $room_id = $reservation_row['room_id'];
        $guest_name = $reservation_row['first_name'] . ' ' . $reservation_row['last_name'];
        $room_number = $reservation_row['room_number'];

        // Only allow cancellation for confirmed reservations
        if ($current_status === 'confirmed') {
            $update_query = "UPDATE tbl_reservations SET reservation_status = 'cancelled' WHERE reservation_id = $reservation_id";
            if ($SQL_connection->query($update_query)) {
                
                $SQL_log = "INSERT INTO tbl_logs (user_id, user_name, account_type, action, datetime)
                    VALUES ('$user_id', '$userName', '$account_type', '(Operator) Cancelled reservation of $guest_name for Room $room_number" . "', NOW())";
                $SQL_connection->query($SQL_log);
                
                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => "Reservation for $guest_name (Room $room_number) has been cancelled successfully",
                    'show_progress' => true,
                    'duration' => 5000,
                ];
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => "Error occurred while cancelling reservation.",
                    'show_progress' => true,
                    'duration' => 5000,
                ];
            }
        } else {
            $_SESSION['toast'] = [
                'type' => 'warning',
                'message' => "Cannot cancel reservation with current status.",
                'show_progress' => true,
                'duration' => 5000,
            ];
        }
        
        header("Location: admin-reservations.php");
        exit();
    } else {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => "Reservation not found.",
            'show_progress' => true,
            'duration' => 5000,
        ];
        header("Location: admin-reservations.php");
        exit();
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
    <link href="../../css/style.css" rel="stylesheet" />
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
                    <?php if ($is_logged_in && ($account_type == "admin" || $account_type == "employee")): ?>
                        <li class="nav-item cx-navbar-item">
                            <a class="nav-link active" href="<?= $account_type == 'admin' ? 'admin-users.php' : 'admin-rooms.php' ?>">
                                <i class="fa-solid fa-shield me-2"></i>Operator Panel
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <?php if ($is_logged_in): ?>
                        <li class="nav-item cx-navbar-item">
                            <a class="nav-link" href="../main/profile.php"><i class="fa-solid fa-user me-2"></i><?= $userName ?></a>
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
                    <?php if ($is_logged_in && $account_type == "admin"): ?>
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
                        <a class="nav-link active" href="admin-reservations.php">Reservations</a>
                    </li>
                    <li class="nav-item d-flex align-items-center">
                        <i class="fa-solid fa-pen-to-square ms-3 me-1"></i>
                    </li>
                    <?php if ($is_logged_in && $account_type == "admin"): ?>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="admin-register.php">Register</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="admin-reserve.php">Reserve</a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    <li class="nav-item cx-navbar-item">
                        <a class="cx-navbar-font cx-navbar-button nav-link"><i class="fa-solid fa-image-portrait me-2"></i><?= $account_type ?></a>
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
        <!-- Content Container -->
        <div class="container p-4 cx-admin-content-container">

<!-- Search Form -->
<form action="admin-reservations.php" method="post" class="mb-3">
    <div class="row align-items-center mb-3">
        <div class="col-md-6">
            <input type="search" name="searchInput" placeholder="Search by Name, Room Number, Status, or Price" class="form-control" value="<?= $_POST['searchInput'] ?? '' ?>" />
        </div>
        <div class="col-auto ps-0">
            <input type="submit" value="Search" name="search" class="btn btn-light cx-button-admin"/>
        </div>
        <div class="col-auto ps-0">
            <a href="admin-reservations.php" class="btn btn-light cx-button-admin">Clear</a>
        </div>
        <div class="col-auto ps-0">
            <a href="admin-reserve.php" class="btn btn-light cx-button-admin">
                <i class="fa-solid fa-arrow-up-right-from-square me-2"></i>Add
            </a>
        </div>
    </div>
</form>

<?php
$searchInput = $_POST['searchInput'] ?? '';
if (isset($_POST['search']) && !empty($searchInput)) {
    $SQL_query = "SELECT r.*, u.first_name, u.last_name, rm.room_number 
                  FROM tbl_reservations r
                  JOIN tbl_users u ON r.user_id = u.user_id
                  JOIN tbl_rooms rm ON r.room_id = rm.room_id
                  WHERE u.first_name LIKE '%$searchInput%'
                     OR u.last_name LIKE '%$searchInput%'
                     OR u.user_name LIKE '%$searchInput%'
                     OR rm.room_number LIKE '%$searchInput%'
                     OR r.reservation_status LIKE '%$searchInput%'
                     OR r.total_price LIKE '%$searchInput%'
                     OR r.total_guests LIKE '%$searchInput%'
                  ORDER BY 
                    CASE 
                        WHEN r.reservation_status = 'confirmed' THEN 1
                        WHEN r.reservation_status = 'checked_in' THEN 2
                        WHEN r.reservation_status = 'checked_out' THEN 3
                        WHEN r.reservation_status = 'cancelled' THEN 4
                        WHEN r.reservation_status = 'no_show' THEN 5
                        ELSE 6
                    END,
                    r.check_in_date ASC";
} else {
    $SQL_query = "SELECT r.*, u.first_name, u.last_name, rm.room_number 
                  FROM tbl_reservations r
                  JOIN tbl_users u ON r.user_id = u.user_id
                  JOIN tbl_rooms rm ON r.room_id = rm.room_id
                  ORDER BY 
                    CASE 
                        WHEN r.reservation_status = 'confirmed' THEN 1
                        WHEN r.reservation_status = 'checked_in' THEN 2
                        WHEN r.reservation_status = 'checked_out' THEN 3
                        WHEN r.reservation_status = 'cancelled' THEN 4
                        WHEN r.reservation_status = 'no_show' THEN 5
                        ELSE 6
                    END,
                    r.check_in_date ASC";
}

$SQL_result = $SQL_connection->query($SQL_query);

if ($SQL_result && $SQL_result->num_rows > 0): ?>
    <div class="cx-table-wrapper">
        <table class="cx-table">
            <thead>
                <tr>
                    <th>Reservation ID</th>
                    <th>Room Number</th>
                    <th>Full Name</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Total Price</th>
                    <th>Total Guests</th>
                    <th>Reservation Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($SQL_row = $SQL_result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $SQL_row['reservation_id'] ?></td>
                        <td><?= $SQL_row['room_number'] ?></td>
                        <td><?= $SQL_row['first_name'] . ' ' . $SQL_row['last_name'] ?></td>
                        <td><?= $SQL_row['check_in_date'] ?></td>
                        <td><?= $SQL_row['check_out_date'] ?></td>
                        <td>₱<?= number_format($SQL_row['total_price'], 2) ?></td>
                        <td><?= $SQL_row['total_guests'] ?? 'N/A' ?></td>
                        <td>
                            <?php
                                if ($SQL_row['reservation_status'] === "confirmed") {
                                    echo '<span class="cx-badge cx-badge-confirmed">Confirmed</span>'; // Green
                                } elseif ($SQL_row['reservation_status'] === "checked_in") {
                                    echo '<span class="cx-badge cx-badge-checked-in">Checked In</span>';
                                } elseif ($SQL_row['reservation_status'] === "checked_out") {
                                    echo '<span class="cx-badge cx-badge-checked-out">Checked Out</span>';
                                } elseif ($SQL_row['reservation_status'] === "cancelled") {
                                    echo '<span class="cx-badge cx-badge-cancelled">Cancelled</span>';
                                }
                            ?>
                        </td>
                        <td class="cx-col-action">
                            <form method="POST" action="admin-reservations.php" style="display:inline;">
                            <input type="hidden" name="reservation_id" value="<?= $SQL_row['reservation_id'] ?>">
                            <!-- Check-in/Check-out buttons -->
                            <?php if ($SQL_row['reservation_status'] === 'confirmed'): ?>
                                <button type="submit" name="status_change" class="btn cx-button-action btn-success btn-sm mb-2">
                                    <i class="fa-solid fa-right-to-bracket me-2"></i>Check-in
                                </button>
                            <?php elseif ($SQL_row['reservation_status'] === 'checked_in'): ?>
                                <button type="submit" name="status_change" class="btn cx-button-action btn-danger btn-sm mb-2">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i>Check-out
                                </button>
                            <?php endif; ?>
                            </form>
                            
                            <!-- Cancel button -->
                            <form method="POST" action="admin-reservations.php" style="display:inline;">
                            <input type="hidden" name="reservation_id" value="<?= $SQL_row['reservation_id'] ?>">
                            <?php if ($SQL_row['reservation_status'] === 'confirmed'): ?>
                                <button type="submit" name="cancel_reservation" class="btn cx-button-action btn-warning btn-sm">
                                    <i class="fa-solid fa-xmark me-2"></i>Cancel
                                </button>
                            <?php endif; ?>
                            </form>

                            <?php if ($SQL_row['reservation_status'] === 'cancelled' || $SQL_row['reservation_status'] === 'checked_out'): ?>
                                <button class="btn cx-button-action btn-secondary btn-sm" disabled>
                                    <i class="fa-solid fa-ban me-2"></i>No Action
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="alert alert-warning fade show" role="alert">
        <strong>No reservations found!</strong>
    </div>
<?php endif; ?>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
const editButtons = document.querySelectorAll('.edit-button');
const statusSelect = document.getElementById('id_status');
const originalStatusInput = document.getElementById('original_status');
const updateButton = document.getElementById('updateButton');
const pendingWarning = document.getElementById('pendingWarning');

function checkStatusSubmission() {
const originalStatus = originalStatusInput.value;
const selectedStatus = statusSelect.value;

if ((originalStatus === 'active' || originalStatus === 'inactive') && selectedStatus === 'pending') {
    updateButton.disabled = true;
    updateButton.classList.add('disabled');
    pendingWarning.style.display = 'block';
} else {
    updateButton.disabled = false;
    updateButton.classList.remove('disabled');
    pendingWarning.style.display = 'none';
}
}

statusSelect.addEventListener('change', checkStatusSubmission);

editButtons.forEach(button => {
button.addEventListener('click', function() {
    const userId = this.getAttribute('data-user-id');
    
    const accountType = this.getAttribute('data-accountType');
    const email = this.getAttribute('data-email');
    const user_name = this.getAttribute('data-user-name');
    const first_name = this.getAttribute('data-first-name');
    const last_name = this.getAttribute('data-last-name');
    const phoneNumber = this.getAttribute('data-phoneNumber');
    const address = this.getAttribute('data-address');
    const status = this.getAttribute('data-status');
    
    
    document.getElementById('id_userId').value = userId;
    document.getElementById('id_email').value = email;
    document.getElementById('id_userName').value = user_name;
    document.getElementById('id_firstName').value = first_name;
    document.getElementById('id_lastName').value = last_name;
    document.getElementById('id_phoneNumber').value = phoneNumber;
    document.getElementById('id_address').value = address;
    
    originalStatusInput.value = status;

    const accountTypeSelect = document.getElementById('id_accountType');
    accountTypeSelect.value = accountType;
    accountTypeSelect.dispatchEvent(new Event('change'));
    
    statusSelect.value = status;
    statusSelect.dispatchEvent(new Event('change'));
    
    checkStatusSubmission();
});
});
});
</script>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<?php
    include_once "../services/toast.php";
?>
</body>
</html>