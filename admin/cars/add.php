<?php
// Thiết lập tiêu đề trang
$pageTitle = "Thêm xe mới";

// Include các file cần thiết
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../auth/auth_functions.php';

// Kiểm tra đăng nhập và quyền admin
requireAdmin();

// Lấy thông tin người dùng
$currentUser = getCurrentUser();

// Khởi tạo biến
$brand = $model = $year = $licensePlate = $color = $seats = $transmission = $fuel = $pricePerDay = $description = '';
$status = 'available';

// Xử lý form thêm xe
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
        // Kiểm tra biển số xe đã tồn tại chưa
        $conn = getConnection();
        $checkLicensePlate = "SELECT id FROM cars WHERE license_plate = ?";
        $stmt = mysqli_prepare($conn, $checkLicensePlate);
        mysqli_stmt_bind_param($stmt, "s", $licensePlate);
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
    
    // Xử lý upload hình ảnh
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = $_FILES['image']['name']; // Chỉ lưu tên file
        
        // Di chuyển file tải lên vào thư mục đích
        $uploadDir = '../../uploads/cars/'; // Đảm bảo thư mục này tồn tại
        $uploadPath = $uploadDir . $image;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath)) {
            // Tùy chọn: Kiểm tra kích thước và định dạng file
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxFileSize = 5 * 1024 * 1024; // 5MB
            
            $fileType = mime_content_type($uploadPath);
            $fileSize = filesize($uploadPath);
            
            if (!in_array($fileType, $allowedTypes) || $fileSize > $maxFileSize) {
                // Xóa file nếu không hợp lệ
                unlink($uploadPath);
                $image = '';
                $errors[] = "Hình ảnh không hợp lệ. Vui lòng chọn file JPG, PNG, GIF dưới 5MB";
            }
        } else {
            $image = '';
            $errors[] = "Không thể upload hình ảnh. Vui lòng thử lại.";
        }
    }
    
    // Nếu không có lỗi, thêm xe mới
    if (empty($errors)) {
        $result = addCar($brand, $model, $year, $licensePlate, $color, $seats, $transmission, $fuel, $pricePerDay, $image, $description, $status);
        
        if ($result) {
            $_SESSION['message'] = "Thêm xe mới thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Có lỗi xảy ra khi thêm xe. Vui lòng thử lại sau.";
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
            
            <div class="admin-nav-category">// Xử lý upload hình ảnh
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
            <h4 class="admin-header-title">Thêm xe mới</h4>
            
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
        
        <!-- Car Add Content -->
        <div class="container-fluid py-4">
            <!-- Page Header -->
            <div class="admin-page-header">
                <div class="row align-items-center">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <h3 class="admin-page-title mb-0">Thêm xe mới</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="index.php">Quản lý xe</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Thêm xe mới</li>
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
            
            <!-- Car Add Form -->
            <div class="admin-card">
                <div class="admin-card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" id="car-form">
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
                                    <input type="number" id="year" name="year" class="admin-form-control" min="1900" max="<?php echo date('Y') + 1; ?>" value="<?php echo $year ? $year : date('Y'); ?>" required>
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
                                    <input type="number" id="seats" name="seats" class="admin-form-control" min="2" max="50" value="<?php echo $seats ? $seats : 5; ?>" required>
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
                                    <input type="number" id="price_per_day" name="price_per_day" class="admin-form-control" min="0" step="10000" value="<?php echo $pricePerDay ? $pricePerDay : 500000; ?>" required>
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
                            
                            <!-- Hình ảnh và mô tả -->
                            <div class="col-md-12">
                                <div class="admin-form-group">
                                    <label for="image" class="admin-form-label">Hình ảnh</label>
                                    <input type="file" id="image" name="image" class="admin-form-control" accept="image/*">
                                    <div class="form-text">Chấp nhận các định dạng: JPG, PNG, GIF. Kích thước tối đa: 5MB</div>
                                </div>
                                
                                <div class="admin-form-group">
                                    <label for="description" class="admin-form-label">Mô tả</label>
                                    <textarea id="description" name="description" class="admin-form-control" rows="4"><?php echo htmlspecialchars($description); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn-primary">
                                <i class="fas fa-save admin-btn-icon"></i> Lưu xe mới
                            </button>
                            <a href="index.php" class="admin-btn admin-btn-outline">
                                <i class="fas fa-times admin-btn-icon"></i> Hủy
                            </a>
                        </div>
                    </form>
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
        
        imageInput.addEventListener('change', function() {
            // Remove previous preview
            const oldPreview = document.querySelector('.image-preview');
            if (oldPreview) {
                oldPreview.remove();
            }
            
            // Create preview
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'image-preview mt-2';
                    
                    const image = document.createElement('img');
                    image.src = e.target.result;
                    image.style.maxHeight = '200px';
                    image.style.maxWidth = '100%';
                    image.className = 'rounded';
                    
                    preview.appendChild(image);
                    imageInput.parentNode.appendChild(preview);
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
</style>

<?php
// Không include footer vì trang admin sử dụng layout riêng
?>
</body>
</html>