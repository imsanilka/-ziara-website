<?php
session_start();
require_once 'includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Ziara - Our Story, Mission, and Values</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hover-scale-105:hover {
            transform: scale(1.05);
        }
        .transition-transform {
            transition: transform 0.3s ease-in-out;
        }
        .bg-custom-dark {
            background-color: #1e3c72;
        }
        .philosophy-card {
            transition: transform 0.3s ease;
        }
        .philosophy-card:hover {
            transform: translateY(-10px);
        }
        .text-slate-600 {
            color: #475569;
        }
        .text-slate-200 {
            color: #e2e8f0;
        }
        .font-serif {
            font-family: Georgia, serif;
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <!-- Fix image paths and add proper error handling -->
    <section>
        <div class="cover">
             <div class="position-relative overflow-hidden">
                <img alt="A collage of elegant fabrics and textures." 
                     class="position-absolute h-100 w-100 object-fit-cover object-position-end object-position-md-center" 
                     src="assets/images/about_cover.jpeg" 
                     onerror="this.src='assets/images/default-cover.jpg'"
                     style="z-index: -1;" />
                <div class="position-absolute top-0 bottom-0 start-0 end-0 bg-black opacity-50" style="z-index: -1;"></div>
                <div class="container-fluid container-lg px-4 py-5 py-sm-5 py-md-5">
                    <div class="text-center mx-auto" style="max-width: 42rem;">
                        <h2 class="display-4 fw-bold tracking-tight text-white font-serif">About Ziara</h2>
                        <p class="mt-4 fs-5 text-white text-slate-200">Discover the heart behind our creations. A story of passion, craftsmanship, and timeless elegance.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--Story-->
    <section>
         <div class="container-fluid container-lg px-4 py-5 py-sm-5 py-md-5">
                <div class="row g-5 align-items-center">
                    <div class="col-md-6">
                        <h3 class="fs-2 fw-bold font-serif text-black">Our Story</h3>
                        <p class="mt-4 text-slate-600">
                            Founded on the principle of creating fashion that transcends seasons, Ziara was born from a desire to bring timeless pieces to the modern woman's wardrobe. Our journey began with a small sketchbook and a big dream: to design clothing that is not only beautiful but also tells a story of elegance and empowerment.
                        </p>
                        <p class="mt-4 text-slate-600">
                            Each collection is a chapter in our narrative, meticulously crafted with attention to detail, quality fabrics, and a deep respect for the art of tailoring. We believe that style is a personal expression, and our designs are a canvas for you to paint your own.
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="overflow-hidden rounded-3">
                            <img alt="A designer sketching a new clothing design." 
                                 class="img-fluid rounded-3 hover-scale-105 transition-transform" 
                                 src="assets/images/ourstory.jpg"
                                 onerror="this.src='assets/images/default-story.jpg'">
                        </div>
                    </div>
                </div>
        </div>
    </section>

    <!--Philosophy-->
    <section>
        <div class="bg-light">
                <div class="container-fluid container-lg px-4 py-5 py-sm-5 py-md-5">
                    <div class="text-center ">
                        <h3 class="fs-2 fw-bold font-serif text-black">Our Philosophy</h3>
                        <p class="mt-4 text-slate-600 mx-auto" style="max-width: 42rem;">
                            We are guided by a core set of principles that define our brand and our creations.
                        </p>
                    </div>
                    <div class="row mt-5 g-4 text-center">
                        <div class="col-md-4">
                            <div class="p-4 bg-white rounded-3 shadow-sm philosophy-card">
                                <div class="d-flex align-items-center justify-content-center mx-auto rounded-circle bg-custom-dark text-white" style="height: 3rem; width: 3rem;">
                                    <i class="bi bi-stars"></i>
                                </div>
                                <h4 class="mt-4 fs-5 fw-semibold text-black">Mission</h4>
                                <p class="mt-2 text-slate-600">To create sophisticated, high-quality fashion that empowers women to feel confident and express their unique style.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 bg-white rounded-3 shadow-sm philosophy-card">
                                <div class="d-flex align-items-center justify-content-center mx-auto rounded-circle bg-custom-dark text-white" style="height: 3rem; width: 3rem;">
                                    <i class="bi bi-eye"></i>
                                </div>
                                <h4 class="mt-4 fs-5 fw-semibold text-black">Vision</h4>
                                <p class="mt-2 text-slate-600">To be a leading voice in timeless fashion, celebrated for our commitment to craftsmanship, sustainability, and elegance.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-4 bg-white rounded-3 shadow-sm philosophy-card">
                                <div class="d-flex align-items-center justify-content-center mx-auto rounded-circle bg-dark-primary text-white" style="height: 3rem; width: 3rem;">
                                    <i class="bi bi-heart"></i>
                                </div>
                                <h4 class="mt-4 fs-5 fw-semibold text-black">Values</h4>
                                <p class="mt-2 text-slate-600">Quality, integrity, creativity, and a deep appreciation for the art of fashion are the pillars of our brand.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Add error handling for includes -->
    <?php
    if (!file_exists('includes/footer.php')) {
        echo '<div class="alert alert-danger">Footer file not found</div>';
    }
    ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>