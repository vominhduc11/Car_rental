<?php
// Thiết lập tiêu đề trang
$pageTitle = "Chỉnh sửa xe";

// Include các file cần thiết
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../auth/auth_functions.php';

// Kiểm tra đăng nhập và quyền admin
requireAdmin();

// Lấy thông tin người dùng
$currentUser = getCurrentUser();

// Lấy ID xe từ URL
$carId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Kiểm tra xe có tồn tại không
$car = getCarById($carId);
if (!$car) {
    $_SESSION['message'] = "Không tìm thấy xe với ID: $carId";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit;
}

// Khởi tạo biến với dữ liệu hiện tại của xe
$brand = $car['brand'];
$model = $car['model'];
$year = $car['year'];
$licensePlate = $car['license_plate'];
$color = $car['color'];
$seats = $car['seats'];
$transmission = $car['transmission'];
$fuel = $car['fuel'];
$pricePerDay = $car['price_per_day'];
$description = $car['description'];
$status = $car['status'];
$currentImage = $car['image'];

// Xử lý form cập nhật xe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $brand = sanitizeInput($_POST['brand']);
    $model = sanitizeInput($_POST['model']);
    $year = (int)$_POST['year'];
    $licensePlate = sanitizeInput($_POST['license_plate']);
    $color = sanitizeInput($_POST['color']);
    $seats = (int)$_POST['seats'];
    $transmission = sanitizeInput($_POST['transmission']);
    $fuel = sanitizeInput($_POST['fuel']);
    $pricePerDay = (float)$_POST['price_per_day'];
    $description = sanitizeInput($_POST['description']);
    $status = sanitizeInput($_POST['status']);
    
    // Validate dữ liệu
    $errors = array();
    
    if (empty($brand)) {
        $errors[] = "Hãng xe không được để trống";
    }
    
    if (empty($model)) {
        $errors[] = "Model xe không được để trống";
    }
    
    if (empty($year) || $year < 1900 || $year > date('Y') + 1) {
        $errors[] = "Năm sản xuất không hợp lệ";
    }
    
    if (empty($licensePlate)) {
        $errors[] = "Biển số xe không được để trống";
    } else {
        // Kiểm tra biển số xe đã tồn tại chưa (ngoại trừ xe hiện tại)
        $conn = getConnection();
        $checkLicensePlate = "SELECT id FROM cars WHERE license_plate = ? AND id != ?";
        $stmt = mysqli_prepare($conn, $checkLicensePlate);
        mysqli_stmt_bind_param($stmt, "si", $licensePlate, $carId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Biển số xe đã tồn tại trong hệ thống";
        }
    }
    
    if (empty($color)) {
        $errors[] = "Màu sắc không được để trống";
    }
    
    if (empty($seats) || $seats < 2 || $seats > 50) {
        $errors[] = "Số chỗ ngồi không hợp lệ";
    }
    
    if (empty($transmission) || !in_array($transmission, ['auto', 'manual'])) {
        $errors[] = "Hộp số không hợp lệ";
    }
    
    if (empty($fuel)) {
        $errors[] = "Nhiên liệu không được để trống";
    }
    
    if (empty($pricePerDay) || $pricePerDay <= 0) {
        $errors[] = "Giá thuê không hợp lệ";
    }
    
    // Xử lý upload hình ảnh mới (nếu có)
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = uploadImage($_FILES['image']);
        
        if (empty($image)) {
            $errors[] = "Không thể upload hình ảnh. Vui lòng kiểm tra định dạng và kích thước (JPG, PNG, GIF)";
        }
    }
    
    // Nếu không có lỗi, cập nhật thông tin xe
    if (empty($errors)) {
        $result = updateCar($carId, $brand, $model, $year, $licensePlate, $color, $seats, $transmission, $fuel, $pricePerDay, $image, $description, $status);
        
        if ($result) {
            $_SESSION['message'] = "Cập nhật thông tin xe thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Có lỗi xảy ra khi cập nhật thông tin xe. Vui lòng thử lại sau.";
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
            <h4 class="admin-header-title">Chỉnh sửa xe</h4>
            
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
        
        <!-- Car Edit Content -->
        <div class="container-fluid py-4">
            <!-- Page Header -->
            <div class="admin-page-header">
                <div class="row align-items-center">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <h3 class="admin-page-title mb-0">Chỉnh sửa xe</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="index.php">Quản lý xe</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa xe</li>
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
            
            <!-- Car Edit Form -->
            <div class="row">
                <div class="col-md-8">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Thông tin xe</h5>
                        </div>
                        <div class="admin-card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $carId; ?>" method="post" enctype="multipart/form-data" id="car-form">
                                <div class="row">
                                    <!-- Thông tin cơ bản -->
                                    <div class="col-md-6">
                                        <div class="admin-form-group">
                                            <label for="brand" class="admin-form-label">Hãng xe <span class="text-danger">*</span></label>
                                            <input type="text" id="brand" name="brand" class="admin-form-control" value="<?php echo htmlspecialchars($brand); ?>" required>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="model" class="admin-form-label">Model <span class="text-danger">*</span></label>
                                            <input type="text" id="model" name="model" class="admin-form-control" value="<?php echo htmlspecialchars($model); ?>" required>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="year" class="admin-form-label">Năm sản xuất <span class="text-danger">*</span></label>
                                            <input type="number" id="year" name="year" class="admin-form-control" min="1900" max="<?php echo date('Y') + 1; ?>" value="<?php echo $year; ?>" required>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="license_plate" class="admin-form-label">Biển số xe <span class="text-danger">*</span></label>
                                            <input type="text" id="license_plate" name="license_plate" class="admin-form-control" value="<?php echo htmlspecialchars($licensePlate); ?>" required>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="color" class="admin-form-label">Màu sắc <span class="text-danger">*</span></label>
                                            <input type="text" id="color" name="color" class="admin-form-control" value="<?php echo htmlspecialchars($color); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <!-- Thông số kỹ thuật -->
                                    <div class="col-md-6">
                                        <div class="admin-form-group">
                                            <label for="seats" class="admin-form-label">Số chỗ ngồi <span class="text-danger">*</span></label>
                                            <input type="number" id="seats" name="seats" class="admin-form-control" min="2" max="50" value="<?php echo $seats; ?>" required>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="transmission" class="admin-form-label">Hộp số <span class="text-danger">*</span></label>
                                            <select id="transmission" name="transmission" class="admin-form-control" required>
                                                <option value="auto" <?php echo $transmission == 'auto' ? 'selected' : ''; ?>>Tự động</option>
                                                <option value="manual" <?php echo $transmission == 'manual' ? 'selected' : ''; ?>>Số sàn</option>
                                            </select>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="fuel" class="admin-form-label">Nhiên liệu <span class="text-danger">*</span></label>
                                            <input type="text" id="fuel" name="fuel" class="admin-form-control" value="<?php echo htmlspecialchars($fuel); ?>" required>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="price_per_day" class="admin-form-label">Giá thuê/ngày (VND) <span class="text-danger">*</span></label>
                                            <input type="number" id="price_per_day" name="price_per_day" class="admin-form-control" min="0" step="10000" value="<?php echo $pricePerDay; ?>" required>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="status" class="admin-form-label">Trạng thái <span class="text-danger">*</span></label>
                                            <select id="status" name="status" class="admin-form-control" required>
                                                <option value="available" <?php echo $status == 'available' ? 'selected' : ''; ?>>Sẵn sàng</option>
                                                <option value="maintenance" <?php echo $status == 'maintenance' ? 'selected' : ''; ?>>Bảo dưỡng</option>
                                                <option value="rented" <?php echo $status == 'rented' ? 'selected' : ''; ?>>Đang cho thuê</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Mô tả -->
                                    <div class="col-md-12">
                                        <div class="admin-form-group">
                                            <label for="description" class="admin-form-label">Mô tả</label>
                                            <textarea id="description" name="description" class="admin-form-control" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
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
                    <!-- Car Image -->
                    <div class="admin-card mb-4">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Hình ảnh xe</h5>
                        </div>
                        <div class="admin-card-body">
                            <?php if (!empty($currentImage)): ?>
                                <div class="text-center mb-3">
                                    <img src="<?php echo $currentImage; ?>" alt="<?php echo $brand . ' ' . $model; ?>" class="img-fluid rounded" style="max-height: 200px;">
                                </div>
                            <?php else: ?>
                                <div class="text-center mb-3">
                                    <div class="no-image-placeholder rounded p-4 bg-light border">
                                        <i class="fas fa-image fa-4x text-muted"></i>
                                        <p class="mt-2 mb-0 text-muted">Chưa có hình ảnh</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $carId; ?>" method="post" enctype="multipart/form-data" id="image-form">
                                <!-- Copy hidden fields from main form -->
                                <input type="hidden" name="brand" value="<?php echo htmlspecialchars($brand); ?>">
                                <input type="hidden" name="model" value="<?php echo htmlspecialchars($model); ?>">
                                <input type="hidden" name="year" value="<?php echo $year; ?>">
                                <input type="hidden" name="license_plate" value="<?php echo htmlspecialchars($licensePlate); ?>">
                                <input type="hidden" name="color" value="<?php echo htmlspecialchars($color); ?>">
                                <input type="hidden" name="seats" value="<?php echo $seats; ?>">
                                <input type="hidden" name="transmission" value="<?php echo $transmission; ?>">
                                <input type="hidden" name="fuel" value="<?php echo htmlspecialchars($fuel); ?>">
                                <input type="hidden" name="price_per_day" value="<?php echo $pricePerDay; ?>">
                                <input type="hidden" name="description" value="<?php echo htmlspecialchars($description); ?>">
                                <input type="hidden" name="status" value="<?php echo $status; ?>">
                                
                                <div class="admin-form-group">
                                    <label for="image" class="admin-form-label">Thay đổi hình ảnh</label>
                                    <input type="file" id="image" name="image" class="admin-form-control" accept="image/*">
                                    <div class="form-text">Chấp nhận các định dạng: JPG, PNG, GIF. Kích thước tối đa: 5MB</div>
                                </div>
                                
                                <div class="image-preview mt-3"></div>
                                
                                <div class="d-grid gap-2 mt-3">
                                    <button type="submit" class="admin-btn admin-btn-primary">
                                        <i class="fas fa-upload admin-btn-icon"></i> Cập nhật hình ảnh
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Car Info -->
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Thông tin bổ sung</h5>
                        </div>
                        <div class="admin-card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span>ID:</span>
                                <span class="fw-semibold">#<?php echo $carId; ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Ngày tạo:</span>
                                <span class="fw-semibold"><?php echo formatDate($car['created_at']); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Cập nhật lần cuối:</span>
                                <span class="fw-semibold"><?php echo formatDate($car['updated_at']); ?></span>
                            </div>
                        </div>
                        
                        <?php if ($status != 'rented'): ?>
                            <div class="admin-card-footer">
                                <a href="delete.php?id=<?php echo $carId; ?>" class="admin-btn admin-btn-danger w-100" onclick="return confirm('Bạn có chắc chắn muốn xóa xe này?')">
                                    <i class="fas fa-trash-alt admin-btn-icon"></i> Xóa xe này
                                </a>
                            </div>
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
        
        // Form Validation
        const carForm = document.getElementById('car-form');
        
        carForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Basic validation
            const brand = document.getElementById('brand');
            const model = document.getElementById('model');
            const year = document.getElementById('year');
            const licensePlate = document.getElementById('license_plate');
            const pricePerDay = document.getElementById('price_per_day');
            
            if (brand.value.trim() === '') {
                alert('Vui lòng nhập hãng xe');
                isValid = false;
            }
            
            if (model.value.trim() === '') {
                alert('Vui lòng nhập model xe');
                isValid = false;
            }
            
            if (year.value < 1900 || year.value > <?php echo date('Y') + 1; ?>) {
                alert('Năm sản xuất không hợp lệ');
                isValid = false;
            }
            
            if (licensePlate.value.trim() === '') {
                alert('Vui lòng nhập biển số xe');
                isValid = false;
            }
            
            if (pricePerDay.value <= 0) {
                alert('Giá thuê không hợp lệ');
                isValid = false;
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });
        
        // Image Preview
        const imageInput = document.getElementById('image');
        const imagePreview = document.querySelector('.image-preview');
        
        imageInput.addEventListener('change', function() {
            // Clear previous preview
            imagePreview.innerHTML = '';
            
            // Create preview
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const image = document.createElement('img');
                    image.src = e.target.result;
                    image.style.maxHeight = '200px';
                    image.style.maxWidth = '100%';
                    image.className = 'rounded';
                    
                    imagePreview.appendChild(image);
                };
                
                reader.readAsDataURL(this.files[0]);
            }
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
    
    .image-preview {
        text-align: center;
    }
</style>

<?php
// Không include footer vì trang admin sử dụng layout riêng
?>
</body>
</html>