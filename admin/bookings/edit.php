<?php
// Thiết lập tiêu đề trang
$pageTitle = "Cập nhật đơn đặt xe";

// Include các file cần thiết
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../auth/auth_functions.php';

// Kiểm tra đăng nhập và quyền admin
requireAdmin();

// Lấy thông tin người dùng
$currentUser = getCurrentUser();

// Lấy ID đặt xe từ URL
$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Kiểm tra đơn đặt xe có tồn tại không
$booking = getBookingById($bookingId);

if (!$booking) {
    $_SESSION['message'] = "Không tìm thấy đơn đặt xe với ID: $bookingId";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit;
}

// Lấy thông tin xe
$car = getCarById($booking['car_id']);

// Lấy thông tin người dùng
$user = getUserById($booking['user_id']);

// Xử lý form cập nhật đơn đặt xe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $status = sanitizeInput($_POST['status']);
    $paymentStatus = sanitizeInput($_POST['payment_status']);
    $pickupDate = sanitizeInput($_POST['pickup_date']);
    $returnDate = sanitizeInput($_POST['return_date']);
    $pickupLocation = sanitizeInput($_POST['pickup_location']);
    $returnLocation = sanitizeInput($_POST['return_location']);
    $totalPrice = (float)$_POST['total_price'];
    $adminNotes = isset($_POST['admin_notes']) ? sanitizeInput($_POST['admin_notes']) : '';
    
    // Validate dữ liệu
    $errors = array();
    
    if (empty($status) || !in_array($status, ['pending', 'confirmed', 'completed', 'cancelled'])) {
        $errors[] = "Trạng thái đơn đặt xe không hợp lệ";
    }
    
    if (empty($paymentStatus) || !in_array($paymentStatus, ['pending', 'paid'])) {
        $errors[] = "Trạng thái thanh toán không hợp lệ";
    }
    
    if (empty($pickupDate)) {
        $errors[] = "Ngày nhận xe không được để trống";
    }
    
    if (empty($returnDate)) {
        $errors[] = "Ngày trả xe không được để trống";
    }
    
    if (strtotime($returnDate) < strtotime($pickupDate)) {
        $errors[] = "Ngày trả xe phải sau ngày nhận xe";
    }
    
    if (empty($pickupLocation)) {
        $errors[] = "Địa điểm nhận xe không được để trống";
    }
    
    if (empty($returnLocation)) {
        $errors[] = "Địa điểm trả xe không được để trống";
    }
    
    if (empty($totalPrice) || $totalPrice <= 0) {
        $errors[] = "Tổng tiền không hợp lệ";
    }
    
    // Nếu không có lỗi, cập nhật đơn đặt xe
    if (empty($errors)) {
        $conn = getConnection();
        
        // Cập nhật đơn đặt xe
        $updateBooking = "UPDATE bookings SET 
                          status = ?, 
                          payment_status = ?, 
                          pickup_date = ?, 
                          return_date = ?, 
                          pickup_location = ?, 
                          return_location = ?, 
                          total_price = ?,
                          admin_notes = ?
                          WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $updateBooking);
        mysqli_stmt_bind_param($stmt, "ssssssdsi", $status, $paymentStatus, $pickupDate, $returnDate, $pickupLocation, $returnLocation, $totalPrice, $adminNotes, $bookingId);
        
        // Nếu trạng thái đơn đặt xe thay đổi, cập nhật trạng thái xe
        if ($status != $booking['status']) {
            // Nếu cập nhật sang trạng thái đã xác nhận, cập nhật xe sang trạng thái đang cho thuê
            if ($status == 'confirmed' && $booking['status'] != 'confirmed') {
                $updateCar = "UPDATE cars SET status = 'rented' WHERE id = ?";
                $stmtCar = mysqli_prepare($conn, $updateCar);
                mysqli_stmt_bind_param($stmtCar, "i", $booking['car_id']);
                mysqli_stmt_execute($stmtCar);
            }
            
            // Nếu cập nhật sang trạng thái hoàn thành hoặc hủy, cập nhật xe sang trạng thái sẵn sàng
            if (($status == 'completed' || $status == 'cancelled') && ($booking['status'] == 'confirmed' || $booking['status'] == 'pending')) {
                $updateCar = "UPDATE cars SET status = 'available' WHERE id = ?";
                $stmtCar = mysqli_prepare($conn, $updateCar);
                mysqli_stmt_bind_param($stmtCar, "i", $booking['car_id']);
                mysqli_stmt_execute($stmtCar);
            }
        }
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Cập nhật đơn đặt xe thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Có lỗi xảy ra khi cập nhật đơn đặt xe. Vui lòng thử lại sau.";
        }
    }
}

// CSS cho trang admin
$extraCSS = '<link rel="stylesheet" href="/assets/css/admin.css">';

// Include header
include '../../includes/header.php';
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
                    <a href="../index.php" class="admin-nav-link">
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
                    <a href="../cars/index.php" class="admin-nav-link">
                        <i class="fas fa-car"></i>
                        <span>Quản lý xe</span>
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="index.php" class="admin-nav-link active">
                        <i class="fas fa-calendar-check"></i>
                        <span>Quản lý đặt xe</span>
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="../users/index.php" class="admin-nav-link">
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
                    <a href="../settings.php" class="admin-nav-link">
                        <i class="fas fa-cog"></i>
                        <span>Cài đặt hệ thống</span>
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="../../auth/logout.php" class="admin-nav-link">
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
            <h4 class="admin-header-title">Cập nhật đơn đặt xe</h4>
            
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
                        <li><a class="dropdown-item" href="../../auth/logout.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Booking Edit Content -->
        <div class="container-fluid py-4">
            <!-- Page Header -->
            <div class="admin-page-header">
                <div class="row align-items-center">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <h3 class="admin-page-title mb-0">Cập nhật đơn đặt xe #<?php echo $bookingId; ?></h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="index.php">Quản lý đặt xe</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Cập nhật đơn đặt xe</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="index.php" class="admin-btn admin-btn-outline">
                            <i class="fas fa-arrow-left admin-btn-icon"></i> Quay lại danh sách
                        </a>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Booking Status -->
            <div class="admin-card mb-4">
                <div class="admin-card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="booking-status-badge mb-2">
                                <?php 
                                    switch($booking['status']) {
                                        case 'pending':
                                            echo '<span class="admin-badge admin-badge-warning py-2 px-3">Đang chờ xác nhận</span>';
                                            break;
                                        case 'confirmed':
                                            echo '<span class="admin-badge admin-badge-primary py-2 px-3">Đã xác nhận</span>';
                                            break;
                                        case 'completed':
                                            echo '<span class="admin-badge admin-badge-success py-2 px-3">Hoàn thành</span>';
                                            break;
                                        case 'cancelled':
                                            echo '<span class="admin-badge admin-badge-danger py-2 px-3">Đã hủy</span>';
                                            break;
                                    }
                                ?>
                            </div>
                            <h4 class="mb-2">Đơn đặt xe từ: <?php echo htmlspecialchars($user['full_name']); ?></h4>
                            <p class="mb-2">Xe: <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></p>
                            <p class="mb-0">Ngày đặt: <?php echo formatDate($booking['created_at']); ?></p>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <div class="payment-status mb-2">
                                <span class="admin-badge <?php echo $booking['payment_status'] == 'paid' ? 'admin-badge-success' : 'admin-badge-warning'; ?> py-2 px-3">
                                    <?php echo $booking['payment_status'] == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán'; ?>
                                </span>
                            </div>
                            <h5 class="mb-0">Tổng tiền: <?php echo formatPrice($booking['total_price']); ?></h5>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Booking Edit Form -->
            <div class="row">
                <div class="col-md-8">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Chi tiết đơn đặt xe</h5>
                        </div>
                        <div class="admin-card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $bookingId; ?>" method="post" id="booking-form">
                                <div class="row">
                                    <!-- Thông tin đặt xe -->
                                    <div class="col-md-6">
                                        <div class="admin-form-group">
                                            <label for="status" class="admin-form-label">Trạng thái đơn đặt xe <span class="text-danger">*</span></label>
                                            <select id="status" name="status" class="admin-form-control" required>
                                                <option value="pending" <?php echo $booking['status'] == 'pending' ? 'selected' : ''; ?>>Đang chờ xác nhận</option>
                                                <option value="confirmed" <?php echo $booking['status'] == 'confirmed' ? 'selected' : ''; ?>>Đã xác nhận</option>
                                                <option value="completed" <?php echo $booking['status'] == 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                                                <option value="cancelled" <?php echo $booking['status'] == 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                                            </select>
                                            <div class="form-text">Lưu ý: Thay đổi trạng thái sẽ ảnh hưởng đến trạng thái của xe.</div>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="payment_status" class="admin-form-label">Trạng thái thanh toán <span class="text-danger">*</span></label>
                                            <select id="payment_status" name="payment_status" class="admin-form-control" required>
                                                <option value="pending" <?php echo $booking['payment_status'] == 'pending' ? 'selected' : ''; ?>>Chưa thanh toán</option>
                                                <option value="paid" <?php echo $booking['payment_status'] == 'paid' ? 'selected' : ''; ?>>Đã thanh toán</option>
                                            </select>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="pickup_date" class="admin-form-label">Ngày nhận xe <span class="text-danger">*</span></label>
                                            <input type="date" id="pickup_date" name="pickup_date" class="admin-form-control" value="<?php echo $booking['pickup_date']; ?>" required>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="return_date" class="admin-form-label">Ngày trả xe <span class="text-danger">*</span></label>
                                            <input type="date" id="return_date" name="return_date" class="admin-form-control" value="<?php echo $booking['return_date']; ?>" required>
                                        </div>
                                    </div>
                                    
                                    <!-- Thông tin địa điểm và giá -->
                                    <div class="col-md-6">
                                        <div class="admin-form-group">
                                            <label for="pickup_location" class="admin-form-label">Địa điểm nhận xe <span class="text-danger">*</span></label>
                                            <input type="text" id="pickup_location" name="pickup_location" class="admin-form-control" value="<?php echo htmlspecialchars($booking['pickup_location']); ?>" required>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="return_location" class="admin-form-label">Địa điểm trả xe <span class="text-danger">*</span></label>
                                            <input type="text" id="return_location" name="return_location" class="admin-form-control" value="<?php echo htmlspecialchars($booking['return_location']); ?>" required>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="total_price" class="admin-form-label">Tổng tiền (VND) <span class="text-danger">*</span></label>
                                            <input type="number" id="total_price" name="total_price" class="admin-form-control" min="0" step="10000" value="<?php echo $booking['total_price']; ?>" required>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="admin_notes" class="admin-form-label">Ghi chú của admin</label>
                                            <textarea id="admin_notes" name="admin_notes" class="admin-form-control" rows="3"><?php echo isset($booking['admin_notes']) ? htmlspecialchars($booking['admin_notes']) : ''; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="admin-form-actions">
                                    <button type="submit" class="admin-btn admin-btn-primary">
                                        <i class="fas fa-save admin-btn-icon"></i> Lưu thay đổi
                                    </button>
                                    <a href="index.php" class="admin-btn admin-btn-outline">
                                        <i class="fas fa-times admin-btn-icon"></i> Hủy
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <!-- Customer Info -->
                    <div class="admin-card mb-4">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Thông tin khách hàng</h5>
                        </div>
                        <div class="admin-card-body">
                            <div class="customer-info">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="customer-avatar me-3">
                                        <i class="fas fa-user-circle fa-3x text-primary"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                                        <p class="mb-0 text-muted"><?php echo htmlspecialchars($user['email']); ?></p>
                                    </div>
                                </div>
                                
                                <div class="customer-details">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>ID người dùng:</span>
                                        <span class="fw-semibold">#<?php echo $user['id']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Số điện thoại:</span>
                                        <span class="fw-semibold"><?php echo htmlspecialchars($user['phone']); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Địa chỉ:</span>
                                        <span class="fw-semibold"><?php echo htmlspecialchars($user['address'] ?? 'Chưa cập nhật'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="admin-card-footer">
                            <a href="../users/edit.php?id=<?php echo $user['id']; ?>" class="admin-btn admin-btn-outline w-100">
                                <i class="fas fa-user-edit admin-btn-icon"></i> Xem chi tiết người dùng
                            </a>
                        </div>
                    </div>
                    
                    <!-- Car Info -->
                    <div class="admin-card mb-4">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Thông tin xe</h5>
                        </div>
                        <div class="admin-card-body">
                            <div class="text-center mb-3">
                                <img src="<?php echo !empty($car['image']) ? $car['image'] : '/assets/images/car-placeholder.jpg'; ?>" alt="<?php echo $car['brand'] . ' ' . $car['model']; ?>" class="img-fluid rounded" style="max-height: 120px;">
                            </div>
                            <h5 class="text-center mb-3"><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h5>
                            
                            <div class="car-details">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Biển số xe:</span>
                                    <span class="fw-semibold"><?php echo htmlspecialchars($car['license_plate']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Màu sắc:</span>
                                    <span class="fw-semibold"><?php echo htmlspecialchars($car['color']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Số chỗ ngồi:</span>
                                    <span class="fw-semibold"><?php echo $car['seats']; ?> chỗ</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Giá thuê/ngày:</span>
                                    <span class="fw-semibold"><?php echo formatPrice($car['price_per_day']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Trạng thái xe:</span>
                                    <span class="fw-semibold">
                                        <?php 
                                            switch($car['status']) {
                                                case 'available':
                                                    echo '<span class="text-success">Sẵn sàng</span>';
                                                    break;
                                                case 'maintenance':
                                                    echo '<span class="text-warning">Bảo dưỡng</span>';
                                                    break;
                                                case 'rented':
                                                    echo '<span class="text-primary">Đang cho thuê</span>';
                                                    break;
                                            }
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="admin-card-footer">
                            <a href="../cars/edit.php?id=<?php echo $car['id']; ?>" class="admin-btn admin-btn-outline w-100">
                                <i class="fas fa-car admin-btn-icon"></i> Xem chi tiết xe
                            </a>
                        </div>
                    </div>
                    
                    <!-- Booking Actions -->
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Thao tác</h5>
                        </div>
                        <div class="admin-card-body">
                            <?php if ($booking['status'] == 'pending'): ?>
                                <a href="#" class="admin-btn admin-btn-success w-100 mb-2" onclick="confirmBooking(<?php echo $bookingId; ?>)">
                                    <i class="fas fa-check-circle admin-btn-icon"></i> Xác nhận đơn đặt xe
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($booking['status'] == 'confirmed'): ?>
                                <a href="#" class="admin-btn admin-btn-primary w-100 mb-2" onclick="completeBooking(<?php echo $bookingId; ?>)">
                                    <i class="fas fa-flag-checkered admin-btn-icon"></i> Đánh dấu hoàn thành
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($booking['status'] != 'cancelled' && $booking['status'] != 'completed'): ?>
                                <a href="#" class="admin-btn admin-btn-danger w-100 mb-2" onclick="cancelBooking(<?php echo $bookingId; ?>)">
                                    <i class="fas fa-times-circle admin-btn-icon"></i> Hủy đơn đặt xe
                                </a>
                            <?php endif; ?>
                            
                            <a href="delete.php?id=<?php echo $bookingId; ?>" class="admin-btn admin-btn-outline w-100" onclick="return confirm('Bạn có chắc chắn muốn xóa đơn đặt xe này?')">
                                <i class="fas fa-trash-alt admin-btn-icon"></i> Xóa đơn đặt xe
                            </a>
                        </div>
                    </div>
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
        
        // Update total price when dates change
        const pickupDate = document.getElementById('pickup_date');
        const returnDate = document.getElementById('return_date');
        const totalPrice = document.getElementById('total_price');
        const pricePerDay = <?php echo $car['price_per_day']; ?>;
        
        function updateTotalPrice() {
            if (pickupDate.value && returnDate.value) {
                const startDate = new Date(pickupDate.value);
                const endDate = new Date(returnDate.value);
                
                // Calculate difference in days
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays > 0) {
                    totalPrice.value = diffDays * pricePerDay;
                }
            }
        }
        
        pickupDate.addEventListener('change', updateTotalPrice);
        returnDate.addEventListener('change', updateTotalPrice);
        
        // Form Validation
        const bookingForm = document.getElementById('booking-form');
        
        bookingForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Basic validation
            const status = document.getElementById('status');
            const paymentStatus = document.getElementById('payment_status');
            const pickupLocation = document.getElementById('pickup_location');
            const returnLocation = document.getElementById('return_location');
            
            if (pickupDate.value && returnDate.value) {
                const start = new Date(pickupDate.value);
                const end = new Date(returnDate.value);
                
                if (end < start) {
                    alert('Ngày trả xe phải sau ngày nhận xe');
                    isValid = false;
                }
            }
            
            if (pickupLocation.value.trim() === '') {
                alert('Vui lòng nhập địa điểm nhận xe');
                isValid = false;
            }
            
            if (returnLocation.value.trim() === '') {
                alert('Vui lòng nhập địa điểm trả xe');
                isValid = false;
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    });
    
    // Quick action functions
    function confirmBooking(bookingId) {
        document.getElementById('status').value = 'confirmed';
        document.getElementById('booking-form').submit();
    }
    
    function completeBooking(bookingId) {
        document.getElementById('status').value = 'completed';
        document.getElementById('booking-form').submit();
    }
    
    function cancelBooking(bookingId) {
        if (confirm('Bạn có chắc chắn muốn hủy đơn đặt xe này?')) {
            document.getElementById('status').value = 'cancelled';
            document.getElementById('booking-form').submit();
        }
    }
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
    
    .customer-avatar, .car-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #f0f7ff;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<?php
// Không include footer vì trang admin sử dụng layout riêng
?>
</body>
</html>