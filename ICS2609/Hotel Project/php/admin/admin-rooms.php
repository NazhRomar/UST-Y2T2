
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

if (isset($_POST['status_change']) && isset($_POST['room_id'])) {
    $room_id = (int)$_POST['room_id'];

    // Get current status and room number
    $result = $SQL_connection->query("SELECT room_number, status FROM tbl_rooms WHERE room_id = $room_id");

    if ($result && $result->num_rows > 0) {
        $room = $result->fetch_assoc();
        $current_status = $room['status'];
        $room_number = $room['room_number'];

        // Determine new status
        $new_status = ($current_status === 'available') ? 'maintenance' : 'available';

        // Update status
        $update_query = "UPDATE tbl_rooms SET status = '$new_status' WHERE room_id = $room_id";

        if ($SQL_connection->query($update_query)) {
            $SQL_log = "INSERT INTO tbl_logs (user_id, user_name, account_type, action, datetime)
                    VALUES ('$user_id', '$userName', '$account_type', '(Operator) Changed Room $room_number status to $new_status" . "', NOW())";
                $SQL_connection->query($SQL_log);
                
            $_SESSION['toast'] = [
                'type' => 'success',
                'message' => "Room $room_number status changed to $new_status.",
                'show_progress' => true,
                'duration' => 5000,
            ];
        } else {
            $_SESSION['toast'] = [
                'type' => 'error',
                'message' => "Failed to update room status.",
                'show_progress' => true,
                'duration' => 5000,
            ];
        }

        header("Location: admin-rooms.php");
        exit();
    }
}

if (isset($_POST["button_update"])) {
    $room_id = $_POST['input_roomId'];
    $roomNumber = $_POST['input_roomNumber'];
    $roomType = $_POST['input_roomType'];
    $capacity = $_POST['input_capacity'];
    $price = $_POST['input_price'];
    $status = $_POST['input_status'];
    $description = $_POST['input_description'] ?? NULL;
    $total_bed = $_POST['input_totalBed'];
    $total_bath = $_POST['input_totalBath'];
    $balcony = $_POST['input_balcony'];
    
    $updateImagePath = false;
    $imagepath = null;
    
    if (isset($_POST['image_action'])) {
        switch ($_POST['image_action']) {
            case 'remove':
                $updateImagePath = true;
                $imagepath = NULL;
                break;
                
            case 'replace':
                if (isset($_FILES['input_imgPath']) && $_FILES['input_imgPath']['error'] === UPLOAD_ERR_OK && !empty($_FILES['input_imgPath']['name'])) {
                    // Create upload directory if it doesn't exist
                    $upload_dir = "../../images/room/";
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0755, true);
                    }
                    
                    $file_extension = pathinfo($_FILES['input_imgPath']['name'], PATHINFO_EXTENSION);
                    $unique_filename = uniqid() . '_' . time() . '.' . $file_extension;
                    $imagepath = $upload_dir . $unique_filename;
                    
                    // Validate file type
                    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    if (in_array(strtolower($file_extension), $allowed_types)) {
                        if (move_uploaded_file($_FILES['input_imgPath']['tmp_name'], $imagepath)) {
                            $updateImagePath = true;
                        } else {
                            $_SESSION['toast'] = [
                                'type' => 'error',
                                'message' => 'Failed to upload image file.',
                                'show_progress' => true,
                                'duration' => 5000,
                            ];
                        }
                    } else {
                        $_SESSION['toast'] = [
                            'type' => 'error',
                            'message' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.',
                            'show_progress' => true,
                            'duration' => 5000,
                        ];
                    }
                }
                break;
            case 'manual':
                if (isset($_POST['input_manualImgPath']) && !empty(trim($_POST['input_manualImgPath']))) {
                    $imagepath = trim($_POST['input_manualImgPath']);
                    $updateImagePath = true;
                }
                break;
            case 'keep':
            default:
                $updateImagePath = false;
                break;
        }
    }
    
    if ($updateImagePath) {
        if ($imagepath === NULL) {
            $SQL_update = "UPDATE tbl_rooms SET
                            room_number = '$roomNumber',
                            room_type = '$roomType',
                            capacity = $capacity,
                            price = $price,
                            status = '$status',
                            description = '$description',
                            total_bed = $total_bed,
                            total_bath = $total_bath,
                            balcony = $balcony,
                            img_path = NULL
                          WHERE room_id = $room_id";
        } else {
            $SQL_update = "UPDATE tbl_rooms SET
                            room_number = '$roomNumber',
                            room_type = '$roomType',
                            capacity = $capacity,
                            price = $price,
                            status = '$status',
                            description = '$description',
                            total_bed = $total_bed,
                            total_bath = $total_bath,
                            balcony = $balcony,
                            img_path = '$imagepath'
                          WHERE room_id = $room_id";
        }
    } else {
        $SQL_update = "UPDATE tbl_rooms SET
                        room_number = '$roomNumber',
                        room_type = '$roomType',
                        capacity = $capacity,
                        price = $price,
                        status = '$status',
                        description = '$description',
                        total_bed = $total_bed,
                        total_bath = $total_bath,
                        balcony = $balcony
                      WHERE room_id = $room_id";
    }

    if ($SQL_connection->query($SQL_update) === TRUE) {
        $_SESSION['toast'] = [
            'type' => 'active',
            'message' => 'Successfully updated entry.',
            'show_progress' => true,
            'duration' => 5000,
        ];
        header("Location: admin-rooms.php");
        exit();
    } else {
        $_SESSION['toast_error'] = "Failed to update room: " . $SQL_connection->error;
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
                        <a class="nav-link active" href="admin-rooms.php">Rooms</a>
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

    <div class="container cx-admin-content-container p-4">

        <!-- Search Form -->
        <form action="admin-rooms.php" method="post" class="mb-3">
            <div class="row align-items-center mb-3">
                <div class="col-md-6">
                    <input type="search" name="searchInput" placeholder="Search by Room ID, Room Number, Room Type, Capacity, Price, or Status" class="form-control" value="<?= $_POST['searchInput'] ?? '' ?>" />
                </div>
                <div class="col-auto ps-0">
                    <input type="submit" value="Search" name="search" class="btn btn-light cx-button-admin"/>
                </div>
                <div class="col-auto ps-0">
                    <a href="admin-rooms.php" class="btn btn-light cx-button-admin">Clear</a>
                </div>
            </div>
        </form>

        <?php
        $searchInput = $_POST['searchInput'] ?? '';
        if (isset($_POST['search']) && !empty($searchInput)) {
            $SQL_query = "SELECT * FROM tbl_rooms
                        WHERE room_id LIKE '%$searchInput%'
                            OR room_number LIKE '%$searchInput%'
                            OR room_type LIKE '%$searchInput%'
                            OR capacity LIKE '%$searchInput%'
                            OR price LIKE '%$searchInput%'
                            OR status LIKE '%$searchInput%'";
        } else {
            $SQL_query = "SELECT * FROM tbl_rooms";
        }

        $SQL_result = $SQL_connection->query($SQL_query);

        if ($SQL_result && $SQL_result->num_rows > 0): ?>
            <div class="cx-table-wrapper">
                <table class="cx-table">
                    <thead>
                        <tr>
                            <th>Room ID</th>
                            <th>Room Number</th>
                            <th>Room Type</th>
                            <th>Capacity</th>
                            <th>Beds</th>
                            <th>Baths</th>
                            <th>Balcony</th>
                            <th>Price</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Room Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($SQL_row = $SQL_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $SQL_row['room_id'] ?></td>
                                <td><?= $SQL_row['room_number'] ?></td>
                                <td><?= $SQL_row['room_type'] ?></td>
                                <td><?= $SQL_row['capacity'] ?></td>
                                <td><?= $SQL_row['total_bed'] ?></td>
                                <td><?= $SQL_row['total_bath'] ?></td>
                                <td><?= $SQL_row['balcony'] ?></td>
                                <td><?= number_format($SQL_row['price'],2)?></td>
                                <td class="cx-cell-wide"><?= $SQL_row['description'] ?></td>
                                <td>
                                    <?php if (!empty($SQL_row['img_path'])): ?>
                                        <img src="<?= $SQL_row['img_path'] ?>" style="width: 100px; height: auto;">
                                        <div class="small text-muted mt-1"><?= $SQL_row['img_path'] ?></div>
                                    <?php else: ?>
                                        <div class="text-warning fst-italic">Image Not Set</div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    if ($SQL_row['status'] === 'available') {
                                        echo '<span class="cx-badge cx-badge-available">available</span>';
                                    } else if ($SQL_row['status'] === 'maintenance') {
                                        echo '<span class="cx-badge cx-badge-maintenance">maintenance</span>';
                                    } else if ($SQL_row['status'] === 'occupied') {
                                        echo '<span class="cx-badge cx-badge-occupied">occupied</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="cx-actions">
                                        <button type="button" class="btn btn-primary cx-button-action edit-button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editRoomModal"
                                                data-room-id="<?= $SQL_row['room_id'] ?>"
                                                data-room-number="<?= htmlspecialchars($SQL_row['room_number']) ?>"
                                                data-room-type="<?= htmlspecialchars($SQL_row['room_type']) ?>"
                                                data-capacity="<?= $SQL_row['capacity'] ?>"
                                                data-total-bed="<?= $SQL_row['total_bed'] ?>"
                                                data-total-bath="<?= $SQL_row['total_bath'] ?>"
                                                data-balcony="<?= $SQL_row['balcony'] ?>"
                                                data-price="<?= $SQL_row['price'] ?>"
                                                data-status="<?= htmlspecialchars($SQL_row['status']) ?>"
                                                data-img-path="<?= htmlspecialchars($SQL_row['img_path']) ?>"
                                                data-description="<?= htmlspecialchars($SQL_row['description']) ?>">
                                            <i class="fa-solid fa-pencil me-2"></i>Edit
                                        </button>
                                        <!-- Status Button -->
                                        <form method="POST" action="admin-rooms.php" style="display:inline;">
                                        <input type="hidden" name="room_id" value="<?= $SQL_row['room_id'] ?>">
                                        <?php
                                            if ($SQL_row['status'] === 'maintenance') {
                                                ?><button type="submit" name="status_change" class="btn cx-button-action btn-success">
                                                    <i class="fa-solid fa-toggle-off me-2"></i>Enable
                                                </button><?php
                                            } elseif ($SQL_row['status'] === 'available') {
                                                ?><button type="submit" name="status_change" class="btn cx-button-action btn-danger">
                                                    <i class="fa-solid fa-toggle-off me-2"></i>Disable
                                                </button><?php
                                            }
                                        ?>
                                        </form>
                                    </div>
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

    <!-- Edit Modal -->
    <div class="modal fade" id="editRoomModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="admin-rooms.php" method="post" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Edit Room</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="input_roomId" id="id_roomId">

                        <div class="mb-2">
                            <span class="text-muted fs-6">Room Details</span>
                        </div>
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" name="input_roomNumber" id="id_roomNumber" placeholder="Room Number" required>
                                <label for="id_roomNumber" class="form-label">Room Number</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-floating">
                                <div class="form-floating mb-3">
                                    <select class="form-select" name="input_roomType" id="id_roomType" required>
                                        <option value="" selected disabled hidden>Select Room Type</option>
                                        <option value="classic">Classic</option>
                                        <option value="premiere">Premiere</option>
                                        <option value="signature">Signature</option>
                                    </select>
                                    <label for="id_roomType">Room Type</label>
                                </div>
                                <script>
                                    (function () {
                                        const select = document.getElementById('id_roomType');
                                        
                                        select.style.color = '#6c757d';
                                        
                                        const style = document.createElement('style');
                                        style.textContent = `
                                            #id_roomType option:not([disabled]) {
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
                            <div class="form-floating">
                                <input type="number" class="form-control" name="input_capacity" id="id_capacity" placeholder="Capacity" required>
                                <label for="id_capacity" class="form-label">Capacity</label>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control" name="input_totalBed" id="id_totalBed" placeholder="Total Beds" min="1" required>
                                    <label for="id_totalBed" class="form-label">Total Beds</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="number" class="form-control" name="input_totalBath" id="id_totalBath" placeholder="Total Baths" min="1" required>
                                    <label for="id_totalBath" class="form-label">Total Baths</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" name="input_balcony" id="id_balcony" required>
                                        <option value="" selected disabled hidden>Has Balcony?</option>
                                        <option value="0">No</option>
                                        <option value="1">Yes</option>
                                    </select>
                                    <label for="id_balcony">Balcony</label>
                                </div>
                                <script>
                                    (function () {
                                        const select = document.getElementById('id_balcony');
                                        
                                        select.style.color = '#6c757d';
                                        
                                        const style = document.createElement('style');
                                        style.textContent = `
                                            #id_balcony option:not([disabled]) {
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
                        <div class="mb-2">
                            <span class="text-muted fs-6">Pricing and Availability</span>
                        </div>
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" name="input_price" id="id_price" placeholder="Price" step="0.01" min="0" required>
                                <label for="id_price" class="form-label">Price</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-floating">
                                <div class="form-floating mb-3">
                                    <select class="form-select" name="input_status" id="id_status" required>
                                        <option value="" selected disabled hidden>Select Status</option>
                                        <option value="available">Available</option>
                                        <option value="occupied">Occupied</option>
                                        <option value="maintenance">Maintenance</option>
                                    </select>
                                    <label for="id_status">Status</label>
                                </div>
                                <script>
                                    (function () {
                                        const select = document.getElementById('id_status');
                                        
                                        select.style.color = '#6c757d';
                                        
                                        const style = document.createElement('style');
                                        style.textContent = `
                                            #id_status option:not([disabled]) {
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
                        <div class="mb-2">
                            <span class="text-muted fs-6">Description</span>
                        </div>
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="input_description" id="id_description" placeholder="Description">
                                <label for="id_description" class="form-label">Description</label>
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted fs-6">Image</span>
                        </div>
                        <div class="mb-3 border rounded px-3 py-2">
                            <label class="form-label small text-muted mb-0">Image Action</label>
                            <div class="form-check cx-poppins">
                                <input class="form-check-input" type="radio" name="image_action" id="keep_image" value="keep" checked>
                                <label class="form-check-label" for="keep_image">
                                    Keep current image
                                </label>
                            </div>
                            <div class="form-check cx-poppins">
                                <input class="form-check-input" type="radio" name="image_action" id="replace_image" value="replace">
                                <label class="form-check-label" for="replace_image">
                                    Replace with new image
                                </label>
                            </div>
                            <div class="form-check cx-poppins">
                                <input class="form-check-input" type="radio" name="image_action" id="manual_image" value="manual">
                                <label class="form-check-label" for="manual_image">
                                    Manually type image path
                                </label>
                            </div>
                            <div class="form-check cx-poppins">
                                <input class="form-check-input" type="radio" name="image_action" id="remove_image" value="remove">
                                <label class="form-check-label" for="remove_image">
                                    Remove image (no image)
                                </label>
                            </div>
                            <div class="mt-3">
                                <div class="mb-3" id="currentImageSection">
                                    <label class="form-label text-muted small mb-1">Current Image</label>
                                    <div class="border rounded p-2 bg-light d-flex flex-column align-items-center">
                                        <img id="currentImagePreview" src="#" alt="Current Image" style="max-height: 100px; display: none;">
                                        <div id="currentImagePath" class="small text-muted mt-2 d-block" style="display: none;"></div>
                                        <div id="noImageText" class="text-muted" style="display: none;">No image currently set</div>
                                    </div>
                                </div>
                                <div class="mb-3" id="fileUploadSection" style="display: none;">
                                    <label for="id_imagePathFile" class="form-label text-muted small mb-1">Upload New Image</label>
                                    <input type="file" class="form-control" name="input_imgPath" id="id_imagePathFile" accept="image/*">
                                    <div class="border rounded p-2 bg-light d-flex flex-column align-items-center mt-2" id="newImagePreviewContainer" style="display: none;">
                                        <img id="newImagePreview" src="#" alt="No Image Set" style="max-height: 100px;">
                                    </div>
                                </div>
                                <div class="mb-3" id="manualImageSection" style="display: none;">
                                    <label for="id_manualImagePath" class="form-label text-muted small mb-1">Enter Image Path</label>
                                    <input type="text" class="form-control" name="input_manualImgPath" id="id_manualImagePath" placeholder="e.g., ../images/room/room001.jpg">
                                    <div class="border rounded p-2 bg-light d-flex flex-column align-items-center mt-2" id="manualImagePreviewContainer" style="display: none;">
                                        <img id="manualImagePreview" src="#" alt="Manual Image Preview" style="max-height: 100px;">
                                    </div>
                                </div>
                                <div class="mb-3" id="removeImageSection" style="display: none;">
                                    <label class="form-label text-danger mb-0">Image will be removed. No image will be associated.</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="button_update" class="btn btn-primary cx-button-admin">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const currentImageSection = document.getElementById('currentImageSection');
    const fileUploadSection = document.getElementById('fileUploadSection');
    const removeImageSection = document.getElementById('removeImageSection');
    const fileInput = document.getElementById('id_imagePathFile');

    const imageActionRadios = document.querySelectorAll('input[name="image_action"]');

    const manualImageSection = document.getElementById('manualImageSection');

    imageActionRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            currentImageSection.style.display = 'none';
            fileUploadSection.style.display = 'none';
            removeImageSection.style.display = 'none';
            manualImageSection.style.display = 'none';

            if (this.value === 'keep') {
                currentImageSection.style.display = 'block';
            } else if (this.value === 'replace') {
                fileUploadSection.style.display = 'block';
                fileInput.value = '';
            } else if (this.value === 'remove') {
                removeImageSection.style.display = 'block';
            } else if (this.value === 'manual') {
                manualImageSection.style.display = 'block';
            }
        });
    });

    fileInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('newImagePreviewContainer');
        const previewImg = document.getElementById('newImagePreview');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
        }
    });

    const manualImageInput = document.getElementById('id_manualImagePath');
    manualImageInput.addEventListener('input', function() {
        const imagePath = this.value.trim();
        const previewContainer = document.getElementById('manualImagePreviewContainer');
        const previewImg = document.getElementById('manualImagePreview');

        if (imagePath) {
            previewImg.src = imagePath;
            previewImg.onerror = function() {
                previewContainer.style.display = 'none';
            };
            previewImg.onload = function() {
                previewContainer.style.display = 'block';
            };
        } else {
            previewContainer.style.display = 'none';
        }
    });

    const editButtons = document.querySelectorAll('.edit-button');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const roomId = this.getAttribute('data-room-id');
            const roomNumber = this.getAttribute('data-room-number');
            const roomType = this.getAttribute('data-room-type');
            const capacity = this.getAttribute('data-capacity');
            const totalBed = this.getAttribute('data-total-bed');
            const totalBath = this.getAttribute('data-total-bath');
            const balcony = this.getAttribute('data-balcony');
            const price = this.getAttribute('data-price');
            const status = this.getAttribute('data-status');
            const description = this.getAttribute('data-description');
            const imgPath = this.getAttribute('data-img-path');

            document.getElementById('id_roomId').value = roomId;
            document.getElementById('id_roomNumber').value = roomNumber;
            document.getElementById('id_roomType').value = roomType;
            document.getElementById('id_capacity').value = capacity;
            document.getElementById('id_totalBed').value = totalBed;
            document.getElementById('id_totalBath').value = totalBath;
            document.getElementById('id_balcony').value = balcony;
            document.getElementById('id_price').value = price;
            document.getElementById('id_status').value = status;
            document.getElementById('id_description').value = description || '';

            const currentImagePreview = document.getElementById('currentImagePreview');
            const currentImagePath = document.getElementById('currentImagePath');
            const noImageText = document.getElementById('noImageText');

            if (imgPath && imgPath.trim() !== '') {
                currentImagePreview.src = imgPath;
                currentImagePreview.style.display = 'block';
                currentImagePath.textContent = imgPath;
                currentImagePath.style.display = 'block';
                noImageText.style.display = 'none';
            } else {
                currentImagePreview.style.display = 'none';
                currentImagePath.style.display = 'none';
                noImageText.style.display = 'block';
            }

            document.getElementById('keep_image').checked = true;
            currentImageSection.style.display = 'block';
            fileUploadSection.style.display = 'none';
            removeImageSection.style.display = 'none';

            fileInput.value = '';
            document.getElementById('newImagePreviewContainer').style.display = 'none';

            const roomTypeSelect = document.getElementById('id_roomType');
            const statusSelect = document.getElementById('id_status');
            roomTypeSelect.dispatchEvent(new Event('change'));
            statusSelect.dispatchEvent(new Event('change'));
        });
    });

    const editModal = document.getElementById('editRoomModal');
    editModal.addEventListener('hidden.bs.modal', function() {
        const form = editModal.querySelector('form');
        form.reset();

        document.getElementById('keep_image').checked = true;
        currentImageSection.style.display = 'block';
        fileUploadSection.style.display = 'none';
        removeImageSection.style.display = 'none';
        manualImageSection.style.display = 'none';

        document.getElementById('currentImagePreview').style.display = 'none';
        document.getElementById('currentImagePath').style.display = 'none';
        document.getElementById('noImageText').style.display = 'none';
        document.getElementById('newImagePreviewContainer').style.display = 'none';

        fileInput.value = '';

        const roomTypeSelect = document.getElementById('id_roomType');
        const statusSelect = document.getElementById('id_status');
        roomTypeSelect.style.color = '#6c757d';
        statusSelect.style.color = '#6c757d';

        document.getElementById('id_manualImagePath').value = '';
        document.getElementById('manualImagePreviewContainer').style.display = 'none';
    });
});
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
<?php
    include_once "../services/toast.php";
?>
</body>
</html>