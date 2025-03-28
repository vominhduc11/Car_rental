<?php
// Include các file cần thiết
require_once '../../config/database.php';
require_once '../../includes/functions.php';
require_once '../../auth/auth_functions.php';

// Kiểm tra đăng nhập và quyền admin
requireAdmin();

// Lấy ID xe từ URL
$carId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Kiểm tra xe có tồn tại không
$car = getCarById($carId);
if (!$car) {
    $_SESSION['message'] = "Không tìm thấy xe với ID: $carId";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit;
}

// Kiểm tra xem xe có đang được thuê không
if ($car['status'] == 'rented') {
    $_SESSION['message'] = "Không thể xóa xe đang cho thuê.";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit;
}

// Kiểm tra xem xe có đơn đặt đang chờ xử lý hoặc đã xác nhận không
$conn = getConnection();
$checkBookings = "SELECT COUNT(*) as count FROM bookings WHERE car_id = ? AND (status = 'pending' OR status = 'confirmed')";
$stmt = mysqli_prepare($conn, $checkBookings);
mysqli_stmt_bind_param($stmt, "i", $carId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);

if ($row['count'] > 0) {
    $_SESSION['message'] = "Không thể xóa xe vì có đơn đặt đang chờ xử lý hoặc đã xác nhận.";
    $_SESSION['message_type'] = "danger";
    header("Location: index.php");
    exit;
}

// Tiến hành xóa xe
$result = deleteCar($carId);

if ($result) {
    $_SESSION['message'] = "Đã xóa xe thành công!";
    $_SESSION['message_type'] = "success";
} else {
    $_SESSION['message'] = "Có lỗi xảy ra khi xóa xe. Vui lòng thử lại sau.";
    $_SESSION['message_type'] = "danger";
}

// Chuyển hướng về trang danh sách xe
header("Location: index.php");
exit;
?>