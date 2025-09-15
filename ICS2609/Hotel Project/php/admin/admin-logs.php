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
                <?php if ($is_logged_in && $account_type == "admin"): ?>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="../main/home.php">Home</a>
                    </li>
                    <li class="nav-item cx-navbar-item">
                        <a class="nav-link" href="../main/rooms.php">Rooms</a>
                    </li>
                    <?php endif; ?>
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
                        <a class="nav-link active" href="admin-logs.php">Logs</a>
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

<!-- Page Content -->
<div class="container p-4">
    <form action="admin-logs.php" method="post" class="mb-3">
        <div class="row align-items-center mb-3">
            <div class="col-md-6">
                <input type="search" name="searchInput" placeholder="Search by Action, Username, User ID, or Account Type" class="form-control" value="<?= $_POST['searchInput'] ?? '' ?>" />
            </div>
            <div class="col-auto ps-0">
                <input type="submit" value="Search" name="search" class="btn btn-light cx-button-admin" />
            </div>
            <div class="col-auto ps-0">
                <a href="admin-logs.php" class="btn btn-light cx-button-admin">Clear</a>
            </div>
        </div>
    </form>

    <?php
    $searchInput = $_POST['searchInput'] ?? '';
    if (isset($_POST['search']) && !empty($searchInput)) {
        $SQL_query = "SELECT log_id, user_id, user_name, account_type, action, datetime
                    FROM tbl_logs
                    WHERE action LIKE '%$searchInput%'
                        OR user_id LIKE '%$searchInput%'
                        OR user_name LIKE '%$searchInput%'
                        OR account_type LIKE '%$searchInput%'
                    ORDER BY datetime DESC";
    } else {
        $SQL_query = "SELECT log_id, user_id, user_name, account_type, action, datetime FROM tbl_logs ORDER BY datetime DESC";
    }

    $SQL_result = $SQL_connection->query($SQL_query);

    if ($SQL_result && $SQL_result->num_rows > 0): ?>
        <div class="cx-table-wrapper">
            <table class="cx-table">
                <thead>
                    <tr>
                        <th>Log ID</th>
                        <th>User ID</th>
                        <th>Username</th>
                        <th>Account Type</th>
                        <th>Action</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $SQL_result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['log_id'] ?></td>
                            <td><?= $row['user_id'] ?></td>
                            <td><?= $row['user_name'] ?></td>
                            <td>
                                <span class="cx-badge <?= strtolower($row['account_type']) === 'admin' ? 'cx-badge-admin' : (strtolower($row['account_type']) === 'employee' ? 'cx-badge-employee' : 'cx-badge-customer') ?>">
                                    <?= $row['account_type'] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['action']) ?></td>
                            <td><?= $row['datetime'] ?></td>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<?php
    include_once "../services/toast.php";
?>
</body>
</html>