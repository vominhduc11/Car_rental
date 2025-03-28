<?php
require_once '../auth/auth_functions.php';

// Thực hiện đăng xuất
logoutUser();

// Chuyển hướng về trang đăng nhập
header("Location: /index.php");
exit;
?>