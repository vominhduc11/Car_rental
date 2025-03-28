<?php
// Thiết lập tiêu đề trang
$pageTitle = "Quản lý đặt xe";

// Include các file cần thiết
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../auth/auth_functions.php';

// Kiểm tra đăng nhập và quyền admin
requireAdmin();

// Lấy thông tin người dùng
$currentUser = getCurrentUser();

// Xử lý lọc và tìm kiếm
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$status = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$dateFrom = isset($_GET['date_from']) ? sanitizeInput($_GET['date_from']) : '';
$dateTo = isset($_GET['date_to']) ? sanitizeInput($_GET['date_to']) : '';

// Lấy danh sách đặt xe
$conn = getConnection();

// Xây dựng câu truy vấn
$query = "SELECT b.*, u.username, u.full_name, u.phone, c.brand, c.model, c.license_plate 
          FROM bookings b
          JOIN users u ON b.user_id = u.id
          JOIN cars c ON b.car_id = c.id
          WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (u.full_name LIKE '%$search%' OR u.username LIKE '%$search%' OR c.brand LIKE '%$search%' OR c.model LIKE '%$search%' OR c.license_plate LIKE '%$search%')";
}

if (!empty($status)) {
    $query .= " AND b.status = '$status'";
}

if (!empty($dateFrom) && !empty($dateTo)) {
    $query .= " AND ((b.pickup_date BETWEEN '$dateFrom' AND '$dateTo') 
                    OR (b.return_date BETWEEN '$dateFrom' AND '$dateTo') 
                    OR (b.pickup_date <= '$dateFrom' AND b.return_date >= '$dateTo'))";
} elseif (!empty($dateFrom)) {
    $query .= " AND b.pickup_date >= '$dateFrom'";
} elseif (!empty($dateTo)) {
    $query .= " AND b.return_date <= '$dateTo'";
}

$query .= " ORDER BY b.created_at DESC";

$result = mysqli_query($conn, $query);
$bookings = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $bookings[] = $row;
    }
}

// Đếm số lượng đặt xe theo trạng thái
$pendingCount = 0;
$confirmedCount = 0;
$completedCount = 0;
$cancelledCount = 0;

foreach ($bookings as $booking) {
    switch ($booking['status']) {
        case 'pending':
            $pendingCount++;
            break;
        case 'confirmed':
            $confirmedCount++;
            break;
        case 'completed':
            $completedCount++;
            break;
        case 'cancelled':
            $cancelledCount++;
            break;
    }
}

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$bookingsPerPage = 10;
$totalBookings = count($bookings);
$totalPages = ceil($totalBookings / $bookingsPerPage);

// Đảm bảo trang hiện tại hợp lệ
if ($page < 1) $page = 1;
if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

// Lấy đặt xe cho trang hiện tại
$offset = ($page - 1) * $bookingsPerPage;
$bookingsOnPage = array_slice($bookings, $offset, $bookingsPerPage);

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
            <h4 class="admin-header-title">Quản lý đặt xe</h4>
            
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
        
        <!-- Bookings Management Content -->
        <div class="container-fluid py-4">
            <?php echo displayMessage(); ?>
            
            <!-- Page Header -->
            <div class="admin-page-header">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h3 class="admin-page-title mb-0">Danh sách đặt xe</h3>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="export.php" class="admin-btn admin-btn-primary">
                            <i class="fas fa-file-export admin-btn-icon"></i> Xuất báo cáo
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Booking Status Cards -->
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="admin-card h-100">
                        <div class="admin-card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="admin-stats-icon bg-warning bg-opacity-10">
                                        <i class="fas fa-clock text-warning"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-1"><?php echo $pendingCount; ?></h5>
                                    <p class="card-text text-muted mb-0">Đang chờ</p>
                                </div>
                                <a href="index.php?status=pending" class="admin-stats-link text-warning">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="admin-card h-100">
                        <div class="admin-card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="admin-stats-icon bg-primary bg-opacity-10">
                                        <i class="fas fa-check-circle text-primary"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-1"><?php echo $confirmedCount; ?></h5>
                                    <p class="card-text text-muted mb-0">Đã xác nhận</p>
                                </div>
                                <a href="index.php?status=confirmed" class="admin-stats-link text-primary">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="admin-card h-100">
                        <div class="admin-card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="admin-stats-icon bg-success bg-opacity-10">
                                        <i class="fas fa-flag-checkered text-success"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-1"><?php echo $completedCount; ?></h5>
                                    <p class="card-text text-muted mb-0">Hoàn thành</p>
                                </div>
                                <a href="index.php?status=completed" class="admin-stats-link text-success">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="admin-card h-100">
                        <div class="admin-card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <div class="admin-stats-icon bg-danger bg-opacity-10">
                                        <i class="fas fa-times-circle text-danger"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="card-title mb-1"><?php echo $cancelledCount; ?></h5>
                                    <p class="card-text text-muted mb-0">Đã hủy</p>
                                </div>
                                <a href="index.php?status=cancelled" class="admin-stats-link text-danger">
                                    <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Filter & Search -->
            <div class="admin-card mb-4">
                <div class="admin-card-body">
                    <form action="index.php" method="get" class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Tìm kiếm..." value="<?php echo $search; ?>">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <select class="form-select" name="status" onchange="this.form.submit()">
                                <option value="">Tất cả trạng thái</option>
                                <option value="pending" <?php echo ($status == 'pending') ? 'selected' : ''; ?>>Đang chờ</option>
                                <option value="confirmed" <?php echo ($status == 'confirmed') ? 'selected' : ''; ?>>Đã xác nhận</option>
                                <option value="completed" <?php echo ($status == 'completed') ? 'selected' : ''; ?>>Hoàn thành</option>
                                <option value="cancelled" <?php echo ($status == 'cancelled') ? 'selected' : ''; ?>>Đã hủy</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="date_from" placeholder="Từ ngày" value="<?php echo $dateFrom; ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <input type="date" class="form-control" name="date_to" placeholder="Đến ngày" value="<?php echo $dateTo; ?>">
                        </div>
                        
                        <div class="col-md-2">
                            <div class="d-grid">
                                <a href="index.php" class="btn btn-outline-secondary">
                                    <i class="fas fa-sync-alt"></i> Làm mới
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Bookings List -->
            <div class="admin-card">
                <div class="admin-card-body p-0">
                    <div class="admin-table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Khách hàng</th>
                                    <th>Xe</th>
                                    <th>Thời gian</th>
                                    <th>Tổng tiền</th>
                                    <th>Ngày đặt</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($bookingsOnPage)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <p class="text-muted mb-0">Không tìm thấy đơn đặt xe nào.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($bookingsOnPage as $booking): ?>
                                        <tr>
                                            <td>#<?php echo $booking['id']; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="ms-2">
                                                        <div class="fw-semibold"><?php echo $booking['full_name']; ?></div>
                                                        <div class="text-muted small"><?php echo $booking['phone']; ?></div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php echo $booking['brand'] . ' ' . $booking['model']; ?>
                                                <div class="text-muted small"><?php echo $booking['license_plate']; ?></div>
                                            </td>
                                            <td>
                                                <div><?php echo formatDate($booking['pickup_date']); ?></div>
                                                <div><?php echo formatDate($booking['return_date']); ?></div>
                                                <div class="text-muted small">
                                                    <?php echo getDaysBetween($booking['pickup_date'], $booking['return_date']); ?> ngày
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold"><?php echo formatPrice($booking['total_price']); ?></div>
                                                <div class="text-muted small">
                                                    <?php echo $booking['payment_status'] == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán'; ?>
                                                </div>
                                            </td>
                                            <td><?php echo formatDate($booking['created_at']); ?></td>
                                            <td>
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
                                            </td>
                                            <td>
                                                <div class="admin-actions">
                                                    <a href="view.php?id=<?php echo $booking['id']; ?>" class="admin-btn admin-btn-sm admin-btn-info" data-bs-toggle="tooltip" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <?php if ($booking['status'] == 'pending'): ?>
                                                        <a href="edit.php?id=<?php echo $booking['id']; ?>" class="admin-btn admin-btn-sm admin-btn-primary" data-bs-toggle="tooltip" title="Cập nhật">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <?php if ($totalPages > 1): ?>
                    <div class="admin-card-footer">
                        <div class="admin-pagination">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <div class="admin-pagination-item">
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&status=<?php echo $status; ?>&date_from=<?php echo $dateFrom; ?>&date_to=<?php echo $dateTo; ?>" class="admin-pagination-link <?php echo ($page == $i) ? 'active' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
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
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>

<style>
    /* Additional custom styles */
    .admin-stats-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    
    .admin-stats-link {
        text-decoration: none;
    }
    
    .admin-page-header {
        margin-bottom: 1.5rem;
    }
    
    .admin-page-title {
        font-weight: 600;
        color: #333;
    }
    
    .admin-actions {
        display: flex;
        gap: 5px;
    }
</style>

<?php
// Không include footer vì trang admin sử dụng layout riêng
?>
</body>
</html>