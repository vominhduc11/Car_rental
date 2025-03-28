<?php
// Thông tin kết nối đến MySQL
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'car_rental');

// Kết nối đến MySQL database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Tạo database nếu chưa tồn tại
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if (mysqli_query($conn, $sql)) {
    // Chọn database
    mysqli_select_db($conn, DB_NAME);

    // Tạo bảng users nếu chưa tồn tại
    $sql_users = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        full_name VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        address TEXT,
        role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    // Tạo bảng cars nếu chưa tồn tại
    $sql_cars = "CREATE TABLE IF NOT EXISTS cars (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        brand VARCHAR(50) NOT NULL,
        model VARCHAR(50) NOT NULL,
        year INT(4) NOT NULL,
        license_plate VARCHAR(20) NOT NULL UNIQUE,
        color VARCHAR(30) NOT NULL,
        seats INT(2) NOT NULL,
        transmission ENUM('auto', 'manual') NOT NULL,
        fuel VARCHAR(20) NOT NULL,
        price_per_day DECIMAL(10,2) NOT NULL,
        image VARCHAR(255),
        description TEXT,
        status ENUM('available', 'maintenance', 'rented') NOT NULL DEFAULT 'available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    // Tạo bảng bookings nếu chưa tồn tại
    $sql_bookings = "CREATE TABLE IF NOT EXISTS bookings (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        car_id INT(11) NOT NULL,
        pickup_date DATE NOT NULL,
        return_date DATE NOT NULL,
        pickup_location VARCHAR(255) NOT NULL,
        return_location VARCHAR(255) NOT NULL,
        total_price DECIMAL(10,2) NOT NULL,
        status ENUM('pending', 'confirmed', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
        payment_status ENUM('pending', 'paid') NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
    )";

    // Tạo bảng reviews nếu chưa tồn tại
    $sql_reviews = "CREATE TABLE IF NOT EXISTS reviews (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        user_id INT(11) NOT NULL,
        car_id INT(11) NOT NULL,
        booking_id INT(11) NOT NULL,
        rating INT(1) NOT NULL,
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE,
        FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
    )";

    // Thực thi các câu lệnh SQL
    mysqli_query($conn, $sql_users);
    mysqli_query($conn, $sql_cars);
    mysqli_query($conn, $sql_bookings);
    mysqli_query($conn, $sql_reviews);

    // Thêm tài khoản admin mặc định nếu chưa tồn tại
    $check_admin = "SELECT * FROM users WHERE username = 'admin'";
    $result = mysqli_query($conn, $check_admin);
    if (mysqli_num_rows($result) == 0) {
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $insert_admin = "INSERT INTO users (username, password, email, full_name, phone, role) 
                         VALUES ('admin', '$admin_password', 'admin@example.com', 'Administrator', '0123456789', 'admin')";
        mysqli_query($conn, $insert_admin);
    }
} else {
    echo "Lỗi khi tạo database: " . mysqli_error($conn);
}

// Hàm để lấy kết nối
function getConnection()
{
    global $conn;
    return $conn;
}

// Hàm đóng kết nối
function closeConnection()
{
    global $conn;
    mysqli_close($conn);
}
