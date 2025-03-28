<?php
// Thiết lập tiêu đề trang
$pageTitle = "Trang chủ";

// Include các file cần thiết
require_once './includes/functions.php';
require_once './config/database.php';

// Lấy danh sách xe nổi bật (6 xe mới nhất)
$featuredCars = array_slice(getAvailableCars(), 0, 6);

// Include header
include './includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content animate-on-scroll" data-animation="fadeInUp">
            <h1 class="hero-title">Thuê Xe Tự Lái & Có Tài Xế</h1>
            <p class="hero-subtitle">Đa dạng xe, giá cả hợp lý, thủ tục đơn giản, phục vụ 24/7</p>
            <a href="./cars.php" class="btn btn-primary btn-lg btn-animate">
                <i class="fas fa-car-side me-2"></i>Xem danh sách xe
            </a>
        </div>
    </div>
</section>

<!-- Search Form Section -->
<section class="search-section">
    <div class="container">
        <div class="search-form animate-on-scroll" data-animation="fadeInUp">
            <form id="search-form" action="./cars.php" method="get">
                <div class="row">
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="search_pickup_date">Ngày nhận xe</label>
                            <input type="date" class="form-control" id="search_pickup_date" name="pickup_date" required>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="search_return_date">Ngày trả xe</label>
                            <input type="date" class="form-control" id="search_return_date" name="return_date" required>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="form-group">
                            <label for="search_car_type">Loại xe</label>
                            <select class="form-control" id="search_car_type" name="car_type">
                                <option value="">Tất cả loại xe</option>
                                <option value="4">4 chỗ</option>
                                <option value="5">5 chỗ</option>
                                <option value="7">7 chỗ</option>
                                <option value="16">16 chỗ</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3 mb-md-0">
                        <div class="form-group d-flex h-100 align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-2"></i>Tìm xe
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- Featured Cars Section -->
<section class="py-5">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Xe Nổi Bật</h2>
            <p class="section-subtitle">Khám phá các xe được yêu thích nhất tại Car Rental</p>
        </div>
        
        <div class="row">
            <?php if (empty($featuredCars)): ?>
                <div class="col-12 text-center">
                    <p>Hiện tại không có xe nào khả dụng. Vui lòng quay lại sau.</p>
                </div>
            <?php else: ?>
                <?php foreach ($featuredCars as $index => $car): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="car-card animate-on-scroll" data-animation="fadeInUp" data-delay="<?php echo $index * 100; ?>">
                            <div class="car-image">
                                <img src="<?php echo !empty($car['image']) ? '/showImg.php?filename='.$car['image'] : './assets/images/car-placeholder.jpg'; ?>" alt="<?php echo $car['brand'] . ' ' . $car['model']; ?>">'$car['image'] : './assets/images/car-placeholder.jpg'; ?>" alt="<?php echo $car['brand'] . ' ' . $car['model']; ?>">
                            </div>
                            <div class="car-body">
                                <h3 class="car-title"><?php echo $car['brand'] . ' ' . $car['model']; ?></h3>
                                <div class="car-price"><?php echo formatPrice($car['price_per_day']); ?> / ngày</div>
                                <div class="car-features">
                                    <span class="car-feature"><i class="fas fa-calendar"></i> <?php echo $car['year']; ?></span>
                                    <span class="car-feature"><i class="fas fa-user"></i> <?php echo $car['seats']; ?> chỗ</span>
                                    <span class="car-feature"><i class="fas fa-gas-pump"></i> <?php echo $car['fuel']; ?></span>
                                    <span class="car-feature"><i class="fas fa-cog"></i> <?php echo $car['transmission'] == 'auto' ? 'Tự động' : 'Số sàn'; ?></span>
                                </div>
                                <a href="./car-detail.php?id=<?php echo $car['id']; ?>" class="btn btn-primary w-100 mt-3">Xem chi tiết</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="text-center mt-4">
            <a href="./cars.php" class="btn btn-outline-primary btn-lg">
                Xem tất cả xe <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Dịch Vụ Của Chúng Tôi</h2>
            <p class="section-subtitle">Chúng tôi cung cấp đa dạng dịch vụ thuê xe chất lượng cao</p>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-card text-center p-4 bg-white rounded shadow-sm animate-on-scroll" data-animation="fadeInUp">
                    <div class="service-icon mb-3">
                        <i class="fas fa-car fa-3x text-primary"></i>
                    </div>
                    <h4>Thuê xe tự lái</h4>
                    <p class="text-muted">Tự do di chuyển với dịch vụ thuê xe tự lái đa dạng từ xe 4 chỗ đến 16 chỗ.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-card text-center p-4 bg-white rounded shadow-sm animate-on-scroll" data-animation="fadeInUp" data-delay="100">
                    <div class="service-icon mb-3">
                        <i class="fas fa-user-tie fa-3x text-primary"></i>
                    </div>
                    <h4>Thuê xe có tài xế</h4>
                    <p class="text-muted">Thoải mái và tiện lợi với dịch vụ thuê xe kèm tài xế chuyên nghiệp, am hiểu đường xá.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-card text-center p-4 bg-white rounded shadow-sm animate-on-scroll" data-animation="fadeInUp" data-delay="200">
                    <div class="service-icon mb-3">
                        <i class="fas fa-calendar-alt fa-3x text-primary"></i>
                    </div>
                    <h4>Thuê xe dài hạn</h4>
                    <p class="text-muted">Tiết kiệm chi phí với dịch vụ thuê xe dài hạn theo tháng hoặc năm dành cho doanh nghiệp và cá nhân.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-card text-center p-4 bg-white rounded shadow-sm animate-on-scroll" data-animation="fadeInUp" data-delay="300">
                    <div class="service-icon mb-3">
                        <i class="fas fa-plane fa-3x text-primary"></i>
                    </div>
                    <h4>Đưa đón sân bay</h4>
                    <p class="text-muted">Dịch vụ đưa đón sân bay chuyên nghiệp, đúng giờ với đội ngũ tài xế thân thiện.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-card text-center p-4 bg-white rounded shadow-sm animate-on-scroll" data-animation="fadeInUp" data-delay="400">
                    <div class="service-icon mb-3">
                        <i class="fas fa-route fa-3x text-primary"></i>
                    </div>
                    <h4>Thuê xe du lịch</h4>
                    <p class="text-muted">Gói dịch vụ thuê xe du lịch trọn gói cho cá nhân, gia đình và đoàn thể với nhiều ưu đãi.</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="service-card text-center p-4 bg-white rounded shadow-sm animate-on-scroll" data-animation="fadeInUp" data-delay="500">
                    <div class="service-icon mb-3">
                        <i class="fas fa-heart fa-3x text-primary"></i>
                    </div>
                    <h4>Thuê xe cưới</h4>
                    <p class="text-muted">Dịch vụ thuê xe cưới cao cấp với nhiều mẫu xe sang trọng, trang trí đẹp mắt.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="why-choose-image animate-on-scroll" data-animation="fadeInLeft">
                    <img src="./assets/images/why-choose-us.jpg" alt="Why Choose Us" class="img-fluid rounded">
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="why-choose-content animate-on-scroll" data-animation="fadeInRight">
                    <h2 class="section-title">Tại Sao Chọn Chúng Tôi?</h2>
                    <p class="mb-4">Car Rental cam kết mang đến trải nghiệm thuê xe tốt nhất với nhiều ưu điểm vượt trội:</p>
                    
                    <div class="why-choose-item d-flex mb-4">
                        <div class="why-choose-icon me-3">
                            <i class="fas fa-check-circle fa-2x text-primary"></i>
                        </div>
                        <div class="why-choose-text">
                            <h5>Đa dạng mẫu xe</h5>
                            <p class="text-muted">Sở hữu đội xe đa dạng từ xe phổ thông đến xe sang, đáp ứng mọi nhu cầu của khách hàng.</p>
                        </div>
                    </div>
                    
                    <div class="why-choose-item d-flex mb-4">
                        <div class="why-choose-icon me-3">
                            <i class="fas fa-tag fa-2x text-primary"></i>
                        </div>
                        <div class="why-choose-text">
                            <h5>Giá cả cạnh tranh</h5>
                            <p class="text-muted">Chúng tôi cam kết mang đến mức giá tốt nhất trên thị trường cùng nhiều chương trình ưu đãi.</p>
                        </div>
                    </div>
                    
                    <div class="why-choose-item d-flex mb-4">
                        <div class="why-choose-icon me-3">
                            <i class="fas fa-headset fa-2x text-primary"></i>
                        </div>
                        <div class="why-choose-text">
                            <h5>Dịch vụ 24/7</h5>
                            <p class="text-muted">Đội ngũ hỗ trợ luôn sẵn sàng phục vụ 24/7, giải quyết mọi vấn đề của khách hàng.</p>
                        </div>
                    </div>
                    
                    <div class="why-choose-item d-flex">
                        <div class="why-choose-icon me-3">
                            <i class="fas fa-shield-alt fa-2x text-primary"></i>
                        </div>
                        <div class="why-choose-text">
                            <h5>An toàn & Tin cậy</h5>
                            <p class="text-muted">Tất cả xe đều được bảo dưỡng định kỳ, kiểm tra an toàn trước khi bàn giao cho khách hàng.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="section-header text-center mb-5">
            <h2 class="section-title">Khách Hàng Nói Gì Về Chúng Tôi?</h2>
            <p class="section-subtitle">Những đánh giá thực tế từ khách hàng đã sử dụng dịch vụ</p>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="testimonial-card p-4 bg-white rounded shadow-sm animate-on-scroll" data-animation="fadeInUp">
                    <div class="testimonial-rating text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text mb-4">"Dịch vụ thuê xe rất chuyên nghiệp, xe sạch sẽ và đúng như hình ảnh. Thủ tục đơn giản, nhân viên thân thiện. Tôi sẽ tiếp tục sử dụng dịch vụ này trong tương lai."</p>
                    <div class="testimonial-author d-flex align-items-center">
                        <div class="testimonial-author-avatar me-3">
                            <img src="./assets/images/avatar-1.jpg" alt="Customer" class="rounded-circle" width="50" height="50">
                        </div>
                        <div class="testimonial-author-info">
                            <h6 class="mb-0">Nguyễn Văn A</h6>
                            <small class="text-muted">Đã thuê Toyota Camry</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="testimonial-card p-4 bg-white rounded shadow-sm animate-on-scroll" data-animation="fadeInUp" data-delay="100">
                    <div class="testimonial-rating text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="testimonial-text mb-4">"Tôi đã thuê xe kèm tài xế cho chuyến du lịch gia đình và rất hài lòng. Tài xế rất am hiểu đường xá, nhiệt tình, xe rất thoải mái. Giá cả phải chăng so với dịch vụ nhận được."</p>
                    <div class="testimonial-author d-flex align-items-center">
                        <div class="testimonial-author-avatar me-3">
                            <img src="./assets/images/avatar-2.jpg" alt="Customer" class="rounded-circle" width="50" height="50">
                        </div>
                        <div class="testimonial-author-info">
                            <h6 class="mb-0">Trần Thị B</h6>
                            <small class="text-muted">Đã thuê xe 16 chỗ</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="testimonial-card p-4 bg-white rounded shadow-sm animate-on-scroll" data-animation="fadeInUp" data-delay="200">
                    <div class="testimonial-rating text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text mb-4">"Lần đầu thuê xe tự lái và tôi đã có trải nghiệm tuyệt vời. Xe mới, sạch sẽ, vận hành tốt. Dịch vụ hỗ trợ 24/7 rất hữu ích khi tôi gặp vấn đề nhỏ trên đường. Chắc chắn sẽ quay lại!"</p>
                    <div class="testimonial-author d-flex align-items-center">
                        <div class="testimonial-author-avatar me-3">
                            <img src="./assets/images/avatar-3.jpg" alt="Customer" class="rounded-circle" width="50" height="50">
                        </div>
                        <div class="testimonial-author-info">
                            <h6 class="mb-0">Lê Văn C</h6>
                            <small class="text-muted">Đã thuê Honda Civic</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h3 class="cta-title">Bạn đang tìm kiếm dịch vụ thuê xe?</h3>
                <p class="cta-text mb-0">Hãy liên hệ với chúng tôi ngay hôm nay để nhận tư vấn miễn phí và ưu đãi đặc biệt!</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="./contact.php" class="btn btn-light btn-lg btn-animate">
                    <i class="fas fa-phone-alt me-2"></i>Liên hệ ngay
                </a>
            </div>
        </div>
    </div>
</section>

<?php
// Include footer
include './includes/footer.php';
?>