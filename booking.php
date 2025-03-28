<?php
// Thiết lập tiêu đề trang
$pageTitle = "Đặt xe";

// Include các file cần thiết
require_once './includes/functions.php';
require_once './config/database.php';
require_once './auth/auth_functions.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    // Lưu URL hiện tại vào session để sau khi đăng nhập có thể quay lại
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: ./auth/login.php");
    exit;
}

// Lấy thông tin từ URL
$carId = isset($_GET['car_id']) ? (int)$_GET['car_id'] : 0;
$pickupDate = isset($_GET['pickup_date']) ? sanitizeInput($_GET['pickup_date']) : '';
$returnDate = isset($_GET['return_date']) ? sanitizeInput($_GET['return_date']) : '';

// Lấy thông tin xe
$car = getCarById($carId);

// Nếu không tìm thấy xe, chuyển hướng về trang danh sách xe
if (!$car) {
    header("Location: ./cars.php");
    exit;
}

// Kiểm tra xe có khả dụng trong khoảng thời gian không
$isAvailable = true;
if (!empty($pickupDate) && !empty($returnDate)) {
    $isAvailable = isCarAvailable($carId, $pickupDate, $returnDate);
    
    // Nếu xe không khả dụng, thông báo và chuyển về trang chi tiết xe
    if (!$isAvailable) {
        $_SESSION['message'] = "Xe không khả dụng trong khoảng thời gian bạn chọn. Vui lòng chọn ngày khác.";
        $_SESSION['message_type'] = "danger";
        header("Location: ./car-detail.php?id=$carId");
        exit;
    }
}

// Lấy thông tin người dùng
$currentUser = getCurrentUser();

// Kiểm tra xem người dùng có thể đặt xe không (dựa vào số lượng đặt xe chưa hoàn thành)
$canBook = canUserBook($currentUser['id']);
if (!$canBook) {
    $_SESSION['message'] = "Bạn đã đạt giới hạn số lượng đặt xe. Vui lòng hoàn thành các đơn đặt xe hiện tại trước khi đặt thêm.";
    $_SESSION['message_type'] = "warning";
    header("Location: ./car-detail.php?id=$carId");
    exit;
}

// Tính số ngày thuê và tổng tiền
$rentalDays = 0;
$totalPrice = 0;

if (!empty($pickupDate) && !empty($returnDate)) {
    $start = new DateTime($pickupDate);
    $end = new DateTime($returnDate);
    $interval = $start->diff($end);
    $rentalDays = $interval->days > 0 ? $interval->days : 1; // Tối thiểu 1 ngày
    $totalPrice = $rentalDays * $car['price_per_day'];
}

// Xử lý form đặt xe
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $pickupDate = sanitizeInput($_POST['pickup_date']);
    $returnDate = sanitizeInput($_POST['return_date']);
    $pickupLocation = sanitizeInput($_POST['pickup_location']);
    $returnLocation = sanitizeInput($_POST['return_location']);
    $totalPrice = (float)$_POST['total_price'];
    
    // Kiểm tra dữ liệu
    if (empty($pickupDate) || empty($returnDate) || empty($pickupLocation) || empty($returnLocation)) {
        $_SESSION['message'] = "Vui lòng nhập đầy đủ thông tin đặt xe.";
        $_SESSION['message_type'] = "danger";
    } else {
        // Kiểm tra lại xe có khả dụng không
        if (isCarAvailable($carId, $pickupDate, $returnDate)) {
            // Tạo đơn đặt xe
            $bookingId = createBooking($currentUser['id'], $carId, $pickupDate, $returnDate, $pickupLocation, $returnLocation, $totalPrice);
            
            if ($bookingId) {
                $_SESSION['message'] = "Đặt xe thành công! Chúng tôi sẽ liên hệ với bạn để xác nhận.";
                $_SESSION['message_type'] = "success";
                header("Location: ./user/bookings.php");
                exit;
            } else {
                $_SESSION['message'] = "Có lỗi xảy ra trong quá trình đặt xe. Vui lòng thử lại sau.";
                $_SESSION['message_type'] = "danger";
            }
        } else {
            $_SESSION['message'] = "Xe không còn khả dụng trong khoảng thời gian bạn chọn. Vui lòng chọn ngày khác.";
            $_SESSION['message_type'] = "danger";
            header("Location: ./car-detail.php?id=$carId");
            exit;
        }
    }
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
                <li class="breadcrumb-item"><a href="./car-detail.php?id=<?php echo $car['id']; ?>"><?php echo $car['brand'] . ' ' . $car['model']; ?></a></li>
                <li class="breadcrumb-item active" aria-current="page">Đặt xe</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Booking Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Booking Form -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="booking-form animate-on-scroll" data-animation="fadeInLeft">
                    <h2 class="mb-4">Đặt Xe</h2>
                    
                    <?php echo displayMessage(); ?>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?car_id=' . $carId; ?>" method="post" id="booking-form">
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo $currentUser['full_name']; ?>" readonly>
                                    <label for="full_name">Họ và tên</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $currentUser['email']; ?>" readonly>
                                    <label for="email">Email</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="form-floating">
                                    <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $currentUser['phone']; ?>" readonly>
                                    <label for="phone">Số điện thoại</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="address" name="address" value="<?php echo $currentUser['address']; ?>" readonly>
                                    <label for="address">Địa chỉ</label>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="pickup_date" name="pickup_date" value="<?php echo $pickupDate; ?>" required>
                                    <label for="pickup_date">Ngày nhận xe *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" class="form-control" id="return_date" name="return_date" value="<?php echo $returnDate; ?>" required>
                                    <label for="return_date">Ngày trả xe *</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-md-6 mb-3 mb-md-0">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="pickup_location" name="pickup_location" required>
                                    <label for="pickup_location">Địa điểm nhận xe *</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" id="return_location" name="return_location" required>
                                    <label for="return_location">Địa điểm trả xe *</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-floating">
                                <textarea class="form-control" id="notes" name="notes" style="height: 120px"></textarea>
                                <label for="notes">Ghi chú</label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="driver_service" name="extra_services[]" value="driver">
                                <label class="form-check-label" for="driver_service">
                                    Thuê tài xế (800.000đ/ngày)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="delivery_service" name="extra_services[]" value="delivery">
                                <label class="form-check-label" for="delivery_service">
                                    Giao xe tận nơi (100.000đ)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="baby_seat" name="extra_services[]" value="baby_seat">
                                <label class="form-check-label" for="baby_seat">
                                    Ghế em bé (50.000đ/ngày)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="gps" name="extra_services[]" value="gps">
                                <label class="form-check-label" for="gps">
                                    GPS (30.000đ/ngày)
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Phương thức thanh toán</h5>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_cash" value="cash" checked>
                                <label class="form-check-label" for="payment_cash">
                                    Thanh toán tiền mặt khi nhận xe
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="payment_transfer" value="transfer">
                                <label class="form-check-label" for="payment_transfer">
                                    Chuyển khoản ngân hàng
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                <label class="form-check-label" for="terms">
                                    Tôi đã đọc và đồng ý với <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">điều khoản dịch vụ</a> *
                                </label>
                            </div>
                        </div>
                        
                        <!-- Hidden input for total price -->
                        <input type="hidden" id="total_price" name="total_price" value="<?php echo $totalPrice; ?>">
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check-circle me-2"></i>Xác nhận đặt xe
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Booking Summary -->
            <div class="col-lg-4">
                <div class="booking-summary animate-on-scroll" data-animation="fadeInRight">
                    <div class="summary-header">
                        <h4>Thông tin đặt xe</h4>
                    </div>
                    <div class="summary-body">
                        <div class="car-info d-flex align-items-center mb-4">
                            <div class="car-thumbnail me-3">
                                <img src="<?php echo !empty($car['image']) ? $car['image'] : './assets/images/car-placeholder.jpg'; ?>" alt="<?php echo $car['brand'] . ' ' . $car['model']; ?>" class="img-fluid rounded" width="80">
                            </div>
                            <div>
                                <h5 class="car-model mb-1"><?php echo $car['brand'] . ' ' . $car['model']; ?></h5>
                                <div class="car-features small text-muted">
                                    <span><i class="fas fa-user me-1"></i><?php echo $car['seats']; ?> chỗ</span>
                                    <span class="mx-2">|</span>
                                    <span><i class="fas fa-cog me-1"></i><?php echo $car['transmission'] == 'auto' ? 'Tự động' : 'Số sàn'; ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <ul class="summary-details">
                            <li class="d-flex justify-content-between">
                                <span class="detail-label">Giá thuê:</span>
                                <span class="detail-value"><?php echo formatPrice($car['price_per_day']); ?> / ngày</span>
                            </li>
                            <li class="d-flex justify-content-between" id="rental-days-row">
                                <span class="detail-label">Thời gian thuê:</span>
                                <span class="detail-value" id="rental-days"><?php echo $rentalDays; ?> ngày</span>
                            </li>
                            <li class="d-flex justify-content-between" id="driver-service-row" style="display: none !important;">
                                <span class="detail-label">Phí tài xế:</span>
                                <span class="detail-value" id="driver-fee">0 VND</span>
                            </li>
                            <li class="d-flex justify-content-between" id="delivery-service-row" style="display: none !important;">
                                <span class="detail-label">Phí giao xe:</span>
                                <span class="detail-value">100.000 VND</span>
                            </li>
                            <li class="d-flex justify-content-between" id="baby-seat-row" style="display: none !important;">
                                <span class="detail-label">Ghế em bé:</span>
                                <span class="detail-value" id="baby-seat-fee">0 VND</span>
                            </li>
                            <li class="d-flex justify-content-between" id="gps-row" style="display: none !important;">
                                <span class="detail-label">GPS:</span>
                                <span class="detail-value" id="gps-fee">0 VND</span>
                            </li>
                        </ul>
                        
                        <div class="summary-total">
                            <div class="d-flex justify-content-between">
                                <span class="total-label">Tổng cộng:</span>
                                <span class="total-value" id="total-price-display"><?php echo formatPrice($totalPrice); ?></span>
                            </div>
                        </div>
                        
                        <div class="summary-note mt-4">
                            <p class="small mb-0">
                                <i class="fas fa-info-circle me-1 text-primary"></i>
                                Đặt cọc tối thiểu 30% tổng giá trị khi nhận xe. Thanh toán đầy đủ khi trả xe.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Promotions -->
                <div class="promotions-card mt-4 animate-on-scroll" data-animation="fadeInRight" data-delay="200">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-gift me-2"></i>Mã giảm giá</h5>
                    </div>
                    <div class="card-body">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Nhập mã giảm giá">
                            <button class="btn btn-outline-primary" type="button">Áp dụng</button>
                        </div>
                        <div class="mt-3">
                            <div class="promo-code-item d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <span class="badge bg-primary me-2">NEWUSER</span>
                                    <span class="small">Giảm 10% cho khách hàng mới</span>
                                </div>
                                <button class="btn btn-sm btn-link p-0">Sử dụng</button>
                            </div>
                            <div class="promo-code-item d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-primary me-2">WEEKEND</span>
                                    <span class="small">Giảm 5% cho thuê cuối tuần</span>
                                </div>
                                <button class="btn btn-sm btn-link p-0">Sử dụng</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Need Help -->
                <div class="help-card mt-4 animate-on-scroll" data-animation="fadeInRight" data-delay="400">
                    <div class="card-body">
                        <h5><i class="fas fa-question-circle me-2 text-primary"></i>Cần hỗ trợ?</h5>
                        <p class="small mb-3">Nếu bạn có thắc mắc về việc đặt xe, vui lòng liên hệ với chúng tôi.</p>
                        <div class="d-grid gap-2">
                            <a href="tel:0987654321" class="btn btn-outline-primary">
                                <i class="fas fa-phone-alt me-2"></i>0987 654 321
                            </a>
                            <a href="mailto:support@carrental.com" class="btn btn-outline-primary">
                                <i class="fas fa-envelope me-2"></i>support@carrental.com
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Terms and Conditions Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Điều khoản dịch vụ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h5>1. Điều kiện thuê xe</h5>
                <p>Khách hàng phải từ 21 tuổi trở lên và có giấy phép lái xe còn hiệu lực ít nhất 12 tháng. Khách hàng phải xuất trình CMND/CCCD, hộ khẩu hoặc KT3 và đặt cọc theo quy định.</p>
                
                <h5>2. Thanh toán và đặt cọc</h5>
                <p>Khách hàng phải đặt cọc tối thiểu 30% giá trị hợp đồng khi nhận xe. Số tiền còn lại sẽ được thanh toán khi trả xe. Tiền cọc sẽ được hoàn trả sau khi kiểm tra xe không có hư hỏng.</p>
                
                <h5>3. Hủy đặt xe</h5>
                <p>- Hủy trước 48 giờ: Hoàn tiền 100%<br>
                - Hủy từ 24-48 giờ: Hoàn tiền 50%<br>
                - Hủy trong vòng 24 giờ: Không hoàn tiền</p>
                
                <h5>4. Trách nhiệm của khách hàng</h5>
                <p>Khách hàng phải bảo quản xe, tuân thủ luật giao thông và chịu trách nhiệm về các vi phạm giao thông trong thời gian thuê xe. Khách hàng phải bồi thường cho mọi hư hỏng do lỗi của mình gây ra.</p>
                
                <h5>5. Giới hạn sử dụng</h5>
                <p>Xe chỉ được sử dụng trong phạm vi lãnh thổ Việt Nam và không được dùng cho mục đích bất hợp pháp. Không hút thuốc, uống rượu bia khi lái xe.</p>
                
                <h5>6. Bảo hiểm</h5>
                <p>Xe được bảo hiểm theo quy định của pháp luật. Tuy nhiên, khách hàng vẫn phải chịu trách nhiệm cho khoản miễn thường và các thiệt hại không được bảo hiểm chi trả.</p>
                
                <h5>7. Gia hạn thuê xe</h5>
                <p>Khách hàng phải thông báo trước ít nhất 24 giờ nếu muốn gia hạn thời gian thuê xe. Việc gia hạn phụ thuộc vào tình trạng đặt xe và phải được xác nhận từ công ty.</p>
                
                <h5>8. Trả xe sớm</h5>
                <p>Trong trường hợp trả xe sớm hơn thời gian đã đặt, chúng tôi không hoàn tiền cho thời gian không sử dụng.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Đồng ý</button>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles for this page -->
<style>
    .booking-form, .booking-summary, .promotions-card, .help-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        padding: 25px;
    }
    
    .summary-header {
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
    }
    
    .summary-details {
        list-style: none;
        padding: 0;
        margin: 0 0 20px;
    }
    
    .summary-details li {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    
    .detail-label, .total-label {
        color: #6c757d;
    }
    
    .detail-value, .total-value {
        font-weight: 600;
    }
    
    .summary-total {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-top: 15px;
    }
    
    .total-value {
        color: #4A6FDC;
        font-size: 20px;
    }
    
    .card-header {
        background-color: #f8f9fa;
        padding: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .form-floating > label {
        padding-left: 1rem;
    }
    
    .form-floating > .form-control {
        padding: 1rem 0.75rem;
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
            updateRentalDays();
        });
        
        document.getElementById('return_date').addEventListener('change', function() {
            updateRentalDays();
        });
        
        // Update extra services display
        const driverService = document.getElementById('driver_service');
        const deliveryService = document.getElementById('delivery_service');
        const babySeat = document.getElementById('baby_seat');
        const gps = document.getElementById('gps');
        
        driverService.addEventListener('change', updateTotalPrice);
        deliveryService.addEventListener('change', updateTotalPrice);
        babySeat.addEventListener('change', updateTotalPrice);
        gps.addEventListener('change', updateTotalPrice);
        
        // Initial update
        updateRentalDays();
        
        // Form validation
        document.getElementById('booking-form').addEventListener('submit', function(event) {
            const pickupDate = document.getElementById('pickup_date').value;
            const returnDate = document.getElementById('return_date').value;
            const pickupLocation = document.getElementById('pickup_location').value;
            const returnLocation = document.getElementById('return_location').value;
            const terms = document.getElementById('terms').checked;
            
            let isValid = true;
            
            if (!pickupDate) {
                alert('Vui lòng chọn ngày nhận xe');
                isValid = false;
            }
            
            if (!returnDate) {
                alert('Vui lòng chọn ngày trả xe');
                isValid = false;
            }
            
            if (pickupDate && returnDate) {
                const pickup = new Date(pickupDate);
                const returnD = new Date(returnDate);
                
                if (returnD <= pickup) {
                    alert('Ngày trả xe phải sau ngày nhận xe');
                    isValid = false;
                }
            }
            
            if (!pickupLocation.trim()) {
                alert('Vui lòng nhập địa điểm nhận xe');
                isValid = false;
            }
            
            if (!returnLocation.trim()) {
                alert('Vui lòng nhập địa điểm trả xe');
                isValid = false;
            }
            
            if (!terms) {
                alert('Vui lòng đồng ý với điều khoản dịch vụ');
                isValid = false;
            }
            
            if (!isValid) {
                event.preventDefault();
            }
        });
    });
    
    function updateRentalDays() {
        const pickupDate = document.getElementById('pickup_date').value;
        const returnDate = document.getElementById('return_date').value;
        
        if (pickupDate && returnDate) {
            const pickup = new Date(pickupDate);
            const returnD = new Date(returnDate);
            
            // Calculate difference in days
            const diffTime = Math.abs(returnD - pickup);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            
            if (diffDays >= 0) {
                // Update rental days display
                document.getElementById('rental-days').textContent = diffDays + ' ngày';
                
                // Update total price
                updateTotalPrice(diffDays);
            }
        }
    }
    
    function updateTotalPrice(days) {
        // If days parameter is not provided, get it from the display
        if (typeof days !== 'number') {
            const rentalDaysText = document.getElementById('rental-days').textContent;
            days = parseInt(rentalDaysText.match(/\d+/)[0]);
        }
        
        // Get base price per day
        const pricePerDay = <?php echo $car['price_per_day']; ?>;
        
        // Calculate base rental price
        let totalPrice = days * pricePerDay;
        
        // Check for extra services
        const driverService = document.getElementById('driver_service').checked;
        const deliveryService = document.getElementById('delivery_service').checked;
        const babySeat = document.getElementById('baby_seat').checked;
        const gps = document.getElementById('gps').checked;
        
        // Show/hide service rows
        document.getElementById('driver-service-row').style.display = driverService ? 'flex' : 'none';
        document.getElementById('delivery-service-row').style.display = deliveryService ? 'flex' : 'none';
        document.getElementById('baby-seat-row').style.display = babySeat ? 'flex' : 'none';
        document.getElementById('gps-row').style.display = gps ? 'flex' : 'none';
        
        // Add driver service cost
        if (driverService) {
            const driverFee = 800000 * days;
            document.getElementById('driver-fee').textContent = formatPrice(driverFee);
            totalPrice += driverFee;
        }
        
        // Add delivery service cost
        if (deliveryService) {
            totalPrice += 100000;
        }
        
        // Add baby seat cost
        if (babySeat) {
            const babySeatFee = 50000 * days;
            document.getElementById('baby-seat-fee').textContent = formatPrice(babySeatFee);
            totalPrice += babySeatFee;
        }
        
        // Add GPS cost
        if (gps) {
            const gpsFee = 30000 * days;
            document.getElementById('gps-fee').textContent = formatPrice(gpsFee);
            totalPrice += gpsFee;
        }
        
        // Update total price display
        document.getElementById('total-price-display').textContent = formatPrice(totalPrice);
        
        // Update hidden input
        document.getElementById('total_price').value = totalPrice;
    }
    
    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(price);
    }
</script>

<?php
// Include footer
include './includes/footer.php';
?>