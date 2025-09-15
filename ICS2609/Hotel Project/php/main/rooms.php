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
    header("Location: verify.php");
    exit();
}

$SQL_rooms = "SELECT 
                    room_type, 
                    MIN(price) as min_price, 
                    MAX(price) as max_price,
                    MIN(capacity) as min_capacity,
                    MAX(capacity) as max_capacity,
                    MIN(total_bed) as min_beds,
                    MAX(total_bed) as max_beds,
                    MIN(total_bath) as min_baths,
                    MAX(total_bath) as max_baths,
                    MAX(CASE 
                        WHEN description IS NOT NULL AND description != '' 
                        THEN description 
                        ELSE NULL 
                    END) as description,
                    MAX(CASE 
                        WHEN img_path IS NOT NULL AND img_path != '' 
                        THEN img_path 
                        ELSE NULL 
                    END) as img_path,
                    COUNT(*) as room_count
                FROM tbl_rooms 
                WHERE status = 'available'
                GROUP BY room_type
                ORDER BY min_price;";

$rooms_result = $SQL_connection->query($SQL_rooms);
$roomTypes = [];

if ($rooms_result->num_rows > 0) {
    while ($row = $rooms_result->fetch_assoc()) {
        $roomTypes[] = $row;
    }
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
                    <a class="nav-link active" href="rooms.php">Rooms</a>
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
    
    <!-- Rooms Display Section -->
    <section class="cx-rooms-section container py-5 cx-poppins">
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h2 class="fw-bold mb-3 text-muted fw-semibold">
                    <i class="fa-solid fa-bed me-3"></i>Our Room Types
                </h2>
                <p class="text-muted lead">Choose from our selection of comfortable and luxurious accommodations.</p>
            </div>
        </div>

        <?php if (empty($roomTypes)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info text-center py-5">
                        <i class="fa-solid fa-info-circle fa-3x mb-3 text-info"></i>
                        <h4>No Rooms Available</h4>
                        <p class="mb-0">Currently, no room types are available. Please check back later.</p>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($roomTypes as $room): ?>
                    <div class="col-lg-6 col-xl-4">
                        <div class="card cx-room-type-card border-0 shadow-sm h-100">
                            <?php if (!empty($room['img_path'])): ?>
                                <div class="cx-room-type-image-container">
                                    <img src="<?= $room['img_path'] ?>" class="card-img-top cx-room-type-image" alt="<?= ucfirst($room['room_type']) ?>">
                                    <div class="cx-room-type-overlay">
                                        <span class="cx-room-count-badge">
                                            <?= $room['room_count'] ?> room<?= $room['room_count'] > 1 ? 's' : '' ?> available
                                        </span>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="card-img-top cx-room-type-image bg-light d-flex align-items-center justify-content-center">
                                    <i class="fa-solid fa-image fa-4x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="card-body d-flex flex-column">
                                <div class="mb-3">
                                    <h4 class="card-title mb-2 text-primary fw-semibold"><?= ucfirst($room['room_type']) ?></h4>
                                    <div class="cx-description-container mb-3">
                                        <p class="card-text text-muted mb-0"><?= $room['description'] ?></p>
                                    </div>
                                </div>
                                
                                <!-- Room Features Grid -->
                                <div class="cx-room-features mb-4">
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="cx-feature-item text-center p-2">
                                                <i class="fa-solid fa-bed text-primary mb-1"></i>
                                                <div class="small">
                                                    <?= $room['min_beds'] == $room['max_beds'] ? 
                                                        $room['min_beds'] : 
                                                        $room['min_beds'] . '-' . $room['max_beds'] ?> Bed<?= $room['max_beds'] > 1 ? 's' : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="cx-feature-item text-center p-2">
                                                <i class="fa-solid fa-bath text-primary mb-1"></i>
                                                <div class="small">
                                                    <?= $room['min_baths'] == $room['max_baths'] ? 
                                                        $room['min_baths'] : 
                                                        $room['min_baths'] . '-' . $room['max_baths'] ?> Bath<?= $room['max_baths'] > 1 ? 's' : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="cx-feature-item text-center p-2">
                                                <i class="fa-solid fa-users text-primary mb-1"></i>
                                                <div class="small">
                                                    Up to <?= $room['min_capacity'] == $room['max_capacity'] ? 
                                                        $room['max_capacity'] : 
                                                        $room['min_capacity'] . '-' . $room['max_capacity'] ?> Guest<?= $room['max_capacity'] > 1 ? 's' : '' ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Pricing and Action -->
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div>
                                            <div class="cx-price-range">
                                                <?php if ($room['min_price'] == $room['max_price']): ?>
                                                    <span class="fw-bold text-success fs-5">₱<?= number_format($room['min_price'], 2) ?></span>
                                                <?php else: ?>
                                                    <span class="fw-bold text-success fs-5">₱<?= number_format($room['min_price'], 2) ?> - ₱<?= number_format($room['max_price'], 2) ?></span>
                                                <?php endif; ?>
                                                <div class="small text-muted">per night</div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-grid">
                                        <a href="reservation.php" class="btn btn-primary">
                                            <i class="fa-solid fa-calendar-check me-2"></i>Check Availability
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<?php
    include_once "../services/toast.php";
?>
</body>
</html>