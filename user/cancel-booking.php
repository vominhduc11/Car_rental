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

// Lấy ID đặt xe từ URL
$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Kiểm tra quyền hủy đặt xe
if (!canViewBooking($bookingId, $userId)) {
    $_SESSION['message'] = "Bạn không có quyền hủy đơn đặt xe này.";
    $_SESSION['message_type'] = "danger";
    header("Location: bookings.php");
    exit;
}

// Lấy thông tin đặt xe
$booking = getBookingById($bookingId);

// Kiểm tra trạng thái đặt xe (chỉ hủy được đơn đang chờ)
if ($booking['status'] !== 'pending') {
    $_SESSION['message'] = "Chỉ có thể hủy đơn đặt xe đang ở trạng thái chờ xác nhận.";
    $_SESSION['message_type'] = "warning";
    header("Location: view-booking.php?id=" . $bookingId);
    exit;
}

// Hủy đặt xe
$result = cancelBooking($bookingId);

if ($result) {
    $_SESSION['message'] = "Đã hủy đơn đặt xe thành công.";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Có lỗi xảy ra khi hủy đơn đặt xe. Vui lòng thử lại sau.";
    $_SESSION['message_type'] = "danger";
}

// Redirect về trang chi tiết đặt xe
header("Location: view-booking.php?id=" . $bookingId);
exit;
?>