<?php
// Thiết lập tiêu đề trang
$pageTitle = "Lịch sử đặt xe";

// Include các file cần thiết
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../auth/auth_functions.php';

// Kiểm tra đăng nhập
requireLogin();

// Lấy thông tin người dùng
$currentUser = getCurrentUser();
$userId = $currentUser['id'];

// Lấy trạng thái lọc từ URL
$statusFilter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';

// Lấy lịch sử đặt xe của người dùng
$allBookings = getUserBookings($userId);

// Lọc theo trạng thái nếu có
if (!empty($statusFilter)) {
    $allBookings = array_filter($allBookings, function($booking) use ($statusFilter) {
        return $booking['status'] == $statusFilter;
    });
}

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalBookings = count($allBookings);
$bookingsPerPage = 10;
$totalPages = ceil($totalBookings / $bookingsPerPage);

// Đảm bảo trang hiện tại hợp lệ
if ($page < 1) $page = 1;
if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

// Lấy đơn đặt xe cho trang hiện tại
$offset = ($page - 1) * $bookingsPerPage;
$bookingsOnPage = array_slice($allBookings, $offset, $bookingsPerPage);

// Include header
include '../includes/header.php';
?>

<!-- Dashboard Header -->
<section class="bg-primary text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Lịch sử đặt xe</h1>
            </div>
            <div class="col-md-6 text-md-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 justify-content-md-end">
                        <li class="breadcrumb-item"><a href="../index.php" class="text-white">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Dashboard</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Lịch sử đặt xe</li>
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
                <div class="card border-0 shadow-sm animate-on-scroll" data-animation="fadeInUp">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Danh sách đặt xe</h5>
                        <div>
                            <a href="../cars.php" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Đặt xe mới
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Filter Options -->
                        <div class="mb-4">
                            <div class="btn-group" role="group" aria-label="Booking status filter">
                                <a href="bookings.php" class="btn btn-outline-primary <?php echo empty($statusFilter) ? 'active' : ''; ?>">
                                    Tất cả
                                </a>
                                <a href="bookings.php?status=pending" class="btn btn-outline-primary <?php echo $statusFilter == 'pending' ? 'active' : ''; ?>">
                                    Đang chờ
                                </a>
                                <a href="bookings.php?status=confirmed" class="btn btn-outline-primary <?php echo $statusFilter == 'confirmed' ? 'active' : ''; ?>">
                                    Đã xác nhận
                                </a>
                                <a href="bookings.php?status=completed" class="btn btn-outline-primary <?php echo $statusFilter == 'completed' ? 'active' : ''; ?>">
                                    Hoàn thành
                                </a>
                                <a href="bookings.php?status=cancelled" class="btn btn-outline-primary <?php echo $statusFilter == 'cancelled' ? 'active' : ''; ?>">
                                    Đã hủy
                                </a>
                            </div>
                        </div>
                        
                        <?php if (empty($bookingsOnPage)): ?>
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-calendar-times fa-4x text-muted"></i>
                                </div>
                                <h4>Không có đơn đặt xe nào</h4>
                                <p class="text-muted">
                                    <?php if (!empty($statusFilter)): ?>
                                        Không tìm thấy đơn đặt xe nào với trạng thái đã chọn.
                                    <?php else: ?>
                                        Bạn chưa có đơn đặt xe nào. Hãy đặt xe ngay để trải nghiệm dịch vụ của chúng tôi.
                                    <?php endif; ?>
                                </p>
                                <a href="../cars.php" class="btn btn-primary mt-2">
                                    <i class="fas fa-car me-1"></i> Thuê xe ngay
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Xe</th>
                                            <th>Ngày đặt</th>
                                            <th>Thời gian thuê</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($bookingsOnPage as $booking): ?>
                                            <tr>
                                                <td>#<?php echo $booking['id']; ?></td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <img src="<?php echo !empty($booking['image']) ? '/showImg.php?filename='.$booking['image'] : '../assets/images/car-placeholder.jpg'; ?>" alt="<?php echo $booking['brand'] . ' ' . $booking['model']; ?>" class="me-2 rounded" width="50">
                                                        <div>
                                                            <div class="fw-semibold"><?php echo $booking['brand'] . ' ' . $booking['model']; ?></div>
                                                            <small class="text-muted">
                                                                <i class="fas fa-map-marker-alt me-1"></i><?php echo $booking['pickup_location']; ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td><?php echo formatDate($booking['created_at']); ?></td>
                                                <td>
                                                    <div><?php echo formatDate($booking['pickup_date']); ?></div>
                                                    <div><?php echo formatDate($booking['return_date']); ?></div>
                                                    <small class="text-muted">
                                                        <?php echo getDaysBetween($booking['pickup_date'], $booking['return_date']); ?> ngày
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold"><?php echo formatPrice($booking['total_price']); ?></div>
                                                    <small class="text-muted">
                                                        <?php echo $booking['payment_status'] == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán'; ?>
                                                    </small>
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
                                                    <div class="btn-group">
                                                        <a href="view-booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Xem chi tiết">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <?php if ($booking['status'] == 'pending'): ?>
                                                            <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Bạn có chắc chắn muốn hủy đơn đặt xe này?')" data-bs-toggle="tooltip" title="Hủy đơn">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        <?php if ($booking['status'] == 'completed'): ?>
                                                            <a href="#" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#reviewModal<?php echo $booking['id']; ?>" title="Đánh giá">
                                                                <i class="fas fa-star"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                            <?php if ($booking['status'] == 'completed'): ?>
                                                <!-- Review Modal -->
                                                <div class="modal fade" id="reviewModal<?php echo $booking['id']; ?>" tabindex="-1" aria-labelledby="reviewModalLabel<?php echo $booking['id']; ?>" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="reviewModalLabel<?php echo $booking['id']; ?>">Đánh giá dịch vụ</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <form action="submit-review.php" method="post">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                                                    <input type="hidden" name="car_id" value="<?php echo $booking['car_id']; ?>">
                                                                    
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Xe thuê: <?php echo $booking['brand'] . ' ' . $booking['model']; ?></label>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Đánh giá</label>
                                                                        <div class="rating-stars mb-2">
                                                                            <div class="d-flex">
                                                                                <?php for($i = 5; $i >= 1; $i--): ?>
                                                                                    <div class="form-check form-check-inline">
                                                                                        <input class="form-check-input" type="radio" name="rating" id="rating<?php echo $booking['id']; ?>_<?php echo $i; ?>" value="<?php echo $i; ?>" required <?php echo $i == 5 ? 'checked' : ''; ?>>
                                                                                        <label class="form-check-label" for="rating<?php echo $booking['id']; ?>_<?php echo $i; ?>">
                                                                                            <?php echo $i; ?> <i class="fas fa-star text-warning"></i>
                                                                                        </label>
                                                                                    </div>
                                                                                <?php endfor; ?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label for="comment<?php echo $booking['id']; ?>" class="form-label">Nhận xét</label>
                                                                        <textarea class="form-control" id="comment<?php echo $booking['id']; ?>" name="comment" rows="4" placeholder="Chia sẻ trải nghiệm của bạn về dịch vụ thuê xe"></textarea>
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
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                                <div class="pagination-container mt-4">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination justify-content-center">
                                            <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($statusFilter) ? '&status=' . $statusFilter : ''; ?>" aria-label="Previous">
                                                    <span aria-hidden="true">&laquo;</span>
                                                </a>
                                            </li>
                                            
                                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                                    <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($statusFilter) ? '&status=' . $statusFilter : ''; ?>">
                                                        <?php echo $i; ?>
                                                    </a>
                                                </li>
                                            <?php endfor; ?>
                                            
                                            <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($statusFilter) ? '&status=' . $statusFilter : ''; ?>" aria-label="Next">
                                                    <span aria-hidden="true">&raquo;</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
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

<!-- Initialize tooltips -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>

<?php
// Include footer
include '../includes/footer.php';
?>