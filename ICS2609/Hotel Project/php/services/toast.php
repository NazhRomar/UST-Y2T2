<?php

// Basic toast
// $_SESSION['toast'] = [
//     'type' => 'success',
//     'message' => 'Profile updated successfully!',
//     'show_progress' => true,
//     'duration' => 5000,
// ];

// Danger toast with long duration
// $_SESSION['toast'] = [
//     'type' => 'danger',
//     'message' => 'Username already exists.',
//     'show_progress' => true,
//     'duration' => 10000,
// ];

// Warning toast without auto-hide
// $_SESSION['toast'] = [
//     'type' => 'warning',
//     'message' => 'Your session will expire in 5 minutes.',
//     'auto_hide' => false,
//     'show_progress' => false,
// ];

// Info toast with default settings
// $_SESSION['toast'] = [
//     'type' => 'info',
//     'message' => 'New feature available! Check out the dashboard.',
// ];




if (isset($_SESSION['toast'])):
    $auto_hide = $_SESSION['toast']['auto_hide'] ?? true;
    $duration = $_SESSION['toast']['duration'] ?? 5000;
    $show_progress = $_SESSION['toast']['show_progress'] ?? false;
    $type = $_SESSION['toast']['type'] ?? 'info';
    $message = $_SESSION['toast']['message'] ?? '';

    $colors = [
        'success' => '#198754',
        'danger'  => '#dc3545',
        'warning' => '#ffc107',
        'info'    => '#0dcaf0'
    ];
    $color = $colors[$type] ?? '#0dcaf0';

    $iconClass = [
        'success' => 'fa-circle-check',
        'danger'  => 'fa-circle-xmark',
        'warning' => 'fa-triangle-exclamation',
        'info'    => 'fa-circle-info'
    ][$type] ?? 'fa-circle-info';
?>
    <div class="position-fixed bottom-0 start-0 p-3" style="z-index: 99999">
        <div id="liveToast" class="toast border-0 shadow position-relative" role="alert" style="background-color: #fff; border-radius: 8px; overflow: hidden;">
            
            <div class="d-flex justify-content-between align-items-start p-3" style="padding-left: 1.25rem;">
                <div class="d-flex align-items-center">
                    <i class="fa-solid <?php echo $iconClass; ?> me-2" style="color: <?php echo $color; ?>; font-size: 1.1em;"></i>
                    <div id="toastMessage" class="toast-body p-0 text-dark cx-toast">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                </div>
                <button type="button" class="btn-close ms-3 flex-shrink-0" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>

            <?php if ($show_progress && $auto_hide): ?>
                <div class="position-absolute bottom-0 start-0 w-100" style="height: 2px; background-color: #d9d9d9">
                    <div id="toastProgressBar" class="h-100"
                        style="width: 100%; background-color: <?php echo $color; ?>; transition: width linear;">
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toastContainer = document.getElementById('liveToast');
            const progressBar = document.getElementById('toastProgressBar');
            const duration = <?php echo (int)$duration; ?>;
            const autoHide = <?php echo $auto_hide ? 'true' : 'false'; ?>;
            const showProgress = <?php echo $show_progress ? 'true' : 'false'; ?>;

            const toast = new bootstrap.Toast(toastContainer, { autohide: false });

            toastContainer.addEventListener('shown.bs.toast', () => {
                if (autoHide) {
                    if (showProgress && progressBar) {
                        progressBar.style.transitionDuration = duration + 'ms';
                        
                        setTimeout(() => {
                            progressBar.style.width = '0%';
                        }, 50);
                        
                        setTimeout(() => {
                            bootstrap.Toast.getInstance(toastContainer).hide();
                        }, duration);
                    } else {
                        setTimeout(() => {
                            bootstrap.Toast.getInstance(toastContainer).hide();
                        }, duration);
                    }
                }
            });

            toast.show();
        });
    </script>

    <?php unset($_SESSION['toast']); ?>
<?php endif; ?>