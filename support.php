<?php
session_start();
require_once 'includes/config.php';

// Verify file includes
if (!file_exists('includes/navbar.php')) {
    die('Navigation file not found');
}

if (!file_exists('includes/footer.php')) {
    die('Footer file not found');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support - ZIARA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
            color: #212529;
        }
        .card {
            transition: transform 0.2s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <div class="container mt-5">
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php 
                    echo htmlspecialchars($_SESSION['error']);
                    unset($_SESSION['error']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <h1 class="mb-4 fw-bold">Customer Support</h1>
        <p>If you have any questions or need assistance, please refer to the following options:</p>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <i class="bi bi-question-circle me-2"></i>Frequently Asked Questions
                    </div>
                    <div class="card-body">
                        <p>Browse our FAQ section for answers to common questions about orders, shipping, returns, and more.</p>
                        <a href="#faq" class="btn btn-info btn-dark text-white">View FAQs</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-dark text-white">
                        <i class="bi bi-envelope me-2"></i>Contact Us Directly
                    </div>
                    <div class="card-body">
                        <p>Can't find what you're looking for? Our support team is ready to help.</p>
                        <a href="contact.php" class="btn btn-dark text-white">Go to Contact Form</a>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="mt-5" id="faq">Frequently Asked Questions</h2>
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        How do I track my order?
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        You can track your order by logging into your account and visiting the "Order History" page. Once your order has shipped, you will receive an email with tracking information.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        What is your return policy?
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        We offer a 30-day return policy for most items. Please visit our Returns & Refunds page for detailed information and instructions on how to initiate a return.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        How can I change my shipping address?
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        If your order has not yet shipped, you may be able to change your shipping address by contacting our customer support team immediately. Please have your order number ready.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
        // Add error handling for script loading
        window.addEventListener('error', function(e) {
            if (e.target.tagName === 'SCRIPT') {
                console.error('Script loading failed:', e.target.src);
            }
        }, true);
    </script>
</body>
</html>

