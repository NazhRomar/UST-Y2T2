<?php
require_once "../services/dbaseconnection.php";
session_start();

$is_logged_in = isset($_SESSION['is_logged_in']) ? $_SESSION['is_logged_in'] : false;
$account_type = $is_logged_in ? $_SESSION['account_type'] : null;
$user_id = $is_logged_in ? $_SESSION['user_id'] : null;
$status = $is_logged_in ? $_SESSION['status'] : null;
$fullName = $is_logged_in ? $_SESSION['fullName'] : null;
$userName = $is_logged_in ? $_SESSION['userName'] : null;

// if session exists and status is 'pending', redirect to verify.php
if ($is_logged_in && $status === 'pending') {
    header("Location: verify.php");
    exit();
}

// Variables for form data and available rooms
$checkIn = '';
$checkOut = '';
$guests = '';
$availableRooms = [];
$showResults = false;

// Process booking submission
if (isset($_POST['button_book'])) {
    $room_id = $_POST['input_roomId'];
    $check_in = $_POST['input_checkIn'];
    $check_out = $_POST['input_checkOut'];
    $capacity = $_POST['input_guests'];
    
    // Calculate total nights and price
    $check_in_date = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
    $nights = $check_in_date->diff($check_out_date)->days;
    
    // Get room price
    $price_query = "SELECT price FROM tbl_rooms WHERE room_id = $room_id";
    $price_result = $SQL_connection->query($price_query);
    $room_data = $price_result->fetch_assoc();
    $total_price = $room_data['price'] * $nights;
    
    // Insert reservation with total_guests column
    $SQL_insert = "INSERT INTO tbl_reservations(user_id, room_id, check_in_date, check_out_date, total_price, total_guests, reservation_status)
                   VALUES ($user_id, $room_id, '$check_in', '$check_out', $total_price, $capacity, 'confirmed')";
    
    $SQL_connection->query($SQL_insert);

    
    $SQL_log = "INSERT INTO tbl_logs (user_id, user_name, account_type, action, datetime)
        VALUES ('$user_id', '$userName', '$account_type', 'Reserved Room ID $room_id" . "', NOW())";
    $SQL_connection->query($SQL_log);

    $_SESSION['toast'] = [
        'type' => 'success',
        'message' => 'Reservation successfully submitted! Room has been reserved.',
        'show_progress' => true,
        'duration' => 5000,
    ];
    
    header("Location: profile.php");
    exit();
}

// Process form submission
if (isset($_POST['id_checkIn']) && isset($_POST['id_checkOut']) && isset($_POST['id_guests'])) {
    $checkIn = $_POST['id_checkIn'];
    $checkOut = $_POST['id_checkOut'];
    $guests = $_POST['id_guests'];
    
    $SQL_available = "SELECT r.* FROM tbl_rooms r
            WHERE r.room_id NOT IN (
                SELECT res.room_id
                FROM tbl_reservations res
                WHERE res.reservation_status IN ('confirmed', 'pending', 'checked_in')
                AND (
                    (res.check_in_date <= '$checkIn' AND res.check_out_date > '$checkIn') OR
                    (res.check_in_date < '$checkOut' AND res.check_out_date >= '$checkOut') OR
                    (res.check_in_date >= '$checkIn' AND res.check_out_date <= '$checkOut')
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="../../css/style.css" rel="stylesheet" />
    <style>
        
    </style>
</head>
<body>

    <!-- Navigation Bar -->
    <section class="cx-navbar-container">
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
                        <a class="nav-link active" href="reservation.php">Reservation</a>
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
                            <a class="nav-link" href="profile.php"><i class="fa-solid fa-user me-2"></i><?= $userName ?></a>
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
    </section>

    <!-- Reservation Search Section -->
    <section class="cx-reservation container d-flex align-items-center justify-content-center cx-poppins <?= $showResults ? 'py-5' : '' ?>" style="<?= $showResults ? '' : 'min-height: calc(100vh - 100px);' ?>">
        <div>
            <div class="row">
                <div class="col-12">
                    <div class="border rounded shadow p-4 bg-white">
                        <form action="" method="post">
                        <div class="row mb-5 pt-3">
                            <div class="col-12 text-center">
                                <h2 class="fw-bold mb-3 text-muted fw-semibold">
                                    <i class="fa-solid fa-calendar me-3"></i>Reserve Now
                                </h2>
                                <p class="text-muted lead">Reserve your perfect room today and enjoy a relaxing stay with us.</p>
                            </div>
                        </div>
                            <div class="d-flex gap-3 align-items-end justify-content-between flex-wrap">
                                <div class="d-flex gap-3 flex-wrap">
                                    <div class="cx-reservation-input">
                                        <label for="id_checkIn" class="form-label small text-muted">Check-In</label>
                                        <input type="date" class="form-control" id="id_checkIn" name="id_checkIn" value="<?= $checkIn ?>" required>
                                    </div>
                                    <div class="cx-reservation-input">
                                        <label for="id_checkOut" class="form-label small text-muted">Check-Out</label>
                                        <input type="date" class="form-control" id="id_checkOut" name="id_checkOut" value="<?= $checkOut ?>" required <?= empty($checkIn) ? 'disabled' : '' ?>>
                                    </div>
                                    <div class="cx-reservation-input">
                                        <label for="id_guests" class="form-label small text-muted"><i class="fa-solid fa-users me-2"></i>Guests</label>
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
                                            placeholder="Enter number of guests (1–10)"
                                        >
                                    </div>
                                </div>
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-search me-2"></i>Check Availability
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
                                    <p class="mb-0">Sorry, no rooms are available for your selected dates. Please try different dates or contact us for assistance.</p>
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
                                                <span class="cx-price-badge">
                                                    ₱<?= number_format($room['price'], 2) ?>/night
                                                </span>
                                                
                                                <?php if ($is_logged_in): ?>
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
                                                            data-guests="<?= $guests ?>">
                                                        <i class="fa-solid fa-calendar-check me-2"></i>Book Now
                                                    </button>
                                                <?php else: ?>
                                                    <a href="login.php" class="btn btn-outline-primary btn-md">
                                                        <i class="fa-solid fa-sign-in-alt me-2"></i>Login to Book
                                                    </a>
                                                <?php endif; ?>
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
    <?php if ($is_logged_in): ?>
    <div class="modal fade cx-poppins" id="bookRoomModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="post">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">
                            <i class="fa-solid fa-calendar-check me-2"></i>Book Room
                        </h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="input_roomId" id="booking_roomId">
                        <input type="hidden" name="input_checkIn" id="booking_checkIn">
                        <input type="hidden" name="input_checkOut" id="booking_checkOut">
                        <input type="hidden" name="input_guests" id="booking_guests">
                        <!-- Room Details Section -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <div class="border rounded p-2 bg-light d-flex align-items-center justify-content-center" style="height: 170px;">
                                    <img id="booking_roomImage" src="#" alt="Room Image" style="max-height: 170px; max-width: 100%; object-fit: cover; display: none;">
                                    <!-- doesnt load when text muted but loads when text secondary???????????? -->
                                    <i id="booking_noImage" class="fa-solid fa-image fa-3x text-secondary"></i>

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
                                
                                <div class="cx-price-badge d-inline-block">
                                    ₱<span id="booking_roomPrice"></span>/night
                                </div>
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
                                    <span class="text-success fw-semibold">₱<span id="booking_total"></span></span>
                                </div>
                            </div>
                        </div>

                        <!-- Customer Information -->
                        <div class="mb-3">
                            <h6 class="text-muted">
                                <i class="fa-solid fa-user me-2"></i>Customer Information
                            </h6>
                            <div class="border rounded p-3">
                                <div class="text-muted small">Booking for:</div>
                                <div class="fw-semibold"><?= $fullName ?> (<?= $userName ?>)</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="button_book" class="btn btn-primary">
                            <i class="fa-solid fa-check me-2"></i>Confirm Booking
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkinInput = document.getElementById('id_checkIn');
            const checkoutInput = document.getElementById('id_checkOut');

            const today = new Date().toISOString().split('T')[0];
            checkinInput.min = today;
            checkoutInput.min = today;

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