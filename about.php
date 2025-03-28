<?php
// Thiết lập tiêu đề trang
$pageTitle = "Giới thiệu";

// Include các file cần thiết
require_once './config/database.php';
require_once './includes/functions.php';

// Include header
include './includes/header.php';
?>

<!-- Page Header -->
<section class="page-header bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="page-title mb-2">Giới thiệu</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 text-white-50">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Trang chủ</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Giới thiệu</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 text-end d-none d-md-block">
                <i class="fas fa-info-circle fa-5x text-white-50"></i>
            </div>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="about-image animate-on-scroll" data-animation="fadeInLeft">
                    <img src="./assets/images/about-us.jpg" alt="Về chúng tôi" class="img-fluid rounded shadow">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="about-content animate-on-scroll" data-animation="fadeInRight">
                    <div class="section-title-wrapper mb-4">
                        <h6 class="text-primary text-uppercase fw-bold mb-1">Câu chuyện của chúng tôi</h6>
                        <h2 class="section-title">Dịch vụ thuê xe hàng đầu Việt Nam</h2>
                        <div class="title-line"></div>
                    </div>
                    <p>Car Rental được thành lập vào năm 2010 với mục tiêu mang đến dịch vụ thuê xe chất lượng cao, an toàn và thuận tiện cho mọi khách hàng. Với hơn 13 năm kinh nghiệm trong ngành, chúng tôi tự hào là đối tác đáng tin cậy cho mọi nhu cầu đi lại của bạn.</p>
                    <p>Bắt đầu với chỉ 10 chiếc xe, đến nay chúng tôi đã phát triển đội xe lên hơn 200 chiếc với đa dạng các loại xe từ 4 đến 16 chỗ, đáp ứng mọi nhu cầu của khách hàng từ đi công tác, du lịch đến tổ chức sự kiện.</p>
                    <div class="about-highlight mt-4">
                        <div class="row g-4">
                            <div class="col-sm-6">
                                <div class="about-feature d-flex align-items-center">
                                    <div class="feature-icon me-3 text-primary">
                                        <i class="fas fa-car fa-2x"></i>
                                    </div>
                                    <div class="feature-text">
                                        <h5 class="mb-1">Đa dạng xe</h5>
                                        <p class="mb-0 text-muted">200+ xe các loại</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="about-feature d-flex align-items-center">
                                    <div class="feature-icon me-3 text-primary">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                    <div class="feature-text">
                                        <h5 class="mb-1">Khách hàng</h5>
                                        <p class="mb-0 text-muted">10,000+ khách hàng</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Mission Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-title-wrapper mb-4 animate-on-scroll" data-animation="fadeInUp">
                    <h6 class="text-primary text-uppercase fw-bold mb-1">Sứ mệnh & Tầm nhìn</h6>
                    <h2 class="section-title">Giá trị cốt lõi của chúng tôi</h2>
                    <div class="title-line mx-auto"></div>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="mission-card text-center p-4 bg-white rounded shadow-sm h-100 animate-on-scroll" data-animation="fadeInUp" data-delay="100">
                    <div class="mission-icon mb-3">
                        <div class="icon-circle bg-primary bg-opacity-10 mx-auto">
                            <i class="fas fa-bullseye text-primary"></i>
                        </div>
                    </div>
                    <h4 class="mission-title mb-3">Sứ mệnh</h4>
                    <p class="mission-text mb-0">Cung cấp dịch vụ thuê xe chất lượng cao với mức giá hợp lý, đảm bảo sự hài lòng tuyệt đối của khách hàng với trải nghiệm thuê xe an toàn, tiện lợi và chuyên nghiệp.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mission-card text-center p-4 bg-white rounded shadow-sm h-100 animate-on-scroll" data-animation="fadeInUp" data-delay="200">
                    <div class="mission-icon mb-3">
                        <div class="icon-circle bg-primary bg-opacity-10 mx-auto">
                            <i class="fas fa-eye text-primary"></i>
                        </div>
                    </div>
                    <h4 class="mission-title mb-3">Tầm nhìn</h4>
                    <p class="mission-text mb-0">Trở thành công ty cho thuê xe hàng đầu Việt Nam, được khách hàng tin tưởng lựa chọn nhờ vào chất lượng dịch vụ vượt trội, đội ngũ nhân viên chuyên nghiệp và đội xe hiện đại, an toàn.</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="mission-card text-center p-4 bg-white rounded shadow-sm h-100 animate-on-scroll" data-animation="fadeInUp" data-delay="300">
                    <div class="mission-icon mb-3">
                        <div class="icon-circle bg-primary bg-opacity-10 mx-auto">
                            <i class="fas fa-heart text-primary"></i>
                        </div>
                    </div>
                    <h4 class="mission-title mb-3">Giá trị cốt lõi</h4>
                    <p class="mission-text mb-0">Chúng tôi xây dựng dịch vụ dựa trên các giá trị: Chất lượng, An toàn, Minh bạch, Tận tâm và Đổi mới. Mỗi chuyến đi của khách hàng là niềm tự hào của chúng tôi.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Team Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-title-wrapper mb-4 animate-on-scroll" data-animation="fadeInUp">
                    <h6 class="text-primary text-uppercase fw-bold mb-1">Đội ngũ của chúng tôi</h6>
                    <h2 class="section-title">Gặp gỡ những người lãnh đạo</h2>
                    <div class="title-line mx-auto"></div>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-3 col-md-6">
                <div class="team-card animate-on-scroll" data-animation="fadeInUp" data-delay="100">
                    <div class="team-image position-relative mb-3">
                        <img src="./assets/images/team-1.jpg" alt="Team Member" class="img-fluid rounded">
                        <div class="team-social position-absolute w-100 d-flex justify-content-center">
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="team-info text-center">
                        <h5 class="team-name mb-1">Nguyễn Văn A</h5>
                        <p class="team-position text-primary mb-0">Giám đốc điều hành</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="team-card animate-on-scroll" data-animation="fadeInUp" data-delay="200">
                    <div class="team-image position-relative mb-3">
                        <img src="./assets/images/team-2.jpg" alt="Team Member" class="img-fluid rounded">
                        <div class="team-social position-absolute w-100 d-flex justify-content-center">
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="team-info text-center">
                        <h5 class="team-name mb-1">Trần Thị B</h5>
                        <p class="team-position text-primary mb-0">Giám đốc tài chính</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="team-card animate-on-scroll" data-animation="fadeInUp" data-delay="300">
                    <div class="team-image position-relative mb-3">
                        <img src="./assets/images/team-3.jpg" alt="Team Member" class="img-fluid rounded">
                        <div class="team-social position-absolute w-100 d-flex justify-content-center">
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="team-info text-center">
                        <h5 class="team-name mb-1">Lê Văn C</h5>
                        <p class="team-position text-primary mb-0">Giám đốc vận hành</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="team-card animate-on-scroll" data-animation="fadeInUp" data-delay="400">
                    <div class="team-image position-relative mb-3">
                        <img src="./assets/images/team-4.jpg" alt="Team Member" class="img-fluid rounded">
                        <div class="team-social position-absolute w-100 d-flex justify-content-center">
                            <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <div class="team-info text-center">
                        <h5 class="team-name mb-1">Phạm Thị D</h5>
                        <p class="team-position text-primary mb-0">Giám đốc dịch vụ khách hàng</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="why-choose-content animate-on-scroll" data-animation="fadeInLeft">
                    <div class="section-title-wrapper mb-4">
                        <h6 class="text-primary text-uppercase fw-bold mb-1">Tại sao chọn chúng tôi</h6>
                        <h2 class="section-title">Trải nghiệm thuê xe tuyệt vời với Car Rental</h2>
                        <div class="title-line"></div>
                    </div>
                    
                    <div class="why-choose-item d-flex mb-4">
                        <div class="item-icon me-3">
                            <div class="icon-circle bg-primary text-white">
                                <i class="fas fa-car-alt"></i>
                            </div>
                        </div>
                        <div class="item-text">
                            <h5>Đội xe hiện đại</h5>
                            <p class="text-muted mb-0">Với hơn 200 xe mới, đa dạng từ xe 4 chỗ đến 16 chỗ, chúng tôi đảm bảo mỗi chuyến đi của bạn đều thoải mái và an toàn.</p>
                        </div>
                    </div>
                    
                    <div class="why-choose-item d-flex mb-4">
                        <div class="item-icon me-3">
                            <div class="icon-circle bg-primary text-white">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                        <div class="item-text">
                            <h5>Giá cả hợp lý</h5>
                            <p class="text-muted mb-0">Chúng tôi cam kết cung cấp dịch vụ thuê xe với giá tốt nhất trên thị trường, kèm theo nhiều ưu đãi hấp dẫn cho khách hàng thân thiết.</p>
                        </div>
                    </div>
                    
                    <div class="why-choose-item d-flex mb-4">
                        <div class="item-icon me-3">
                            <div class="icon-circle bg-primary text-white">
                                <i class="fas fa-headset"></i>
                            </div>
                        </div>
                        <div class="item-text">
                            <h5>Hỗ trợ 24/7</h5>
                            <p class="text-muted mb-0">Đội ngũ hỗ trợ khách hàng luôn sẵn sàng phục vụ 24/7, đảm bảo mọi vấn đề của bạn đều được giải quyết nhanh chóng và hiệu quả.</p>
                        </div>
                    </div>
                    
                    <div class="why-choose-item d-flex">
                        <div class="item-icon me-3">
                            <div class="icon-circle bg-primary text-white">
                                <i class="fas fa-check-circle"></i>
                            </div>
                        </div>
                        <div class="item-text">
                            <h5>Thủ tục đơn giản</h5>
                            <p class="text-muted mb-0">Quy trình đặt xe đơn giản, nhanh chóng chỉ với vài bước. Chúng tôi tôn trọng thời gian của bạn và đảm bảo mọi thủ tục đều được xử lý hiệu quả.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mt-5 mt-lg-0">
                <div class="why-choose-image animate-on-scroll" data-animation="fadeInRight">
                    <img src="./assets/images/why-choose-us-2.jpg" alt="Tại sao chọn chúng tôi" class="img-fluid rounded shadow">
                    
                    <div class="experience-badge">
                        <div class="badge-inner">
                            <span class="years">13+</span>
                            <span class="text">Năm kinh nghiệm</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-lg-6 text-center">
                <div class="section-title-wrapper animate-on-scroll" data-animation="fadeInUp">
                    <h6 class="text-primary text-uppercase fw-bold mb-1">Đánh giá từ khách hàng</h6>
                    <h2 class="section-title">Khách hàng nói gì về chúng tôi</h2>
                    <div class="title-line mx-auto"></div>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="testimonial-card p-4 bg-white rounded shadow-sm h-100 animate-on-scroll" data-animation="fadeInUp" data-delay="100">
                    <div class="testimonial-rating text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text mb-4">"Tôi đã thuê xe tại Car Rental nhiều lần và luôn hài lòng với dịch vụ. Xe luôn sạch sẽ, mới và đúng như mô tả. Nhân viên rất thân thiện và hỗ trợ nhiệt tình. Đây là nơi đáng tin cậy nhất khi bạn cần thuê xe."</p>
                    <div class="testimonial-author d-flex align-items-center">
                        <div class="testimonial-author-avatar me-3">
                            <img src="./assets/images/testimonial-1.jpg" alt="Testimonial" class="rounded-circle" width="60" height="60">
                        </div>
                        <div class="testimonial-author-info">
                            <h5 class="mb-1">Nguyễn Văn An</h5>
                            <p class="text-muted mb-0">Giám đốc kinh doanh</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card p-4 bg-white rounded shadow-sm h-100 animate-on-scroll" data-animation="fadeInUp" data-delay="200">
                    <div class="testimonial-rating text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="testimonial-text mb-4">"Chuyến đi gia đình của chúng tôi đã thành công tốt đẹp nhờ dịch vụ thuê xe của Car Rental. Xe 7 chỗ rộng rãi, thoải mái và rất tiết kiệm nhiên liệu. Quy trình đặt xe đơn giản và giá cả rất hợp lý. Chắc chắn sẽ tiếp tục sử dụng dịch vụ này."</p>
                    <div class="testimonial-author d-flex align-items-center">
                        <div class="testimonial-author-avatar me-3">
                            <img src="./assets/images/testimonial-2.jpg" alt="Testimonial" class="rounded-circle" width="60" height="60">
                        </div>
                        <div class="testimonial-author-info">
                            <h5 class="mb-1">Trần Thị Bình</h5>
                            <p class="text-muted mb-0">Kế toán trưởng</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="testimonial-card p-4 bg-white rounded shadow-sm h-100 animate-on-scroll" data-animation="fadeInUp" data-delay="300">
                    <div class="testimonial-rating text-warning mb-3">
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                        <i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text mb-4">"Lần đầu thuê xe tự lái, tôi lo lắng nhiều điều nhưng Car Rental đã giúp tôi có trải nghiệm tuyệt vời. Họ hướng dẫn rất cụ thể, xe mới và an toàn. Đặc biệt, dịch vụ hỗ trợ 24/7 thực sự hữu ích khi tôi cần trợ giúp trên đường. Tuyệt vời!"</p>
                    <div class="testimonial-author d-flex align-items-center">
                        <div class="testimonial-author-avatar me-3">
                            <img src="./assets/images/testimonial-3.jpg" alt="Testimonial" class="rounded-circle" width="60" height="60">
                        </div>
                        <div class="testimonial-author-info">
                            <h5 class="mb-1">Lê Văn Cường</h5>
                            <p class="text-muted mb-0">Kỹ sư phần mềm</p>
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
                <h3 class="cta-title">Sẵn sàng trải nghiệm dịch vụ thuê xe tốt nhất?</h3>
                <p class="cta-text mb-0">Liên hệ với chúng tôi ngay hôm nay để được tư vấn và nhận ưu đãi đặc biệt dành riêng cho bạn!</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="contact.php" class="btn btn-light btn-lg">
                    <i class="fas fa-phone-alt me-2"></i>Liên hệ ngay
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Custom CSS for this page -->
<style>
    .title-line {
        width: 50px;
        height: 3px;
        background-color: #4A6FDC;
        margin-top: 15px;
        margin-bottom: 15px;
    }
    
    .icon-circle {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 15px;
    }
    
    .team-social {
        bottom: 20px;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .social-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background-color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4A6FDC;
        margin: 0 5px;
        transition: all 0.3s ease;
    }
    
    .social-icon:hover {
        background-color: #4A6FDC;
        color: #fff;
    }
    
    .team-image:hover .team-social {
        opacity: 1;
    }
    
    .experience-badge {
        position: absolute;
        bottom: -25px;
        right: 50px;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background-color: #4A6FDC;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .badge-inner {
        text-align: center;
    }
    
    .years {
        font-size: 36px;
        font-weight: 700;
        display: block;
        line-height: 1;
    }
    
    .text {
        font-size: 14px;
    }
    
    @media (max-width: 768px) {
        .experience-badge {
            width: 100px;
            height: 100px;
            right: 30px;
        }
        
        .years {
            font-size: 28px;
        }
        
        .text {
            font-size: 12px;
        }
    }
</style>

<?php
// Include footer
include './includes/footer.php';
?>