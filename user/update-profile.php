<?php
// Thiết lập tiêu đề trang
$pageTitle = "Cập nhật thông tin";

// Include các file cần thiết
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../auth/auth_functions.php';

// Kiểm tra đăng nhập
requireLogin();

// Lấy thông tin người dùng
$currentUser = getCurrentUser();
$userId = $currentUser['id'];

// Xử lý cập nhật thông tin
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $email = sanitizeInput($_POST['email']);
    $fullName = sanitizeInput($_POST['full_name']);
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    
    // Validate dữ liệu
    $errors = array();
    
    if (empty($email)) {
        $errors[] = "Email không được để trống";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email không hợp lệ";
    }
    
    if (empty($fullName)) {
        $errors[] = "Họ và tên không được để trống";
    }
    
    if (empty($phone)) {
        $errors[] = "Số điện thoại không được để trống";
    } elseif (!preg_match("/^[0-9]{10,11}$/", $phone)) {
        $errors[] = "Số điện thoại không hợp lệ (cần 10-11 số)";
    }
    
    // Nếu không có lỗi, cập nhật thông tin
    if (empty($errors)) {
        $result = updateUserProfile($userId, $email, $fullName, $phone, $address);
        
        if ($result['success']) {
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = "success";
            header("Location: profile.php");
            exit;
        } else {
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = implode("<br>", $errors);
        $_SESSION['message_type'] = "danger";
    }
}

// Include header
include '../includes/header.php';
?>

<!-- Dashboard Header -->
<section class="bg-primary text-white py-4">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="mb-0">Cập nhật thông tin</h1>
            </div>
            <div class="col-md-6 text-md-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 justify-content-md-end">
                        <li class="breadcrumb-item"><a href="../index.php" class="text-white">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="profile.php" class="text-white">Thông tin cá nhân</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Cập nhật thông tin</li>
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
                            <li class="list-group-item">
                                <a href="profile.php" class="d-flex align-items-center text-decoration-none text-dark">
                                    <i class="fas fa-user me-2 text-primary"></i> Thông tin cá nhân
                                </a>
                            </li>
                            <li class="list-group-item active">
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
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Cập nhật thông tin cá nhân</h5>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="update-profile-form">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Tên đăng nhập</label>
                                    <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($currentUser['username']); ?>" readonly>
                                    <div class="form-text">Tên đăng nhập không thể thay đổi.</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($currentUser['email']); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($currentUser['full_name']); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($currentUser['phone']); ?>" required>
                                </div>
                                
                                <div class="col-md-12 mb-3">
                                    <label for="address" class="form-label">Địa chỉ</label>
                                    <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($currentUser['address']); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="form-group mt-4 text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Lưu thay đổi
                                </button>
                                <a href="profile.php" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-times me-1"></i> Hủy
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mt-4 animate-on-scroll" data-animation="fadeInUp" data-delay="200">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Bảo mật tài khoản</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-7 mb-3 mb-md-0">
                                <h6>Đổi mật khẩu</h6>
                                <p class="text-muted mb-0">Cập nhật mật khẩu định kỳ để tăng cường bảo mật tài khoản.</p>
                            </div>
                            <div class="col-md-5 text-md-end">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                                    <i class="fas fa-key me-1"></i> Đổi mật khẩu
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-0 shadow-sm mt-4 animate-on-scroll" data-animation="fadeInUp" data-delay="400">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Yêu cầu hỗ trợ</h5>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-7 mb-3 mb-md-0">
                                <h6>Bạn cần hỗ trợ?</h6>
                                <p class="text-muted mb-0">Liên hệ với chúng tôi nếu bạn gặp vấn đề hoặc cần hỗ trợ.</p>
                            </div>
                            <div class="col-md-5 text-md-end">
                                <a href="../contact.php" class="btn btn-outline-primary">
                                    <i class="fas fa-headset me-1"></i> Liên hệ hỗ trợ
                                </a>
                            </div>
                        </div>
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

<!-- Validation Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('update-profile-form');
        
        form.addEventListener('submit', function(event) {
            let isValid = true;
            
            // Validate email
            const email = document.getElementById('email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value)) {
                alert('Email không hợp lệ');
                isValid = false;
            }
            
            // Validate full name
            const fullName = document.getElementById('full_name');
            if (fullName.value.trim() === '') {
                alert('Họ và tên không được để trống');
                isValid = false;
            }
            
            // Validate phone
            const phone = document.getElementById('phone');
            const phoneRegex = /^[0-9]{10,11}$/;
            if (!phoneRegex.test(phone.value)) {
                alert('Số điện thoại không hợp lệ (cần 10-11 số)');
                isValid = false;
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    });
</script>

<?php
// Include footer
include '../includes/footer.php';
?>