<!-- Footer -->
<footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4 mb-md-0">
                    <div class="footer-logo">
                        <i class="fas fa-car"></i> CAR RENTAL
                    </div>
                    <p class="mb-4">
                        Dịch vụ cho thuê xe hàng đầu Việt Nam, cung cấp đa dạng các loại xe với giá cả hợp lý và dịch vụ chuyên nghiệp.
                    </p>
                    <div class="footer-social">
                        <a href="#" class="footer-social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="footer-social-icon"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="footer-social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="footer-social-icon"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-white mb-4">Liên kết nhanh</h5>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="/index.php">Trang chủ</a></li>
                        <li class="footer-link"><a href="/cars.php">Danh sách xe</a></li>
                        <li class="footer-link"><a href="/about.php">Giới thiệu</a></li>
                        <li class="footer-link"><a href="/contact.php">Liên hệ</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-white mb-4">Dịch vụ</h5>
                    <ul class="footer-links">
                        <li class="footer-link"><a href="#">Thuê xe tự lái</a></li>
                        <li class="footer-link"><a href="#">Thuê xe có tài xế</a></li>
                        <li class="footer-link"><a href="#">Thuê xe theo tháng</a></li>
                        <li class="footer-link"><a href="#">Thuê xe du lịch</a></li>
                        <li class="footer-link"><a href="#">Thuê xe cưới</a></li>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5 class="text-white mb-4">Liên hệ</h5>
                    <ul class="footer-links">
                        <li class="footer-link">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            123 Đường ABC, Quận XYZ, TP. Hồ Chí Minh
                        </li>
                        <li class="footer-link">
                            <i class="fas fa-phone-alt me-2"></i>
                            (028) 1234 5678
                        </li>
                        <li class="footer-link">
                            <i class="fas fa-mobile-alt me-2"></i>
                            0987 654 321
                        </li>
                        <li class="footer-link">
                            <i class="fas fa-envelope me-2"></i>
                            info@carrental.com
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Car Rental. Tất cả quyền được bảo lưu.</p>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/animations.js"></script>
    
    <?php if (isset($extraJS)): ?>
        <?php echo $extraJS; ?>
    <?php endif; ?>
    
</body>
</html>