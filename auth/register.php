<?php
require_once '../auth/auth_functions.php';

// Nếu đã đăng nhập, chuyển hướng đến trang chủ hoặc dashboard
if (isLoggedIn()) {
    if (isAdmin()) {
        header("Location: /admin/index.php");
    } else {
        header("Location: /user/index.php");
    }
    exit;
}

$error = '';
$success = '';
$username = $email = $full_name = $phone = $address = '';

// Xử lý form đăng ký
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);
    $email = trim($_POST["email"]);
    $full_name = trim($_POST["full_name"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($username) || empty($password) || empty($confirm_password) || empty($email) || empty($full_name) || empty($phone)) {
        $error = "Vui lòng nhập đầy đủ các trường bắt buộc";
    } elseif ($password !== $confirm_password) {
        $error = "Mật khẩu xác nhận không khớp";
    } elseif (strlen($password) < 6) {
        $error = "Mật khẩu phải có ít nhất 6 ký tự";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ";
    } else {
        // Đăng ký người dùng
        $result = registerUser($username, $password, $email, $full_name, $phone, $address);
        
        if ($result["success"]) {
            $success = $result["message"];
            // Xóa trắng form
            $username = $email = $full_name = $phone = $address = '';
        } else {
            $error = $result["message"];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Hệ thống Cho Thuê Xe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/animations.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 40px 0;
        }
        .register-container {
            max-width: 650px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 0.5s;
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-header h1 {
            color: #4A6FDC;
            font-weight: bold;
        }
        .form-control:focus {
            border-color: #4A6FDC;
            box-shadow: 0 0 0 0.25rem rgba(74, 111, 220, 0.25);
        }
        .btn-primary {
            background-color: #4A6FDC;
            border-color: #4A6FDC;
            width: 100%;
            padding: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #3758b9;
            transform: translateY(-2px);
        }
        .form-floating {
            margin-bottom: 20px;
        }
        .register-footer {
            text-align: center;
            margin-top: 20px;
        }
        .register-footer a {
            color: #4A6FDC;
            text-decoration: none;
            transition: color 0.3s;
        }
        .register-footer a:hover {
            color: #3758b9;
            text-decoration: underline;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="register-header">
                <h1><i class="fas fa-car"></i> CAR RENTAL</h1>
                <p class="text-muted">Đăng ký tài khoản mới</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success; ?>
                    <br>
                    <a href="/auth/login.php">Đăng nhập ngay</a>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="register-form">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Tên đăng nhập" value="<?php echo $username; ?>" required>
                            <label for="username" class="required-field">Tên đăng nhập</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $email; ?>" required>
                            <label for="email" class="required-field">Email</label>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Mật khẩu" required>
                            <label for="password" class="required-field">Mật khẩu</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating">
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Xác nhận mật khẩu" required>
                            <label for="confirm_password" class="required-field">Xác nhận mật khẩu</label>
                        </div>
                    </div>
                </div>
                
                <div class="form-floating">
                    <input type="text" class="form-control" id="full_name" name="full_name" placeholder="Họ và tên" value="<?php echo $full_name; ?>" required>
                    <label for="full_name" class="required-field">Họ và tên</label>
                </div>
                
                <div class="form-floating">
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Số điện thoại" value="<?php echo $phone; ?>" required>
                    <label for="phone" class="required-field">Số điện thoại</label>
                </div>
                
                <div class="form-floating">
                    <textarea class="form-control" id="address" name="address" placeholder="Địa chỉ" style="height: 100px"><?php echo $address; ?></textarea>
                    <label for="address">Địa chỉ</label>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agree-terms" required>
                        <label class="form-check-label" for="agree-terms">
                            Tôi đồng ý với các <a href="#">điều khoản dịch vụ</a>
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-user-plus me-2"></i>Đăng ký
                </button>
            </form>
            
            <div class="register-footer">
                <p>Đã có tài khoản? <a href="/auth/login.php">Đăng nhập</a></p>
                <p><a href="/index.php">← Quay lại trang chủ</a></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/validation.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Animation khi load trang
            document.querySelector('.register-container').classList.add('animate__animated', 'animate__fadeIn');
            
            // Validation form
            const form = document.getElementById('register-form');
            const password = document.getElementById('password');
            const confirmPassword = document.getElementById('confirm_password');
            const email = document.getElementById('email');
            const phone = document.getElementById('phone');
            
            form.addEventListener('submit', function(event) {
                let isValid = true;
                
                // Kiểm tra mật khẩu
                if (password.value.length < 6) {
                    alert('Mật khẩu phải có ít nhất 6 ký tự');
                    isValid = false;
                }
                
                // Kiểm tra mật khẩu xác nhận
                if (password.value !== confirmPassword.value) {
                    alert('Mật khẩu xác nhận không khớp');
                    isValid = false;
                }
                
                // Kiểm tra email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email.value)) {
                    alert('Email không hợp lệ');
                    isValid = false;
                }
                
                // Kiểm tra số điện thoại
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
</body>
</html>