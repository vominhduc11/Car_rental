<?php
// Thiết lập tiêu đề trang
$pageTitle = "Chi tiết xe";

// Include các file cần thiết
require_once './includes/functions.php';
require_once './config/database.php';

// Lấy ID xe từ URL
$carId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin ngày đặt từ URL (nếu có)
$pickupDate = isset($_GET['pickup_date']) ? sanitizeInput($_GET['pickup_date']) : '';
$returnDate = isset($_GET['return_date']) ? sanitizeInput($_GET['return_date']) : '';

// Lấy thông tin chi tiết xe
$car = getCarById($carId);

// Nếu không tìm thấy xe, chuyển hướng về trang danh sách xe
if (!$car) {
    header("Location: ./cars.php");
    exit;
}

// Lấy danh sách xe tương tự (cùng hãng hoặc cùng số chỗ ngồi)
$similarCars = array_filter(getAvailableCars(), function($item) use ($car) {
    return ($item['id'] != $car['id']) && 
           ($item['brand'] == $car['brand'] || $item['seats'] == $car['seats']);
});
// Giới hạn 3 xe tương tự
$similarCars = array_slice($similarCars, 0, 3);

// Kiểm tra xe có khả dụng trong khoảng thời gian không (nếu có ngày đặt)
$isAvailable = true;
if (!empty($pickupDate) && !empty($returnDate)) {
    $isAvailable = isCarAvailable($carId, $pickupDate, $returnDate);
}

// Include header
include './includes/header.php';
?>

<!-- Breadcrumb -->
<section class="bg-light py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="./index.php">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="./cars.php">Danh sách xe</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo $car['brand'] . ' ' . $car['model']; ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Car Detail Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Car Images -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="car-detail-image rounded overflow-hidden mb-4 animate-on-scroll" data-animation="fadeInLeft">
                    <img src="<?php echo !empty($car['image']) ? '/showImg.php?filename='.$car['image'] : './assets/images/car-placeholder.jpg'; ?>" alt="<?php echo $car['brand'] . ' ' . $car['model']; ?>" class="img-fluid w-100">
                </div>
                
                <div class="car-detail-info animate-on-scroll" data-animation="fadeInUp">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="car-detail-title mb-0"><?php echo $car['brand'] . ' ' . $car['model']; ?></h1>
                        <span class="car-status status-<?php echo $car['status']; ?>">
                            <?php 
                                switch($car['status']) {
                                    case 'available':
                                        echo '<span class="badge bg-success">Sẵn sàng</span>';
                                        break;
                                    case 'maintenance':
                                        echo '<span class="badge bg-warning">Bảo dưỡng</span>';
                                        break;
                                    case 'rented':
                                        echo '<span class="badge bg-danger">Đã thuê</span>';
                                        break;
                                }
                            ?>
                        </span>
                    </div>
                    
                    <p class="car-detail-description"><?php echo nl2br($car['description']); ?></p>
                    
                    <h4 class="mt-4 mb-3">Thông số kỹ thuật</h4>
                    <div class="car-specs">
                        <div class="car-spec-item">
                            <div class="car-spec-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="car-spec-details">
                                <span class="car-spec-label">Năm sản xuất</span>
                                <span class="car-spec-value"><?php echo $car['year']; ?></span>
                            </div>
                        </div>
                        
                        <div class="car-spec-item">
                            <div class="car-spec-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="car-spec-details">
                                <span class="car-spec-label">Số chỗ ngồi</span>
                                <span class="car-spec-value"><?php echo $car['seats']; ?> chỗ</span>
                            </div>
                        </div>
                        
                        <div class="car-spec-item">
                            <div class="car-spec-icon">
                                <i class="fas fa-cog"></i>
                            </div>
                            <div class="car-spec-details">
                                <span class="car-spec-label">Hộp số</span>
                                <span class="car-spec-value"><?php echo $car['transmission'] == 'auto' ? 'Tự động' : 'Số sàn'; ?></span>
                            </div>
                        </div>
                        
                        <div class="car-spec-item">
                            <div class="car-spec-icon">
                                <i class="fas fa-gas-pump"></i>
                            </div>
                            <div class="car-spec-details">
                                <span class="car-spec-label">Nhiên liệu</span>
                                <span class="car-spec-value"><?php echo $car['fuel']; ?></span>
                            </div>
                        </div>
                        
                        <div class="car-spec-item">
                            <div class="car-spec-icon">
                                <i class="fas fa-palette"></i>
                            </div>
                            <div class="car-spec-details">
                                <span class="car-spec-label">Màu sắc</span>
                                <span class="car-spec-value"><?php echo $car['color']; ?></span>
                            </div>
                        </div>
                        
                        <div class="car-spec-item">
                            <div class="car-spec-icon">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="car-spec-details">
                                <span class="car-spec-label">Biển số</span>
                                <span class="car-spec-value"><?php echo $car['license_plate']; ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <h4 class="mt-4 mb-3">Đặc điểm xe</h4>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="car-features-list">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Điều hòa hai chiều</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Hệ thống âm thanh cao cấp</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Kết nối Bluetooth</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Cảm biến lùi</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Túi khí an toàn</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="car-features-list">
                                <li><i class="fas fa-check-circle text-success me-2"></i>Camera hành trình</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Cửa sổ trời</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Gương chỉnh điện</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Phanh ABS</li>
                                <li><i class="fas fa-check-circle text-success me-2"></i>Đèn LED ban ngày</li>
                            </ul>
                        </div>
                    </div>
                    
                    <h4 class="mt-4 mb-3">Yêu cầu thuê xe</h4>
                    <div class="car-requirements p-3 bg-light rounded">
                        <ul class="car-requirements-list mb-0">
                            <li><i class="fas fa-id-card me-2"></i>CMND/CCCD và Giấy phép lái xe</li>
                            <li><i class="fas fa-home me-2"></i>Hộ khẩu hoặc KT3</li>
                            <li><i class="fas fa-money-bill-wave me-2"></i>Đặt cọc từ 5 triệu đồng (tùy xe)</li>
                            <li><i class="fas fa-info-circle me-2"></i>Thời gian thuê tối thiểu 24h</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Booking Sidebar -->
            <div class="col-lg-4">
                <div class="booking-sidebar sticky-top" style="top: 90px;">
                    <div class="car-detail-price-card animate-on-scroll" data-animation="fadeInRight">
                        <div class="card-price-header">
                            <h3 class="car-detail-price"><?php echo formatPrice($car['price_per_day']); ?> <span>/ ngày</span></h3>
                        </div>
                        
                        <div class="card-body">
                            <form action="booking.php" method="get" id="booking-form">
                                <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                                
                                <div class="mb-3">
                                    <label for="pickup_date" class="form-label">Ngày nhận xe</label>
                                    <input type="date" class="form-control" id="pickup_date" name="pickup_date" required value="<?php echo $pickupDate; ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="return_date" class="form-label">Ngày trả xe</label>
                                    <input type="date" class="form-control" id="return_date" name="return_date" required value="<?php echo $returnDate; ?>">
                                </div>
                                
                                <?php if (!empty($pickupDate) && !empty($returnDate) && !$isAvailable): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <i class="fas fa-exclamation-circle me-2"></i>
                                        Xe không khả dụng trong khoảng thời gian này. Vui lòng chọn ngày khác.
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg <?php echo (!$isAvailable && !empty($pickupDate) && !empty($returnDate)) ? 'disabled' : ''; ?>">
                                        <i class="fas fa-calendar-check me-2"></i>Đặt xe ngay
                                    </button>
                                    
                                    <a href="tel:0987654321" class="btn btn-outline-primary">
                                        <i class="fas fa-phone-alt me-2"></i>Gọi để đặt xe
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Extra Services -->
                    <div class="extra-services-card mt-4 animate-on-scroll" data-animation="fadeInRight" data-delay="200">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Dịch vụ thêm</h5>
                        </div>
                        <div class="card-body">
                            <ul class="extra-services-list">
                                <li>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                            <span>Giao xe tận nơi</span>
                                        </div>
                                        <span class="extra-service-price">100.000đ</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <i class="fas fa-user-tie me-2 text-primary"></i>
                                            <span>Thuê tài xế</span>
                                        </div>
                                        <span class="extra-service-price">800.000đ/ngày</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <i class="fas fa-baby-carriage me-2 text-primary"></i>
                                            <span>Ghế em bé</span>
                                        </div>
                                        <span class="extra-service-price">50.000đ/ngày</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <i class="fas fa-map me-2 text-primary"></i>
                                            <span>GPS</span>
                                        </div>
                                        <span class="extra-service-price">30.000đ/ngày</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Owner Contact -->
                    <div class="owner-card mt-4 animate-on-scroll" data-animation="fadeInRight" data-delay="400">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-headset me-2"></i>Hỗ trợ 24/7</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-2">Có câu hỏi về xe này? Liên hệ ngay:</p>
                            <div class="d-flex align-items-center mb-3">
                                <div class="owner-avatar me-3">
                                    <i class="fas fa-user-circle fa-3x text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Tư vấn viên</h6>
                                    <p class="mb-0 text-muted">Phục vụ 24/7</p>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <a href="tel:0987654321" class="btn btn-outline-primary">
                                    <i class="fas fa-phone-alt me-2"></i>0987 654 321
                                </a>
                                <a href="https://zalo.me/0987654321" class="btn btn-outline-primary">
                                    <i class="fab fa-facebook-messenger me-2"></i>Nhắn tin
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Similar Cars Section -->
<?php if (!empty($similarCars)): ?>
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="section-title mb-4">Xe tương tự</h2>
        
        <div class="row">
            <?php foreach ($similarCars as $similarCar): ?>
                <div class="col-md-4 mb-4">
                    <div class="car-card animate-on-scroll" data-animation="fadeInUp">
                        <div class="car-image">
                            <img src="<?php echo !empty($similarCar['image']) ?  $similarCar['image'] : './assets/images/car-placeholder.jpg'; ?>" alt="<?php echo $similarCar['brand'] . ' ' . $similarCar['model']; ?>">
                        </div>
                        <div class="car-body">
                            <h3 class="car-title"><?php echo $similarCar['brand'] . ' ' . $similarCar['model']; ?></h3>
                            <div class="car-price"><?php echo formatPrice($similarCar['price_per_day']); ?> / ngày</div>
                            <div class="car-features">
                                <span class="car-feature"><i class="fas fa-calendar"></i> <?php echo $similarCar['year']; ?></span>
                                <span class="car-feature"><i class="fas fa-user"></i> <?php echo $similarCar['seats']; ?> chỗ</span>
                                <span class="car-feature"><i class="fas fa-gas-pump"></i> <?php echo $similarCar['fuel']; ?></span>
                                <span class="car-feature"><i class="fas fa-cog"></i> <?php echo $similarCar['transmission'] == 'auto' ? 'Tự động' : 'Số sàn'; ?></span>
                            </div>
                            <a href="./car-detail.php?id=<?php echo $similarCar['id']; ?><?php echo !empty($pickupDate) && !empty($returnDate) ? '&pickup_date=' . $pickupDate . '&return_date=' . $returnDate : ''; ?>" class="btn btn-primary w-100 mt-3">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Call to Action Section -->
<section class="cta-section py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h3 class="cta-title">Bạn cần tư vấn thêm?</h3>
                <p class="cta-text mb-0">Liên hệ với chúng tôi để nhận được tư vấn chi tiết và ưu đãi đặc biệt!</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="./contact.php" class="btn btn-light btn-lg">
                    <i class="fas fa-phone-alt me-2"></i>Liên hệ ngay
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Custom Styles for this page -->
<style>
    .car-detail-price-card, .extra-services-card, .owner-card {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        background-color: white;
    }
    
    .card-price-header {
        background-color: #4A6FDC;
        color: white;
        padding: 20px;
        text-align: center;
    }
    
    .car-detail-price {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
    }
    
    .car-detail-price span {
        font-size: 16px;
        font-weight: 400;
    }
    
    .card-body {
        padding: 20px;
    }
    
    .card-header {
        background-color: #f8f9fa;
        padding: 15px 20px;
        border-bottom: 1px solid #dee2e6;
    }
    
    .extra-services-list, .car-features-list, .car-requirements-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .extra-services-list li, .car-features-list li, .car-requirements-list li {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    
    .extra-services-list li:last-child, .car-features-list li:last-child, .car-requirements-list li:last-child {
        border-bottom: none;
    }
    
    .extra-service-price {
        font-weight: 600;
    }
    
    .owner-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #f8f9fa;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .car-specs {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .car-spec-item {
        display: flex;
        align-items: center;
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 10px;
    }
    
    .car-spec-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: rgba(74, 111, 220, 0.1);
        color: #4A6FDC;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 18px;
    }
    
    .car-spec-details {
        flex: 1;
    }
    
    .car-spec-label {
        display: block;
        font-size: 12px;
        color: #6c757d;
    }
    
    .car-spec-value {
        font-weight: 600;
    }
    
    /* Responsive adjustments */
    @media (max-width: 991.98px) {
        .booking-sidebar {
            position: static !important;
            margin-top: 30px;
        }
        
        .car-specs {
            grid-template-columns: 1fr;
        }
    }
</style>

<!-- Custom Script for this page -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Set min date for pickup date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('pickup_date').setAttribute('min', today);
        
        // Update min date for return date based on pickup date
        document.getElementById('pickup_date').addEventListener('change', function() {
            document.getElementById('return_date').setAttribute('min', this.value);
            
            // If return date is before new pickup date, update it
            if (document.getElementById('return_date').value && 
                document.getElementById('return_date').value < this.value) {
                document.getElementById('return_date').value = this.value;
            }
        });
        
        // Form validation
        document.getElementById('booking-form').addEventListener('submit', function(event) {
            const pickupDate = document.getElementById('pickup_date').value;
            const returnDate = document.getElementById('return_date').value;
            
            if (!pickupDate || !returnDate) {
                alert('Vui lòng chọn ngày nhận và trả xe');
                event.preventDefault();
                return;
            }
            
            const pickup = new Date(pickupDate);
            const returnD = new Date(returnDate);
            
            if (returnD <= pickup) {
                alert('Ngày trả xe phải sau ngày nhận xe');
                event.preventDefault();
                return;
            }
        });
    });
</script>

<?php
// Include footer
include './includes/footer.php';
?>