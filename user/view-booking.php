<?php
// Thiết lập tiêu đề trang
$pageTitle = "Chi tiết đặt xe";

// Include các file cần thiết
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../auth/auth_functions.php';

// Kiểm tra đăng nhập
requireLogin();

// Lấy thông tin người dùng
$currentUser = getCurrentUser();
$userId = $currentUser['id'];

// Lấy ID đặt xe từ URL
$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Kiểm tra quyền xem đặt xe
if (!canViewBooking($bookingId, $userId)) {
    $_SESSION['message'] = "Bạn không có quyền xem đơn đặt xe này.";
    $_SESSION['message_type'] = "danger";
    header("Location: bookings.php");
    exit;
}

// Lấy thông tin chi tiết đặt xe
$booking = getBookingById($bookingId);

// Lấy thông tin xe
$car = getCarById($booking['car_id']);

// Tính số ngày thuê
$rentalDays = getDaysBetween($booking['pickup_date'], $booking['return_date']);

// Include header
include '../includes/header.php';
?>

<!-- Dashboard Header -->
<section class="bg-primary text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Chi tiết đặt xe</h1>
            </div>
            <div class="col-md-6 text-md-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 justify-content-md-end">
                        <li class="breadcrumb-item"><a href="../index.php" class="text-white">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="bookings.php" class="text-white">Lịch sử đặt xe</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Chi tiết đặt xe #<?php echo $booking['id']; ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Dashboard Content -->
<section class="py-5">
    <div class="container">
        <?php echo displayMessage(); ?>
        
        <div class="row">
            <!-- Sidebar Menu -->
            <div class="col-lg-3 mb-4">
                <div class="user-sidebar rounded bg-white shadow-sm overflow-hidden">
                    <div class="user-sidebar-header bg-light p-4 text-center">
                        <div class="user-avatar mb-3">
                            <i class="fas fa-user-circle fa-5x text-primary"></i>
                        </div>
                        <h5 class="mb-1"><?php echo htmlspecialchars($currentUser['full_name']); ?></h5>
                        <p class="text-muted mb-0">
                            <i class="fas fa-user me-1"></i> Tài khoản thường
                        </p>
                    </div>
                    
                    <div class="user-sidebar-menu p-0">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item">
                                <a href="index.php" class="d-flex align-items-center text-decoration-none text-dark">
                                    <i class="fas fa-tachometer-alt me-2 text-primary"></i> Dashboard
                                </a>
                            </li>
                            <li class="list-group-item active">
                                <a href="bookings.php" class="d-flex align-items-center text-decoration-none text-dark">
                                    <i class="fas fa-calendar-check me-2 text-primary"></i> Lịch sử đặt xe
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="profile.php" class="d-flex align-items-center text-decoration-none text-dark">
                                    <i class="fas fa-user me-2 text-primary"></i> Thông tin cá nhân
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="update-profile.php" class="d-flex align-items-center text-decoration-none text-dark">
                                    <i class="fas fa-edit me-2 text-primary"></i> Cập nhật thông tin
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="#" data-bs-toggle="modal" data-bs-target="#changePasswordModal" class="d-flex align-items-center text-decoration-none text-dark">
                                    <i class="fas fa-key me-2 text-primary"></i> Đổi mật khẩu
                                </a>
                            </li>
                            <li class="list-group-item">
                                <a href="../auth/logout.php" class="d-flex align-items-center text-decoration-none text-dark">
                                    <i class="fas fa-sign-out-alt me-2 text-primary"></i> Đăng xuất
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="col-lg-9">
                <!-- Booking Status -->
                <div class="booking-status-card mb-4 animate-on-scroll" data-animation="fadeInUp">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="mb-0">Đơn đặt xe #<?php echo $booking['id']; ?></h4>
                                <div>
                                    <?php 
                                        switch($booking['status']) {
                                            case 'pending':
                                                echo '<span class="badge bg-warning py-2 px-3">Đang chờ</span>';
                                                break;
                                            case 'confirmed':
                                                echo '<span class="badge bg-primary py-2 px-3">Đã xác nhận</span>';
                                                break;
                                            case 'completed':
                                                echo '<span class="badge bg-success py-2 px-3">Hoàn thành</span>';
                                                break;
                                            case 'cancelled':
                                                echo '<span class="badge bg-danger py-2 px-3">Đã hủy</span>';
                                                break;
                                        }
                                    ?>
                                </div>
                            </div>
                            
                            <div class="booking-timeline mb-3">
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
                            
                            <?php if ($booking['status'] == 'pending'): ?>
                                <div class="booking-actions">
                                    <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-danger" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn đặt xe này?')">
                                        <i class="fas fa-times me-1"></i> Hủy đặt xe
                                    </a>
                                </div>
                            <?php elseif ($booking['status'] == 'cancelled'): ?>
                                <div class="alert alert-danger">
                                    <i class="fas fa-info-circle me-2"></i> Đơn đặt xe này đã bị hủy.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Car Details -->
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 animate-on-scroll" data-animation="fadeInLeft">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Thông tin xe</h5>
                            </div>
                            <div class="card-body">
                                <div class="car-details">
                                    <div class="car-image mb-3">
                                        <img src="<?php echo !empty($car['image']) ? $car['image'] : '../assets/images/car-placeholder.jpg'; ?>" alt="<?php echo $car['brand'] . ' ' . $car['model']; ?>" class="img-fluid rounded">
                                    </div>
                                    <h5 class="car-title mb-3"><?php echo $car['brand'] . ' ' . $car['model']; ?></h5>
                                    <div class="row g-3 mb-3">
                                        <div class="col-6">
                                            <div class="detail-item">
                                                <div class="detail-label">Năm sản xuất</div>
                                                <div class="detail-value"><?php echo $car['year']; ?></div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-item">
                                                <div class="detail-label">Biển số</div>
                                                <div class="detail-value"><?php echo $car['license_plate']; ?></div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-item">
                                                <div class="detail-label">Màu sắc</div>
                                                <div class="detail-value"><?php echo $car['color']; ?></div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-item">
                                                <div class="detail-label">Số chỗ</div>
                                                <div class="detail-value"><?php echo $car['seats']; ?> chỗ</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-item">
                                                <div class="detail-label">Hộp số</div>
                                                <div class="detail-value"><?php echo $car['transmission'] == 'auto' ? 'Tự động' : 'Số sàn'; ?></div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="detail-item">
                                                <div class="detail-label">Nhiên liệu</div>
                                                <div class="detail-value"><?php echo $car['fuel']; ?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <a href="../car-detail.php?id=<?php echo $car['id']; ?>" class="btn btn-outline-primary">
                                            <i class="fas fa-info-circle me-1"></i> Xem chi tiết xe
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Booking Details -->
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 animate-on-scroll" data-animation="fadeInRight">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Chi tiết đặt xe</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Ngày đặt</div>
                                            <div class="detail-value"><?php echo formatDate($booking['created_at']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Tổng thời gian thuê</div>
                                            <div class="detail-value"><?php echo $rentalDays; ?> ngày</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Ngày nhận xe</div>
                                            <div class="detail-value"><?php echo formatDate($booking['pickup_date']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Ngày trả xe</div>
                                            <div class="detail-value"><?php echo formatDate($booking['return_date']); ?></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="detail-item">
                                            <div class="detail-label">Địa điểm nhận xe</div>
                                            <div class="detail-value"><?php echo $booking['pickup_location']; ?></div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="detail-item">
                                            <div class="detail-label">Địa điểm trả xe</div>
                                            <div class="detail-value"><?php echo $booking['return_location']; ?></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr class="my-3">
                                
                                <div class="price-details">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Giá thuê xe:</span>
                                        <span><?php echo formatPrice($car['price_per_day']); ?> x <?php echo $rentalDays; ?> ngày</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>Tổng cộng:</span>
                                        <span class="fw-bold"><?php echo formatPrice($booking['total_price']); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span>Trạng thái thanh toán:</span>
                                        <span class="<?php echo $booking['payment_status'] == 'paid' ? 'text-success' : 'text-warning'; ?>">
                                            <?php echo $booking['payment_status'] == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán'; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <?php if ($booking['status'] == 'completed'): ?>
                                    <div class="mt-4 text-center">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reviewModal">
                                            <i class="fas fa-star me-1"></i> Đánh giá dịch vụ
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Booking Notes -->
                <div class="card border-0 shadow-sm mb-4 animate-on-scroll" data-animation="fadeInUp">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Thông tin liên hệ</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Thông tin người đặt</h6>
                                <p class="mb-1">Họ và tên: <?php echo htmlspecialchars($currentUser['full_name']); ?></p>
                                <p class="mb-1">Email: <?php echo htmlspecialchars($currentUser['email']); ?></p>
                                <p class="mb-1">Số điện thoại: <?php echo htmlspecialchars($currentUser['phone']); ?></p>
                                <p class="mb-1">Địa chỉ: <?php echo htmlspecialchars($currentUser['address']); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h6>Liên hệ hỗ trợ</h6>
                                <p class="mb-1">Nếu cần hỗ trợ, vui lòng liên hệ:</p>
                                <p class="mb-1"><i class="fas fa-phone me-2"></i>Hotline: 0987 654 321</p>
                                <p class="mb-1"><i class="fas fa-envelope me-2"></i>Email: support@carrental.com</p>
                                <p class="mb-1"><i class="fas fa-map-marker-alt me-2"></i>Địa chỉ: 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Back Button -->
                <div class="text-center">
                    <a href="bookings.php" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách đặt xe
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Review Modal -->
<?php if ($booking['status'] == 'completed'): ?>
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Đánh giá dịch vụ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="submit-review.php" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                        <input type="hidden" name="car_id" value="<?php echo $booking['car_id']; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Xe thuê: <?php echo $car['brand'] . ' ' . $car['model']; ?></label>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Đánh giá</label>
                            <div class="rating-stars mb-2">
                                <div class="d-flex">
                                    <?php for($i = 5; $i >= 1; $i--): ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="rating" id="rating_<?php echo $i; ?>" value="<?php echo $i; ?>" required <?php echo $i == 5 ? 'checked' : ''; ?>>
                                            <label class="form-check-label" for="rating_<?php echo $i; ?>">
                                                <?php echo $i; ?> <i class="fas fa-star text-warning"></i>
                                            </label>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="comment" class="form-label">Nhận xét</label>
                            <textarea class="form-control" id="comment" name="comment" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về dịch vụ thuê xe"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Gửi đánh giá</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changePasswordModalLabel">Đổi mật khẩu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="change-password.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự.</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
    .user-sidebar {
        position: sticky;
        top: 90px;
    }
    
    .user-sidebar-menu a {
        padding: 12px 20px;
        display: block;
        transition: all 0.3s ease;
    }
    
    .user-sidebar-menu a:hover {
        background-color: #f8f9fa;
        color: #4A6FDC !important;
    }
    
    .user-sidebar-menu .list-group-item.active {
        background-color: #f0f7ff;
        border-left: 3px solid #4A6FDC;
    }
    
    .user-sidebar-menu .list-group-item.active a {
        color: #4A6FDC !important;
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
    
    .detail-label {
        color: #6c757d;
        font-size: 14px;
        margin-bottom: 5px;
    }
    
    .detail-value {
        font-weight: 600;
    }
    
    .detail-item {
        margin-bottom: 10px;
    }
    
    @media (max-width: 992px) {
        .user-sidebar {
            position: static;
            margin-bottom: 30px;
        }
    }
</style>

<?php
// Include footer
include '../includes/footer.php';
?>