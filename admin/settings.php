<?php
// Thiết lập tiêu đề trang
$pageTitle = "Cài đặt hệ thống";

// Include các file cần thiết
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../auth/auth_functions.php';

// Kiểm tra đăng nhập và quyền admin
requireAdmin();

// Lấy thông tin người dùng
$currentUser = getCurrentUser();

// Khởi tạo biến cài đặt
$settings = array();

// Kết nối database
$conn = getConnection();

// Kiểm tra và tạo bảng settings nếu chưa tồn tại
$tableExistsQuery = "SHOW TABLES LIKE 'settings'";
$tableExists = mysqli_query($conn, $tableExistsQuery);

if (mysqli_num_rows($tableExists) == 0) {
    // Tạo bảng settings
    $createTableSQL = "CREATE TABLE settings (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(50) NOT NULL UNIQUE,
        setting_value TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";
    
    if (!mysqli_query($conn, $createTableSQL)) {
        die("Lỗi khi tạo bảng cài đặt: " . mysqli_error($conn));
    }

    // Thêm dữ liệu mặc định
    $defaultSettings = [
        ['company_name', 'Car Rental'],
        ['company_address', '123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh'],
        ['company_phone', '0987654321'],
        ['company_email', 'info@carrental.com'],
        ['booking_limit', '3'],
        ['maintenance_mode', '0'],
        ['default_currency', 'VND'],
        ['tax_rate', '10'],
        ['min_rental_days', '1']
    ];

    foreach ($defaultSettings as $setting) {
        $insertQuery = "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $insertQuery);
        mysqli_stmt_bind_param($stmt, "ss", $setting[0], $setting[1]);
        mysqli_stmt_execute($stmt);
    }
}

// Lấy cài đặt từ database
$getSettings = "SELECT * FROM settings";
$result = mysqli_query($conn, $getSettings);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
}

// Đặt giá trị mặc định nếu không có trong database
if (!isset($settings['company_name'])) $settings['company_name'] = 'Car Rental';
if (!isset($settings['company_address'])) $settings['company_address'] = '123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh';
if (!isset($settings['company_phone'])) $settings['company_phone'] = '0987654321';
if (!isset($settings['company_email'])) $settings['company_email'] = 'info@carrental.com';
if (!isset($settings['booking_limit'])) $settings['booking_limit'] = '3';
if (!isset($settings['maintenance_mode'])) $settings['maintenance_mode'] = '0';
if (!isset($settings['default_currency'])) $settings['default_currency'] = 'VND';
if (!isset($settings['tax_rate'])) $settings['tax_rate'] = '10';
if (!isset($settings['min_rental_days'])) $settings['min_rental_days'] = '1';

// Xử lý form cập nhật cài đặt
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $companyName = sanitizeInput($_POST['company_name']);
    $companyAddress = sanitizeInput($_POST['company_address']);
    $companyPhone = sanitizeInput($_POST['company_phone']);
    $companyEmail = sanitizeInput($_POST['company_email']);
    $bookingLimit = (int)$_POST['booking_limit'];
    $maintenanceMode = isset($_POST['maintenance_mode']) ? '1' : '0';
    $defaultCurrency = sanitizeInput($_POST['default_currency']);
    $taxRate = (float)$_POST['tax_rate'];
    $minRentalDays = (int)$_POST['min_rental_days'];
    
    // Validate dữ liệu
    $errors = array();
    
    if (empty($companyName)) {
        $errors[] = "Tên công ty không được để trống";
    }
    
    if (empty($companyEmail) || !filter_var($companyEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email công ty không hợp lệ";
    }
    
    if ($bookingLimit < 1) {
        $errors[] = "Giới hạn đặt xe không hợp lệ";
    }
    
    if ($taxRate < 0 || $taxRate > 100) {
        $errors[] = "Thuế suất không hợp lệ";
    }
    
    if ($minRentalDays < 1) {
        $errors[] = "Số ngày thuê tối thiểu không hợp lệ";
    }
    
    // Nếu không có lỗi, cập nhật cài đặt
    if (empty($errors)) {
        // Mảng cài đặt cần cập nhật
        $settingsToUpdate = array(
            'company_name' => $companyName,
            'company_address' => $companyAddress,
            'company_phone' => $companyPhone,
            'company_email' => $companyEmail,
            'booking_limit' => $bookingLimit,
            'maintenance_mode' => $maintenanceMode,
            'default_currency' => $defaultCurrency,
            'tax_rate' => $taxRate,
            'min_rental_days' => $minRentalDays
        );
        
        if (empty($errors)) {
            // Cập nhật hoặc thêm mới cài đặt
            foreach ($settingsToUpdate as $key => $value) {
                // Kiểm tra cài đặt đã tồn tại chưa
                $checkSetting = "SELECT id FROM settings WHERE setting_key = ?";
                $stmt = mysqli_prepare($conn, $checkSetting);
                mysqli_stmt_bind_param($stmt, "s", $key);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);
                
                if (mysqli_stmt_num_rows($stmt) > 0) {
                    // Cập nhật cài đặt
                    $updateSetting = "UPDATE settings SET setting_value = ? WHERE setting_key = ?";
                    $stmt = mysqli_prepare($conn, $updateSetting);
                    mysqli_stmt_bind_param($stmt, "ss", $value, $key);
                } else {
                    // Thêm mới cài đặt
                    $insertSetting = "INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)";
                    $stmt = mysqli_prepare($conn, $insertSetting);
                    mysqli_stmt_bind_param($stmt, "ss", $key, $value);
                }
                
                if (!mysqli_stmt_execute($stmt)) {
                    $errors[] = "Lỗi khi cập nhật cài đặt: " . mysqli_error($conn);
                    break;
                }
            }
            
            if (empty($errors)) {
                $_SESSION['message'] = "Cập nhật cài đặt hệ thống thành công!";
                $_SESSION['message_type'] = "success";
                
                // Cập nhật lại biến cài đặt
                $settings = $settingsToUpdate;
            }
        }
    }
}

// CSS cho trang admin
$extraCSS = '<link rel="stylesheet" href="/assets/css/admin.css">';

// Include header
include '../includes/header.php';
?>

<!-- Admin Wrapper -->
<div class="admin-wrapper">
    <!-- Admin Sidebar -->
    <div class="admin-sidebar">
        <div class="admin-sidebar-header">
            <div class="admin-logo">
                <i class="fas fa-car"></i> ADMIN
            </div>
            <button class="admin-sidebar-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
        
        <div class="admin-nav">
            <div class="admin-nav-category">
                Dashboard
            </div>
            <ul class="list-unstyled">
                <li class="admin-nav-item">
                    <a href="index.php" class="admin-nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Tổng quan</span>
                    </a>
                </li>
            </ul>
            
            <div class="admin-nav-category">
                Quản lý
            </div>
            <ul class="list-unstyled">
                <li class="admin-nav-item">
                    <a href="cars/index.php" class="admin-nav-link">
                        <i class="fas fa-car"></i>
                        <span>Quản lý xe</span>
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="bookings/index.php" class="admin-nav-link">
                        <i class="fas fa-calendar-check"></i>
                        <span>Quản lý đặt xe</span>
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="users/index.php" class="admin-nav-link">
                        <i class="fas fa-users"></i>
                        <span>Quản lý người dùng</span>
                    </a>
                </li>
            </ul>
            
            <div class="admin-nav-category">
                Cài đặt
            </div>
            <ul class="list-unstyled">
                <li class="admin-nav-item">
                    <a href="settings.php" class="admin-nav-link active">
                        <i class="fas fa-cog"></i>
                        <span>Cài đặt hệ thống</span>
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="../auth/logout.php" class="admin-nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Đăng xuất</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    
    <!-- Admin Content -->
    <div class="admin-content">
        <!-- Admin Header -->
        <div class="admin-header">
            <h4 class="admin-header-title">Cài đặt hệ thống</h4>
            
            <div class="admin-header-actions">
                <div class="admin-notification">
                    <i class="fas fa-bell"></i>
                    <span class="admin-notification-badge">3</span>
                </div>
                
                <div class="admin-user-dropdown dropdown">
                    <a href="#" class="dropdown-toggle text-decoration-none" id="adminUserDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="admin-user-avatar">
                            <?php echo substr($currentUser['full_name'], 0, 1); ?>
                        </div>
                        <span class="admin-user-name d-none d-md-inline-block">
                            <?php echo htmlspecialchars($currentUser['full_name']); ?>
                        </span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminUserDropdown">
                        <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Hồ sơ</a></li>
                        <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Cài đặt</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Settings Content -->
        <div class="container-fluid py-4">
            <!-- Page Header -->
            <div class="admin-page-header">
                <div class="row align-items-center">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <h3 class="admin-page-title mb-0">Cài đặt hệ thống</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Cài đặt hệ thống</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            
            <?php echo displayMessage(); ?>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="settings-form">
                <div class="row">
                    <!-- Company Settings -->
                    <div class="col-lg-6">
                        <div class="admin-card mb-4">
                            <div class="admin-card-header">
                                <h5 class="admin-card-title">Thông tin công ty</h5>
                            </div>
                            <div class="admin-card-body">
                                <div class="admin-form-group">
                                    <label for="company_name" class="admin-form-label">Tên công ty <span class="text-danger">*</span></label>
                                    <input type="text" id="company_name" name="company_name" class="admin-form-control" value="<?php echo htmlspecialchars($settings['company_name']); ?>" required>
                                </div>
                                
                                <div class="admin-form-group">
                                    <label for="company_address" class="admin-form-label">Địa chỉ</label>
                                    <textarea id="company_address" name="company_address" class="admin-form-control" rows="2"><?php echo htmlspecialchars($settings['company_address']); ?></textarea>
                                </div>
                                
                                <div class="admin-form-group">
                                    <label for="company_phone" class="admin-form-label">Số điện thoại</label>
                                    <input type="text" id="company_phone" name="company_phone" class="admin-form-control" value="<?php echo htmlspecialchars($settings['company_phone']); ?>">
                                </div>
                                
                                <div class="admin-form-group">
                                    <label for="company_email" class="admin-form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="company_email" name="company_email" class="admin-form-control" value="<?php echo htmlspecialchars($settings['company_email']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Email Settings -->
                        <div class="admin-card mb-4">
                            <div class="admin-card-header">
                                <h5 class="admin-card-title">Cài đặt email</h5>
                            </div>
                            <div class="admin-card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Chức năng cài đặt email sẽ được phát triển trong phiên bản tiếp theo.
                                </div>
                                
                                <div class="admin-form-group mb-0">
                                    <button type="button" class="admin-btn admin-btn-primary" disabled>
                                        <i class="fas fa-envelope admin-btn-icon"></i> Kiểm tra cài đặt email
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- System Settings -->
                    <div class="col-lg-6">
                        <div class="admin-card mb-4">
                            <div class="admin-card-header">
                                <h5 class="admin-card-title">Cài đặt hệ thống</h5>
                            </div>
                            <div class="admin-card-body">
                                <div class="admin-form-group">
                                    <label for="booking_limit" class="admin-form-label">Giới hạn đặt xe tối đa</label>
                                    <input type="number" id="booking_limit" name="booking_limit" class="admin-form-control" min="1" value="<?php echo (int)$settings['booking_limit']; ?>">
                                    <div class="form-text">Số lượng đơn đặt xe tối đa mà một người dùng có thể đặt đồng thời.</div>
                                </div>
                                
                                <div class="admin-form-group">
                                    <label for="min_rental_days" class="admin-form-label">Số ngày thuê tối thiểu</label>
                                    <input type="number" id="min_rental_days" name="min_rental_days" class="admin-form-control" min="1" value="<?php echo (int)$settings['min_rental_days']; ?>">
                                </div>
                                
                                <div class="admin-form-group">
                                    <label for="default_currency" class="admin-form-label">Đơn vị tiền tệ</label>
                                    <select id="default_currency" name="default_currency" class="admin-form-control">
                                        <option value="VND" <?php echo $settings['default_currency'] == 'VND' ? 'selected' : ''; ?>>VND - Việt Nam Đồng</option>
                                        <option value="USD" <?php echo $settings['default_currency'] == 'USD' ? 'selected' : ''; ?>>USD - US Dollar</option>
                                    </select>
                                </div>
                                
                                <div class="admin-form-group">
                                    <label for="tax_rate" class="admin-form-label">Thuế suất (%)</label>
                                    <input type="number" id="tax_rate" name="tax_rate" class="admin-form-control" min="0" max="100" step="0.1" value="<?php echo (float)$settings['tax_rate']; ?>">
                                </div>
                                
                                <div class="admin-form-check form-switch mb-3">
                                    <input type="checkbox" class="form-check-input" id="maintenance_mode" name="maintenance_mode" <?php echo $settings['maintenance_mode'] == '1' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="maintenance_mode">Chế độ bảo trì</label>
                                    <div class="form-text">Bật chế độ này sẽ hiển thị trang bảo trì cho tất cả người dùng trừ admin.</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Backup & Restore -->
                        <div class="admin-card mb-4">
                            <div class="admin-card-header">
                                <h5 class="admin-card-title">Sao lưu & Phục hồi</h5>
                            </div>
                            <div class="admin-card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i> Chức năng sao lưu và phục hồi sẽ được phát triển trong phiên bản tiếp theo.
                                </div>
                                
                                <div class="d-flex gap-2">
                                    <button type="button" class="admin-btn admin-btn-primary flex-grow-1" disabled>
                                        <i class="fas fa-download admin-btn-icon"></i> Sao lưu dữ liệu
                                    </button>
                                    <button type="button" class="admin-btn admin-btn-outline flex-grow-1" disabled>
                                        <i class="fas fa-upload admin-btn-icon"></i> Phục hồi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="admin-form-actions text-center">
                    <button type="submit" class="admin-btn admin-btn-primary">
                        <i class="fas fa-save admin-btn-icon"></i> Lưu cài đặt
                    </button>
                    <button type="reset" class="admin-btn admin-btn-outline">
                        <i class="fas fa-undo admin-btn-icon"></i> Đặt lại
                    </button>
                </div>
            </form>
            
            <!-- System Information -->
            <div class="admin-card mt-4">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">Thông tin hệ thống</h5>
                </div>
                <div class="admin-card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Phiên bản PHP:</span>
                                <span class="fw-semibold"><?php echo phpversion(); ?></span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex justify-content-between">
                                <span>MySQL Version:</span>
                                <span class="fw-semibold"><?php echo mysqli_get_server_info($conn); ?></span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Phiên bản hệ thống:</span>
                                <span class="fw-semibold">1.0.0</span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Thời gian hiện tại:</span>
                                <span class="fw-semibold"><?php echo date('Y-m-d H:i:s'); ?></span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Múi giờ:</span>
                                <span class="fw-semibold"><?php echo date_default_timezone_get(); ?></span>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Memory Limit:</span>
                                <span class="fw-semibold"><?php echo ini_get('memory_limit'); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="admin-card-footer">
                    <button type="button" class="admin-btn admin-btn-outline w-100" id="check-for-updates" disabled>
                        <i class="fas fa-sync-alt admin-btn-icon"></i> Kiểm tra cập nhật
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Admin Dashboard Scripts -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle Sidebar
        const sidebarToggle = document.querySelector('.admin-sidebar-toggle');
        const adminSidebar = document.querySelector('.admin-sidebar');
        const adminContent = document.querySelector('.admin-content');
        
        sidebarToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('admin-sidebar-collapsed');
            adminContent.classList.toggle('admin-content-expanded');
        });
        
        // Form Validation
        const settingsForm = document.getElementById('settings-form');
        
        settingsForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Validate company name
            const companyName = document.getElementById('company_name');
            if (companyName.value.trim() === '') {
                alert('Vui lòng nhập tên công ty');
                isValid = false;
            }
            
            // Validate email
            const companyEmail = document.getElementById('company_email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(companyEmail.value)) {
                alert('Email công ty không hợp lệ');
                isValid = false;
            }
            
            // Validate booking limit
            const bookingLimit = document.getElementById('booking_limit');
            if (bookingLimit.value < 1) {
                alert('Giới hạn đặt xe không hợp lệ');
                isValid = false;
            }
            
            // Validate min rental days
            const minRentalDays = document.getElementById('min_rental_days');
            if (minRentalDays.value < 1) {
                alert('Số ngày thuê tối thiểu không hợp lệ');
                isValid = false;
            }
            
            // Validate tax rate
            const taxRate = document.getElementById('tax_rate');
            if (taxRate.value < 0 || taxRate.value > 100) {
                alert('Thuế suất không hợp lệ (0-100%)');
                isValid = false;
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    });
</script>

<style>
    /* Additional custom styles */
    .admin-page-header {
        margin-bottom: 1.5rem;
    }
    
    .admin-page-title {
        font-weight: 600;
        color: #333;
    }
    
    .form-switch .form-check-input {
        width: 3em;
        height: 1.5em;
        margin-top: 0;
    }
    
    .form-switch .form-check-label {
        padding-left: 0.5em;
    }
</style>

<?php
// Không include footer vì trang admin sử dụng layout riêng
?>
</body>
</html>