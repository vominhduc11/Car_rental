<?php
// Thiết lập tiêu đề trang
$pageTitle = "Giới thiệu";

// Include các file cần thiết
require_once './includes/functions.php';
require_once './config/database.php';

// Include header
include './includes/header.php';
?>

<!-- Page Header -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-3 animate-on-scroll" data-animation="fadeInUp">Về Chúng Tôi</h1>
                <p class="lead mb-0 animate-on-scroll" data-animation="fadeInUp" data-delay="200">
                    Đối tác đáng tin cậy cho mọi hành trình của bạn
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<section class="bg-light py-2">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="./index.php">Trang chủ</a></li>
                <li class="breadcrumb-item active" aria-current="page">Giới thiệu</li>
            </ol>
        </nav>
    </div>
</section>

<!-- Our Story Section -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="position-relative animate-on-scroll" data-animation="fadeInLeft">
                    <img src="./assets/images/about-story.jpg" alt="Our Story" class="img-fluid rounded-3 shadow">
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-primary opacity-10 rounded-3"></div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="ps-lg-4 animate-on-scroll" data-animation="fadeInRight">
                    <div class="d-flex align-items-center mb-3">
                        <span class="line-title bg-primary"></span>
                        <h6 class="text-uppercase fw-semibold text-primary mb-0 ms-2">Câu chuyện của chúng tôi</h6>
                    </div>
                    <h2 class="fw-bold mb-4">Hơn 10 năm kinh nghiệm trong lĩnh vực cho thuê xe</h2>
                    <p class="lead mb-4">
                        CAR RENTAL được thành lập vào năm 2012 với sứ mệnh mang đến giải pháp di chuyển tiện lợi và linh hoạt cho mọi nhu cầu.
                    </p>
                    <p class="mb-4">
                        Từ những bước đầu khiêm tốn với đội xe 5 chiếc, chúng tôi đã không ngừng mở rộng và phát triển để trở thành một trong những công ty cho thuê xe hàng đầu tại Việt Nam với hơn 200 xe đa dạng từ xe phổ thông đến xe sang, đáp ứng mọi nhu cầu của khách hàng.
                    </p>
                    <p>
                        Với phương châm "Chất lượng là danh dự", chúng tôi cam kết mang đến dịch vụ thuê xe chất lượng cao, an toàn và chuyên nghiệp, để mỗi hành trình của khách hàng đều trọn vẹn và đáng nhớ.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission & Vision Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5 animate-on-scroll" data-animation="fadeInUp">
            <div class="d-flex align-items-center justify-content-center mb-3">
                <span class="line-title bg-primary"></span>
                <h6 class="text-uppercase fw-semibold text-primary mb-0 mx-2">Sứ mệnh & Tầm nhìn</h6>
                <span class="line-title bg-primary"></span>
            </div>
            <h2 class="fw-bold">Giá trị cốt lõi chúng tôi theo đuổi</h2>
        </div>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow h-100 animate-on-scroll" data-animation="fadeInLeft">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="mission-icon me-3">
                                <i class="fas fa-bullseye fa-2x text-primary"></i>
                            </div>
                            <h3 class="mb-0">Sứ mệnh</h3>
                        </div>
                        <p class="mb-0">
                            Chúng tôi cam kết mang đến giải pháp di chuyển tiện lợi, an toàn và linh hoạt cho mọi khách hàng. Bằng dịch vụ chuyên nghiệp và đội xe chất lượng, chúng tôi góp phần tạo nên những hành trình trọn vẹn, nâng cao trải nghiệm di chuyển và kết nối con người.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6 mb-4">
                <div class="card border-0 shadow h-100 animate-on-scroll" data-animation="fadeInRight">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="vision-icon me-3">
                                <i class="fas fa-eye fa-2x text-primary"></i>
                            </div>
                            <h3 class="mb-0">Tầm nhìn</h3>
                        </div>
                        <p class="mb-0">
                            Trở thành đơn vị dẫn đầu trong lĩnh vực cho thuê xe tại Việt Nam, được khách hàng tin tưởng lựa chọn nhờ chất lượng dịch vụ vượt trội và đội xe đa dạng. Chúng tôi hướng đến việc ứng dụng công nghệ hiện đại để tạo nên trải nghiệm thuê xe đơn giản, tiện lợi và an toàn.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-2">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 text-center animate-on-scroll" data-animation="fadeInUp" data-delay="0">
                    <div class="card-body p-4">
                        <div class="value-icon mb-3 mx-auto">
                            <i class="fas fa-handshake text-primary"></i>
                        </div>
                        <h4>Tận Tâm</h4>
                        <p class="mb-0 text-muted">
                            Đặt lợi ích khách hàng lên hàng đầu, luôn sẵn sàng hỗ trợ 24/7.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 text-center animate-on-scroll" data-animation="fadeInUp" data-delay="100">
                    <div class="card-body p-4">
                        <div class="value-icon mb-3 mx-auto">
                            <i class="fas fa-shield-alt text-primary"></i>
                        </div>
                        <h4>An Toàn</h4>
                        <p class="mb-0 text-muted">
                            Đảm bảo xe luôn được bảo dưỡng và kiểm tra kỹ lưỡng trước mỗi chuyến đi.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 text-center animate-on-scroll" data-animation="fadeInUp" data-delay="200">
                    <div class="card-body p-4">
                        <div class="value-icon mb-3 mx-auto">
                            <i class="fas fa-star text-primary"></i>
                        </div>
                        <h4>Chất Lượng</h4>
                        <p class="mb-0 text-muted">
                            Cam kết cung cấp dịch vụ và xe đạt tiêu chuẩn chất lượng cao nhất.
                        </p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="card border-0 shadow-sm h-100 text-center animate-on-scroll" data-animation="fadeInUp" data-delay="300">
                    <div class="card-body p-4">
                        <div class="value-icon mb-3 mx-auto">
                            <i class="fas fa-lightbulb text-primary"></i>
                        </div>
                        <h4>Đổi Mới</h4>
                        <p class="mb-0 text-muted">
                            Không ngừng cải tiến và áp dụng công nghệ mới vào quy trình dịch vụ.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row text-center">
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="animate-on-scroll" data-animation="fadeInUp">
                    <h2 class="display-4 fw-bold mb-0 counter">200+</h2>
                    <p class="mb-0">Xe các loại</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 mb-lg-0">
                <div class="animate-on-scroll" data-animation="fadeInUp" data-delay="100">
                    <h2 class="display-4 fw-bold mb-0 counter">15,000+</h2>
                    <p class="mb-0">Khách hàng tin dùng</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                <div class="animate-on-scroll" data-animation="fadeInUp" data-delay="200">
                    <h2 class="display-4 fw-bold mb-0 counter">10+</h2>
                    <p class="mb-0">Năm kinh nghiệm</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6">
                <div class="animate-on-scroll" data-animation="fadeInUp" data-delay="300">
                    <h2 class="display-4 fw-bold mb-0 counter">4.8/5</h2>
                    <p class="mb-0">Đánh giá từ khách hàng</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Team Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5 animate-on-scroll" data-animation="fadeInUp">
            <div class="d-flex align-items-center justify-content-center mb-3">
                <span class="line-title bg-primary"></span>
                <h6 class="text-uppercase fw-semibold text-primary mb-0 mx-2">Đội ngũ của chúng tôi</h6>
                <span class="line-title bg-primary"></span>
            </div>
            <h2 class="fw-bold">Những người làm nên thành công</h2>
            <p class="lead mx-auto" style="max-width: 700px;">
                Chúng tôi tự hào với đội ngũ nhân viên chuyên nghiệp, tận tâm và giàu kinh nghiệm, luôn sẵn sàng hỗ trợ khách hàng 24/7.
            </p>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="team-member text-center animate-on-scroll" data-animation="fadeInUp">
                    <div class="team-img position-relative mb-3">
                        <img src="./assets/images/team-1.jpg" alt="Team Member" class="img-fluid rounded-3">
                        <div class="team-social">
                            <a href="#" class="team-social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="team-social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="team-social-icon"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <h5>Nguyễn Văn A</h5>
                    <p class="text-muted mb-0">Giám đốc điều hành</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="team-member text-center animate-on-scroll" data-animation="fadeInUp" data-delay="100">
                    <div class="team-img position-relative mb-3">
                        <img src="./assets/images/team-2.jpg" alt="Team Member" class="img-fluid rounded-3">
                        <div class="team-social">
                            <a href="#" class="team-social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="team-social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="team-social-icon"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <h5>Trần Thị B</h5>
                    <p class="text-muted mb-0">Giám đốc kinh doanh</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="team-member text-center animate-on-scroll" data-animation="fadeInUp" data-delay="200">
                    <div class="team-img position-relative mb-3">
                        <img src="./assets/images/team-3.jpg" alt="Team Member" class="img-fluid rounded-3">
                        <div class="team-social">
                            <a href="#" class="team-social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="team-social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="team-social-icon"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <h5>Lê Văn C</h5>
                    <p class="text-muted mb-0">Quản lý đội xe</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="team-member text-center animate-on-scroll" data-animation="fadeInUp" data-delay="300">
                    <div class="team-img position-relative mb-3">
                        <img src="./assets/images/team-4.jpg" alt="Team Member" class="img-fluid rounded-3">
                        <div class="team-social">
                            <a href="#" class="team-social-icon"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="team-social-icon"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="team-social-icon"><i class="fab fa-linkedin-in"></i></a>
                        </div>
                    </div>
                    <h5>Phạm Thị D</h5>
                    <p class="text-muted mb-0">Trưởng phòng CSKH</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5 animate-on-scroll" data-animation="fadeInUp">
            <div class="d-flex align-items-center justify-content-center mb-3">
                <span class="line-title bg-primary"></span>
                <h6 class="text-uppercase fw-semibold text-primary mb-0 mx-2">Khách hàng nói gì</h6>
                <span class="line-title bg-primary"></span>
            </div>
            <h2 class="fw-bold">Ý kiến từ khách hàng của chúng tôi</h2>
        </div>
        
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow h-100 animate-on-scroll" data-animation="fadeInUp">
                    <div class="card-body p-4">
                        <div class="d-flex mb-4">
                            <div class="testimonial-rating text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="testimonial-text mb-4">
                            "Dịch vụ vô cùng chuyên nghiệp, xe mới và sạch sẽ. Thủ tục đơn giản và nhanh chóng. Tôi rất hài lòng và chắc chắn sẽ quay lại lần sau."
                        </p>
                        <div class="d-flex align-items-center">
                            <img src="./assets/images/avatar-1.jpg" alt="Customer" class="rounded-circle me-3" width="50" height="50">
                            <div>
                                <h6 class="mb-0">Nguyễn Văn X</h6>
                                <small class="text-muted">Giám đốc công ty ABC</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow h-100 animate-on-scroll" data-animation="fadeInUp" data-delay="100">
                    <div class="card-body p-4">
                        <div class="d-flex mb-4">
                            <div class="testimonial-rating text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                            </div>
                        </div>
                        <p class="testimonial-text mb-4">
                            "Đã sử dụng dịch vụ thuê xe của CAR RENTAL cho chuyến du lịch gia đình và vô cùng hài lòng. Xe đẹp, chạy êm, nhân viên nhiệt tình và giá cả hợp lý."
                        </p>
                        <div class="d-flex align-items-center">
                            <img src="./assets/images/avatar-2.jpg" alt="Customer" class="rounded-circle me-3" width="50" height="50">
                            <div>
                                <h6 class="mb-0">Trần Thị Y</h6>
                                <small class="text-muted">Kế toán trưởng</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow h-100 animate-on-scroll" data-animation="fadeInUp" data-delay="200">
                    <div class="card-body p-4">
                        <div class="d-flex mb-4">
                            <div class="testimonial-rating text-warning">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star-half-alt"></i>
                            </div>
                        </div>
                        <p class="testimonial-text mb-4">
                            "Thuê xe dài hạn cho công ty và rất ấn tượng với chất lượng dịch vụ. Đội ngũ hỗ trợ 24/7 luôn sẵn sàng giải quyết mọi vấn đề. Chắc chắn sẽ tiếp tục hợp tác lâu dài."
                        </p>
                        <div class="d-flex align-items-center">
                            <img src="./assets/images/avatar-3.jpg" alt="Customer" class="rounded-circle me-3" width="50" height="50">
                            <div>
                                <h6 class="mb-0">Lê Văn Z</h6>
                                <small class="text-muted">Doanh nhân</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5 animate-on-scroll" data-animation="fadeInUp">
            <div class="d-flex align-items-center justify-content-center mb-3">
                <span class="line-title bg-primary"></span>
                <h6 class="text-uppercase fw-semibold text-primary mb-0 mx-2">Câu hỏi thường gặp</h6>
                <span class="line-title bg-primary"></span>
            </div>
            <h2 class="fw-bold">Những điều bạn có thể thắc mắc</h2>
        </div>
        
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="accordion" id="aboutFaq">
                    <div class="accordion-item border-0 mb-3 shadow-sm animate-on-scroll" data-animation="fadeInUp">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Điều kiện để thuê xe là gì?
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#aboutFaq">
                            <div class="accordion-body">
                                Để thuê xe, bạn cần có CMND/CCCD, giấy phép lái xe còn hiệu lực, hộ khẩu hoặc KT3. Ngoài ra, bạn cần đặt cọc một khoản tiền (tùy loại xe) và tuân thủ các điều khoản trong hợp đồng thuê xe.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 mb-3 shadow-sm animate-on-scroll" data-animation="fadeInUp" data-delay="100">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Quy trình thuê xe như thế nào?
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#aboutFaq">
                            <div class="accordion-body">
                                Quy trình thuê xe bao gồm: Chọn xe phù hợp, đặt xe trực tuyến hoặc qua điện thoại, xác nhận lịch thuê, ký hợp đồng và đặt cọc, nhận xe, sử dụng xe trong thời gian thuê, và cuối cùng là trả xe và thanh toán số tiền còn lại.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 mb-3 shadow-sm animate-on-scroll" data-animation="fadeInUp" data-delay="200">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                Chính sách hủy đặt xe như thế nào?
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#aboutFaq">
                            <div class="accordion-body">
                                Chính sách hủy đặt xe của chúng tôi như sau: Hủy trước 48 giờ - hoàn tiền 100%, hủy từ 24-48 giờ - hoàn tiền 50%, hủy trong vòng 24 giờ - không hoàn tiền. Vui lòng liên hệ với chúng tôi sớm nhất nếu có thay đổi kế hoạch.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 mb-3 shadow-sm animate-on-scroll" data-animation="fadeInUp" data-delay="300">
                        <h2 class="accordion-header" id="headingFour">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                Xe có được bảo hiểm không?
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#aboutFaq">
                            <div class="accordion-body">
                                Tất cả xe của chúng tôi đều được mua bảo hiểm đầy đủ theo quy định của pháp luật. Ngoài ra, khách hàng có thể lựa chọn mua thêm bảo hiểm vật chất để được bảo vệ toàn diện hơn trong quá trình thuê xe.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item border-0 shadow-sm animate-on-scroll" data-animation="fadeInUp" data-delay="400">
                        <h2 class="accordion-header" id="headingFive">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                Làm thế nào để đặt xe?
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#aboutFaq">
                            <div class="accordion-body">
                                Bạn có thể đặt xe trực tuyến thông qua website của chúng tôi, gọi điện đến số hotline 0987 654 321, hoặc đến trực tiếp văn phòng của chúng tôi tại địa chỉ 123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8 mb-4 mb-lg-0">
                <h3 class="fw-bold mb-1">Sẵn sàng trải nghiệm dịch vụ của chúng tôi?</h3>
                <p class="mb-0">Hãy liên hệ ngay để được tư vấn và nhận báo giá miễn phí!</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="./contact.php" class="btn btn-light btn-lg animated-button">
                    <i class="fas fa-phone-alt me-2"></i>Liên hệ ngay
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Custom CSS -->
<style>
    .line-title {
        width: 30px;
        height: 2px;
        display: block;
    }
    
    .value-icon, .mission-icon, .vision-icon {
        width: 60px;
        height: 60px;
        background-color: rgba(74, 111, 220, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .team-img {
        overflow: hidden;
    }
    
    .team-img img {
        transition: all 0.3s ease;
    }
    
    .team-member:hover .team-img img {
        transform: scale(1.05);
    }
    
    .team-social {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgba(74, 111, 220, 0.8);
        padding: 10px 0;
        display: flex;
        justify-content: center;
        gap: 15px;
        transform: translateY(100%);
        transition: all 0.3s ease;
        opacity: 0;
    }
    
    .team-member:hover .team-social {
        transform: translateY(0);
        opacity: 1;
    }
    
    .team-social-icon {
        color: white;
        font-size: 16px;
        width: 32px;
        height: 32px;
        background-color: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .team-social-icon:hover {
        background-color: white;
        color: var(--primary-color);
    }
    
    .accordion-button:not(.collapsed) {
        background-color: rgba(74, 111, 220, 0.1);
        color: var(--primary-color);
    }
    
    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(74, 111, 220, 0.25);
    }
    
    .animated-button {
        position: relative;
        overflow: hidden;
        z-index: 1;
        transition: all 0.3s ease;
    }
    
    .animated-button:hover {
        transform: translateY(-2px);
    }
    
    .animated-button:after {
        content: "";
        position: absolute;
        left: 50%;
        top: 50%;
        width: 120%;
        height: 0;
        background: rgba(255, 255, 255, 0.1);
        padding-bottom: 120%;
        border-radius: 50%;
        transform: translate(-50%, -50%) scale(0);
        opacity: 0;
        z-index: -1;
        transition: transform 0.5s ease, opacity 0.5s ease;
    }
    
    .animated-button:hover:after {
        transform: translate(-50%, -50%) scale(1);
        opacity: 1;
    }
</style>

<?php
// Include footer
include './includes/footer.php';
?>