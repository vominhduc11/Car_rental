<?php
require_once __DIR__ . '/../config/database.php';

/**
 * Lấy danh sách tất cả xe
 */
function getAllCars() {
    $conn = getConnection();
    
    $sql = "SELECT * FROM cars ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    
    $cars = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $cars[] = $row;
    }
    
    return $cars;
}

/**
 * Lấy danh sách xe có sẵn để đặt
 */
function getAvailableCars() {
    $conn = getConnection();
    
    $sql = "SELECT * FROM cars WHERE status = 'available' ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    
    $cars = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $cars[] = $row;
    }
    
    return $cars;
}

/**
 * Lấy thông tin xe theo ID
 */
function getCarById($carId) {
    $conn = getConnection();
    
    $sql = "SELECT * FROM cars WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $carId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Lấy xe theo tiêu chí tìm kiếm
 */
function searchCars($keywords = '', $brand = '', $minPrice = 0, $maxPrice = 9999999999) {
    $conn = getConnection();
    
    $sql = "SELECT * FROM cars WHERE 
            (brand LIKE ? OR model LIKE ? OR description LIKE ?) 
            AND (brand = ? OR ? = '') 
            AND price_per_day BETWEEN ? AND ? 
            ORDER BY id DESC";
    
    $keywordsParam = "%" . $keywords . "%";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssdd", $keywordsParam, $keywordsParam, $keywordsParam, $brand, $brand, $minPrice, $maxPrice);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $cars = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $cars[] = $row;
    }
    
    return $cars;
}

/**
 * Lấy xe khả dụng trong khoảng thời gian
 */
function getAvailableCarsForDates($startDate, $endDate) {
    $conn = getConnection();
    
    $sql = "SELECT c.* FROM cars c 
            WHERE c.status = 'available' 
            AND c.id NOT IN (
                SELECT b.car_id FROM bookings b 
                WHERE (b.status = 'confirmed' OR b.status = 'pending') 
                AND ((b.pickup_date BETWEEN ? AND ?) 
                OR (b.return_date BETWEEN ? AND ?) 
                OR (b.pickup_date <= ? AND b.return_date >= ?))
            )
            ORDER BY c.id DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssssss", $startDate, $endDate, $startDate, $endDate, $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $cars = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $cars[] = $row;
    }
    
    return $cars;
}

/**
 * Lấy danh sách tất cả hãng xe
 */
function getAllBrands() {
    $conn = getConnection();
    
    $sql = "SELECT DISTINCT brand FROM cars ORDER BY brand";
    $result = mysqli_query($conn, $sql);
    
    $brands = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $brands[] = $row['brand'];
    }
    
    return $brands;
}

/**
 * Kiểm tra xe có khả dụng không
 */
function isCarAvailable($carId, $startDate, $endDate) {
    $conn = getConnection();
    
    $sql = "SELECT COUNT(*) as count FROM bookings 
            WHERE car_id = ? 
            AND (status = 'confirmed' OR status = 'pending') 
            AND ((pickup_date BETWEEN ? AND ?) 
            OR (return_date BETWEEN ? AND ?) 
            OR (pickup_date <= ? AND return_date >= ?))";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "issssss", $carId, $startDate, $endDate, $startDate, $endDate, $startDate, $endDate);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    // Nếu không có đặt trùng, xe khả dụng
    return $row['count'] == 0;
}

/**
 * Kiểm tra xem người dùng có được đặt xe không
 */
function canUserBook($userId) {
    $conn = getConnection();
    
    // Kiểm tra số lượng đặt xe chưa hoàn thành
    $sql = "SELECT COUNT(*) as count FROM bookings 
            WHERE user_id = ? 
            AND (status = 'pending' OR status = 'confirmed')";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    // Giới hạn mỗi người dùng 3 đặt xe chưa hoàn thành
    return $row['count'] < 3;
}

/**
 * Tạo đặt xe mới
 */
function createBooking($userId, $carId, $pickupDate, $returnDate, $pickupLocation, $returnLocation, $totalPrice) {
    $conn = getConnection();
    
    $sql = "INSERT INTO bookings (user_id, car_id, pickup_date, return_date, pickup_location, return_location, total_price, status, payment_status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', 'pending')";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iissssd", $userId, $carId, $pickupDate, $returnDate, $pickupLocation, $returnLocation, $totalPrice);
    
    if (mysqli_stmt_execute($stmt)) {
        return mysqli_insert_id($conn);
    } else {
        return 0;
    }
}

/**
 * Lấy thông tin đặt xe theo ID
 */
function getBookingById($bookingId) {
    $conn = getConnection();
    
    $sql = "SELECT b.*, u.username, u.full_name, u.phone, u.email, c.brand, c.model, c.license_plate, c.image
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN cars c ON b.car_id = c.id
            WHERE b.id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $bookingId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Lấy danh sách đặt xe của người dùng
 */
function getUserBookings($userId) {
    $conn = getConnection();
    
    $sql = "SELECT b.*, c.brand, c.model, c.image
            FROM bookings b
            JOIN cars c ON b.car_id = c.id
            WHERE b.user_id = ?
            ORDER BY b.created_at DESC";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $bookings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $bookings[] = $row;
    }
    
    return $bookings;
}

/**
 * Lấy tất cả đặt xe cho admin
 */
function getAllBookings() {
    $conn = getConnection();
    
    $sql = "SELECT b.*, u.username, u.full_name, c.brand, c.model, c.license_plate
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            JOIN cars c ON b.car_id = c.id
            ORDER BY b.created_at DESC";
    
    $result = mysqli_query($conn, $sql);
    
    $bookings = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $bookings[] = $row;
    }
    
    return $bookings;
}

/**
 * Cập nhật trạng thái đặt xe
 */
function updateBookingStatus($bookingId, $status) {
    $conn = getConnection();
    
    $sql = "UPDATE bookings SET status = ? WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $bookingId);
    
    return mysqli_stmt_execute($stmt);
}

/**
 * Cập nhật trạng thái thanh toán
 */
function updatePaymentStatus($bookingId, $status) {
    $conn = getConnection();
    
    $sql = "UPDATE bookings SET payment_status = ? WHERE id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "si", $status, $bookingId);
    
    return mysqli_stmt_execute($stmt);
}

/**
 * Hủy đặt xe
 */
function cancelBooking($bookingId) {
    return updateBookingStatus($bookingId, 'cancelled');
}

/**
 * Lấy tất cả người dùng (cho admin)
 */
function getAllUsers() {
    $conn = getConnection();
    
    $sql = "SELECT * FROM users ORDER BY id DESC";
    $result = mysqli_query($conn, $sql);
    
    $users = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    
    return $users;
}

/**
 * Lấy thông tin người dùng theo ID
 */
function getUserById($userId) {
    $conn = getConnection();
    
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

/**
 * Thêm xe mới
 */
function addCar($brand, $model, $year, $licensePlate, $color, $seats, $transmission, $fuel, $pricePerDay, $image, $description) {
    $conn = getConnection();
    
    $sql = "INSERT INTO cars (brand, model, year, license_plate, color, seats, transmission, fuel, price_per_day, image, description, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'available')";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ssisssssdss", $brand, $model, $year, $licensePlate, $color, $seats, $transmission, $fuel, $pricePerDay, $image, $description);
    
    return mysqli_stmt_execute($stmt);
}

/**
 * Cập nhật thông tin xe
 */
function updateCar($carId, $brand, $model, $year, $licensePlate, $color, $seats, $transmission, $fuel, $pricePerDay, $image, $description, $status) {
    $conn = getConnection();
    
    // Nếu không có ảnh mới, giữ nguyên ảnh cũ
    if (empty($image)) {
        $sql = "UPDATE cars SET brand = ?, model = ?, year = ?, license_plate = ?, color = ?, 
                seats = ?, transmission = ?, fuel = ?, price_per_day = ?, description = ?, status = ? 
                WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssisssssdssi", $brand, $model, $year, $licensePlate, $color, $seats, $transmission, $fuel, $pricePerDay, $description, $status, $carId);
    } else {
        $sql = "UPDATE cars SET brand = ?, model = ?, year = ?, license_plate = ?, color = ?, 
                seats = ?, transmission = ?, fuel = ?, price_per_day = ?, image = ?, description = ?, status = ? 
                WHERE id = ?";
        
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ssisssssdsssi", $brand, $model, $year, $licensePlate, $color, $seats, $transmission, $fuel, $pricePerDay, $image, $description, $status, $carId);
    }
    
    return mysqli_stmt_execute($stmt);
}

/**
 * Xóa xe
 */
function deleteCar($carId) {
    $conn = getConnection();
    
    // Kiểm tra xem xe có đang được đặt không
    $checkSql = "SELECT COUNT(*) as count FROM bookings 
                WHERE car_id = ? 
                AND (status = 'confirmed' OR status = 'pending')";
    
    $stmt = mysqli_prepare($conn, $checkSql);
    mysqli_stmt_bind_param($stmt, "i", $carId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    if ($row['count'] > 0) {
        return false; // Không thể xóa vì xe đang được đặt
    }
    
    // Tiến hành xóa xe
    $sql = "DELETE FROM cars WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $carId);
    
    return mysqli_stmt_execute($stmt);
}

/**
 * Upload hình ảnh
 */
function uploadImage($file, $targetDir = '../uploads/cars/') {
    // Kiểm tra thư mục tồn tại, nếu không thì tạo mới
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    // Tạo tên file duy nhất
    $fileName = time() . '_' . basename($file['name']);
    $targetFilePath = $targetDir . $fileName;
    $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);
    
    // Chỉ cho phép upload hình ảnh
    $allowTypes = array('jpg', 'jpeg', 'png', 'gif');
    if (in_array(strtolower($fileType), $allowTypes)) {
        // Upload file
        if (move_uploaded_file($file['tmp_name'], $targetFilePath)) {
            return '/uploads/cars/' . $fileName;
        }
    }
    
    return '';
}

/**
 * Định dạng giá tiền
 */
function formatPrice($price) {
    return number_format($price, 0, ',', '.') . ' VND';
}

/**
 * Định dạng ngày
 */
function formatDate($date) {
    $timestamp = strtotime($date);
    return date('d/m/Y', $timestamp);
}

/**
 * Lấy số ngày giữa hai ngày
 */
function getDaysBetween($startDate, $endDate) {
    $start = new DateTime($startDate);
    $end = new DateTime($endDate);
    $interval = $start->diff($end);
    return $interval->days;
}

/**
 * Kiểm tra quyền xem booking
 */
function canViewBooking($bookingId, $userId, $isAdmin = false) {
    $booking = getBookingById($bookingId);
    
    if ($booking) {
        if ($isAdmin) {
            return true;
        } else {
            return $booking['user_id'] == $userId;
        }
    }
    
    return false;
}

/**
 * Lấy thống kê tổng quan cho dashboard
 */
function getDashboardStats() {
    $conn = getConnection();
    
    // Tổng số xe
    $totalCars = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM cars"));
    
    // Tổng số xe khả dụng
    $availableCars = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM cars WHERE status = 'available'"));
    
    // Tổng số lượt đặt xe
    $totalBookings = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM bookings"));
    
    // Tổng số đặt xe đang chờ xử lý
    $pendingBookings = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM bookings WHERE status = 'pending'"));
    
    // Tổng số người dùng
    $totalUsers = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM users WHERE role = 'user'"));
    
    // Doanh thu tháng hiện tại
    $firstDayOfMonth = date('Y-m-01');
    $lastDayOfMonth = date('Y-m-t');
    
    $sql = "SELECT SUM(total_price) as revenue FROM bookings 
            WHERE status = 'completed' 
            AND created_at BETWEEN ? AND ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $firstDayOfMonth, $lastDayOfMonth);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    $monthlyRevenue = $row['revenue'] ? $row['revenue'] : 0;
    
    return [
        'totalCars' => $totalCars,
        'availableCars' => $availableCars,
        'totalBookings' => $totalBookings,
        'pendingBookings' => $pendingBookings,
        'totalUsers' => $totalUsers,
        'monthlyRevenue' => $monthlyRevenue
    ];
}

/**
 * Tạo phân trang
 */
function createPagination($currentPage, $totalItems, $itemsPerPage, $url) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    
    if ($totalPages <= 1) {
        return '';
    }
    
    $pagination = '<nav aria-label="Phân trang"><ul class="pagination justify-content-center">';
    
    // Previous button
    $prevClass = ($currentPage <= 1) ? 'disabled' : '';
    $prevPage = ($currentPage <= 1) ? '#' : $url . '?page=' . ($currentPage - 1);
    $pagination .= '<li class="page-item ' . $prevClass . '"><a class="page-link" href="' . $prevPage . '">Trước</a></li>';
    
    // Page numbers
    $startPage = max(1, $currentPage - 2);
    $endPage = min($totalPages, $currentPage + 2);
    
    if ($startPage > 1) {
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=1">1</a></li>';
        if ($startPage > 2) {
            $pagination .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        $activeClass = ($i == $currentPage) ? 'active' : '';
        $pagination .= '<li class="page-item ' . $activeClass . '"><a class="page-link" href="' . $url . '?page=' . $i . '">' . $i . '</a></li>';
    }
    
    if ($endPage < $totalPages) {
        if ($endPage < $totalPages - 1) {
            $pagination .= '<li class="page-item disabled"><a class="page-link" href="#">...</a></li>';
        }
        $pagination .= '<li class="page-item"><a class="page-link" href="' . $url . '?page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    
    // Next button
    $nextClass = ($currentPage >= $totalPages) ? 'disabled' : '';
    $nextPage = ($currentPage >= $totalPages) ? '#' : $url . '?page=' . ($currentPage + 1);
    $pagination .= '<li class="page-item ' . $nextClass . '"><a class="page-link" href="' . $nextPage . '">Tiếp</a></li>';
    
    $pagination .= '</ul></nav>';
    
    return $pagination;
}

/**
 * Bảo vệ dữ liệu đầu vào
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Hàm redirect với thông báo
 */
function redirectWithMessage($url, $message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header("Location: $url");
    exit();
}

/**
 * Hiển thị thông báo từ session
 */
function displayMessage() {
    if (isset($_SESSION['message'])) {
        $type = isset($_SESSION['message_type']) ? $_SESSION['message_type'] : 'info';
        $message = $_SESSION['message'];
        
        // Xóa thông báo sau khi hiển thị
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        
        return '<div class="alert alert-' . $type . ' alert-dismissible fade show" role="alert" data-auto-dismiss="5000">
                    ' . $message . '
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>';
    }
    
    return '';
}

/**
 * Hàm mã hóa chuỗi
 */
function encryptId($id) {
    return base64_encode($id * 7 + 3);
}

/**
 * Hàm giải mã chuỗi
 */
function decryptId($encrypted) {
    return (base64_decode($encrypted) - 3) / 7;
}