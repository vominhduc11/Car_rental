<?php
// Thiết lập tiêu đề trang
$pageTitle = "Admin Dashboard";

// Include các file cần thiết
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../auth/auth_functions.php';

// Kiểm tra đăng nhập và quyền admin
requireAdmin();

// Lấy thông tin người dùng
$currentUser = getCurrentUser();

// Lấy thống kê tổng quan
$dashboardStats = getDashboardStats();

// Lấy đơn đặt xe gần đây (5 đơn)
$recentBookings = array_slice(getAllBookings(), 0, 5);

// Lấy doanh thu theo tháng
$revenueByMonth = getRevenueByMonth();

// CSS cho trang admin
$extraCSS = '<link rel="stylesheet" href="/assets/css/admin.css">';

// Include header
include '../includes/header.php';

/**
 * Hàm lấy doanh thu theo tháng (12 tháng gần nhất)
 */
function getRevenueByMonth() {
    $conn = getConnection();
    
    $revenueData = array();
    
    // Lấy tháng hiện tại
    $currentMonth = date('m');
    $currentYear = date('Y');
    
    // Lấy doanh thu 12 tháng gần nhất
    for ($i = 0; $i < 12; $i++) {
        $month = $currentMonth - $i;
        $year = $currentYear;
        
        // Xử lý nếu tháng âm
        if ($month <= 0) {
            $month += 12;
            $year -= 1;
        }
        
        // Tạo ngày đầu và cuối tháng
        $startDate = sprintf("%04d-%02d-01", $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));
        
        // Lấy doanh thu tháng
        $sql = "SELECT SUM(total_price) as revenue 
                FROM bookings 
                WHERE status = 'completed' 
                AND created_at BETWEEN ? AND ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ss", $startDate, $endDate);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        
        // Thêm vào mảng doanh thu
        $monthName = date('M', strtotime($startDate));
        $revenue = $row['revenue'] ? $row['revenue'] : 0;
        
        $revenueData[] = [
            'month' => $monthName,
            'revenue' => $revenue
        ];
    }
    
    // Đảo ngược mảng để hiển thị từ tháng xa nhất đến tháng gần nhất
    return array_reverse($revenueData);
}
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
                    <a href="index.php" class="admin-nav-link active">
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
                    <a href="settings.php" class="admin-nav-link">
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
            <h4 class="admin-header-title">Dashboard</h4>
            
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
        
        <!-- Dashboard Stats -->
        <div class="container-fluid py-4">
            <?php echo displayMessage(); ?>
            
            <div class="row">
                <!-- Total Cars -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="admin-stats-card admin-stats-primary">
                        <div class="admin-stats-icon">
                            <i class="fas fa-car"></i>
                        </div>
                        <div class="admin-stats-info">
                            <h3 class="admin-stats-value"><?php echo $dashboardStats['totalCars']; ?></h3>
                            <p class="admin-stats-label">Tổng số xe</p>
                            <div class="admin-stats-trend up">
                                <i class="fas fa-arrow-up"></i> 12% so với tháng trước
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Available Cars -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="admin-stats-card admin-stats-success">
                        <div class="admin-stats-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="admin-stats-info">
                            <h3 class="admin-stats-value"><?php echo $dashboardStats['availableCars']; ?></h3>
                            <p class="admin-stats-label">Xe khả dụng</p>
                            <div class="admin-stats-trend up">
                                <i class="fas fa-arrow-up"></i> 5% so với tháng trước
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Total Bookings -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="admin-stats-card admin-stats-warning">
                        <div class="admin-stats-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="admin-stats-info">
                            <h3 class="admin-stats-value"><?php echo $dashboardStats['totalBookings']; ?></h3>
                            <p class="admin-stats-label">Đơn đặt xe</p>
                            <div class="admin-stats-trend up">
                                <i class="fas fa-arrow-up"></i> 8% so với tháng trước
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Total Users -->
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="admin-stats-card admin-stats-danger">
                        <div class="admin-stats-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="admin-stats-info">
                            <h3 class="admin-stats-value"><?php echo $dashboardStats['totalUsers']; ?></h3>
                            <p class="admin-stats-label">Người dùng</p>
                            <div class="admin-stats-trend up">
                                <i class="fas fa-arrow-up"></i> 15% so với tháng trước
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Revenue Chart -->
                <div class="col-xl-8 mb-4">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Doanh thu theo tháng</h5>
                            <a href="#" class="admin-card-action">Xem chi tiết</a>
                        </div>
                        <div class="admin-card-body">
                            <canvas id="revenueChart" height="300"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Pending Bookings -->
                <div class="col-xl-4 mb-4">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Đơn đặt chờ xử lý</h5>
                            <a href="bookings/index.php?status=pending" class="admin-card-action">Xem tất cả</a>
                        </div>
                        <div class="admin-card-body p-0">
                            <div class="list-group list-group-flush">
                                <?php if (empty($recentBookings)): ?>
                                    <div class="list-group-item p-4 text-center">
                                        <p class="text-muted mb-0">Không có đơn đặt xe nào chờ xử lý.</p>
                                    </div>
                                <?php else: ?>
                                    <?php foreach ($recentBookings as $booking): ?>
                                        <?php if ($booking['status'] == 'pending'): ?>
                                            <div class="list-group-item p-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <span class="admin-badge admin-badge-warning">Chờ xử lý</span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1"><?php echo $booking['full_name']; ?></h6>
                                                        <p class="text-muted mb-0 small">
                                                            <?php echo $booking['brand'] . ' ' . $booking['model']; ?> | 
                                                            <?php echo formatDate($booking['pickup_date']); ?> - <?php echo formatDate($booking['return_date']); ?>
                                                        </p>
                                                    </div>
                                                    <a href="bookings/view.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <!-- Recent Bookings -->
                <div class="col-xl-8 mb-4">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Đơn đặt xe gần đây</h5>
                            <a href="bookings/index.php" class="admin-card-action">Xem tất cả</a>
                        </div>
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
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($recentBookings)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center">Không có đơn đặt xe nào.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($recentBookings as $booking): ?>
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
                                                    <td><?php echo $booking['brand'] . ' ' . $booking['model']; ?></td>
                                                    <td>
                                                        <div><?php echo formatDate($booking['pickup_date']); ?></div>
                                                        <div><?php echo formatDate($booking['return_date']); ?></div>
                                                    </td>
                                                    <td><?php echo formatPrice($booking['total_price']); ?></td>
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
                                                        <a href="bookings/view.php?id=<?php echo $booking['id']; ?>" class="admin-btn admin-btn-sm admin-btn-info" data-bs-toggle="tooltip" title="Xem chi tiết">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="bookings/edit.php?id=<?php echo $booking['id']; ?>" class="admin-btn admin-btn-sm admin-btn-primary" data-bs-toggle="tooltip" title="Cập nhật">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats & Actions -->
                <div class="col-xl-4">
                    <!-- Monthly Revenue -->
                    <div class="admin-card mb-4">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Doanh thu tháng này</h5>
                        </div>
                        <div class="admin-card-body text-center">
                            <h3 class="text-primary mb-3"><?php echo formatPrice($dashboardStats['monthlyRevenue']); ?></h3>
                            <div class="progress mb-3" style="height: 10px;">
                                <div class="progress-bar bg-primary" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <p class="text-muted mb-0">
                                <i class="fas fa-arrow-up text-success me-1"></i> 
                                15% tăng so với tháng trước
                            </p>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="admin-card mb-4">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Thao tác nhanh</h5>
                        </div>
                        <div class="admin-card-body">
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="cars/add.php" class="admin-btn admin-btn-primary w-100 mb-2">
                                        <i class="fas fa-plus-circle admin-btn-icon"></i> Thêm xe mới
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="bookings/index.php?status=pending" class="admin-btn admin-btn-warning w-100 mb-2">
                                        <i class="fas fa-clock admin-btn-icon"></i> Đơn chờ xử lý
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="users/index.php" class="admin-btn admin-btn-info w-100">
                                        <i class="fas fa-users admin-btn-icon"></i> Quản lý người dùng
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="settings.php" class="admin-btn admin-btn-outline w-100">
                                        <i class="fas fa-cog admin-btn-icon"></i> Cài đặt
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- System Info -->
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Thông tin hệ thống</h5>
                        </div>
                        <div class="admin-card-body p-0">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Phiên bản</span>
                                    <span class="fw-semibold">1.0.0</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>PHP Version</span>
                                    <span class="fw-semibold"><?php echo phpversion(); ?></span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Database</span>
                                    <span class="fw-semibold">MySQL</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Thời gian chạy</span>
                                    <span class="fw-semibold">30 ngày</span>
                                </li>
                                <li class="list-group-item text-center">
                                    <a href="#" class="text-primary">Kiểm tra cập nhật</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
        
        // Revenue Chart
        const revenueCanvas = document.getElementById('revenueChart');
        if (revenueCanvas) {
            const revenueChart = new Chart(revenueCanvas, {
                type: 'line',
                data: {
                    labels: [
                        <?php 
                            foreach ($revenueByMonth as $data) {
                                echo "'" . $data['month'] . "', ";
                            }
                        ?>
                    ],
                    datasets: [{
                        label: 'Doanh thu (VND)',
                        data: [
                            <?php 
                                foreach ($revenueByMonth as $data) {
                                    echo $data['revenue'] . ", ";
                                }
                            ?>
                        ],
                        backgroundColor: 'rgba(74, 111, 220, 0.2)',
                        borderColor: 'rgba(74, 111, 220, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value.toLocaleString() + ' đ';
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.raw.toLocaleString() + ' đ';
                                }
                            }
                        }
                    }
                }
            });
        }
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>

<?php
// Không include footer vì trang admin sử dụng layout riêng
?>
</body>
</html>