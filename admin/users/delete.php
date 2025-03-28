<?php
// Include các file cần thiết
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../auth/auth_functions.php';

// Kiểm tra đăng nhập và quyền admin
requireAdmin();

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

// Kiểm tra không thể xóa tài khoản admin
if ($user['role'] === 'admin') {
    $_SESSION['message'] = "Không thể xóa tài khoản admin!";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit;
}

// Kiểm tra người dùng không có đơn đặt xe đang hoạt động
$conn = getConnection();
$checkActiveBookings = "SELECT COUNT(*) as count FROM bookings WHERE user_id = ? AND (status = 'pending' OR status = 'confirmed')";
$stmt = mysqli_prepare($conn, $checkActiveBookings);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if ($row['count'] > 0) {
    $_SESSION['message'] = "Không thể xóa người dùng có đơn đặt xe đang hoạt động. Vui lòng hủy tất cả đơn đặt xe trước.";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit;
}

// Thực hiện xóa người dùng
$deleteUser = "DELETE FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $deleteUser);
mysqli_stmt_bind_param($stmt, "i", $userId);

if (mysqli_stmt_execute($stmt)) {
    // Xóa các đánh giá của người dùng (nếu có)
    $deleteReviews = "DELETE FROM reviews WHERE user_id = ?";
    $stmt = mysqli_prepare($conn, $deleteReviews);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    
    // Xóa các đơn đặt xe đã hoàn thành hoặc đã hủy của người dùng (nếu có)
    $deleteBookings = "DELETE FROM bookings WHERE user_id = ? AND (status = 'completed' OR status = 'cancelled')";
    $stmt = mysqli_prepare($conn, $deleteBookings);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    
    $_SESSION['message'] = "Đã xóa người dùng thành công!";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Có lỗi xảy ra khi xóa người dùng. Vui lòng thử lại sau.";
    $_SESSION['message_type'] = "danger";
}

// Redirect về trang danh sách người dùng
header("Location: index.php");
exit;
?>