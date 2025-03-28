<?php
// Bằng đoạn code này:
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

/**
 * Đăng ký người dùng mới
 */
function registerUser($username, $password, $email, $full_name, $phone, $address = '') {
    $conn = getConnection();
    
    // Kiểm tra username đã tồn tại chưa
    $check_user = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $check_user);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return array("success" => false, "message" => "Tên đăng nhập đã tồn tại");
    }
    
    // Kiểm tra email đã tồn tại chưa
    $check_email = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $check_email);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return array("success" => false, "message" => "Email đã tồn tại");
    }
    
    // Mã hóa mật khẩu
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Thêm người dùng vào database
    $insert_user = "INSERT INTO users (username, password, email, full_name, phone, address, role) 
                    VALUES (?, ?, ?, ?, ?, ?, 'user')";
    $stmt = mysqli_prepare($conn, $insert_user);
    mysqli_stmt_bind_param($stmt, "ssssss", $username, $hashed_password, $email, $full_name, $phone, $address);
    
    if (mysqli_stmt_execute($stmt)) {
        return array("success" => true, "message" => "Đăng ký thành công");
    } else {
        return array("success" => false, "message" => "Lỗi đăng ký: " . mysqli_error($conn));
    }
}

/**
 * Đăng nhập người dùng
 */
function loginUser($username, $password) {
    $conn = getConnection();
    
    // Lấy thông tin người dùng từ database
    $get_user = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $get_user);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Xác thực mật khẩu
        if (password_verify($password, $user['password'])) {
            // Lưu thông tin người dùng vào session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            return array("success" => true, "user" => $user);
        } else {
            return array("success" => false, "message" => "Mật khẩu không chính xác");
        }
    } else {
        return array("success" => false, "message" => "Tên đăng nhập không tồn tại");
    }
}

/**
 * Đăng xuất người dùng
 */
function logoutUser() {
    // Xóa toàn bộ session
    session_unset();
    session_destroy();
    
    return true;
}

/**
 * Kiểm tra đăng nhập
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Kiểm tra quyền admin
 */
function isAdmin() {
    return (isset($_SESSION['role']) && $_SESSION['role'] == 'admin');
}

/**
 * Yêu cầu đăng nhập, chuyển hướng nếu chưa đăng nhập
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /auth/login.php");
        exit;
    }
}

/**
 * Yêu cầu quyền admin, chuyển hướng nếu không phải admin
 */
function requireAdmin() {
    if (!isLoggedIn() || !isAdmin()) {
        header("Location: /auth/login.php");
        exit;
    }
}

/**
 * Lấy thông tin người dùng hiện tại
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        $conn = getConnection();
        $user_id = $_SESSION['user_id'];
        
        $get_user = "SELECT * FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $get_user);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 1) {
            return mysqli_fetch_assoc($result);
        }
    }
    
    return null;
}

/**
 * Cập nhật thông tin người dùng
 */
function updateUserProfile($user_id, $email, $full_name, $phone, $address) {
    $conn = getConnection();
    
    // Kiểm tra email đã tồn tại chưa (nếu đổi email)
    $check_email = "SELECT * FROM users WHERE email = ? AND id != ?";
    $stmt = mysqli_prepare($conn, $check_email);
    mysqli_stmt_bind_param($stmt, "si", $email, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return array("success" => false, "message" => "Email đã tồn tại");
    }
    
    // Cập nhật thông tin
    $update_user = "UPDATE users SET email = ?, full_name = ?, phone = ?, address = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $update_user);
    mysqli_stmt_bind_param($stmt, "ssssi", $email, $full_name, $phone, $address, $user_id);
    
    if (mysqli_stmt_execute($stmt)) {
        return array("success" => true, "message" => "Cập nhật thông tin thành công");
    } else {
        return array("success" => false, "message" => "Lỗi cập nhật: " . mysqli_error($conn));
    }
}

/**
 * Đổi mật khẩu
 */
function changePassword($user_id, $current_password, $new_password) {
    $conn = getConnection();
    
    // Lấy thông tin người dùng
    $get_user = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $get_user);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Xác thực mật khẩu hiện tại
        if (password_verify($current_password, $user['password'])) {
            // Mã hóa mật khẩu mới
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Cập nhật mật khẩu
            $update_password = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $update_password);
            mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                return array("success" => true, "message" => "Đổi mật khẩu thành công");
            } else {
                return array("success" => false, "message" => "Lỗi cập nhật: " . mysqli_error($conn));
            }
        } else {
            return array("success" => false, "message" => "Mật khẩu hiện tại không chính xác");
        }
    } else {
        return array("success" => false, "message" => "Người dùng không tồn tại");
    }
}
?>