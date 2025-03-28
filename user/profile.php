<?php
// Thiết lập tiêu đề trang
$pageTitle = "Thông tin cá nhân";

// Include các file cần thiết
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../auth/auth_functions.php';

// Kiểm tra đăng nhập
requireLogin();

// Lấy thông tin người dùng
$currentUser = getCurrentUser();
$userId = $currentUser['id'];

// Lấy số lượng đơn đặt xe
$allUserBookings = getUserBookings($userId);
$totalBookings = count($allUserBookings);

// Lấy số đơn đặt xe thành công
$completedBookings = array_filter($allUserBookings, function($booking) {
    return $booking['status'] == 'completed';
});
$totalCompletedBookings = count($completedBookings);

// Include header
include '../includes/header.php';
?>

<!-- Dashboard Header -->
<section class="bg-primary text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Thông tin cá nhân</h1>
            </div>
            <div class="col-md-6 text-md-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 justify-content-md-end">
                        <li class="breadcrumb-item"><a href="../index.php" class="text-white">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Dashboard</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Thông tin cá nhân</li>
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
                            <li class="list-group-item">
                                <a href="bookings.php" class="d-flex align-items-center text-decoration-none text-dark">
                                    <i class="fas fa-calendar-check me-2 text-primary"></i> Lịch sử đặt xe
                                </a>
                            </li>
                            <li class="list-group-item active">
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
                <div class="row">
                    <!-- Profile Details -->
                    <div class="col-md-8 mb-4">
                        <div class="card border-0 shadow-sm h-100 animate-on-scroll" data-animation="fadeInUp">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Thông tin cá nhân</h5>
                                <a href="update-profile.php" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit me-1"></i> Chỉnh sửa
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="user-profile">
                                    <div class="row mb-4">
                                        <div class="col-md-12 text-center">
                                            <div class="profile-avatar mb-3">
                                                <i class="fas fa-user-circle fa-6x text-primary"></i>
                                            </div>
                                            <h4 class="mb-1"><?php echo htmlspecialchars($currentUser['full_name']); ?></h4>
                                            <p class="text-muted mb-2">
                                                <i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($currentUser['email']); ?>
                                            </p>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($currentUser['phone']); ?>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="user-info">
                                        <div class="info-row mb-3 pb-3 border-bottom">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="info-label text-muted">Tài khoản</div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="info-value"><?php echo htmlspecialchars($currentUser['username']); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="info-row mb-3 pb-3 border-bottom">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="info-label text-muted">Họ và tên</div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="info-value"><?php echo htmlspecialchars($currentUser['full_name']); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="info-row mb-3 pb-3 border-bottom">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="info-label text-muted">Email</div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="info-value"><?php echo htmlspecialchars($currentUser['email']); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="info-row mb-3 pb-3 border-bottom">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="info-label text-muted">Số điện thoại</div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="info-value"><?php echo htmlspecialchars($currentUser['phone']); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="info-row mb-3 pb-3 border-bottom">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="info-label text-muted">Địa chỉ</div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="info-value">
                                                        <?php echo !empty($currentUser['address']) ? htmlspecialchars($currentUser['address']) : '<em class="text-muted">Chưa cập nhật</em>'; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="info-row">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="info-label text-muted">Ngày tạo tài khoản</div>
                                                </div>
                                                <div class="col-sm-8">
                                                    <div class="info-value"><?php echo formatDate($currentUser['created_at']); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- User Stats -->
                    <div class="col-md-4 mb-4">
                        <div class="card border-0 shadow-sm mb-4 animate-on-scroll" data-animation="fadeInRight">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Thống kê</h5>
                            </div>
                            <div class="card-body">
                                <div class="user-stat mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="stat-icon me-3">
                                            <div class="icon-circle bg-primary bg-opacity-10">
                                                <i class="fas fa-calendar-check text-primary"></i>
                                            </div>
                                        </div>
                                        <div class="stat-info">
                                            <h3 class="stat-value mb-0"><?php echo $totalBookings; ?></h3>
                                            <div class="stat-label text-muted">Đơn đặt xe</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="user-stat mb-4">
                                    <div class="d-flex align-items-center">
                                        <div class="stat-icon me-3">
                                            <div class="icon-circle bg-success bg-opacity-10">
                                                <i class="fas fa-check-circle text-success"></i>
                                            </div>
                                        </div>
                                        <div class="stat-info">
                                            <h3 class="stat-value mb-0"><?php echo $totalCompletedBookings; ?></h3>
                                            <div class="stat-label text-muted">Hoàn thành</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="user-stat">
                                    <div class="d-flex align-items-center">
                                        <div class="stat-icon me-3">
                                            <div class="icon-circle bg-warning bg-opacity-10">
                                                <i class="fas fa-star text-warning"></i>
                                            </div>
                                        </div>
                                        <div class="stat-info">
                                            <h3 class="stat-value mb-0">
                                                <?php 
                                                    // Hiển thị số sao trung bình, ví dụ 4.5
                                                    echo "4.5";
                                                ?>
                                            </h3>
                                            <div class="stat-label text-muted">Đánh giá trung bình</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card border-0 shadow-sm animate-on-scroll" data-animation="fadeInRight" data-delay="200">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Tài khoản của bạn</h5>
                            </div>
                            <div class="card-body">
                                <div class="account-status mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="status-icon me-2">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                        <div class="status-label">Email đã xác thực</div>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="status-icon me-2">
                                            <i class="fas fa-check-circle text-success"></i>
                                        </div>
                                        <div class="status-label">Số điện thoại đã xác thực</div>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <div class="status-icon me-2">
                                            <i class="fas fa-user-shield text-primary"></i>
                                        </div>
                                        <div class="status-label">Tài khoản đang hoạt động</div>
                                    </div>
                                </div>
                                
                                <div class="account-actions">
                                    <button type="button" class="btn btn-primary w-100 mb-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                        <i class="fas fa-key me-1"></i> Đổi mật khẩu
                                    </button>
                                    <a href="update-profile.php" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-edit me-1"></i> Cập nhật thông tin
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Activity -->
                <div class="card border-0 shadow-sm mb-4 animate-on-scroll" data-animation="fadeInUp" data-delay="400">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Hoạt động gần đây</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush activity-list">
                            <?php if (empty($allUserBookings)): ?>
                                <div class="text-center py-4">
                                    <p class="text-muted mb-0">Bạn chưa có hoạt động nào gần đây.</p>
                                </div>
                            <?php else: ?>
                                <?php 
                                // Lấy 5 đơn đặt xe gần nhất
                                $recentActivities = array_slice($allUserBookings, 0, 5);
                                foreach ($recentActivities as $activity):
                                ?>
                                    <div class="list-group-item p-3">
                                        <div class="d-flex align-items-center">
                                            <div class="activity-icon me-3">
                                                <?php 
                                                    switch ($activity['status']) {
                                                        case 'pending':
                                                            echo '<i class="fas fa-clock fa-lg text-warning"></i>';
                                                            break;
                                                        case 'confirmed':
                                                            echo '<i class="fas fa-check-circle fa-lg text-primary"></i>';
                                                            break;
                                                        case 'completed':
                                                            echo '<i class="fas fa-flag-checkered fa-lg text-success"></i>';
                                                            break;
                                                        case 'cancelled':
                                                            echo '<i class="fas fa-times-circle fa-lg text-danger"></i>';
                                                            break;
                                                    }
                                                ?>
                                            </div>
                                            <div class="activity-details flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <h6 class="mb-1">
                                                            <?php 
                                                                switch ($activity['status']) {
                                                                    case 'pending':
                                                                        echo 'Đã đặt xe ' . $activity['brand'] . ' ' . $activity['model'];
                                                                        break;
                                                                    case 'confirmed':
                                                                        echo 'Đơn đặt xe ' . $activity['brand'] . ' ' . $activity['model'] . ' đã được xác nhận';
                                                                        break;
                                                                    case 'completed':
                                                                        echo 'Đã hoàn thành thuê xe ' . $activity['brand'] . ' ' . $activity['model'];
                                                                        break;
                                                                    case 'cancelled':
                                                                        echo 'Đã hủy đơn đặt xe ' . $activity['brand'] . ' ' . $activity['model'];
                                                                        break;
                                                                }
                                                            ?>
                                                        </h6>
                                                        <p class="text-muted mb-0 small">
                                                            <?php echo formatDate($activity['created_at']); ?>
                                                        </p>
                                                    </div>
                                                    <a href="view-booking.php?id=<?php echo $activity['id']; ?>" class="btn btn-sm btn-outline-primary">Xem</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!empty($allUserBookings) && count($allUserBookings) > 5): ?>
                        <div class="card-footer bg-white text-center">
                            <a href="bookings.php" class="btn btn-link text-primary">Xem tất cả hoạt động</a>
                        </div>
                    <?php endif; ?>
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
    
    .icon-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .info-label {
        font-size: 14px;
    }
    
    .info-value {
        font-weight: 600;
    }
    
    .activity-list .list-group-item:hover {
        background-color: #f8f9fa;
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