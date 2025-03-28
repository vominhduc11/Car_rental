<?php
// Thiết lập tiêu đề trang
$pageTitle = "Đặt lại mật khẩu";

// Include các file cần thiết
require_once '../config/database.php';
require_once '../includes/functions.php';

// Khởi tạo biến
$step = 1; // Bước 1: Nhập email, Bước 2: Nhập token & mật khẩu mới
$email = '';
$token = '';
$error = '';
$success = '';

// Nếu có token trong URL, chuyển sang bước 2
if (isset($_GET['token']) && !empty($_GET['token']) && isset($_GET['email']) && !empty($_GET['email'])) {
    $token = sanitizeInput($_GET['token']);
    $email = sanitizeInput($_GET['email']);
    $step = 2;
    
    // Kiểm tra token có hợp lệ không
    $conn = getConnection();
    $checkToken = "SELECT * FROM password_resets WHERE email = ? AND token = ? AND expires_at > NOW()";
    $stmt = mysqli_prepare($conn, $checkToken);
    mysqli_stmt_bind_param($stmt, "ss", $email, $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        $error = "Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn. Vui lòng thử lại.";
        $step = 1;
    }
}

// Xử lý form gửi email đặt lại mật khẩu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_reset'])) {
    $email = sanitizeInput($_POST['email']);
    
    // Kiểm tra email có tồn tại không
    $conn = getConnection();
    $checkEmail = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $checkEmail);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 0) {
        $error = "Email không tồn tại trong hệ thống.";
    } else {
        // Tạo token và thời gian hết hạn (24 giờ)
        $token = bin2hex(random_bytes(32));
        $expires_at = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Kiểm tra và xóa token cũ nếu có
        $deleteOldToken = "DELETE FROM password_resets WHERE email = ?";
        $stmt = mysqli_prepare($conn, $deleteOldToken);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        
        // Lưu token mới vào database
        $insertToken = "INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertToken);
        mysqli_stmt_bind_param($stmt, "sss", $email, $token, $expires_at);
        
        if (mysqli_stmt_execute($stmt)) {
            // Tạo URL đặt lại mật khẩu
            $resetUrl = "http://" . $_SERVER['HTTP_HOST'] . "/auth/reset-password.php?token=" . $token . "&email=" . urlencode($email);
            
            // Ở đây sẽ gửi email, nhưng vì không có chức năng gửi email, chúng ta sẽ hiển thị liên kết
            $success = "Link đặt lại mật khẩu đã được tạo. Trong môi trường thực tế, liên kết sẽ được gửi đến email của bạn.<br>";
            $success .= "Liên kết đặt lại: <a href=\"$resetUrl\">$resetUrl</a>";
        } else {
            $error = "Có lỗi xảy ra. Vui lòng thử lại sau.";
        }
    }
}

// Xử lý form đặt lại mật khẩu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $email = sanitizeInput($_POST['email']);
    $token = sanitizeInput($_POST['token']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Kiểm tra mật khẩu
    if (strlen($password) < 6) {
        $error = "Mật khẩu phải có ít nhất 6 ký tự.";
    } elseif ($password != $confirmPassword) {
        $error = "Mật khẩu xác nhận không khớp.";
    } else {
        // Kiểm tra token có hợp lệ không
        $conn = getConnection();
        $checkToken = "SELECT * FROM password_resets WHERE email = ? AND token = ? AND expires_at > NOW()";
        $stmt = mysqli_prepare($conn, $checkToken);
        mysqli_stmt_bind_param($stmt, "ss", $email, $token);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 0) {
            $error = "Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn.";
        } else {
            // Mã hóa mật khẩu mới
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Cập nhật mật khẩu
            $updatePassword = "UPDATE users SET password = ? WHERE email = ?";
            $stmt = mysqli_prepare($conn, $updatePassword);
            mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $email);
            
            if (mysqli_stmt_execute($stmt)) {
                // Xóa token đã sử dụng
                $deleteToken = "DELETE FROM password_resets WHERE email = ?";
                $stmt = mysqli_prepare($conn, $deleteToken);
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                
                $success = "Mật khẩu đã được đặt lại thành công. Bạn có thể <a href=\"login.php\">đăng nhập</a> ngay bây giờ.";
                $step = 1; // Reset về bước 1
            } else {
                $error = "Có lỗi xảy ra khi cập nhật mật khẩu. Vui lòng thử lại sau.";
            }
        }
    }
}

// Kiểm tra bảng password_resets có tồn tại không
$conn = getConnection();
$checkTableExists = "SHOW TABLES LIKE 'password_resets'";
$result = mysqli_query($conn, $checkTableExists);

if (mysqli_num_rows($result) == 0) {
    // Tạo bảng password_resets nếu chưa tồn tại
    $createTable = "CREATE TABLE password_resets (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(100) NOT NULL,
        token VARCHAR(100) NOT NULL,
        expires_at DATETIME NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    mysqli_query($conn, $createTable);
}

// Include header
include '../includes/header.php';
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 animate-on-scroll" data-animation="fadeInUp">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0"><i class="fas fa-key me-2"></i>Đặt lại mật khẩu</h4>
                    </div>
                    
                    <div class="card-body p-4">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo $success; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($step == 1): ?>
                            <!-- Bước 1: Form yêu cầu đặt lại mật khẩu -->
                            <p class="text-muted mb-4">Nhập email của bạn để nhận liên kết đặt lại mật khẩu.</p>
                            
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $email; ?>" required>
                                    <label for="email">Email</label>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" name="request_reset" class="btn btn-primary btn-lg">
                                        <i class="fas fa-paper-plane me-2"></i>Gửi yêu cầu
                                    </button>
                                    <a href="login.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Quay lại đăng nhập
                                    </a>
                                </div>
                            </form>
                            
                        <?php else: ?>
                            <!-- Bước 2: Form đặt lại mật khẩu -->
                            <p class="text-muted mb-4">Nhập mật khẩu mới cho tài khoản của bạn.</p>
                            
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                <input type="hidden" name="email" value="<?php echo $email; ?>">
                                <input type="hidden" name="token" value="<?php echo $token; ?>">
                                
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Mật khẩu mới" required>
                                    <label for="password">Mật khẩu mới</label>
                                    <div class="form-text">Mật khẩu phải có ít nhất 6 ký tự.</div>
                                </div>
                                
                                <div class="form-floating mb-4">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Xác nhận mật khẩu" required>
                                    <label for="confirm_password">Xác nhận mật khẩu</label>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" name="reset_password" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i>Đặt lại mật khẩu
                                    </button>
                                    <a href="login.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Quay lại đăng nhập
                                    </a>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <p class="mb-0">Đã nhớ mật khẩu? <a href="login.php">Đăng nhập</a></p>
                    <p class="mt-2">Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .card {
        border-radius: 10px;
        overflow: hidden;
    }
    
    .card-header {
        border-bottom: none;
    }
    
    .btn-primary {
        background-color: #4A6FDC;
        border-color: #4A6FDC;
    }
    
    .btn-primary:hover {
        background-color: #3758b9;
        border-color: #3758b9;
    }
    
    .form-control:focus {
        border-color: #4A6FDC;
        box-shadow: 0 0 0 0.25rem rgba(74, 111, 220, 0.25);
    }
</style>

<!-- Validation script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Nếu form đặt lại mật khẩu tồn tại
        const resetForm = document.querySelector('form[name="reset_password"]');
        if (resetForm) {
            resetForm.addEventListener('submit', function(event) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm_password').value;
                
                if (password.length < 6) {
                    alert('Mật khẩu phải có ít nhất 6 ký tự.');
                    event.preventDefault();
                } else if (password !== confirmPassword) {
                    alert('Mật khẩu xác nhận không khớp.');
                    event.preventDefault();
                }
            });
        }
    });
</script>

<?php
// Include footer
include '../includes/footer.php';
?>