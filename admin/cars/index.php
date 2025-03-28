<?php
// Thiết lập tiêu đề trang
$pageTitle = "Quản lý xe";

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
$brand = isset($_GET['brand']) ? sanitizeInput($_GET['brand']) : '';
$status = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';

// Lấy danh sách xe
$conn = getConnection();

// Xây dựng câu truy vấn
$query = "SELECT * FROM cars WHERE 1=1";

if (!empty($search)) {
    $query .= " AND (brand LIKE '%$search%' OR model LIKE '%$search%' OR license_plate LIKE '%$search%')";
}

if (!empty($brand)) {
    $query .= " AND brand = '$brand'";
}

if (!empty($status)) {
    $query .= " AND status = '$status'";
}

$query .= " ORDER BY id DESC";

$result = mysqli_query($conn, $query);
$cars = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cars[] = $row;
    }
}

// Lấy danh sách các hãng xe
$brandsQuery = "SELECT DISTINCT brand FROM cars ORDER BY brand";
$brandsResult = mysqli_query($conn, $brandsQuery);
$brands = [];

if ($brandsResult) {
    while ($row = mysqli_fetch_assoc($brandsResult)) {
        $brands[] = $row['brand'];
    }
}

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$carsPerPage = 10;
$totalCars = count($cars);
$totalPages = ceil($totalCars / $carsPerPage);

// Đảm bảo trang hiện tại hợp lệ
if ($page < 1) $page = 1;
if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

// Lấy xe cho trang hiện tại
$offset = ($page - 1) * $carsPerPage;
$carsOnPage = array_slice($cars, $offset, $carsPerPage);

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
                    <a href="index.php" class="admin-nav-link active">
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
            <h4 class="admin-header-title">Quản lý xe</h4>
            
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
        
        <!-- Cars Management Content -->
        <div class="container-fluid py-4">
            <?php echo displayMessage(); ?>
            
            <!-- Page Header -->
            <div class="admin-page-header">
                <div class="row align-items-center">
                    <div class="col-md-6 mb-3 mb-md-0">
                        <h3 class="admin-page-title mb-0">Danh sách xe</h3>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <a href="add.php" class="admin-btn admin-btn-primary">
                            <i class="fas fa-plus-circle admin-btn-icon"></i> Thêm xe mới
                        </a>
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
                        
                        <div class="col-md-3">
                            <select class="form-select" name="brand" onchange="this.form.submit()">
                                <option value="">Tất cả hãng xe</option>
                                <?php foreach ($brands as $brandOption): ?>
                                    <option value="<?php echo $brandOption; ?>" <?php echo ($brand == $brandOption) ? 'selected' : ''; ?>>
                                        <?php echo $brandOption; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <select class="form-select" name="status" onchange="this.form.submit()">
                                <option value="">Tất cả trạng thái</option>
                                <option value="available" <?php echo ($status == 'available') ? 'selected' : ''; ?>>Sẵn sàng</option>
                                <option value="maintenance" <?php echo ($status == 'maintenance') ? 'selected' : ''; ?>>Bảo dưỡng</option>
                                <option value="rented" <?php echo ($status == 'rented') ? 'selected' : ''; ?>>Đang cho thuê</option>
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
            
            <!-- Cars List -->
            <div class="admin-card">
                <div class="admin-card-body p-0">
                    <div class="admin-table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Hình ảnh</th>
                                    <th>Thông tin xe</th>
                                    <th>Biển số</th>
                                    <th>Năm SX</th>
                                    <th>Giá thuê</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($carsOnPage)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <p class="text-muted mb-0">Không tìm thấy xe nào.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($carsOnPage as $car): ?>
                                        <tr>
                                            <td>#<?php echo $car['id']; ?></td>
                                            <td>
                                                <img src="<?php echo !empty($car['image']) ? '/showImg.php?filename='.$car['image'] : '/assets/images/car-placeholder.jpg'; ?>" alt="<?php echo $car['brand'] . ' ' . $car['model']; ?>" class="admin-car-thumbnail">
                                            </td>
                                            <td>
                                                <div class="fw-semibold"><?php echo $car['brand'] . ' ' . $car['model']; ?></div>
                                                <div class="text-muted small">
                                                    <span><i class="fas fa-user me-1"></i><?php echo $car['seats']; ?> chỗ</span> | 
                                                    <span><i class="fas fa-gas-pump me-1"></i><?php echo $car['fuel']; ?></span> | 
                                                    <span><i class="fas fa-cog me-1"></i><?php echo $car['transmission'] == 'auto' ? 'Tự động' : 'Số sàn'; ?></span>
                                                </div>
                                            </td>
                                            <td><?php echo $car['license_plate']; ?></td>
                                            <td><?php echo $car['year']; ?></td>
                                            <td><?php echo formatPrice($car['price_per_day']); ?>/ngày</td>
                                            <td>
                                                <?php 
                                                    switch($car['status']) {
                                                        case 'available':
                                                            echo '<span class="admin-badge admin-badge-success">Sẵn sàng</span>';
                                                            break;
                                                        case 'maintenance':
                                                            echo '<span class="admin-badge admin-badge-warning">Bảo dưỡng</span>';
                                                            break;
                                                        case 'rented':
                                                            echo '<span class="admin-badge admin-badge-primary">Đang thuê</span>';
                                                            break;
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <div class="admin-actions">
                                                    <a href="edit.php?id=<?php echo $car['id']; ?>" class="admin-btn admin-btn-sm admin-btn-primary" data-bs-toggle="tooltip" title="Chỉnh sửa">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <!-- Chỉ hiển thị nút xóa nếu xe không trong trạng thái đang cho thuê -->
                                                    <?php if ($car['status'] != 'rented'): ?>
                                                        <a href="delete.php?id=<?php echo $car['id']; ?>" class="admin-btn admin-btn-sm admin-btn-danger" onclick="return confirm('Bạn có chắc chắn muốn xóa xe này?')" data-bs-toggle="tooltip" title="Xóa">
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
                                    <a href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&brand=<?php echo $brand; ?>&status=<?php echo $status; ?>" class="admin-pagination-link <?php echo ($page == $i) ? 'active' : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Car Stats -->
            <div class="row mt-4">
                <div class="col-lg-4 mb-4">
                    <div class="admin-card h-100">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Thống kê xe</h5>
                        </div>
                        <div class="admin-card-body">
                            <div class="d-flex justify-content-between mb-3">
                                <span>Tổng số xe:</span>
                                <span class="fw-semibold"><?php echo $totalCars; ?> xe</span>
                            </div>
                            
                            <?php
                                // Đếm số lượng xe theo trạng thái
                                $availableCars = 0;
                                $maintenanceCars = 0;
                                $rentedCars = 0;
                                
                                foreach ($cars as $car) {
                                    switch ($car['status']) {
                                        case 'available':
                                            $availableCars++;
                                            break;
                                        case 'maintenance':
                                            $maintenanceCars++;
                                            break;
                                        case 'rented':
                                            $rentedCars++;
                                            break;
                                    }
                                }
                            ?>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span>Xe sẵn sàng:</span>
                                <span class="fw-semibold text-success"><?php echo $availableCars; ?> xe</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <span>Xe đang bảo dưỡng:</span>
                                <span class="fw-semibold text-warning"><?php echo $maintenanceCars; ?> xe</span>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <span>Xe đang cho thuê:</span>
                                <span class="fw-semibold text-primary"><?php echo $rentedCars; ?> xe</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8 mb-4">
                    <div class="admin-card h-100">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Phân bố theo hãng xe</h5>
                        </div>
                        <div class="admin-card-body">
                            <div class="row">
                                <?php
                                    // Đếm số lượng xe theo hãng
                                    $brandCounts = [];
                                    
                                    foreach ($cars as $car) {
                                        if (!isset($brandCounts[$car['brand']])) {
                                            $brandCounts[$car['brand']] = 0;
                                        }
                                        $brandCounts[$car['brand']]++;
                                    }
                                    
                                    // Sắp xếp theo số lượng xe giảm dần
                                    arsort($brandCounts);
                                    
                                    // Hiển thị top 6 hãng xe phổ biến nhất
                                    $topBrands = array_slice($brandCounts, 0, 6, true);
                                    
                                    foreach ($topBrands as $brand => $count):
                                ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span><?php echo $brand; ?>:</span>
                                            <span class="fw-semibold"><?php echo $count; ?> xe</span>
                                        </div>
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo ($count / $totalCars * 100); ?>%" aria-valuenow="<?php echo $count; ?>" aria-valuemin="0" aria-valuemax="<?php echo $totalCars; ?>"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
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
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>

<style>
    /* Additional custom styles */
    .admin-car-thumbnail {
        width: 80px;
        height: 60px;
        object-fit: cover;
        border-radius: 5px;
    }
    
    .admin-actions {
        display: flex;
        gap: 5px;
    }
    
    .admin-page-header {
        margin-bottom: 1.5rem;
    }
    
    .admin-page-title {
        font-weight: 600;
        color: #333;
    }
</style>

<?php
// Không include footer vì trang admin sử dụng layout riêng
?>
</body>
</html>