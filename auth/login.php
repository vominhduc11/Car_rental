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
$username = '';

// Xử lý form đăng nhập
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);
    
    // Kiểm tra dữ liệu đầu vào
    if (empty($username) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu";
    } else {
        // Thực hiện đăng nhập
        $result = loginUser($username, $password);
        
        if ($result["success"]) {
            // Chuyển hướng đến trang phù hợp với quyền
            if ($_SESSION['role'] === 'admin') {
                header("Location: /admin/index.php");
            } else {
                header("Location: /user/index.php");
            }
            exit;
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
    <title>Đăng nhập - Hệ thống Cho Thuê Xe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/animations.css">
    <style>
        body {
            background-color: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 450px;
            width: 100%;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 0.5s;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
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
        .login-footer {
            text-align: center;
            margin-top: 20px;
        }
        .login-footer a {
            color: #4A6FDC;
            text-decoration: none;
            transition: color 0.3s;
        }
        .login-footer a:hover {
            color: #3758b9;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <div class="login-header">
                <h1><i class="fas fa-car"></i> CAR RENTAL</h1>
                <p class="text-muted">Đăng nhập để tiếp tục</p>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="login-form">
                <div class="form-floating">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Tên đăng nhập" value="<?php echo $username; ?>" required>
                    <label for="username">Tên đăng nhập</label>
                </div>
                
                <div class="form-floating">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Mật khẩu" required>
                    <label for="password">Mật khẩu</label>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember-me">
                        <label class="form-check-label" for="remember-me">
                            Ghi nhớ đăng nhập
                        </label>
                    </div>
                    <a href="/auth/reset-password.php">Quên mật khẩu?</a>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                </button>
            </form>
            
            <div class="login-footer">
                <p>Chưa có tài khoản? <a href="/auth/register.php">Đăng ký ngay</a></p>
                <p><a href="/index.php">← Quay lại trang chủ</a></p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/js/validation.js"></script>
    <script>
        // Animation khi load trang
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('.login-container').classList.add('animate__animated', 'animate__fadeIn');
        });
        
        // Validation form
        document.getElementById('login-form').addEventListener('submit', function(event) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value.trim();
            
            if (username === '' || password === '') {
                event.preventDefault();
                alert('Vui lòng nhập đầy đủ thông tin đăng nhập');
            }
        });
    </script>
</body>
</html>