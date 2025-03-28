<?php
// Include các file cần thiết
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../auth/auth_functions.php';

// Kiểm tra đăng nhập và quyền admin
requireAdmin();

// Lấy ID đặt xe từ URL
$bookingId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Kiểm tra đơn đặt xe có tồn tại không
$booking = getBookingById($bookingId);

if (!$booking) {
    $_SESSION['message'] = "Không tìm thấy đơn đặt xe với ID: $bookingId";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit;
}

// Thực hiện xóa đơn đặt xe
$conn = getConnection();
$deleteBooking = "DELETE FROM bookings WHERE id = ?";
$stmt = mysqli_prepare($conn, $deleteBooking);
mysqli_stmt_bind_param($stmt, "i", $bookingId);

if (mysqli_stmt_execute($stmt)) {
    // Xóa thành công
    $_SESSION['message'] = "Đã xóa đơn đặt xe thành công!";
    $_SESSION['message_type'] = "success";
} else {
    // Xóa thất bại
    $_SESSION['message'] = "Có lỗi xảy ra khi xóa đơn đặt xe. Vui lòng thử lại sau.";
    $_SESSION['message_type'] = "danger";
}

// Redirect về trang danh sách đặt xe
header("Location: index.php");
exit;
?>