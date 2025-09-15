<?php
require_once "../services/dbaseconnection.php";
session_start();

$is_logged_in = isset($_SESSION['is_logged_in']) ? $_SESSION['is_logged_in'] : false;
$account_type = $is_logged_in ? $_SESSION['account_type'] : null;
$status = $is_logged_in ? $_SESSION['status'] : null;
$fullName = $is_logged_in ? $_SESSION['fullName'] : null;
$userName = $is_logged_in ? $_SESSION['userName'] : null;

$checkIn = '';
$checkOut = '';
$guests = '';
$selectedCustomer = '';
$availableRooms = [];
$availableCustomers = [];
$showResults = false;

// Get all customers for dropdown
$customer_query = "SELECT user_id, user_name, first_name, last_name, status FROM tbl_users WHERE account_type = 'customer' AND status = 'active' ORDER BY first_name, last_name";
$customer_result = $SQL_connection->query($customer_query);
$availableCustomers = [];
if ($customer_result->num_rows > 0) {
    while ($row = $customer_result->fetch_assoc()) {
        $availableCustomers[] = $row;
    }
}

// Process booking submission
if (isset($_POST['button_book'])) {
    $room_id = $_POST['input_roomId'];
    $customer_id = $_POST['input_customerId'];
    $check_in = $_POST['input_checkIn'];
    $check_out = $_POST['input_checkOut'];
    $capacity = $_POST['input_guests'];
    
    // Verify customer is still active before proceeding
    $customer_check = "SELECT status FROM tbl_users WHERE user_id = $customer_id AND account_type = 'customer'";
    $customer_check_result = $SQL_connection->query($customer_check);
    $customer_data = $customer_check_result->fetch_assoc();
    
    if (!$customer_data || $customer_data['status'] !== 'active') {
        $_SESSION['toast'] = [
            'type' => 'danger',
            'message' => 'Cannot create reservation. Customer account is no longer active.',
            'show_progress' => true,
            'duration' => 5000,
        ];
        header("Location: admin-reserve.php");
        exit();
    }
    
    $check_in_date = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
    $nights = $check_in_date->diff($check_out_date)->days;
    
    $price_query = "SELECT price FROM tbl_rooms WHERE room_id = $room_id";
    $price_result = $SQL_connection->query($price_query);
    $room_data = $price_result->fetch_assoc();
    $total_price = $room_data['price'] * $nights;
    
    $SQL_insert = "INSERT INTO tbl_reservations(user_id, room_id, check_in_date, check_out_date, total_price, total_guests, reservation_status)
                   VALUES ($customer_id, $room_id, '$check_in', '$check_out', $total_price, $capacity, 'confirmed')";
    
    if ($SQL_connection->query($SQL_insert) === TRUE) {
        $customer_info_query = "SELECT user_name FROM tbl_users WHERE user_id = $customer_id";
        $customer_info_result = $SQL_connection->query($customer_info_query);
        $customer_info = $customer_info_result->fetch_assoc();
        
        $room_info_query = "SELECT room_number FROM tbl_rooms WHERE room_id = $room_id";
        $room_info_result = $SQL_connection->query($room_info_query);
        $room_info = $room_info_result->fetch_assoc();
        
        $user_id = $_SESSION['user_id'];
        $SQL_log = "INSERT INTO tbl_logs (user_id, user_name, account_type, action, datetime)
                    VALUES ('$user_id', '$userName', '$account_type', '(Operator) Created reservation for " . $customer_info['user_name'] . " - Room " . $room_info['room_number'] . "', NOW())";
        $SQL_connection->query($SQL_log);
        
        $_SESSION['toast'] = [
            'type' => 'active',
            'message' => 'Reservation successfully created for customer!',
            'show_progress' => true,
            'duration' => 5000,
        ];
        header("Location: admin-reservations.php");
        exit();
    } else {
        $_SESSION['toast'] = [
            'type' => 'danger',
            'message' => 'Error creating reservation. Please try again.',
            'show_progress' => true,
            'duration' => 5000,
        ];
    }
}

// Process form submission for room search
if (isset($_POST['id_checkIn']) && isset($_POST['id_checkOut']) && isset($_POST['id_guests']) && isset($_POST['id_customer'])) {
    $checkIn = $_POST['id_checkIn'];
    $checkOut = $_POST['id_checkOut'];
    $guests = $_POST['id_guests'];
    $selectedCustomer = $_POST['id_customer'];
    
    // Same availability query as reservation.php but with fixed date overlap logic
    $SQL_available = "SELECT r.* FROM tbl_rooms r
            WHERE r.room_id NOT IN (
                SELECT res.room_id
                FROM tbl_reservations res
                WHERE res.reservation_status IN ('confirmed', 'pending', 'checked_in')
                AND (
                    (res.check_in_date < '$checkOut' AND res.check_out_date > '$checkIn')
                )
            )
            AND r.status IN ('available')
            AND r.capacity >= $guests
            ORDER BY r.room_type, r.price";
    
    $result = $SQL_connection->query($SQL_available);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $availableRooms[] = $row;
        }
    }
    
    $showResults = true;
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
                        <a class="nav-link" href="admin-reservations.php">Reservations</a>
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
                        <a class="nav-link active" href="admin-reserve.php">Reserve</a>
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

    <!-- Admin Reservation Search Section -->
    <section class="cx-admin-reserve container cx-poppins <?= $showResults ? 'py-5' : 'd-flex align-items-center justify-content-center' ?>" <?= !$showResults ? 'style="min-height: calc(100vh - 200px);"' : '' ?>>
        <div class="w-100">
            <div class="row">
                <div class="col-12">
                    <div class="border rounded shadow p-4 bg-white">
                        <div class="mb-3">
                            <h4 class="mb-1 fw-semibold">
                                <i class="fa-solid fa-calendar-plus me-2"></i>
                                Create New Reservation
                            </h4>
                            <p class="text-muted mb-0">Search for available rooms and create reservations for customers <span class="text-danger fw-semibold">only</span>.</p>
                        </div>

                        <form action="" method="post">
                            <div class="row">
                                <!-- Inputs Column (10/12 width) -->
                                <div class="col-md-10">
                                    <div class="row">
                                        <!-- Customer Selection -->
                                        <div class="col-md-4">
                                            <label for="id_customer" class="form-label small text-muted">Customer</label>
                                            <select class="form-select" name="id_customer" id="id_customer" required>
                                                <option value="" selected disabled hidden>Select a customer</option>
                                                <?php foreach ($availableCustomers as $customer): ?>
                                                    <option value="<?= $customer['user_id'] ?>" <?= $selectedCustomer == $customer['user_id'] ? 'selected' : '' ?>>
                                                        <?= $customer['first_name'] . ' ' . $customer['last_name'] ?> (<?= $customer['user_name'] ?>) [ID# <?= $customer['user_id'] ?>]
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <!-- Check-in Date -->
                                        <div class="col-md-3">
                                            <label for="id_checkIn" class="form-label small text-muted">Check-In</label>
                                            <input type="date" class="form-control" id="id_checkIn" name="id_checkIn" value="<?= $checkIn ?>" required>
                                        </div>

                                        <!-- Check-out Date -->
                                        <div class="col-md-3">
                                            <label for="id_checkOut" class="form-label small text-muted">Check-Out</label>
                                            <input type="date" class="form-control" id="id_checkOut" name="id_checkOut" value="<?= $checkOut ?>" required <?= empty($checkIn) ? 'disabled' : '' ?>>
                                        </div>

                                        <!-- Guests -->
                                        <div class="col-md-2">
                                            <label for="id_guests" class="form-label small text-muted">
                                                <i class="fa-solid fa-users me-2"></i>Guests
                                            </label>
                                            <input
                                                type="number"
                                                class="form-control"
                                                id="id_guests"
                                                name="id_guests"
                                                min="1"
                                                max="10"
                                                value="<?= $guests ?>"
                                                required
                                                oninput="validity.valid || (value='');"
                                                placeholder="1–10"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Button Column (2/12 width) -->
                                <div class="col-md-2 d-flex align-items-end justify-content-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fa-solid fa-search me-2"></i>Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Available Rooms Results -->
            <?php if ($showResults): ?>
                <div class="cx-results-section">
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <span class="fs-4 fw-bold text-muted">
                                    <i class="fa-solid fa-bed me-2"></i>Available Rooms
                                </span>
                                <div class="text-muted">
                                    <span class="small d-flex align-items-center">
                                        <i class="fa-solid fa-calendar me-2"></i><?= date('M j, Y', strtotime($checkIn)) ?> - <?= date('M j, Y', strtotime($checkOut)) ?>
                                        <span class="mx-2">•</span>
                                        <i class="fa-solid fa-users me-2"></i><?= $guests ?> guest(s)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (empty($availableRooms)): ?>
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info text-center py-5">
                                    <i class="fa-solid fa-info-circle fa-3x mb-3 text-info"></i>
                                    <h4>No Available Rooms</h4>
                                    <p class="mb-0">No rooms are available for the selected dates and guest count. Please try different dates.</p>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="row g-4">
                            <?php foreach ($availableRooms as $room): ?>
                                <div class="col-lg-4 col-md-6">
                                    <div class="card cx-room-card border-0 shadow">
                                        <?php if (!empty($room['img_path'])): ?>
                                            <img src="<?= $room['img_path'] ?>" class="card-img-top cx-room-image">
                                        <?php else: ?>
                                            <div class="card-img-top cx-room-image bg-light d-flex align-items-center justify-content-center">
                                                <i class="fa-solid fa-image fa-3x text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="card-body d-flex flex-column">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h5 class="card-title mb-0"><?= ucfirst($room['room_type']) ?></h5>
                                                <span class="badge bg-secondary"><?= $room['room_number'] ?></span>
                                            </div>
                                            
                                            <!-- Room Features -->
                                            <div class="mb-3">
                                                <div class="d-flex gap-3 text-muted small">
                                                    <span><i class="fa-solid fa-bed me-2"></i><?= $room['total_bed'] ?> bed(s)</span>
                                                    <span><i class="fa-solid fa-bath me-2"></i><?= $room['total_bath'] ?> bath(s)</span>
                                                    <?php if ($room['balcony']): ?>
                                                        <span><i class="fa-solid fa-building me-2"></i>Balcony</span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="text-muted small mt-1">
                                                    <i class="fa-solid fa-users me-2"></i>Up to <?= $room['capacity'] ?> guests
                                                </div>
                                            </div>
                                            
                                            <p class="card-text text-muted small mb-3 flex-grow-1">
                                                <?= $room['description'] ?>
                                            </p>
                                            
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div class="cx-price-badge d-inline-block">
                                                    ₱<?= number_format($room['price'], 2) ?>/night
                                                </div>
                                                
                                                <button type="button" class="btn btn-outline-primary btn-md book-button"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#bookRoomModal"
                                                        data-room-id="<?= $room['room_id'] ?>"
                                                        data-room-number="<?= $room['room_number'] ?>"
                                                        data-room-type="<?= ucfirst($room['room_type']) ?>"
                                                        data-room-price="<?= $room['price'] ?>"
                                                        data-room-image="<?= $room['img_path'] ?>"
                                                        data-room-beds="<?= $room['total_bed'] ?>"
                                                        data-room-baths="<?= $room['total_bath'] ?>"
                                                        data-room-balcony="<?= $room['balcony'] ? '1' : '0' ?>"
                                                        data-room-capacity="<?= $room['capacity'] ?>"
                                                        data-check-in="<?= $checkIn ?>"
                                                        data-check-out="<?= $checkOut ?>"
                                                        data-guests="<?= $guests ?>"
                                                        data-customer-id="<?= $selectedCustomer ?>">
                                                    <i class="fa-solid fa-calendar-check me-2"></i>Book for Customer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Booking Modal -->
    <div class="modal fade cx-poppins" id="bookRoomModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="post">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">
                            <i class="fa-solid fa-calendar-check me-2"></i>Confirm Reservation
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="input_roomId" id="booking_roomId">
                        <input type="hidden" name="input_checkIn" id="booking_checkIn">
                        <input type="hidden" name="input_checkOut" id="booking_checkOut">
                        <input type="hidden" name="input_guests" id="booking_guests">
                        <input type="hidden" name="input_customerId" id="booking_customerId">

                        <!-- Room Details Section -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="border rounded p-2 bg-light d-flex align-items-center justify-content-center" style="height: 170px;">
                                    <img id="booking_roomImage" src="#" alt="Room Image" style="max-height: 170px; max-width: 100%; object-fit: cover; display: none;">
                                    <i id="booking_noImage" class="fa-solid fa-image fa-3x text-muted"></i>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h5 id="booking_roomType" class="mb-2"></h5>
                                <p class="mb-2 small">
                                    <i class="fa-solid fa-door-open me-2"></i>
                                    Room <span id="booking_roomNumber"></span>
                                </p>
                                
                                <!-- Room Features in Modal -->
                                <div class="mb-3">
                                    <div class="d-flex gap-3 text-muted small mb-2">
                                        <span><i class="fa-solid fa-bed me-2"></i><span id="booking_beds"></span> bed(s)</span>
                                        <span><i class="fa-solid fa-bath me-2"></i><span id="booking_baths"></span> bath(s)</span>
                                        <span id="booking_balconyInfo" style="display: none;"><i class="fa-solid fa-building me-2"></i>Balcony</span>
                                    </div>
                                    <div class="text-muted small">
                                        <i class="fa-solid fa-users me-2"></i>
                                        Up to <span id="booking_capacity"></span> guests • <span id="booking_guestCount"></span> guest(s) selected
                                    </div>
                                </div>
                                
                                <!-- im further -->
                                <span class="cx-price-badge d-inline-block">
                                    ₱<span id="booking_roomPrice"></span>/night
                                </span>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="mb-4">
                            <h6 class="text-muted">
                                <i class="fa-solid fa-user me-2"></i>Customer Information
                            </h6>
                            <div class="border rounded p-3">
                                <div class="text-muted small">Creating reservation for:</div>
                                <div class="fw-semibold" id="booking_customerName"></div>
                            </div>
                        </div>

                        <!-- Reservation Details Section -->
                        <div class="mb-4">
                            <h6 class="text-muted">
                                <i class="fa-solid fa-calendar me-2"></i>Reservation Details
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="border rounded p-3 mb-3 mb-md-0">
                                        <div class="text-muted small">Check-In</div>
                                        <div class="fw-semibold" id="booking_checkInDisplay"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="border rounded p-3">
                                        <div class="text-muted small">Check-Out</div>
                                        <div class="fw-semibold" id="booking_checkOutDisplay"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Summary Section -->
                        <div class="mb-4">
                            <h6 class="text-muted">
                                <i class="fa-solid fa-calculator me-2"></i>Pricing Summary
                            </h6>
                            <div class="border rounded p-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>₱<span id="booking_pricePerNight"></span> × <span id="booking_nights"></span> night(s)</span>
                                    <span>₱<span id="booking_subtotal"></span></span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between fw-semibold">
                                    <span>Total</span>
                                    <span class="text-primary fw-semibold">₱<span id="booking_total"></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="button_book" class="btn btn-primary">
                            <i class="fa-solid fa-check me-2"></i>Create Reservation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const customerSelect = document.getElementById('id_customer');
            const checkinInput = document.getElementById('id_checkIn');
            const checkoutInput = document.getElementById('id_checkOut');

            const today = new Date().toISOString().split('T')[0];
            checkinInput.min = today;
            checkoutInput.min = today;

            
            customerSelect.addEventListener('change', function() {
                if (this.value === '' || this.value === null) {
                    this.style.color = '#6c757d';
                } else {
                    this.style.color = 'black';
                }
            });

            if (customerSelect.value === '' || customerSelect.value === null) {
                customerSelect.style.color = '#6c757d';
            } else {
                customerSelect.style.color = 'black';
            }

            const customerOptions = customerSelect.querySelectorAll('option');
            customerOptions.forEach(function(option) {
                if (option.value !== '') {
                    option.style.color = 'black';
                }
            });

            checkinInput.addEventListener('change', function() {
                if (this.value) {
                    checkoutInput.disabled = false;
                    const nextDay = new Date(this.value);
                    nextDay.setDate(nextDay.getDate() + 1);
                    checkoutInput.min = nextDay.toISOString().split('T')[0];

                    if (checkoutInput.value && new Date(checkoutInput.value) <= new Date(this.value)) {
                        checkoutInput.value = '';
                    }
                } else {
                    checkoutInput.disabled = true;
                    checkoutInput.value = '';
                }
            });

            const bookButtons = document.querySelectorAll('.book-button');
            
            bookButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const roomId = this.getAttribute('data-room-id');
                    const roomNumber = this.getAttribute('data-room-number');
                    const roomType = this.getAttribute('data-room-type');
                    const roomPrice = parseFloat(this.getAttribute('data-room-price'));
                    const roomImage = this.getAttribute('data-room-image');
                    const roomBeds = parseInt(this.getAttribute('data-room-beds'));
                    const roomBaths = parseInt(this.getAttribute('data-room-baths'));
                    const roomBalcony = this.getAttribute('data-room-balcony') === '1';
                    const roomCapacity = this.getAttribute('data-room-capacity');
                    const checkIn = this.getAttribute('data-check-in');
                    const checkOut = this.getAttribute('data-check-out');
                    const guests = this.getAttribute('data-guests');
                    const customerId = this.getAttribute('data-customer-id');

                    const customerSelect = document.getElementById('id_customer');
                    const selectedOption = customerSelect.options[customerSelect.selectedIndex];
                    const customerName = selectedOption ? selectedOption.textContent : '';

                    document.getElementById('booking_customerId').value = customerId;
                    document.getElementById('booking_customerName').textContent = customerName;

                    const checkInDate = new Date(checkIn);
                    const checkOutDate = new Date(checkOut);
                    const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
                    const total = roomPrice * nights;

                    document.getElementById('booking_roomId').value = roomId;
                    document.getElementById('booking_checkIn').value = checkIn;
                    document.getElementById('booking_checkOut').value = checkOut;
                    document.getElementById('booking_guests').value = guests;

                    document.getElementById('booking_roomType').textContent = roomType;
                    document.getElementById('booking_roomNumber').textContent = roomNumber;
                    document.getElementById('booking_guestCount').textContent = guests;
                    document.getElementById('booking_roomPrice').textContent = roomPrice.toLocaleString('en-US', {minimumFractionDigits: 2});
                    
                    document.getElementById('booking_beds').textContent = roomBeds;
                    document.getElementById('booking_baths').textContent = roomBaths;
                    document.getElementById('booking_capacity').textContent = roomCapacity;
                    
                    const balconyInfo = document.getElementById('booking_balconyInfo');
                    if (roomBalcony) {
                        balconyInfo.style.display = 'inline';
                    } else {
                        balconyInfo.style.display = 'none';
                    }
                    
                    const bookingImage = document.getElementById('booking_roomImage');
                    const bookingNoImage = document.getElementById('booking_noImage');
                    
                    if (roomImage && roomImage.trim() !== '') {
                        bookingImage.src = roomImage;
                        bookingImage.style.display = 'block';
                        bookingNoImage.style.display = 'none';
                    } else {
                        bookingImage.style.display = 'none';
                        bookingNoImage.style.display = 'block';
                    }

                    const checkInFormatted = new Date(checkIn).toLocaleDateString('en-US', {
                        weekday: 'short',
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                    const checkOutFormatted = new Date(checkOut).toLocaleDateString('en-US', {
                        weekday: 'short',
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric'
                    });
                    
                    document.getElementById('booking_checkInDisplay').textContent = checkInFormatted;
                    document.getElementById('booking_checkOutDisplay').textContent = checkOutFormatted;

                    document.getElementById('booking_pricePerNight').textContent = roomPrice.toLocaleString('en-US', {minimumFractionDigits: 2});
                    document.getElementById('booking_nights').textContent = nights;
                    document.getElementById('booking_subtotal').textContent = total.toLocaleString('en-US', {minimumFractionDigits: 2});
                    document.getElementById('booking_total').textContent = total.toLocaleString('en-US', {minimumFractionDigits: 2});
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