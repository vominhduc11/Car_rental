<?php
// Kiểm tra nếu biến GET 'filename' được truyền vào
if (isset($_GET['filename'])) {
    // Lấy tên file và loại bỏ các ký tự không an toàn
    $filename = basename($_GET['filename']);

    // Đường dẫn thư mục chứa file
    $directory = 'uploads/cars';

    // Tạo đường dẫn đầy đủ đến file
    $filepath = $directory . '/' . $filename;

    // Kiểm tra file có tồn tại hay không
    if (file_exists($filepath)) {
        // Xác định loại file nếu cần (ví dụ: hiển thị ảnh)
        $fileInfo = pathinfo($filepath);
        $extension = strtolower($fileInfo['extension']);

        // Nếu file là ảnh, thiết lập header Content-Type phù hợp và đọc file
        $mimeTypes = [
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif'
        ];

        if (array_key_exists($extension, $mimeTypes)) {
            header('Content-Type: ' . $mimeTypes[$extension]);
            readfile($filepath);
            exit;
        } else {
            // Nếu file không phải ảnh, bạn có thể thực hiện xử lý khác
            echo "File tồn tại: " . htmlspecialchars($filename);
        }
    } else {
        echo "Không tìm thấy file: " . htmlspecialchars($filename);
    }
} else {
    echo "Chưa có filename được truyền vào.";
}
