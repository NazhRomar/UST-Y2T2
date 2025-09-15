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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Romaré Suites</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="../../css/style.css" rel="stylesheet" />
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark cx-navbar-main sticky-top">
        <div class="container">
            <a class="navbar-brand" href="home.php">
                <span class="cx-navbar-title mx-2">Romaré Suites</span>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link active" href="home.php">Home</a>
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
        </div>
    </nav>
    
    <div class="cx-poppins">
        <section class="bg-dark text-white py-5" style="background-image: linear-gradient(rgba(0,0,0,0.4), rgba(0,0,0,0.4)), url('../../images/home/home-banner.png'); background-size: cover; background-position: center; backdrop-filter: blur(1px);">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="py-5">
                        <h1 class="display-2 fw-bold mb-3" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.7);">Romaré Suites</h1>
                        <p class="lead fs-4" style="text-shadow: 1px 1px 3px rgba(0,0,0,0.5);">Experience luxury and comfort in the heart of elegance.</p>
                    </div>
                </div>
            </div>
        </div>
        </section>
        <section class="p-5">
            <div class="container">
                <span class="text-center">
                    <p class="h1 fw-semibold text-muted mb-3">Our Premium Rooms</p>
                    <p class="h5 text-muted">Choose from our three distinct room categories, each offering unique comfort and style for every guest.</p>
                </span>
                <div class="row text-center mt-5 justify-content-center">
                    <div class="col-4 border shadow rounded-4 p-5 mx-5 w-25">
                        <span class="text-primary-emphasis display-1">
                            <i class="fas fa-bed"></i>
                        </span>
                        <p class="h4 text-muted">Classic</>
                            <p class="cx-room-description" style="min-height: 110px;">
                                Cozy room that is perfect for solo travelers or couples looking for a restful stay.
                            </p>
                            <a href="rooms.php" class="btn btn-primary px-3 fw-semibold rounded-pill">View Rooms</a>
                    </div>
                    <div class="col-4 border shadow rounded-4 p-5 mx-5 w-25">
                        <span class="text-primary-emphasis display-1">
                            <i class="fas fa-crown"></i>
                        </span>
                        <p class="h4 text-muted">Premiere</>
                            <p class="cx-room-description" style="min-height: 110px;">
                                Spacious room ideal for families or small groups seeking comfort and style.
                            </p>
                        <a href="rooms.php" class="btn btn-primary px-3 fw-semibold rounded-pill">View Rooms</a>
                    </div>
                    <div class="col-4 border shadow rounded-4 p-5 mx-5 w-25">
                        <span class="text-primary-emphasis display-1">
                            <i class="fas fa-gem"></i>
                        </span>
                        <p class="h4 text-muted">Signature</>
                            <p class="cx-room-description" style="min-height: 110px;">
                                Expansive luxury suite designed for large groups or VIP guests who appreciate extra space and amenities.
                            </p>
                        <a href="rooms.php" class="btn btn-primary px-3 fw-semibold rounded-pill">View Rooms</a>
                    </div>
                </div>
                
            </div>
        </section>

        <section class="bg-secondary text-white py-5">
            <div class="container">
                <div class="row text-center">
                    <div class="col-3">
                        <div class="p-3">
                            <h2 class="display-6 fw-bold mb-2">500+</h2>
                            <p class="h5 mb-0">Happy Guests</p>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-3">
                            <h2 class="display-6 fw-bold mb-2">30</h2>
                            <p class="h5 mb-0">Premium Rooms</p>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-3">
                            <h2 class="display-6 fw-bold mb-2">24/7</h2>
                            <p class="h5 mb-0">Service</p>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="p-3">
                            <h2 class="display-6 fw-bold mb-2">5★</h2>
                            <p class="h5 mb-0">Rating</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <section class="cx-dark-teal py-5" style="min-height: 70vh;">
            <div class="container py-5">
                <div class="text-center text-white mb-5">
                    <h1 class="display-6 fw-semibold mb-3">Make Your Reservation</h1>
                    <p class="lead">Book your stay with us and experience unparalleled hospitality.</p>
                </div>
                
                <div class="card bg-secondary bg-opacity-25 border-light border-opacity-25 rounded-4 p-4 mx-auto" style="max-width: 900px;">
                    <div class="row text-center text-white g-4">
                        <div class="col-md-6">
                            <div class="text-warning display-4 mb-3">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <h3 class="h4 mb-3">Easy Booking</h3>
                            <p class="mb-0">Simple online reservation system available 24/7</p>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="text-warning display-4 mb-3">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <h3 class="h4 mb-3">Flexible Payment</h3>
                            <p class="mb-0">Pay at check-in or check-out with your preferred method</p>
                        </div>
                    </div>
                    
                    <div class="text-center mt-5">
                        <a href="reservation.php" class="btn btn-primary btn-lg px-5 py-3 rounded-pill fw-semibold">
                            <i class="fas fa-bed me-2"></i>
                            Book Your Stay
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous">
    </script>
    <?php
        include_once "../services/toast.php";
    ?>
</body>
</html>