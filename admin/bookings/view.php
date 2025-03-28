<?php
// Thiết lập tiêu đề trang
$pageTitle = "Chi tiết đặt xe";

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

// Kiểm tra đặt xe có tồn tại không
$booking = getBookingById($bookingId);
if (!$booking) {
    $_SESSION['message'] = "Không tìm thấy đơn đặt xe với ID: $bookingId";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit;
}

// Lấy thông tin xe
$car = getCarById($booking['car_id']);

// Lấy thông tin khách hàng
$customer = getUserById($booking['user_id']);

// Tính số ngày thuê
$rentalDays = getDaysBetween($booking['pickup_date'], $booking['return_date']);

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
            <h4 class="admin-header-title">Chi tiết đặt xe</h4>
            
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
        
        <!-- Booking Detail Content -->
        <div class="container-fluid py-4">
            <?php echo displayMessage(); ?>
            
            <!-- Page Header -->
            <div class="admin-page-header">
                <div class="row align-items-center">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <h3 class="admin-page-title mb-0">Chi tiết đơn đặt xe #<?php echo $bookingId; ?></h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="index.php">Quản lý đặt xe</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Chi tiết đặt xe</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="index.php" class="admin-btn admin-btn-outline me-2">
                            <i class="fas fa-arrow-left admin-btn-icon"></i> Quay lại
                        </a>
                        <?php if ($booking['status'] == 'pending'): ?>
                            <a href="edit.php?id=<?php echo $bookingId; ?>" class="admin-btn admin-btn-primary">
                                <i class="fas fa-edit admin-btn-icon"></i> Cập nhật
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Booking Status Timeline -->
            <div class="admin-card mb-4">
                <div class="admin-card-body">
                    <div class="booking-timeline">
                        <div class="row">
                            <div class="col">
                                <div class="step <?php echo in_array($booking['status'], ['pending', 'confirmed', 'completed']) ? 'step-active' : 'step-inactive'; ?>">
                                    <div class="step-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="step-text">Đã đặt</div>
                                    <div class="step-date"><?php echo formatDate($booking['created_at']); ?></div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="step <?php echo in_array($booking['status'], ['confirmed', 'completed']) ? 'step-active' : 'step-inactive'; ?>">
                                    <div class="step-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="step-text">Đã xác nhận</div>
                                    <div class="step-date">
                                        <?php echo $booking['status'] == 'pending' ? '-' : formatDate($booking['updated_at']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="step <?php echo $booking['status'] == 'completed' ? 'step-active' : 'step-inactive'; ?>">
                                    <div class="step-icon">
                                        <i class="fas fa-car"></i>
                                    </div>
                                    <div class="step-text">Đã nhận xe</div>
                                    <div class="step-date">
                                        <?php echo $booking['status'] == 'completed' ? formatDate($booking['pickup_date']) : '-'; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="step <?php echo $booking['status'] == 'completed' ? 'step-active' : 'step-inactive'; ?>">
                                    <div class="step-icon">
                                        <i class="fas fa-flag-checkered"></i>
                                    </div>
                                    <div class="step-text">Hoàn thành</div>
                                    <div class="step-date">
                                        <?php echo $booking['status'] == 'completed' ? formatDate($booking['return_date']) : '-'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="progress step-progress">
                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php
                                switch($booking['status']) {
                                    case 'pending':
                                        echo '25%';
                                        break;
                                    case 'confirmed':
                                        echo '50%';
                                        break;
                                    case 'completed':
                                        echo '100%';
                                        break;
                                    case 'cancelled':
                                        echo '0%';
                                        break;
                                }
                            ?>"></div>
                        </div>
                    </div>
                    
                    <?php if ($booking['status'] == 'cancelled'): ?>
                        <div class="alert alert-danger mt-4">
                            <i class="fas fa-times-circle me-2"></i> Đơn đặt xe này đã bị hủy.
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($booking['status'] == 'pending'): ?>
                        <div class="booking-actions mt-4 text-center">
                            <button type="button" class="admin-btn admin-btn-primary me-2" data-bs-toggle="modal" data-bs-target="#confirmBookingModal">
                                <i class="fas fa-check-circle me-1"></i> Xác nhận đơn
                            </button>
                            <button type="button" class="admin-btn admin-btn-danger" data-bs-toggle="modal" data-bs-target="#cancelBookingModal">
                                <i class="fas fa-times-circle me-1"></i> Hủy đơn
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row">
                <!-- Booking Details -->
                <div class="col-lg-8">
                    <div class="admin-card mb-4">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Thông tin đặt xe</h5>
                        </div>
                        <div class="admin-card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <h6 class="detail-card-title">Thông tin chung</h6>
                                        <div class="detail-list">
                                            <div class="detail-item">
                                                <span class="detail-label">Mã đơn:</span>
                                                <span class="detail-value">#<?php echo $booking['id']; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Ngày tạo đơn:</span>
                                                <span class="detail-value"><?php echo formatDate($booking['created_at']); ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Trạng thái đơn:</span>
                                                <span class="detail-value">
                                                    <?php 
                                                        switch($booking['status']) {
                                                            case 'pending':
                                                                echo '<span class="admin-badge admin-badge-warning">Đang chờ</span>';
                                                                break;
                                                            case 'confirmed':
                                                                echo '<span class="admin-badge admin-badge-primary">Đã xác nhận</span>';
                                                                break;
                                                            case 'completed':
                                                                echo '<span class="admin-badge admin-badge-success">Hoàn thành</span>';
                                                                break;
                                                            case 'cancelled':
                                                                echo '<span class="admin-badge admin-badge-danger">Đã hủy</span>';
                                                                break;
                                                        }
                                                    ?>
                                                </span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Trạng thái thanh toán:</span>
                                                <span class="detail-value">
                                                    <?php 
                                                        if ($booking['payment_status'] == 'paid') {
                                                            echo '<span class="admin-badge admin-badge-success">Đã thanh toán</span>';
                                                        } else {
                                                            echo '<span class="admin-badge admin-badge-warning">Chưa thanh toán</span>';
                                                        }
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <h6 class="detail-card-title">Thời gian thuê</h6>
                                        <div class="detail-list">
                                            <div class="detail-item">
                                                <span class="detail-label">Ngày nhận xe:</span>
                                                <span class="detail-value"><?php echo formatDate($booking['pickup_date']); ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Ngày trả xe:</span>
                                                <span class="detail-value"><?php echo formatDate($booking['return_date']); ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Tổng thời gian:</span>
                                                <span class="detail-value"><?php echo $rentalDays; ?> ngày</span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Địa điểm nhận xe:</span>
                                                <span class="detail-value"><?php echo $booking['pickup_location']; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Địa điểm trả xe:</span>
                                                <span class="detail-value"><?php echo $booking['return_location']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <h6 class="detail-card-title">Thông tin khách hàng</h6>
                                        <div class="detail-list">
                                            <div class="detail-item">
                                                <span class="detail-label">Khách hàng:</span>
                                                <span class="detail-value"><?php echo $customer['full_name']; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Email:</span>
                                                <span class="detail-value"><?php echo $customer['email']; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Số điện thoại:</span>
                                                <span class="detail-value"><?php echo $customer['phone']; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Địa chỉ:</span>
                                                <span class="detail-value"><?php echo $customer['address']; ?></span>
                                            </div>
                                            <div class="detail-item">
                                                <span class="detail-label">Tài khoản:</span>
                                                <span class="detail-value"><?php echo $customer['username']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="detail-card">
                                        <h6 class="detail-card-title">Thông tin xe</h6>
                                        <div class="car-detail-grid">
                                            <div class="car-detail-image">
                                                <img src="<?php echo !empty($car['image']) ? $car['image'] : '/assets/images/car-placeholder.jpg'; ?>" alt="<?php echo $car['brand'] . ' ' . $car['model']; ?>" class="img-fluid rounded">
                                            </div>
                                            <div class="car-detail-info">
                                                <h6><?php echo $car['brand'] . ' ' . $car['model']; ?></h6>
                                                <p class="mb-2"><?php echo $car['license_plate']; ?></p>
                                                <div class="car-features small">
                                                    <span class="car-feature me-2"><i class="fas fa-calendar me-1"></i><?php echo $car['year']; ?></span>
                                                    <span class="car-feature me-2"><i class="fas fa-user me-1"></i><?php echo $car['seats']; ?> chỗ</span>
                                                    <span class="car-feature me-2"><i class="fas fa-gas-pump me-1"></i><?php echo $car['fuel']; ?></span>
                                                    <span class="car-feature"><i class="fas fa-cog me-1"></i><?php echo $car['transmission'] == 'auto' ? 'Tự động' : 'Số sàn'; ?></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Booking Summary and Actions -->
                <div class="col-lg-4">
                    <div class="admin-card mb-4">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Tổng quan thanh toán</h5>
                        </div>
                        <div class="admin-card-body">
                            <div class="payment-detail-list">
                                <div class="payment-detail-item">
                                    <span>Giá thuê xe:</span>
                                    <span><?php echo formatPrice($car['price_per_day']); ?> x <?php echo $rentalDays; ?> ngày</span>
                                </div>
                                <div class="payment-detail-item font-weight-bold total-row">
                                    <span>Tổng tiền:</span>
                                    <span class="fw-bold"><?php echo formatPrice($booking['total_price']); ?></span>
                                </div>
                                <div class="payment-detail-item payment-status">
                                    <span>Trạng thái thanh toán:</span>
                                    <span>
                                        <?php 
                                            if ($booking['payment_status'] == 'paid') {
                                                echo '<span class="admin-badge admin-badge-success">Đã thanh toán</span>';
                                            } else {
                                                echo '<span class="admin-badge admin-badge-warning">Chưa thanh toán</span>';
                                            }
                                        ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if ($booking['payment_status'] == 'pending' && in_array($booking['status'], ['pending', 'confirmed'])): ?>
                                <div class="payment-actions mt-3">
                                    <button type="button" class="admin-btn admin-btn-success w-100" data-bs-toggle="modal" data-bs-target="#markAsPaidModal">
                                        <i class="fas fa-check-circle me-1"></i> Đánh dấu đã thanh toán
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="admin-card mb-4">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Thao tác</h5>
                        </div>
                        <div class="admin-card-body">
                            <div class="action-buttons">
                                <?php if ($booking['status'] == 'confirmed'): ?>
                                    <button type="button" class="admin-btn admin-btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#markAsCompletedModal">
                                        <i class="fas fa-flag-checkered me-1"></i> Đánh dấu hoàn thành
                                    </button>
                                <?php endif; ?>
                                
                                <?php if ($booking['status'] == 'pending'): ?>
                                    <button type="button" class="admin-btn admin-btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#confirmBookingModal">
                                        <i class="fas fa-check-circle me-1"></i> Xác nhận đơn
                                    </button>
                                    <button type="button" class="admin-btn admin-btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#cancelBookingModal">
                                        <i class="fas fa-times-circle me-1"></i> Hủy đơn
                                    </button>
                                <?php endif; ?>
                                
                                <a href="edit.php?id=<?php echo $bookingId; ?>" class="admin-btn admin-btn-outline w-100 mb-2">
                                    <i class="fas fa-edit me-1"></i> Chỉnh sửa đơn
                                </a>
                                
                                <a href="#" class="admin-btn admin-btn-info w-100" onclick="printBookingDetails()">
                                    <i class="fas fa-print me-1"></i> In đơn đặt xe
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Ghi chú</h5>
                        </div>
                        <div class="admin-card-body">
                            <form action="add-note.php" method="post">
                                <input type="hidden" name="booking_id" value="<?php echo $bookingId; ?>">
                                <div class="admin-form-group">
                                    <textarea name="note" id="booking-note" rows="3" class="admin-form-control" placeholder="Thêm ghi chú về đơn đặt xe này..."></textarea>
                                </div>
                                <button type="submit" class="admin-btn admin-btn-primary w-100">
                                    <i class="fas fa-save me-1"></i> Lưu ghi chú
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Booking Modal -->
<div class="modal fade" id="confirmBookingModal" tabindex="-1" aria-labelledby="confirmBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmBookingModalLabel">Xác nhận đơn đặt xe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn xác nhận đơn đặt xe này?</p>
                <p>Sau khi xác nhận, xe sẽ được đặt trước cho khách hàng và không thể đặt bởi người khác trong khoảng thời gian đã chọn.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <a href="update-status.php?id=<?php echo $bookingId; ?>&status=confirmed" class="btn btn-primary">Xác nhận</a>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Booking Modal -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelBookingModalLabel">Hủy đơn đặt xe</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn hủy đơn đặt xe này?</p>
                <p>Hành động này không thể hoàn tác sau khi thực hiện.</p>
                <div class="mb-3">
                    <label for="cancel-reason" class="form-label">Lý do hủy (tùy chọn):</label>
                    <textarea class="form-control" id="cancel-reason" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <a href="update-status.php?id=<?php echo $bookingId; ?>&status=cancelled" class="btn btn-danger">Hủy đơn</a>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Completed Modal -->
<div class="modal fade" id="markAsCompletedModal" tabindex="-1" aria-labelledby="markAsCompletedModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markAsCompletedModalLabel">Đánh dấu hoàn thành</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn đánh dấu đơn đặt xe này là đã hoàn thành?</p>
                <p>Việc này xác nhận rằng khách hàng đã trả xe và hoàn thành quá trình thuê xe.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <a href="update-status.php?id=<?php echo $bookingId; ?>&status=completed" class="btn btn-success">Đánh dấu hoàn thành</a>
            </div>
        </div>
    </div>
</div>

<!-- Mark as Paid Modal -->
<div class="modal fade" id="markAsPaidModal" tabindex="-1" aria-labelledby="markAsPaidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="markAsPaidModalLabel">Đánh dấu đã thanh toán</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bạn có chắc chắn muốn đánh dấu đơn đặt xe này là đã thanh toán?</p>
                <p>Tổng số tiền: <strong><?php echo formatPrice($booking['total_price']); ?></strong></p>
                <div class="mb-3">
                    <label for="payment-method" class="form-label">Phương thức thanh toán:</label>
                    <select class="form-select" id="payment-method">
                        <option value="cash">Tiền mặt</option>
                        <option value="bank_transfer">Chuyển khoản</option>
                        <option value="card">Thẻ tín dụng/ghi nợ</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <a href="update-payment.php?id=<?php echo $bookingId; ?>&status=paid" class="btn btn-success">Đánh dấu đã thanh toán</a>
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
    });
    
    // Print Booking Details
    function printBookingDetails() {
        const printContents = document.querySelector('.admin-content').innerHTML;
        const originalContents = document.body.innerHTML;
        
        document.body.innerHTML = `
            <div class="print-header">
                <h1><i class="fas fa-car"></i> CAR RENTAL</h1>
                <h2>Đơn đặt xe #<?php echo $bookingId; ?></h2>
            </div>
            ${printContents}
        `;
        
        window.print();
        document.body.innerHTML = originalContents;
        location.reload();
    }
</script>

<style>
    /* Additional custom styles */
    .detail-card {
        margin-bottom: 1.5rem;
    }
    
    .detail-card-title {
        font-weight: 600;
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #eee;
    }
    
    .detail-list, .payment-detail-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .detail-item, .payment-detail-item {
        display: flex;
        justify-content: space-between;
    }
    
    .detail-label {
        color: #6c757d;
    }
    
    .detail-value {
        font-weight: 500;
    }
    
    .car-detail-grid {
        display: grid;
        grid-template-columns: 100px 1fr;
        gap: 1rem;
        align-items: center;
    }
    
    .total-row {
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        border-top: 1px solid #eee;
    }
    
    .payment-status {
        margin-top: 0.5rem;
    }
    
    .booking-timeline {
        position: relative;
        padding: 0 0 30px;
    }
    
    .step {
        text-align: center;
        position: relative;
        z-index: 1;
    }
    
    .step-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #f0f7ff;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        color: #6c757d;
        border: 2px solid #dee2e6;
        font-size: 20px;
    }
    
    .step-active .step-icon {
        background-color: #4A6FDC;
        color: white;
        border-color: #4A6FDC;
    }
    
    .step-text {
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .step-active .step-text {
        color: #4A6FDC;
    }
    
    .step-date {
        font-size: 12px;
        color: #6c757d;
    }
    
    .step-progress {
        height: 4px;
        position: absolute;
        top: 25px;
        left: 0;
        right: 0;
        z-index: 0;
    }
    
    /* Print styles */
    @media print {
        .admin-sidebar, .admin-header, .admin-page-header, .booking-actions, .action-buttons, .modal {
            display: none !important;
        }
        
        .admin-content {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        
        .admin-card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            margin-bottom: 20px !important;
        }
        
        .print-header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .print-header h1 {
            color: #4A6FDC;
        }
    }
</style>

<?php
// Không include footer vì trang admin sử dụng layout riêng
?>
</body>
</html>