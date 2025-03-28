<?php
// Thiết lập tiêu đề trang
$pageTitle = "Dashboard";

// Include các file cần thiết
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../auth/auth_functions.php';

// Kiểm tra đăng nhập
requireLogin();

// Lấy thông tin người dùng
$currentUser = getCurrentUser();
$userId = $currentUser['id'];

// Lấy lịch sử đặt xe của người dùng (5 đơn gần nhất)
$recentBookings = array_slice(getUserBookings($userId), 0, 5);

// Lấy số lượng đơn đặt xe
$bookingStats = [
    'total' => 0,
    'pending' => 0,
    'confirmed' => 0,
    'completed' => 0,
    'cancelled' => 0
];

$allUserBookings = getUserBookings($userId);
$bookingStats['total'] = count($allUserBookings);

foreach ($allUserBookings as $booking) {
    $bookingStats[$booking['status']]++;
}

// Include header
include '../includes/header.php';
?>

<!-- Dashboard Header -->
<section class="bg-primary text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Dashboard</h1>
            </div>
            <div class="col-md-6 text-md-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 justify-content-md-end">
                        <li class="breadcrumb-item"><a href="../index.php" class="text-white">Trang chủ</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Dashboard</li>
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
                            <li class="list-group-item active">
                                <a href="index.php" class="d-flex align-items-center text-decoration-none text-dark">
                                    <i class="fas fa-tachometer-alt me-2 text-primary"></i> Dashboard
                                </a>
                            </li>
                            <li class="list-group-item">
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
                <!-- Welcome Card -->
                <div class="card mb-4 border-0 shadow-sm animate-on-scroll" data-animation="fadeInUp">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="me-4">
                                <div class="p-3 bg-primary bg-opacity-10 rounded-circle">
                                    <i class="fas fa-user fa-2x text-primary"></i>
                                </div>
                            </div>
                            <div>
                                <h4 class="mb-1">Xin chào, <?php echo htmlspecialchars($currentUser['full_name']); ?>!</h4>
                                <p class="text-muted mb-0">
                                    Chào mừng bạn đến với hệ thống quản lý thuê xe. Từ đây, bạn có thể xem và quản lý việc đặt xe của mình.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 col-6 mb-4 mb-md-0">
                        <div class="card h-100 border-0 shadow-sm animate-on-scroll" data-animation="fadeInUp" data-delay="100">
                            <div class="card-body text-center p-3">
                                <div class="mb-3">
                                    <div class="icon-circle bg-primary bg-opacity-10 mx-auto mb-2">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                    </div>
                                    <h3 class="card-title mb-0"><?php echo $bookingStats['total']; ?></h3>
                                </div>
                                <h6 class="text-muted mb-0">Tổng đặt xe</h6>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-6 mb-4 mb-md-0">
                        <div class="card h-100 border-0 shadow-sm animate-on-scroll" data-animation="fadeInUp" data-delay="200">
                            <div class="card-body text-center p-3">
                                <div class="mb-3">
                                    <div class="icon-circle bg-warning bg-opacity-10 mx-auto mb-2">
                                        <i class="fas fa-clock text-warning"></i>
                                    </div>
                                    <h3 class="card-title mb-0"><?php echo $bookingStats['pending']; ?></h3>
                                </div>
                                <h6 class="text-muted mb-0">Đang chờ</h6>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-6 mb-4 mb-md-0">
                        <div class="card h-100 border-0 shadow-sm animate-on-scroll" data-animation="fadeInUp" data-delay="300">
                            <div class="card-body text-center p-3">
                                <div class="mb-3">
                                    <div class="icon-circle bg-success bg-opacity-10 mx-auto mb-2">
                                        <i class="fas fa-check-circle text-success"></i>
                                    </div>
                                    <h3 class="card-title mb-0"><?php echo $bookingStats['confirmed'] + $bookingStats['completed']; ?></h3>
                                </div>
                                <h6 class="text-muted mb-0">Đã xác nhận</h6>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 col-6 mb-4 mb-md-0">
                        <div class="card h-100 border-0 shadow-sm animate-on-scroll" data-animation="fadeInUp" data-delay="400">
                            <div class="card-body text-center p-3">
                                <div class="mb-3">
                                    <div class="icon-circle bg-danger bg-opacity-10 mx-auto mb-2">
                                        <i class="fas fa-times-circle text-danger"></i>
                                    </div>
                                    <h3 class="card-title mb-0"><?php echo $bookingStats['cancelled']; ?></h3>
                                </div>
                                <h6 class="text-muted mb-0">Đã hủy</h6>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Bookings -->
                <div class="card border-0 shadow-sm mb-4 animate-on-scroll" data-animation="fadeInUp" data-delay="500">
                    <div class="card-header bg-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Đặt Xe Gần Đây</h5>
                            <a href="bookings.php" class="btn btn-sm btn-primary">
                                <i class="fas fa-list me-1"></i> Xem tất cả
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($recentBookings)): ?>
                            <div class="text-center py-4">
                                <div class="mb-3">
                                    <i class="fas fa-calendar-times fa-3x text-muted"></i>
                                </div>
                                <p class="text-muted mb-0">Bạn chưa có đơn đặt xe nào.</p>
                                <a href="../cars.php" class="btn btn-primary mt-3">
                                    <i class="fas fa-car me-1"></i> Thuê xe ngay
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Xe</th>
                                            <th>Thời gian</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentBookings as $booking): ?>
                                            <tr>
                                                <td>#<?php echo $booking['id']; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php echo !empty($booking['image']) ? $booking['image'] : '../assets/images/car-placeholder.jpg'; ?>" alt="<?php echo $booking['brand'] . ' ' . $booking['model']; ?>" class="me-2 rounded" width="40">
                                                        <span><?php echo $booking['brand'] . ' ' . $booking['model']; ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php 
                                                        echo formatDate($booking['pickup_date']) . ' - ' . formatDate($booking['return_date']);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php echo formatPrice($booking['total_price']); ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                        switch($booking['status']) {
                                                            case 'pending':
                                                                echo '<span class="badge bg-warning">Đang chờ</span>';
                                                                break;
                                                            case 'confirmed':
                                                                echo '<span class="badge bg-primary">Đã xác nhận</span>';
                                                                break;
                                                            case 'completed':
                                                                echo '<span class="badge bg-success">Hoàn thành</span>';
                                                                break;
                                                            case 'cancelled':
                                                                echo '<span class="badge bg-danger">Đã hủy</span>';
                                                                break;
                                                        }
                                                    ?>
                                                </td>
                                                <td>
                                                    <a href="view-booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($booking['status'] == 'pending'): ?>
                                                        <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn đặt xe này?')">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Quick Actions & Promotions -->
                <div class="row">
                    <!-- Quick Actions -->
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 animate-on-scroll" data-animation="fadeInUp" data-delay="600">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Thao Tác Nhanh</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <a href="../cars.php" class="btn btn-outline-primary w-100 h-100 py-3">
                                            <i class="fas fa-car fa-2x mb-2"></i>
                                            <div>Thuê xe</div>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="bookings.php" class="btn btn-outline-primary w-100 h-100 py-3">
                                            <i class="fas fa-list-alt fa-2x mb-2"></i>
                                            <div>Lịch sử thuê</div>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="profile.php" class="btn btn-outline-primary w-100 h-100 py-3">
                                            <i class="fas fa-user-edit fa-2x mb-2"></i>
                                            <div>Hồ sơ</div>
                                        </a>
                                    </div>
                                    <div class="col-6">
                                        <a href="../contact.php" class="btn btn-outline-primary w-100 h-100 py-3">
                                            <i class="fas fa-headset fa-2x mb-2"></i>
                                            <div>Liên hệ</div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Promotions -->
                    <div class="col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100 animate-on-scroll" data-animation="fadeInUp" data-delay="700">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Ưu Đãi Đặc Biệt</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="list-group list-group-flush">
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">Giảm 10% cho khách hàng thân thiết</h6>
                                                <p class="text-muted small mb-0">Áp dụng khi thuê xe lần thứ 3</p>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">-10%</span>
                                        </div>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">Ưu đãi thuê dài hạn</h6>
                                                <p class="text-muted small mb-0">Giảm 15% khi thuê trên 7 ngày</p>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">-15%</span>
                                        </div>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">Đặt sớm - Tiết kiệm hơn</h6>
                                                <p class="text-muted small mb-0">Đặt trước 7 ngày để được giảm 5%</p>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">-5%</span>
                                        </div>
                                    </a>
                                    <a href="#" class="list-group-item list-group-item-action">
                                        <div class="d-flex w-100 justify-content-between align-items-center">
                                            <div>
                                                <h6 class="mb-1">Giới thiệu bạn bè - Nhận ưu đãi</h6>
                                                <p class="text-muted small mb-0">Giảm 100.000 VND cho mỗi người bạn giới thiệu</p>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">-100K</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

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
    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
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