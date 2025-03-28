<?php
// Thiết lập tiêu đề trang
$pageTitle = "Chỉnh sửa người dùng";

// Include các file cần thiết
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../auth/auth_functions.php';

// Kiểm tra đăng nhập và quyền admin
requireAdmin();

// Lấy thông tin người dùng hiện tại (admin)
$currentUser = getCurrentUser();

// Lấy ID người dùng từ URL
$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Kiểm tra người dùng có tồn tại không
$user = getUserById($userId);

if (!$user) {
    $_SESSION['message'] = "Không tìm thấy người dùng với ID: $userId";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit;
}

// Xử lý form cập nhật thông tin người dùng
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $username = isset($_POST['username']) ? sanitizeInput($_POST['username']) : $user['username'];
    $email = sanitizeInput($_POST['email']);
    $fullName = sanitizeInput($_POST['full_name']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    $role = sanitizeInput($_POST['role']);
    $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    
    // Validate dữ liệu
    $errors = array();
    
    if (empty($email)) {
        $errors[] = "Email không được để trống";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    } else {
        // Kiểm tra email đã tồn tại chưa (ngoại trừ email hiện tại của người dùng)
        $conn = getConnection();
        $checkEmail = "SELECT id FROM users WHERE email = ? AND id != ?";
        $stmt = mysqli_prepare($conn, $checkEmail);
        mysqli_stmt_bind_param($stmt, "si", $email, $userId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Email đã tồn tại trong hệ thống";
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
    
    // Nếu có nhập mật khẩu mới
    if (!empty($newPassword) && strlen($newPassword) < 6) {
        $errors[] = "Mật khẩu mới phải có ít nhất 6 ký tự";
    }
    
    // Nếu không có lỗi, cập nhật thông tin người dùng
    if (empty($errors)) {
        $conn = getConnection();
        
        // Chuẩn bị câu lệnh SQL (không bao gồm mật khẩu ban đầu)
        $sql = "UPDATE users SET email = ?, full_name = ?, phone = ?, address = ?, role = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $email, $fullName, $phone, $address, $role, $userId);
        
        // Cập nhật mật khẩu nếu có nhập mật khẩu mới
        if (!empty($newPassword)) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $updatePassword = "UPDATE users SET password = ? WHERE id = ?";
            $stmtPassword = mysqli_prepare($conn, $updatePassword);
            mysqli_stmt_bind_param($stmtPassword, "si", $hashedPassword, $userId);
            mysqli_stmt_execute($stmtPassword);
        }
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Cập nhật thông tin người dùng thành công!";
            $_SESSION['message_type'] = "success";
            header("Location: index.php");
            exit;
        } else {
            $errors[] = "Có lỗi xảy ra khi cập nhật thông tin người dùng. Vui lòng thử lại sau.";
        }
    }
}

// Lấy lịch sử đặt xe của người dùng
$conn = getConnection();
$bookingsQuery = "SELECT b.*, c.brand, c.model 
                 FROM bookings b
                 JOIN cars c ON b.car_id = c.id
                 WHERE b.user_id = ?
                 ORDER BY b.created_at DESC
                 LIMIT 5";
$stmt = mysqli_prepare($conn, $bookingsQuery);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$recentBookings = [];
while ($row = mysqli_fetch_assoc($result)) {
    $recentBookings[] = $row;
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
            <h4 class="admin-header-title">Chỉnh sửa người dùng</h4>
            
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
        
        <!-- User Edit Content -->
        <div class="container-fluid py-4">
            <!-- Page Header -->
            <div class="admin-page-header">
                <div class="row align-items-center">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <h3 class="admin-page-title mb-0">Chỉnh sửa người dùng</h3>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="../index.php">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="index.php">Quản lý người dùng</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Chỉnh sửa người dùng</li>
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
            
            <!-- User Edit Form -->
            <div class="row">
                <div class="col-md-8">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Thông tin người dùng</h5>
                        </div>
                        <div class="admin-card-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $userId; ?>" method="post" id="user-form">
                                <div class="row">
                                    <!-- Thông tin cơ bản -->
                                    <div class="col-md-6">
                                        <div class="admin-form-group">
                                            <label for="username" class="admin-form-label">Tên đăng nhập</label>
                                            <input type="text" id="username" name="username" class="admin-form-control" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                            <div class="form-text">Tên đăng nhập không thể thay đổi.</div>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="email" class="admin-form-label">Email <span class="text-danger">*</span></label>
                                            <input type="email" id="email" name="email" class="admin-form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="full_name" class="admin-form-label">Họ và tên <span class="text-danger">*</span></label>
                                            <input type="text" id="full_name" name="full_name" class="admin-form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <!-- Thông tin liên hệ và quyền -->
                                    <div class="col-md-6">
                                        <div class="admin-form-group">
                                            <label for="phone" class="admin-form-label">Số điện thoại <span class="text-danger">*</span></label>
                                            <input type="text" id="phone" name="phone" class="admin-form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="address" class="admin-form-label">Địa chỉ</label>
                                            <textarea id="address" name="address" class="admin-form-control" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                                        </div>
                                        
                                        <div class="admin-form-group">
                                            <label for="role" class="admin-form-label">Vai trò <span class="text-danger">*</span></label>
                                            <select id="role" name="role" class="admin-form-control" required>
                                                <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>Người dùng</option>
                                                <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Quản trị viên</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- Thay đổi mật khẩu -->
                                    <div class="col-md-12 mt-3">
                                        <h6 class="mb-3">Thay đổi mật khẩu (để trống nếu không muốn thay đổi)</h6>
                                        <div class="admin-form-group">
                                            <label for="new_password" class="admin-form-label">Mật khẩu mới</label>
                                            <input type="password" id="new_password" name="new_password" class="admin-form-control">
                                            <div class="form-text">Mật khẩu mới phải có ít nhất 6 ký tự.</div>
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
                    <!-- User Info Card -->
                    <div class="admin-card mb-4">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Thông tin tài khoản</h5>
                        </div>
                        <div class="admin-card-body">
                            <div class="text-center mb-4">
                                <div class="user-avatar mx-auto mb-3">
                                    <i class="fas fa-user-circle fa-5x text-primary"></i>
                                </div>
                                <h5 class="mb-1"><?php echo htmlspecialchars($user['full_name']); ?></h5>
                                <span class="badge <?php echo $user['role'] == 'admin' ? 'bg-danger' : 'bg-primary'; ?> px-3 py-2">
                                    <?php echo $user['role'] == 'admin' ? 'Quản trị viên' : 'Người dùng'; ?>
                                </span>
                            </div>
                            
                            <div class="user-details">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>ID:</span>
                                    <span class="fw-semibold">#<?php echo $user['id']; ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Ngày đăng ký:</span>
                                    <span class="fw-semibold"><?php echo formatDate($user['created_at']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Cập nhật lần cuối:</span>
                                    <span class="fw-semibold"><?php echo formatDate($user['updated_at']); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php if ($user['role'] != 'admin'): ?>
                            <div class="admin-card-footer">
                                <a href="delete.php?id=<?php echo $userId; ?>" class="admin-btn admin-btn-danger w-100" onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này? Tất cả dữ liệu liên quan sẽ bị xóa và không thể khôi phục.')">
                                    <i class="fas fa-trash-alt admin-btn-icon"></i> Xóa người dùng
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Recent Bookings -->
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h5 class="admin-card-title">Đặt xe gần đây</h5>
                        </div>
                        <div class="admin-card-body p-0">
                            <?php if (empty($recentBookings)): ?>
                                <div class="text-center p-4">
                                    <p class="text-muted mb-0">Người dùng chưa có đơn đặt xe nào.</p>
                                </div>
                            <?php else: ?>
                                <div class="list-group list-group-flush">
                                    <?php foreach ($recentBookings as $booking): ?>
                                        <div class="list-group-item p-3">
                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <div class="fw-semibold"><?php echo $booking['brand'] . ' ' . $booking['model']; ?></div>
                                                    <div class="small text-muted">
                                                        <?php echo formatDate($booking['pickup_date']); ?> - <?php echo formatDate($booking['return_date']); ?>
                                                    </div>
                                                </div>
                                                <div>
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
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <span class="small fw-semibold"><?php echo formatPrice($booking['total_price']); ?></span>
                                                <a href="../bookings/edit.php?id=<?php echo $booking['id']; ?>" class="btn btn-sm btn-outline-primary">Chi tiết</a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="admin-card-footer text-center">
                            <a href="../bookings/index.php?user_id=<?php echo $userId; ?>" class="text-primary">Xem tất cả đơn đặt xe</a>
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
        
        // Form Validation
        const userForm = document.getElementById('user-form');
        
        userForm.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Basic validation
            const email = document.getElementById('email');
            const fullName = document.getElementById('full_name');
            const phone = document.getElementById('phone');
            const newPassword = document.getElementById('new_password');
            
            // Validate email
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
            
            // Validate full name
            if (fullName.value.trim() === '') {
                alert('Vui lòng nhập họ và tên');
                isValid = false;
            }
            
            // Validate phone
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
            
            // Validate new password if provided
            if (newPassword.value.trim() !== '' && newPassword.value.length < 6) {
                alert('Mật khẩu mới phải có ít nhất 6 ký tự');
                isValid = false;
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
    
    .user-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background-color: #f0f7ff;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<?php
// Không include footer vì trang admin sử dụng layout riêng
?>
</body>
</html>