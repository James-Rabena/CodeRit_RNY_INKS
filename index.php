<?php
// Enable error reporting for debugging (remove in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Initialize session variable if not set
if (!isset($_SESSION['user_logged_in'])) {
    $_SESSION['user_logged_in'] = false;
}

// Include database connection
require_once __DIR__ . '/db_connection.php';  // ✅ Corrected path

// Get cart count for the badge (if logged in)
$cartCount = 0;
if ($_SESSION['user_logged_in']) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT SUM(quantity) as cart_count FROM cart_items WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $cartCount = $row['cart_count'] ?: 0;
    }
    $stmt->close();
}

// Handle login simulation
if (isset($_GET['login'])) {
    $_SESSION['user_logged_in'] = true;
    header("Location: index.php");
    exit();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fragrance Fusion</title>
    <meta name="description" content="Fragrance Fusion - Unique, memorable scents that tell stories">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css"> <!-- ✅ Removed '/../' -->
</head>
<body>
    <?php include 'header.php'; ?> <!-- ✅ Fixed path -->

    <!-- Hero Section as Carousel -->
    <section class="hero-section p-0 m-0">
    <div id="carouselExampleIndicators" class="carousel slide carousel-fade w-100" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="3" aria-label="Slide 4"></button>
            </div>
            <div class="carousel-inner">
                <!-- Slide 1 -->
                <div class="carousel-item active">
                    <img src="assets/carousel2.jpg" class="d-block w-100" alt="Fragrance 1" style="object-fit:cover; height:70vh;">
                    <div class="carousel-caption d-flex flex-column justify-content-center align-items-center" style="top:0; bottom:0;">
                        <h1 class="hero-title">Discover the Art of Fragrance</h1>
                        <p class="hero-subtitle">
                            Immerse yourself in a world of handcrafted scents that tell stories, evoke emotions, and create lasting memories.
                        </p>
                        <div class="hero-buttons">
                            <a href="collections.php" class="hero-btn primary-btn btn btn-light me-2">Explore Collections</a>
                            <a href="AboutUs.php" class="hero-btn secondary-btn btn btn-outline-light">Learn More</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="carousel-item">
                    <img src="assets/carousel3.jpg" class="d-block w-100" alt="Fragrance 2" style="object-fit:cover; height:70vh;">
                    <div class="carousel-caption d-flex flex-column justify-content-center align-items-center" style="top:0; bottom:0;">
                        <h1 class="hero-title">Signature Scents</h1>
                        <p class="hero-subtitle">
                            Discover our most loved fragrances for every personality.
                        </p>
                        <div class="hero-buttons">
                            <a href="collections.php" class="hero-btn primary-btn btn btn-light me-2">Explore Collections</a>
                            <a href="AboutUs.php" class="hero-btn secondary-btn btn btn-outline-light">Learn More</a>
                        </div>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="carousel-item">
                    <img src="assets/carousel4.jpg" class="d-block w-100" alt="Fragrance 3" style="object-fit:cover; height:70vh;">
                    <div class="carousel-caption d-flex flex-column justify-content-center align-items-center" style="top:0; bottom:0;">
                        <h1 class="hero-title">Luxury Collection</h1>
                        <p class="hero-subtitle">
                            Indulge in our premium perfumes for special moments.
                        </p>
                        <div class="hero-buttons">
                            <a href="collections.php" class="hero-btn primary-btn btn btn-light me-2">Explore Collections</a>
                            <a href="AboutUs.php" class="hero-btn secondary-btn btn btn-outline-light">Learn More</a>
                        </div>
                    </div>
                </div>
                
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <!-- Perfume Categories -->
    <section class="perfume-categories-section">
        <div class="container">
            <h2 class="section-title">Explore Our Perfume Categories</h2>
            <div class="perfume-categories-grid">

                <!-- Category 1 -->
                <div class="perfume-category-row">
                    <div class="category-image-wrapper">
                        <img src="assets/EAUDE2.jpg" alt="Eau De Cologne" class="category-image">
                    </div>
                    <div class="category-info">
                        <h3 class="category-title">Eau De Cologne</h3>
                        <p class="category-description">Fresh and light fragrances for every occasion.</p>
                        <a href="eaudecologneseasons.php" class="category-link">Learn More</a>
                    </div>
                </div>

                <!-- Category 2 -->
                <div class="perfume-category-row">
                    <div class="category-image-wrapper">
                        <img src="assets/EAUDEPARFUM.jpg" alt="Eau De Parfum" class="category-image">
                    </div>
                    <div class="category-info">
                        <h3 class="category-title">Eau De Parfum</h3>
                        <p class="category-description">Long-lasting scents with a touch of elegance.</p>
                        <a href="eaudeparfumseasons.php" class="category-link">Learn More</a>
                    </div>
                </div>

                <!-- Category 3 -->
                <div class="perfume-category-row">
                    <div class="category-image-wrapper">
                        <img src="assets/EAUDETOILETTE.jpg" alt="Eau De Toilette" class="category-image">
                    </div>
                    <div class="category-info">
                        <h3 class="category-title">Eau De Toilette</h3>
                        <p class="category-description">Perfect for daily wear with a subtle charm.</p>
                        <a href="eaudetoilette.php" class="category-link">Learn More</a>
                    </div>
                </div>

                <!-- Category 4 -->
                <div class="perfume-category-row">
                    <div class="category-image-wrapper">
                        <img src="assets/EAUDEFRAICHE.jpg" alt="Eau De Fraiche" class="category-image">
                    </div>
                    <div class="category-info">
                        <h3 class="category-title">Eau De Fraiche</h3>
                        <p class="category-description">Light and refreshing fragrances for a breezy feel.</p>
                        <a href="eaudefraiche.php" class="category-link">Learn More</a>
                    </div>
                </div>

                <!-- Category 5 -->
                <div class="perfume-category-row">
                    <div class="category-image-wrapper">
                        <img src="assets/PARFUMEXTRAIT.jpg" alt="Parfum Extrait" class="category-image">
                    </div>
                    <div class="category-info">
                        <h3 class="category-title">Parfum Extrait</h3>
                        <p class="category-description">Intense and luxurious scents for special moments.</p>
                        <a href="parfumextraitseasons.php" class="category-link">Learn More</a>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Newsletter & Footer -->
    <?php include 'newsletter.php'; ?>
    <?php include 'header.php'; ?>
    <?php include 'footer.php'; ?>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="home.js"></script> <!-- ✅ Fixed path -->
</body>
</html>
