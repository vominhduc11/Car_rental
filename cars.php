<?php
// Thiết lập tiêu đề trang
$pageTitle = "Danh sách xe";

// Include các file cần thiết
require_once './includes/functions.php';
require_once './config/database.php';

// Xử lý tìm kiếm và lọc
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$pickupDate = isset($_GET['pickup_date']) ? sanitizeInput($_GET['pickup_date']) : '';
$returnDate = isset($_GET['return_date']) ? sanitizeInput($_GET['return_date']) : '';
$brand = isset($_GET['brand']) ? sanitizeInput($_GET['brand']) : '';
$minPrice = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 5000000;
$seats = isset($_GET['car_type']) ? (int)$_GET['car_type'] : 0;

// Khởi tạo danh sách xe
$cars = [];

// Lấy danh sách tất cả hãng xe để lọc
$allBrands = getAllBrands();

// Nếu có tìm kiếm theo ngày, lọc xe theo khả dụng
if (!empty($pickupDate) && !empty($returnDate)) {
    $cars = getAvailableCarsForDates($pickupDate, $returnDate);
} else {
    // Nếu không, lấy tất cả xe khả dụng
    $cars = getAvailableCars();
}

// Lọc theo hãng xe
if (!empty($brand)) {
    $cars = array_filter($cars, function ($car) use ($brand) {
        return $car['brand'] == $brand;
    });
}

// Lọc theo giá
$cars = array_filter($cars, function ($car) use ($minPrice, $maxPrice) {
    return $car['price_per_day'] >= $minPrice && $car['price_per_day'] <= $maxPrice;
});

// Lọc theo số chỗ ngồi
if ($seats > 0) {
    $cars = array_filter($cars, function ($car) use ($seats) {
        return $car['seats'] == $seats;
    });
}

// Lọc theo từ khóa tìm kiếm
if (!empty($search)) {
    $cars = array_filter($cars, function ($car) use ($search) {
        return (
            stripos($car['brand'], $search) !== false ||
            stripos($car['model'], $search) !== false ||
            stripos($car['description'], $search) !== false
        );
    });
}

// Phân trang
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$totalCars = count($cars);
$carsPerPage = 9;
$totalPages = ceil($totalCars / $carsPerPage);

// Đảm bảo trang hiện tại hợp lệ
if ($page < 1) $page = 1;
if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

// Lấy xe cho trang hiện tại
$offset = ($page - 1) * $carsPerPage;
$carsOnPage = array_slice($cars, $offset, $carsPerPage);

// Include header
include './includes/header.php';
?>

<!-- Hero Section -->
<section class="page-header bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="page-title mb-2">Danh Sách Xe</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 text-white-50">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Trang chủ</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">Danh sách xe</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="index.php#search-form" class="btn btn-light">
                    <i class="fas fa-search me-2"></i>Tìm kiếm nâng cao
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Search and Filter Section -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="search-form">
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" placeholder="Tìm kiếm xe theo tên, hãng..." value="<?php echo $search; ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="brand" onchange="this.form.submit()">
                            <option value="">Tất cả hãng xe</option>
                            <?php foreach ($allBrands as $brandOption): ?>
                                <option value="<?php echo $brandOption; ?>" <?php echo $brand == $brandOption ? 'selected' : ''; ?>>
                                    <?php echo $brandOption; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="car_type" onchange="this.form.submit()">
                            <option value="">Tất cả loại xe</option>
                            <option value="4" <?php echo $seats == 4 ? 'selected' : ''; ?>>4 chỗ</option>
                            <option value="5" <?php echo $seats == 5 ? 'selected' : ''; ?>>5 chỗ</option>
                            <option value="7" <?php echo $seats == 7 ? 'selected' : ''; ?>>7 chỗ</option>
                            <option value="16" <?php echo $seats == 16 ? 'selected' : ''; ?>>16 chỗ</option>
                        </select>
                    </div>
                </div>

                <?php if (!empty($pickupDate) && !empty($returnDate)): ?>
                    <input type="hidden" name="pickup_date" value="<?php echo $pickupDate; ?>">
                    <input type="hidden" name="return_date" value="<?php echo $returnDate; ?>">

                    <div class="mt-3 p-3 bg-white rounded shadow-sm">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                Lọc theo ngày: <strong><?php echo formatDate($pickupDate); ?></strong> đến <strong><?php echo formatDate($returnDate); ?></strong>
                            </div>
                            <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-times me-1"></i>Xóa bộ lọc ngày
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
</section>

<!-- Cars List Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Filter Sidebar on Desktop -->
            <div class="col-lg-3 d-none d-lg-block">
                <div class="filter-sidebar">
                    <div class="filter-card mb-4">
                        <div class="filter-header">
                            <h5 class="filter-title">Lọc theo giá (mỗi ngày)</h5>
                        </div>
                        <div class="filter-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" id="price-filter-form">
                                <input type="hidden" name="search" value="<?php echo $search; ?>">
                                <input type="hidden" name="brand" value="<?php echo $brand; ?>">
                                <input type="hidden" name="car_type" value="<?php echo $seats; ?>">
                                <?php if (!empty($pickupDate) && !empty($returnDate)): ?>
                                    <input type="hidden" name="pickup_date" value="<?php echo $pickupDate; ?>">
                                    <input type="hidden" name="return_date" value="<?php echo $returnDate; ?>">
                                <?php endif; ?>

                                <div class="price-range-slider">
                                    <div class="price-range-values d-flex justify-content-between mb-2">
                                        <span id="min-price-display"><?php echo formatPrice($minPrice); ?></span>
                                        <span id="max-price-display"><?php echo formatPrice($maxPrice); ?></span>
                                    </div>
                                    <input type="range" class="form-range mb-2" id="min-price" name="min_price" min="0" max="5000000" step="100000" value="<?php echo $minPrice; ?>" oninput="updatePriceRange()">
                                    <input type="range" class="form-range" id="max-price" name="max_price" min="0" max="5000000" step="100000" value="<?php echo $maxPrice; ?>" oninput="updatePriceRange()">

                                    <button type="submit" class="btn btn-primary w-100 mt-3">
                                        Áp dụng
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="filter-card mb-4">
                        <div class="filter-header">
                            <h5 class="filter-title">Lọc theo hãng xe</h5>
                        </div>
                        <div class="filter-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
                                <input type="hidden" name="search" value="<?php echo $search; ?>">
                                <input type="hidden" name="min_price" value="<?php echo $minPrice; ?>">
                                <input type="hidden" name="max_price" value="<?php echo $maxPrice; ?>">
                                <input type="hidden" name="car_type" value="<?php echo $seats; ?>">
                                <?php if (!empty($pickupDate) && !empty($returnDate)): ?>
                                    <input type="hidden" name="pickup_date" value="<?php echo $pickupDate; ?>">
                                    <input type="hidden" name="return_date" value="<?php echo $returnDate; ?>">
                                <?php endif; ?>

                                <div class="mb-3">
                                    <?php foreach ($allBrands as $brandOption): ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="brand" id="brand-<?php echo $brandOption; ?>" value="<?php echo $brandOption; ?>" <?php echo $brand == $brandOption ? 'checked' : ''; ?> onchange="this.form.submit()">
                                            <label class="form-check-label" for="brand-<?php echo $brandOption; ?>">
                                                <?php echo $brandOption; ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>

                                    <?php if (!empty($brand)): ?>
                                        <div class="mt-2">
                                            <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?search=<?php echo $search; ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>&car_type=<?php echo $seats; ?><?php echo !empty($pickupDate) && !empty($returnDate) ? '&pickup_date=' . $pickupDate . '&return_date=' . $returnDate : ''; ?>" class="text-danger small">
                                                <i class="fas fa-times me-1"></i>Xóa bộ lọc hãng xe
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="filter-card mb-4">
                        <div class="filter-header">
                            <h5 class="filter-title">Lọc theo loại xe</h5>
                        </div>
                        <div class="filter-body">
                            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
                                <input type="hidden" name="search" value="<?php echo $search; ?>">
                                <input type="hidden" name="min_price" value="<?php echo $minPrice; ?>">
                                <input type="hidden" name="max_price" value="<?php echo $maxPrice; ?>">
                                <input type="hidden" name="brand" value="<?php echo $brand; ?>">
                                <?php if (!empty($pickupDate) && !empty($returnDate)): ?>
                                    <input type="hidden" name="pickup_date" value="<?php echo $pickupDate; ?>">
                                    <input type="hidden" name="return_date" value="<?php echo $returnDate; ?>">
                                <?php endif; ?>

                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="car_type" id="car-type-4" value="4" <?php echo $seats == 4 ? 'checked' : ''; ?> onchange="this.form.submit()">
                                        <label class="form-check-label" for="car-type-4">
                                            4 chỗ
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="car_type" id="car-type-5" value="5" <?php echo $seats == 5 ? 'checked' : ''; ?> onchange="this.form.submit()">
                                        <label class="form-check-label" for="car-type-5">
                                            5 chỗ
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="car_type" id="car-type-7" value="7" <?php echo $seats == 7 ? 'checked' : ''; ?> onchange="this.form.submit()">
                                        <label class="form-check-label" for="car-type-7">
                                            7 chỗ
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="car_type" id="car-type-16" value="16" <?php echo $seats == 16 ? 'checked' : ''; ?> onchange="this.form.submit()">
                                        <label class="form-check-label" for="car-type-16">
                                            16 chỗ
                                        </label>
                                    </div>

                                    <?php if ($seats > 0): ?>
                                        <div class="mt-2">
                                            <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?search=<?php echo $search; ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>&brand=<?php echo $brand; ?><?php echo !empty($pickupDate) && !empty($returnDate) ? '&pickup_date=' . $pickupDate . '&return_date=' . $returnDate : ''; ?>" class="text-danger small">
                                                <i class="fas fa-times me-1"></i>Xóa bộ lọc loại xe
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cars Grid -->
            <div class="col-lg-9">
                <div class="cars-header mb-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Tìm thấy <?php echo $totalCars; ?> xe</h4>
                        <?php if (!empty($search) || !empty($brand) || $seats > 0 || $minPrice > 0 || $maxPrice < 5000000): ?>
                            <div class="filter-summary">
                                <span class="text-muted">Bộ lọc: </span>
                                <?php if (!empty($search)): ?>
                                    <span class="badge bg-primary"><?php echo $search; ?> <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?brand=<?php echo $brand; ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>&car_type=<?php echo $seats; ?><?php echo !empty($pickupDate) && !empty($returnDate) ? '&pickup_date=' . $pickupDate . '&return_date=' . $returnDate : ''; ?>" class="text-white text-decoration-none"><i class="fas fa-times ms-1"></i></a></span>
                                <?php endif; ?>

                                <?php if (!empty($brand)): ?>
                                    <span class="badge bg-primary"><?php echo $brand; ?> <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?search=<?php echo $search; ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>&car_type=<?php echo $seats; ?><?php echo !empty($pickupDate) && !empty($returnDate) ? '&pickup_date=' . $pickupDate . '&return_date=' . $returnDate : ''; ?>" class="text-white text-decoration-none"><i class="fas fa-times ms-1"></i></a></span>
                                <?php endif; ?>

                                <?php if ($seats > 0): ?>
                                    <span class="badge bg-primary"><?php echo $seats; ?> chỗ <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?search=<?php echo $search; ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>&brand=<?php echo $brand; ?><?php echo !empty($pickupDate) && !empty($returnDate) ? '&pickup_date=' . $pickupDate . '&return_date=' . $returnDate : ''; ?>" class="text-white text-decoration-none"><i class="fas fa-times ms-1"></i></a></span>
                                <?php endif; ?>

                                <?php if ($minPrice > 0 || $maxPrice < 5000000): ?>
                                    <span class="badge bg-primary"><?php echo formatPrice($minPrice); ?> - <?php echo formatPrice($maxPrice); ?> <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?search=<?php echo $search; ?>&brand=<?php echo $brand; ?>&car_type=<?php echo $seats; ?><?php echo !empty($pickupDate) && !empty($returnDate) ? '&pickup_date=' . $pickupDate . '&return_date=' . $returnDate : ''; ?>" class="text-white text-decoration-none"><i class="fas fa-times ms-1"></i></a></span>
                                <?php endif; ?>
                            </div>

                            <div class="mt-2">
                                <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?><?php echo !empty($pickupDate) && !empty($returnDate) ? '?pickup_date=' . $pickupDate . '&return_date=' . $returnDate : ''; ?>" class="text-danger">
                                    <i class="fas fa-times-circle me-1"></i>Xóa tất cả bộ lọc
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div>
                        <select class="form-select" id="sort-cars">
                            <option value="price-asc">Giá: Thấp đến cao</option>
                            <option value="price-desc">Giá: Cao đến thấp</option>
                            <option value="newest">Mới nhất</option>
                        </select>
                    </div>
                </div>

                <?php if (empty($carsOnPage)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Không tìm thấy xe nào phù hợp với bộ lọc của bạn. Vui lòng thử lại với các tiêu chí khác.
                    </div>
                <?php else: ?>
                    <div class="row car-list">
                        <?php foreach ($carsOnPage as $car): ?>
                            <div class="col-md-6 col-xl-4 mb-4">
                                <div class="car-card animate-on-scroll" data-animation="fadeInUp" data-brand="<?php echo $car['brand']; ?>" data-price="<?php echo $car['price_per_day']; ?>" data-name="<?php echo $car['brand'] . ' ' . $car['model']; ?>">
                                    <div class="car-image">
                                        <img src="<?php echo !empty($car['image']) ? '/showImg.php?filename=' . $car['image'] : './assets/images/car-placeholder.jpg'; ?>" alt="<?php echo $car['brand'] . ' ' . $car['model']; ?>">
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

                                        <div class="mt-3 d-flex">
                                            <a href="./car-detail.php?id=<?php echo $car['id']; ?><?php echo !empty($pickupDate) && !empty($returnDate) ? '&pickup_date=' . $pickupDate . '&return_date=' . $returnDate : ''; ?>" class="btn btn-primary w-100 me-2">
                                                <i class="fas fa-info-circle me-1"></i>Chi tiết
                                            </a>
                                            <?php if (!empty($pickupDate) && !empty($returnDate)): ?>
                                                <a href="./booking.php?car_id=<?php echo $car['id']; ?>&pickup_date=<?php echo $pickupDate; ?>&return_date=<?php echo $returnDate; ?>" class="btn btn-success w-100">
                                                    <i class="fas fa-calendar-check me-1"></i>Đặt xe
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <div class="pagination-container mt-4">
                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo $search; ?>&brand=<?php echo $brand; ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>&car_type=<?php echo $seats; ?><?php echo !empty($pickupDate) && !empty($returnDate) ? '&pickup_date=' . $pickupDate . '&return_date=' . $returnDate : ''; ?>" aria-label="Previous">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>

                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo $search; ?>&brand=<?php echo $brand; ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>&car_type=<?php echo $seats; ?><?php echo !empty($pickupDate) && !empty($returnDate) ? '&pickup_date=' . $pickupDate . '&return_date=' . $returnDate : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>

                                    <li class="page-item <?php echo ($page >= $totalPages) ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo $search; ?>&brand=<?php echo $brand; ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>&car_type=<?php echo $seats; ?><?php echo !empty($pickupDate) && !empty($returnDate) ? '&pickup_date=' . $pickupDate . '&return_date=' . $returnDate : ''; ?>" aria-label="Next">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Mobile Filter Button (Fixed at Bottom) -->
<div class="d-lg-none">
    <div class="mobile-filter-btn">
        <button class="btn btn-primary btn-lg rounded-circle shadow" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileFilterOffcanvas" aria-controls="mobileFilterOffcanvas">
            <i class="fas fa-filter"></i>
        </button>
    </div>
</div>

<!-- Mobile Filter Offcanvas -->
<div class="offcanvas offcanvas-start" tabindex="-1" id="mobileFilterOffcanvas" aria-labelledby="mobileFilterOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileFilterOffcanvasLabel">Lọc xe</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Mobile Filter Content (Same as desktop sidebar) -->
        <div class="filter-sidebar">
            <div class="filter-card mb-4">
                <div class="filter-header">
                    <h5 class="filter-title">Lọc theo giá (mỗi ngày)</h5>
                </div>
                <div class="filter-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" id="mobile-price-filter-form">
                        <input type="hidden" name="search" value="<?php echo $search; ?>">
                        <input type="hidden" name="brand" value="<?php echo $brand; ?>">
                        <input type="hidden" name="car_type" value="<?php echo $seats; ?>">
                        <?php if (!empty($pickupDate) && !empty($returnDate)): ?>
                            <input type="hidden" name="pickup_date" value="<?php echo $pickupDate; ?>">
                            <input type="hidden" name="return_date" value="<?php echo $returnDate; ?>">
                        <?php endif; ?>

                        <div class="price-range-slider">
                            <div class="price-range-values d-flex justify-content-between mb-2">
                                <span id="mobile-min-price-display"><?php echo formatPrice($minPrice); ?></span>
                                <span id="mobile-max-price-display"><?php echo formatPrice($maxPrice); ?></span>
                            </div>
                            <input type="range" class="form-range mb-2" id="mobile-min-price" name="min_price" min="0" max="5000000" step="100000" value="<?php echo $minPrice; ?>" oninput="updateMobilePriceRange()">
                            <input type="range" class="form-range" id="mobile-max-price" name="max_price" min="0" max="5000000" step="100000" value="<?php echo $maxPrice; ?>" oninput="updateMobilePriceRange()">

                            <button type="submit" class="btn btn-primary w-100 mt-3">
                                Áp dụng
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="filter-card mb-4">
                <div class="filter-header">
                    <h5 class="filter-title">Lọc theo hãng xe</h5>
                </div>
                <div class="filter-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
                        <input type="hidden" name="search" value="<?php echo $search; ?>">
                        <input type="hidden" name="min_price" value="<?php echo $minPrice; ?>">
                        <input type="hidden" name="max_price" value="<?php echo $maxPrice; ?>">
                        <input type="hidden" name="car_type" value="<?php echo $seats; ?>">
                        <?php if (!empty($pickupDate) && !empty($returnDate)): ?>
                            <input type="hidden" name="pickup_date" value="<?php echo $pickupDate; ?>">
                            <input type="hidden" name="return_date" value="<?php echo $returnDate; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <?php foreach ($allBrands as $brandOption): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="brand" id="mobile-brand-<?php echo $brandOption; ?>" value="<?php echo $brandOption; ?>" <?php echo $brand == $brandOption ? 'checked' : ''; ?> onchange="this.form.submit()">
                                    <label class="form-check-label" for="mobile-brand-<?php echo $brandOption; ?>">
                                        <?php echo $brandOption; ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>

                            <?php if (!empty($brand)): ?>
                                <div class="mt-2">
                                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?search=<?php echo $search; ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>&car_type=<?php echo $seats; ?><?php echo !empty($pickupDate) && !empty($returnDate) ? '&pickup_date=' . $pickupDate . '&return_date=' . $returnDate : ''; ?>" class="text-danger small">
                                        <i class="fas fa-times me-1"></i>Xóa bộ lọc hãng xe
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="filter-card mb-4">
                <div class="filter-header">
                    <h5 class="filter-title">Lọc theo loại xe</h5>
                </div>
                <div class="filter-body">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
                        <input type="hidden" name="search" value="<?php echo $search; ?>">
                        <input type="hidden" name="min_price" value="<?php echo $minPrice; ?>">
                        <input type="hidden" name="max_price" value="<?php echo $maxPrice; ?>">
                        <input type="hidden" name="brand" value="<?php echo $brand; ?>">
                        <?php if (!empty($pickupDate) && !empty($returnDate)): ?>
                            <input type="hidden" name="pickup_date" value="<?php echo $pickupDate; ?>">
                            <input type="hidden" name="return_date" value="<?php echo $returnDate; ?>">
                        <?php endif; ?>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="car_type" id="mobile-car-type-4" value="4" <?php echo $seats == 4 ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <label class="form-check-label" for="mobile-car-type-4">
                                    4 chỗ
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="car_type" id="mobile-car-type-5" value="5" <?php echo $seats == 5 ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <label class="form-check-label" for="mobile-car-type-5">
                                    5 chỗ
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="car_type" id="mobile-car-type-7" value="7" <?php echo $seats == 7 ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <label class="form-check-label" for="mobile-car-type-7">
                                    7 chỗ
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="car_type" id="mobile-car-type-16" value="16" <?php echo $seats == 16 ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <label class="form-check-label" for="mobile-car-type-16">
                                    16 chỗ
                                </label>
                            </div>

                            <?php if ($seats > 0): ?>
                                <div class="mt-2">
                                    <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?search=<?php echo $search; ?>&min_price=<?php echo $minPrice; ?>&max_price=<?php echo $maxPrice; ?>&brand=<?php echo $brand; ?><?php echo !empty($pickupDate) && !empty($returnDate) ? '&pickup_date=' . $pickupDate . '&return_date=' . $returnDate : ''; ?>" class="text-danger small">
                                        <i class="fas fa-times me-1"></i>Xóa bộ lọc loại xe
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?><?php echo !empty($pickupDate) && !empty($returnDate) ? '?pickup_date=' . $pickupDate . '&return_date=' . $returnDate : ''; ?>" class="btn btn-outline-danger">
                    <i class="fas fa-times-circle me-1"></i>Xóa tất cả bộ lọc
                </a>
            </div>
        </div>
    </div>
</div>

<!-- JS for Price Range Slider -->
<script>
    function updatePriceRange() {
        const minPrice = document.getElementById('min-price').value;
        const maxPrice = document.getElementById('max-price').value;

        // Validate min is less than max
        if (parseInt(minPrice) > parseInt(maxPrice)) {
            document.getElementById('min-price').value = maxPrice;
        }

        // Update display values
        document.getElementById('min-price-display').textContent = formatPrice(document.getElementById('min-price').value);
        document.getElementById('max-price-display').textContent = formatPrice(document.getElementById('max-price').value);
    }

    function updateMobilePriceRange() {
        const minPrice = document.getElementById('mobile-min-price').value;
        const maxPrice = document.getElementById('mobile-max-price').value;

        // Validate min is less than max
        if (parseInt(minPrice) > parseInt(maxPrice)) {
            document.getElementById('mobile-min-price').value = maxPrice;
        }

        // Update display values
        document.getElementById('mobile-min-price-display').textContent = formatPrice(document.getElementById('mobile-min-price').value);
        document.getElementById('mobile-max-price-display').textContent = formatPrice(document.getElementById('mobile-max-price').value);
    }

    function formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    }

    // Initialize price range
    document.addEventListener('DOMContentLoaded', function() {
        updatePriceRange();
        updateMobilePriceRange();

        // Sort cars
        const sortSelect = document.getElementById('sort-cars');
        if (sortSelect) {
            sortSelect.addEventListener('change', function() {
                sortCars(this.value);
            });
        }
    });

    // Sort cars function
    function sortCars(sortBy) {
        const carList = document.querySelector('.car-list');
        const cars = Array.from(carList.children);

        cars.sort(function(a, b) {
            const carA = a.querySelector('.car-card');
            const carB = b.querySelector('.car-card');

            if (sortBy === 'price-asc') {
                return parseFloat(carA.dataset.price) - parseFloat(carB.dataset.price);
            } else if (sortBy === 'price-desc') {
                return parseFloat(carB.dataset.price) - parseFloat(carA.dataset.price);
            } else if (sortBy === 'newest') {
                // Assuming newer cars are listed first in HTML
                return 0;
            }
        });

        // Clear and append sorted cars
        while (carList.firstChild) {
            carList.removeChild(carList.firstChild);
        }

        cars.forEach(function(car) {
            carList.appendChild(car);
        });
    }
</script>

<!-- Styling for mobile filter button -->
<style>
    .mobile-filter-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
    }

    .filter-card {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
    }

    .filter-header {
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .filter-title {
        margin-bottom: 0;
        font-size: 18px;
    }

    /* Style for price range slider */
    .form-range::-webkit-slider-thumb {
        background: #4A6FDC;
    }

    .form-range::-moz-range-thumb {
        background: #4A6FDC;
    }

    .form-range::-ms-thumb {
        background: #4A6FDC;
    }
</style>

<?php
// Include footer
include './includes/footer.php';
?>