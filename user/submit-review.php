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

// Xử lý gửi đánh giá
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $bookingId = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
    $carId = isset($_POST['car_id']) ? (int)$_POST['car_id'] : 0;
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = isset($_POST['comment']) ? sanitizeInput($_POST['comment']) : '';
    
    // Validate dữ liệu
    $errors = array();
    
    if ($bookingId <= 0) {
        $errors[] = "Mã đặt xe không hợp lệ";
    }
    
    if ($carId <= 0) {
        $errors[] = "Mã xe không hợp lệ";
    }
    
    if ($rating <= 0 || $rating > 5) {
        $errors[] = "Đánh giá không hợp lệ (1-5 sao)";
    }
    
    // Kiểm tra quyền đánh giá (phải là người đặt xe và đơn đã hoàn thành)
    $booking = getBookingById($bookingId);
    
    if (!$booking || $booking['user_id'] != $userId) {
        $errors[] = "Bạn không có quyền đánh giá đơn đặt xe này";
    } elseif ($booking['status'] != 'completed') {
        $errors[] = "Chỉ có thể đánh giá sau khi hoàn thành đơn đặt xe";
    }
    
    // Kiểm tra xem đã đánh giá chưa
    $conn = getConnection();
    $checkReview = "SELECT COUNT(*) as count FROM reviews WHERE booking_id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $checkReview);
    mysqli_stmt_bind_param($stmt, "ii", $bookingId, $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['count'] > 0) {
        $errors[] = "Bạn đã đánh giá đơn đặt xe này trước đó";
    }
    
    // Nếu không có lỗi, lưu đánh giá
    if (empty($errors)) {
        $insertReview = "INSERT INTO reviews (user_id, car_id, booking_id, rating, comment) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $insertReview);
        mysqli_stmt_bind_param($stmt, "iiiis", $userId, $carId, $bookingId, $rating, $comment);
        
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['message'] = "Cảm ơn bạn đã gửi đánh giá!";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại sau.";
            $_SESSION['message_type'] = "danger";
        }
    } else {
        $_SESSION['message'] = implode("<br>", $errors);
        $_SESSION['message_type'] = "danger";
    }
    
    // Redirect về trang chi tiết đặt xe
    header("Location: view-booking.php?id=" . $bookingId);
    exit;
} else {
    // Nếu không phải POST request, redirect về trang bookings
    header("Location: bookings.php");
    exit;
}

/**
 * Hàm thêm đánh giá (phòng trường hợp không có sẵn trong functions.php)
 */
function addReview($userId, $carId, $bookingId, $rating, $comment) {
    $conn = getConnection();
    
    $sql = "INSERT INTO reviews (user_id, car_id, booking_id, rating, comment) VALUES (?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iiiis", $userId, $carId, $bookingId, $rating, $comment);
    
    return mysqli_stmt_execute($stmt);
}
?>