<?php
require_once "../services/dbaseconnection.php";
session_start();

$is_logged_in = isset($_SESSION['is_logged_in']) ? $_SESSION['is_logged_in'] : false;
$account_type = $is_logged_in ? $_SESSION['account_type'] : null;
$status = $is_logged_in ? $_SESSION['status'] : null;
$fullName = $is_logged_in ? $_SESSION['fullName'] : null;
$userName = $is_logged_in ? $_SESSION['userName'] : null;
$admin_user_id = $is_logged_in ? $_SESSION['user_id'] : null;

if ($is_logged_in && $status === 'pending') {
    header("Location: ../main/verify.php");
    exit();
}

// Restrict access to Admin only
if (!$is_logged_in || $account_type !== 'admin') {
    header("Location: ../main/home.php");
    exit();
}
// Enable/Disable button
// change status and check if button has user_id
if (isset($_POST['status_change']) && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];

    // Get the target user's account type and current status
    $user_result = $SQL_connection->query("SELECT account_type, status, user_name FROM tbl_users WHERE user_id = $user_id");
    if ($user_result && $user_result->num_rows > 0) {
        $user_row = $user_result->fetch_assoc();
        $target_account_type = $user_row['account_type'];
        $target_userName = $user_row['user_name'];
        $current_status = $user_row['status'];

        if ($current_status === 'active') {
            $update_query = "UPDATE tbl_users SET status = 'inactive' WHERE user_id = $user_id";
            if ($SQL_connection->query($update_query)) {
                $SQL_log = "INSERT INTO tbl_logs (user_id, user_name, account_type, action, datetime)
                    VALUES ('$admin_user_id', '$userName', '$account_type', '(Operator) Changed account status of $target_userName to inactive" . "', NOW())";
                $SQL_connection->query($SQL_log);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => "$target_userName's status was changed successfully",
                    'show_progress' => true,
                    'duration' => 5000,
                ];
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => "Error occured.",
                    'show_progress' => true,
                    'duration' => 5000,
                ];
            }
        } else {
            $update_query = "UPDATE tbl_users SET status = 'active' WHERE user_id = $user_id";
            if ($SQL_connection->query($update_query)) {
                $SQL_log = "INSERT INTO tbl_logs (user_id, user_name, account_type, action, datetime)
                    VALUES ('$admin_user_id', '$userName', '$account_type', '(Operator) Changed account status of $target_userName to active" . "', NOW())";
                $SQL_connection->query($SQL_log);

                $_SESSION['toast'] = [
                    'type' => 'success',
                    'message' => "$target_userName's status was changed successfully",
                    'show_progress' => true,
                    'duration' => 5000,
                ];
            } else {
                $_SESSION['toast'] = [
                    'type' => 'error',
                    'message' => "Error occured.",
                    'show_progress' => true,
                    'duration' => 5000,
                ];
            }
        }
        
        header("Location: admin-users.php");
        exit();
    }
}

// Handle Edit User Form Submission
if (isset($_POST['button_update']) && isset($_POST['input_userId'])) {
    $user_id = (int)$_POST['input_userId'];
    $userName_input = trim($_POST['input_userName']);
    $email_input = trim($_POST['input_email']);
    $firstName_input = trim($_POST['input_firstName']);
    $lastName_input = trim($_POST['input_lastName']);
    $phoneNumber_input = trim($_POST['input_phoneNumber']);
    $address_input = trim($_POST['input_address']);
    $accountType_input = $_POST['input_accountType'];
    $status_input = $_POST['input_status'];
    
    // Validate that required fields are not empty
    if (empty($userName_input) || empty($email_input) || empty($firstName_input) || 
        empty($lastName_input) || empty($phoneNumber_input) || empty($address_input) || 
        empty($accountType_input) || empty($status_input)) {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'All fields are required.',
            'show_progress' => true,
            'duration' => 5000,
        ];
        header("Location: admin-users.php");
        exit();
    }
    
    $check_query = "SELECT user_id FROM tbl_users WHERE user_name = '$userName_input' AND user_id != $user_id";
    $check_result = $SQL_connection->query($check_query);
    
    if ($check_result && $check_result->num_rows > 0) {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Username already exists.',
            'show_progress' => true,
            'duration' => 5000,
        ];
        header("Location: admin-users.php");
        exit();
    }
    
    // Get current user data for logging
    $current_user_query = "SELECT user_name FROM tbl_users WHERE user_id = $user_id";
    $current_user_result = $SQL_connection->query($current_user_query);
    $target_userName = '';
    if ($current_user_result && $current_user_result->num_rows > 0) {
        $current_user_row = $current_user_result->fetch_assoc();
        $target_userName = $current_user_row['user_name'];
    }
    
    // Update user information
    $update_query = "UPDATE tbl_users SET 
                    user_name = '$userName_input',
                    email = '$email_input',
                    first_name = '$firstName_input',
                    last_name = '$lastName_input',
                    phone_number = '$phoneNumber_input',
                    address = '$address_input',
                    account_type = '$accountType_input',
                    status = '$status_input'
                    WHERE user_id = $user_id";
    
    if ($SQL_connection->query($update_query)) {
        // Logs
        $SQL_log = "INSERT INTO tbl_logs (user_id, user_name, account_type, action, datetime)
                    VALUES ('$admin_user_id', '$userName', '$account_type', '(Operator) Updated user information for $target_userName', NOW())";
        $SQL_connection->query($SQL_log);
        
        $_SESSION['toast'] = [
            'type' => 'success',
            'message' => 'User information updated successfully.',
            'show_progress' => true,
            'duration' => 5000,
        ];
    } else {
        $_SESSION['toast'] = [
            'type' => 'error',
            'message' => 'Error occurred while updating user information.',
            'show_progress' => true,
            'duration' => 5000,
        ];
    }
    
    header("Location: admin-users.php");
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
    <link href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css" rel="stylesheet" />
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
                            <a class="nav-link active href="<?= $account_type == 'admin' ? 'admin-users.php' : 'admin-rooms.php' ?>">
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
                        <a class="nav-link active" href="admin-users.php">Users</a>
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
        <form action="admin-users.php" method="post" class="mb-3">
            <div class="row align-items-center mb-3">
                <div class="col-md-6">
                    <input type="search" name="searchInput" placeholder="Search by Name, Email, Account Type, Status, and etc." class="form-control" value="<?= $_POST['searchInput'] ?? '' ?>" />
                </div>
                <div class="col-auto ps-0">
                    <input type="submit" value="Search" name="search" class="btn btn-light cx-button-admin"/>
                </div>
                <div class="col-auto ps-0">
                    <a href="admin-users.php" class="btn btn-light cx-button-admin">Clear</a>
                </div>
                <div class="col-auto ps-0">
                    <a href="admin-register.php" class="btn btn-light cx-button-admin">
                        <i class="fa-solid fa-arrow-up-right-from-square me-2"></i>Add
                    </a>
                </div>
            </div>
        </form>

        <?php
        $searchInput = $_POST['searchInput'] ?? '';
        if (isset($_POST['search']) && !empty($searchInput)) {
            $SQL_query = "SELECT * FROM tbl_users
                        WHERE user_name LIKE '%$searchInput%'
                            OR first_name LIKE '%$searchInput%'
                            OR last_name LIKE '%$searchInput%'
                            OR email LIKE '%$searchInput%'
                            OR phone_number LIKE '%$searchInput%'
                            OR account_type LIKE '%$searchInput%'
                            OR status LIKE '%$searchInput%'
                            OR address LIKE '%$searchInput%'";
        } else { 
            $SQL_query = "SELECT * FROM tbl_users";
        }

        $SQL_result = $SQL_connection->query($SQL_query);

        if ($SQL_result && $SQL_result->num_rows > 0): ?>
            <div class="cx-table-wrapper">
                <table class="cx-table">
                    <thead>
                        <tr>
                            <th>User ID</th>
                            <th>Account Type</th>
                            <th>Email</th>
                            <th>Username</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($SQL_row = $SQL_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $SQL_row['user_id'] ?></td>
                                <td>
                                    <?php
                                    if ($SQL_row['account_type'] === 'admin') {
                                        echo '<span class="cx-badge cx-badge-admin">admin</span>';
                                    } elseif ($SQL_row['account_type'] === 'employee') {
                                        echo '<span class="cx-badge cx-badge-employee">employee</span>';
                                    } else {
                                        echo '<span class="cx-badge cx-badge-customer">customer</span>';
                                    }
                                    ?>
                                </td>
                                <td><?= $SQL_row['email'] ?></td>
                                <td><?= $SQL_row['user_name'] ?></td>
                                <td><?= $SQL_row['first_name'] ?></td>
                                <td><?= $SQL_row['last_name'] ?></td>
                                <td><?= $SQL_row['phone_number'] ?></td>
                                <td class="cx-col-address"><?= $SQL_row['address'] ?></td>
                                <td>
                                    <?php
                                    if ($SQL_row['status'] === 'active') {
                                        echo '<span class="cx-badge cx-badge-active">active</span>';
                                    } else if ($SQL_row['status'] === 'inactive') {
                                        echo '<span class="cx-badge cx-badge-inactive">inactive</span>';
                                    } else if ($SQL_row['status'] === 'pending') {
                                        echo '<span class="cx-badge cx-badge-pending">pending</span>';
                                    }
                                    ?>
                                </td>
                                    <td>
                                        <div class="cx-actions">
                                            <!-- Edit Button -->
                                            <button type="button" class="btn btn-primary cx-button-action edit-button"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editUsersModal"
                                                data-user-id="<?= $SQL_row['user_id']?>"
                                                data-accountType="<?= $SQL_row['account_type']?>"
                                                data-email="<?= $SQL_row['email']?>"
                                                data-user-name="<?= $SQL_row['user_name']?>"
                                                data-first-name="<?= $SQL_row['first_name']?>"
                                                data-last-name="<?= $SQL_row['last_name']?>"
                                                data-phoneNumber="<?= $SQL_row['phone_number']?>"
                                                data-address="<?= $SQL_row['address']?>"
                                                data-status="<?= $SQL_row['status']?>">
                                                <i class="fa-solid fa-pencil me-2"></i>Edit
                                            </button>
                                            <!-- Status Button -->
                                            <form method="POST" action="admin-users.php" style="display:inline;">
                                            <input type="hidden" name="user_id" value="<?= $SQL_row['user_id'] ?>">
                                            <?php
                                                if ($SQL_row['status'] === 'active') {
                                                    ?><button type="submit" name="status_change" class="btn cx-button-action btn-danger">
                                                        <i class="fa-solid fa-toggle-on me-2"></i>Disable
                                                    </button><?php
                                                } else {
                                                    ?><button type="submit" name="status_change" class="btn cx-button-action btn-success">
                                                        <i class="fa-solid fa-toggle-off me-2"></i>Enable
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
    <div class="modal fade" id="editUsersModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="admin-users.php" method="post">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Edit Users</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="input_userId" id="id_userId">
                        <input type="hidden" id="original_status">
                        <div class="mb-2">
                            <span class="text-muted fs-6">Account Credentials</span>
                        </div>
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="input_userName" id="id_userName" placeholder="Username" required>
                                <label for="id_userName" class="form-label">Username</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="email" class="form-control" name="input_email" id="id_email" placeholder="Email" required>
                                <label for="id_email" class="form-label">Email</label>
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted fs-6">Personal Information</span>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="input_firstName" id="id_firstName" placeholder="First Name" required>
                                    <label for="id_firstName" class="form-label">First Name</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="input_lastName" id="id_lastName"
                                        placeholder="id_Last Name" required>
                                    <label for="lastName" class="form-label">Last Name</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="input_address" id="id_address" placeholder="Address" required>
                                <label for="id_address" class="form-label">Address</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" name="input_phoneNumber" id="id_phoneNumber" placeholder="Phone Number" required>
                                <label for="id_phone" class="form-label">Phone Number</label>
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="text-muted fs-6">Admin Fields</span>
                        </div>
                        <div class="mb-3">
                            <div class="form-floating">
                                <div class="form-floating mb-3">
                                    <select class="form-select" name="input_accountType" id="id_accountType" required>
                                        <option value="" selected disabled hidden>Select an account type</option>
                                        <option value="customer">Customer</option>
                                        <option value="employee">Employee</option>
                                        <option value="admin">Admin</option>
                                        
                                    </select>
                                    <label for="id_accountType">Account Type</label>
                                </div>

                                <script>
                                    (function () {
                                        const select = document.getElementById('id_accountType');
                                        
                                        select.style.color = '#6c757d';
                                        
                                        const style = document.createElement('style');
                                        style.textContent = `
                                            #id_accountType option:not([disabled]) {
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
                                <div class="form-floating mb-3">
                                    <select class="form-select" name="input_status" id="id_status" required>
                                        <option value="" selected disabled hidden>Select Status</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="pending">Pending</option>
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
                        
                        <div id="pendingWarning" class="alert alert-warning" style="display: none;">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            Cannot change status from Active/Inactive to Pending.
                        </div>
                        

                    </div>
                    <div class="modal-footer">
                        <button type="submit" name="button_update" id="updateButton" class="btn btn-primary cx-button-admin">Update</button>
                    </div>
                </form>
            </div>
        </div>
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
</script>
</body>
</html>