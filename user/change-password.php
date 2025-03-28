<?php
// Include các file cần thiết
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once '../auth/auth_functions.php';

// Kiểm tra đăng nhập
requireLogin();

// Lấy thông tin người dùng
$currentUser = getCurrentUser();
$userId = $currentUser['id'];

// Xử lý đổi mật khẩu
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validate dữ liệu
    $errors = array();
    
    if (empty($currentPassword)) {
        $errors[] = "Vui lòng nhập mật khẩu hiện tại";
    }
    
    if (empty($newPassword)) {
        $errors[] = "Vui lòng nhập mật khẩu mới";
    } elseif (strlen($newPassword) < 6) {
        $errors[] = "Mật khẩu mới phải có ít nhất 6 ký tự";
    }
    
    if ($newPassword != $confirmPassword) {
        $errors[] = "Mật khẩu xác nhận không khớp";
    }
    
    // Nếu không có lỗi, đổi mật khẩu
    if (empty($errors)) {
        $result = changePassword($userId, $currentPassword, $newPassword);
        
        if ($result['success']) {
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = $result['message'];
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = implode("<br>", $errors);
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect về trang profile
    header("Location: profile.php");
    exit;
} else {
    // Nếu không phải POST request, redirect về trang profile
    header("Location: profile.php");
    exit;
}
?>