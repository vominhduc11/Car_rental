<?php
// Thiết lập tiêu đề trang
$pageTitle = "Thêm người dùng mới";

// Include các file cần thiết
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../auth/auth_functions.php';

// Kiểm tra đăng nhập và quyền admin
requireAdmin();

// Lấy thông tin người dùng hiện tại (admin)
$currentUser = getCurrentUser();

// Khởi tạo biến
$username = $email = $fullName = $phone = $address = '';
$role = 'user';

// Xử lý form thêm người dùng
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $email = sanitizeInput($_POST['email']);
    $fullName = sanitizeInput($_POST['full_name']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    $role = sanitizeInput($_POST['role']);
    
    // Validate dữ liệu
    $errors = array();
    
    if (empty($username)) {
        $errors[] = "Tên đăng nhập không được để trống";
    } elseif (strlen($username) < 3) {
        $errors[] = "Tên đăng nhập phải có ít nhất 3 ký tự";
    } else {
        // Kiểm tra username đã tồn tại chưa
        $conn = getConnection();
        $checkUsername = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $checkUsername);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Tên đăng nhập đã tồn tại";
        }
    }
    
    if (empty($password)) {
        $errors[] = "Mật khẩu không được để trống";
    } elseif (strlen($password) < 6) {
        $errors[] = "Mật khẩu phải có ít nhất 6 ký tự";
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = "Mật khẩu xác nhận không khớp";
    }
    
    if (empty($email)) {
        $errors[] = "Email không được để trống";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    } else {
        // Kiểm tra email đã tồn tại chưa
        $checkEmail = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $checkEmail);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Email đã tồn tại";
        }
    }
    
    if (empty($fullName)) {
        $errors[] = "Họ và tên không được để trống";
    }
    
    if (empty($phone)) {
        $errors[] = "Số điện thoại không được để trống";
    } elseif (!preg_match("/^[0-9]{10,11}$/", $phone)) {
        $errors[] = "Số điện thoại không hợp lệ (cần 10-11 số)";
    }
    
    if (!in_array($role, ['admin', 'user'])) {
        $errors[] = "Quyền không hợp lệ";
    }
    
    // Nếu không có lỗi, thêm người dùng mới
    if (empty($errors)) {
        // Mã hóa mật khẩu
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Thêm người dùng vào database
        $insertUser = "INSERT INTO users (username, password, email, full_name, phone, address, role) 
                       VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertUser);
        mysqli_stmt_bind_param($stmt, "sssssss", $username, $hashedPassword, $email, $fullName, $phone, $address, $role);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Thêm người dùng mới thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Có lỗi xảy ra khi thêm người dùng. Vui lòng thử lại sau.";
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
            <h4 class="admin-header-title">Thêm người dùng mới</h4>
            
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
        
        <!-- User Add Content -->
        <div class="container-fluid py-4">
            <!-- Page Header -->
            <div class="admin-page-header">
                <div class="row align-items-center">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <h3 class="admin-page-title mb-0">Thêm người dùng mới</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="index.php">Quản lý người dùng</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Thêm người dùng</li>
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
            
            <!-- User Add Form -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h5 class="admin-card-title">Thông tin người dùng</h5>
                </div>
                <div class="admin-card-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="user-form">
                        <div class="row">
                            <!-- Thông tin đăng nhập -->
                            <div class="col-md-6">
                                <div class="admin-form-group">
                                    <label for="username" class="admin-form-label">Tên đăng nhập <span class="text-danger">*</span></label>
                                    <input type="text" id="username" name="username" class="admin-form-control" value="<?php echo htmlspecialchars($username); ?>" required>
                                </div>
                                
                                <div class="admin-form-group">
                                    <label for="email" class="admin-form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" id="email" name="email" class="admin-form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                                </div>
                                
                                <div class="admin-form-group">
                                    <label for="password" class="admin-form-label">Mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" id="password" name="password" class="admin-form-control" required>
                                    <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự.</div>
                                </div>
                                
                                <div class="admin-form-group">
                                    <label for="confirm_password" class="admin-form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                    <input type="password" id="confirm_password" name="confirm_password" class="admin-form-control" required>
                                </div>
                                
                                <div class="admin-form-group">
                                    <label for="role" class="admin-form-label">Quyền <span class="text-danger">*</span></label>
                                    <select id="role" name="role" class="admin-form-control" required>
                                        <option value="user" <?php echo $role == 'user' ? 'selected' : ''; ?>>Người dùng</option>
                                        <option value="admin" <?php echo $role == 'admin' ? 'selected' : ''; ?>>Quản trị viên</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Thông tin cá nhân -->
                            <div class="col-md-6">
                                <div class="admin-form-group">
                                    <label for="full_name" class="admin-form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" id="full_name" name="full_name" class="admin-form-control" value="<?php echo htmlspecialchars($fullName); ?>" required>
                                </div>
                                
                                <div class="admin-form-group">
                                    <label for="phone" class="admin-form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" id="phone" name="phone" class="admin-form-control" value="<?php echo htmlspecialchars($phone); ?>" required>
                                </div>
                                
                                <div class="admin-form-group">
                                    <label for="address" class="admin-form-label">Địa chỉ</label>
                                    <textarea id="address" name="address" class="admin-form-control" rows="4"><?php echo htmlspecialchars($address); ?></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="admin-form-actions">
                            <button type="submit" class="admin-btn admin-btn-primary">
                                <i class="fas fa-save admin-btn-icon"></i> Lưu người dùng
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
        const userForm = document.getElementById('user-form');
        
        userForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Basic validation
            const username = document.getElementById('username');
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const fullName = document.getElementById('full_name');
            const phone = document.getElementById('phone');
            
            if (username.value.trim() === '') {
                alert('Vui lòng nhập tên đăng nhập');
                isValid = false;
            } else if (username.value.trim().length < 3) {
                alert('Tên đăng nhập phải có ít nhất 3 ký tự');
                isValid = false;
            }
            
            if (email.value.trim() === '') {
                alert('Vui lòng nhập email');
                isValid = false;
            } else {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email.value.trim())) {
                    alert('Email không hợp lệ');
                    isValid = false;
                }
            }
            
            if (password.value === '') {
                alert('Vui lòng nhập mật khẩu');
                isValid = false;
            } else if (password.value.length < 6) {
                alert('Mật khẩu phải có ít nhất 6 ký tự');
                isValid = false;
            }
            
            if (confirmPassword.value === '') {
                alert('Vui lòng xác nhận mật khẩu');
                isValid = false;
            } else if (password.value !== confirmPassword.value) {
                alert('Mật khẩu xác nhận không khớp');
                isValid = false;
            }
            
            if (fullName.value.trim() === '') {
                alert('Vui lòng nhập họ và tên');
                isValid = false;
            }
            
            if (phone.value.trim() === '') {
                alert('Vui lòng nhập số điện thoại');
                isValid = false;
            } else {
                const phoneRegex = /^[0-9]{10,11}$/;
                if (!phoneRegex.test(phone.value.trim())) {
                    alert('Số điện thoại không hợp lệ (cần 10-11 số)');
                    isValid = false;
                }
            }
            
            if (!isValid) {
                event.preventDefault();
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