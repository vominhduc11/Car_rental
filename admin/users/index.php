<?php
// Thiết lập tiêu đề trang
$pageTitle = "Quản lý người dùng";

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
$role = isset($_GET['role']) ? sanitizeInput($_GET['role']) : '';
$status = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';

// Lấy danh sách người dùng
$conn = getConnection();

// Xây dựng câu truy vấn
$query = "SELECT * FROM users WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (username LIKE '%$search%' OR full_name LIKE '%$search%' OR email LIKE '%$search%' OR phone LIKE '%$search%')";
}

if (!empty($role)) {
    $query .= " AND role = '$role'";
}

// Thêm điều kiện sắp xếp
$query .= " ORDER BY id DESC";

$result = mysqli_query($conn, $query);
$users = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
}

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$usersPerPage = 10;
$totalUsers = count($users);
$totalPages = ceil($totalUsers / $usersPerPage);

// Đảm bảo trang hiện tại hợp lệ
if ($page < 1) $page = 1;
if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

// Lấy người dùng cho trang hiện tại
$offset = ($page - 1) * $usersPerPage;
$usersOnPage = array_slice($users, $offset, $usersPerPage);

// Đếm số lượng người dùng theo vai trò
$adminCount = 0;
$userCount = 0;

foreach ($users as $user) {
    if ($user['role'] == 'admin') {
        $adminCount++;
    } else {
        $userCount++;
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
                    <a href="../bookings/index.php" class="admin-nav-link">
                        <i class="fas fa-calendar-check"></i>
                        <span>Quản lý đặt xe</span>
                    </a>
                </li>
                <li class="admin-nav-item">
                    <a href="index.php" class="admin-nav-link active">
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
            <h4 class="admin-header-title">Quản lý người dùng</h4>
            
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
        
        <!-- Users Management Content -->
        <div class="container-fluid py-4">
            <?php echo displayMessage(); ?>
            
            <!-- Page Header -->
            <div class="admin-page-header">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h3 class="admin-page-title mb-0">Danh sách người dùng</h3>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="add.php" class="admin-btn admin-btn-primary">
                            <i class="fas fa-user-plus admin-btn-icon"></i> Thêm người dùng
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Filter & Search -->
            <div class="admin-card mb-4">
                <div class="admin-card-body">
                    <form action="index.php" method="get" class="row g-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" name="search" placeholder="Tìm kiếm theo tên, username, email..." value="<?php echo $search; ?>">
                                <button class="btn btn-outline-secondary" type="submit">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <select class="form-select" name="role" onchange="this.form.submit()">
                                <option value="">Tất cả vai trò</option>
                                <option value="admin" <?php echo ($role == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                <option value="user" <?php echo ($role == 'user') ? 'selected' : ''; ?>>Người dùng</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2">
                            <a href="index.php" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-sync-alt"></i> Làm mới
                            </a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- User Stats -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="admin-stats-card admin-stats-primary">
                        <div class="admin-stats-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="admin-stats-info">
                            <h3 class="admin-stats-value"><?php echo $totalUsers; ?></h3>
                            <p class="admin-stats-label">Tổng số người dùng</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="admin-stats-card admin-stats-success">
                        <div class="admin-stats-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="admin-stats-info">
                            <h3 class="admin-stats-value"><?php echo $adminCount; ?></h3>
                            <p class="admin-stats-label">Quản trị viên</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="admin-stats-card admin-stats-warning">
                        <div class="admin-stats-icon">
                            <i class="fas fa-user-alt"></i>
                        </div>
                        <div class="admin-stats-info">
                            <h3 class="admin-stats-value"><?php echo $userCount; ?></h3>
                            <p class="admin-stats-label">Người dùng thường</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Users List -->
            <div class="admin-card">
                <div class="admin-card-body p-0">
                    <div class="admin-table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tên đăng nhập</th>
                                    <th>Thông tin</th>
                                    <th>Liên hệ</th>
                                    <th>Vai trò</th>
                                    <th>Ngày đăng ký</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($usersOnPage)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <p class="text-muted mb-0">Không tìm thấy người dùng nào.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($usersOnPage as $user): ?>
                                        <tr>
                                            <td>#<?php echo $user['id']; ?></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="admin-user-list-avatar me-2">
                                                        <?php echo substr($user['full_name'], 0, 1); ?>
                                                    </div>
                                                    <span><?php echo htmlspecialchars($user['username']); ?></span>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                                <div class="text-muted small"><?php echo htmlspecialchars($user['email']); ?></div>
                                            </td>
                                            <td>
                                                <div><?php echo htmlspecialchars($user['phone']); ?></div>
                                                <div class="text-muted small">
                                                    <?php echo !empty($user['address']) ? htmlspecialchars(substr($user['address'], 0, 30)) . (strlen($user['address']) > 30 ? '...' : '') : 'Chưa cập nhật'; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($user['role'] == 'admin'): ?>
                                                    <span class="admin-badge admin-badge-primary">Admin</span>
                                                <?php else: ?>
                                                    <span class="admin-badge admin-badge-secondary">Người dùng</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo formatDate($user['created_at']); ?></td>
                                            <td>
                                                <div class="admin-actions">
                                                    <a href="view.php?id=<?php echo $user['id']; ?>" class="admin-btn admin-btn-sm admin-btn-info" data-bs-toggle="tooltip" title="Xem chi tiết">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="edit.php?id=<?php echo $user['id']; ?>" class="admin-btn admin-btn-sm admin-btn-primary" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <!-- Chỉ hiển thị nút xóa nếu không phải tài khoản admin hoặc tài khoản hiện tại -->
                                                    <?php if ($user['role'] != 'admin' && $user['id'] != $currentUser['id']): ?>
                                                        <a href="delete.php?id=<?php echo $user['id']; ?>" class="admin-btn admin-btn-sm admin-btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?')" data-bs-toggle="tooltip" title="Xóa">
                                                            <i class="fas fa-trash"></i>
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
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&role=<?php echo $role; ?>" class="admin-pagination-link <?php echo ($page == $i) ? 'active' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- User Activity -->
            <div class="admin-card mt-4">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">Người dùng hoạt động gần đây</h5>
                </div>
                <div class="admin-card-body p-0">
                    <?php
                        // Lấy danh sách đơn đặt xe gần đây
                        $recentBookingsQuery = "SELECT b.*, u.username, u.full_name FROM bookings b 
                                                JOIN users u ON b.user_id = u.id 
                                                ORDER BY b.created_at DESC LIMIT 5";
                        $recentBookingsResult = mysqli_query($conn, $recentBookingsQuery);
                        $recentBookings = [];
                        
                        if ($recentBookingsResult) {
                            while ($row = mysqli_fetch_assoc($recentBookingsResult)) {
                                $recentBookings[] = $row;
                            }
                        }
                    ?>
                    
                    <div class="list-group list-group-flush">
                        <?php if (empty($recentBookings)): ?>
                            <div class="list-group-item p-4 text-center">
                                <p class="text-muted mb-0">Không có hoạt động gần đây.</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($recentBookings as $booking): ?>
                                <div class="list-group-item p-3">
                                    <div class="d-flex align-items-center">
                                        <div class="admin-user-list-avatar me-3">
                                            <?php echo substr($booking['full_name'], 0, 1); ?>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1"><?php echo htmlspecialchars($booking['full_name']); ?> đã đặt xe</h6>
                                            <p class="text-muted mb-0 small">
                                                Đơn #<?php echo $booking['id']; ?> | 
                                                <?php echo formatDate($booking['created_at']); ?> | 
                                                <?php echo formatPrice($booking['total_price']); ?>
                                            </p>
                                        </div>
                                        <a href="../bookings/view.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-primary">
                                            Xem chi tiết
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
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
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
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
    
    .admin-user-list-avatar {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background-color: #4A6FDC;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
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