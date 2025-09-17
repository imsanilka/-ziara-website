<?php
session_start();
require_once 'includes/config.php';

// Verify database connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$name = $email = $subject = $message = '';
$name_err = $email_err = $subject_err = $message_err = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate and sanitize name
    if (empty(trim($_POST['name']))) {
        $name_err = 'Please enter your name.';
    } else {
        $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
        if (!$name) {
            $name_err = 'Invalid name format.';
        }
    }

    // Validate and sanitize email
    if (empty(trim($_POST['email']))) {
        $email_err = 'Please enter your email.';
    } else {
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = 'Please enter a valid email address.';
        }
    }

    // Validate and sanitize subject
    if (empty(trim($_POST['subject']))) {
        $subject_err = 'Please enter a subject.';
    } else {
        $subject = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
        if (!$subject) {
            $subject_err = 'Invalid subject format.';
        }
    }

    // Validate and sanitize message
    if (empty(trim($_POST['message']))) {
        $message_err = 'Please enter your message.';
    } else {
        $message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);
        if (!$message) {
            $message_err = 'Invalid message format.';
        }
    }

    // Check input errors before inserting in database
    if (empty($name_err) && empty($email_err) && empty($subject_err) && empty($message_err)) {
        try {
            $sql = 'INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())';
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param('ssss', $name, $email, $subject, $message);
                
                if ($stmt->execute()) {
                    $success_message = 'Your message has been sent successfully!';
                    // Clear form fields after successful submission
                    $name = $email = $subject = $message = '';
                } else {
                    throw new Exception('Something went wrong. Please try again later.');
                }
                $stmt->close();
            } else {
                throw new Exception('Database error. Please try again later.');
            }
        } catch (Exception $e) {
            error_log("Contact Form Error: " . $e->getMessage());
            echo "<div class='alert alert-danger'>An error occurred. Please try again later.</div>";
        }
    }
}

// Close the connection after all database operations
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - ZIARA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>

    <section>
        <div class="position-relative overflow-hidden">
                <img src="assets\images/contactcover.jpeg" class="position-absolute w-100 h-100 object-fit-cover opacity-50" alt="A serene, minimalist workspace with a laptop and a cup of coffee.">
                <div class="position-absolute w-100 h-100 opacity-40"></div>
                <div class="container-fluid py-5 position-relative text-center">
                    <div class="row justify-content-center">
                        <div class="col-lg-8">
                            <h2 class="display-3 fw-bold text-dark font-serif">Contact Us</h2>
                            <p class="lead text-dark mt-3">We're here to help. Get in touch with us for any inquiries or support.</p>
                        </div>
                    </div>
                </div>
        </div>
    </section>
    <section>
        <div class="container">
            <div class="row">

                <div class="col-md-4 ">
                    <h1 class="mb-4 fw-bold">Get in Touch</h1>
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($name); ?>">
                            <span class="invalid-feedback"><?php echo $name_err; ?></span>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($email); ?>">
                            <span class="invalid-feedback"><?php echo $email_err; ?></span>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control <?php echo (!empty($subject_err)) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($subject); ?>">
                            <span class="invalid-feedback"><?php echo $subject_err; ?></span>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea name="message" class="form-control <?php echo (!empty($message_err)) ? 'is-invalid' : ''; ?>" rows="5"><?php echo htmlspecialchars($message); ?></textarea>
                            <span class="invalid-feedback"><?php echo $message_err; ?></span>
                        </div>
                        <button type="submit" class="btn btn-dark mt-3">Send Message</button>
                    </form> 
                    <div class="mt-4">
                        <a href="#" class="text-dark me-3 contact-info-icon"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-dark me-3 contact-info-icon"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-dark contact-info-icon"><i class="bi bi-twitter"></i></a>
                    </div>
                                             
                </div>
                <div class="col-md-8 ">
                    
                        
                                <h3 class="h4 fw-bold">Contact Information</h3>
                                <div class="mt-4">
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="flex-shrink-0">
                                            <span class="material-symbols-outlined contact-info-icon"><i class="bi bi-envelope-at-fill"></i></span>
                                        </div>
                                    <div>
                                            <h4 class="fs-5 fw-bold text-dark">Email</h4>
                                            <a class="text-muted hover-primary text-decoration-none" href="mailto:contact@ziara.com">contact@ziara.com</a>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <span class="material-symbols-outlined contact-info-icon"><i class="bi bi-telephone-fill"></i></span>
                                        </div>
                                        <div>
                                            <h4 class="fs-5 fw-bold text-dark">Phone</h4>
                                            <a class="text-muted hover-primary text-decoration-none" href="tel:+1234567890">+1 (234) 567-890</a>
                                        </div>
                                    </div>
                                     
                            
                            
                            <div class="mt-3">
                                <h3 class="h4 fw-bold">Our Location</h3>
                                <div class="mt-4">
                                    <div class="ratio ratio-16x9 rounded-3 overflow-hidden">
                                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.256247926136!2d-73.98785368459395!3d40.75629997932714!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25855c6434221%3A0x71b623cf50b31e9!2sTimes%20Square!5e0!3m2!1sen!2sus!4v1678886422538!5m2!1sen!2sus" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                                    </div>
                                </div>
                            </div>
                          
                    
                </div>    
            </div>
        </div>
    </section>
    

    <?php include 'includes/footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/script.js"></script>
    <script>
document.querySelector('form').addEventListener('submit', function(e) {
    let hasError = false;
    const fields = ['name', 'email', 'subject', 'message'];
    
    fields.forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (!input.value.trim()) {
            hasError = true;
            input.classList.add('is-invalid');
        } else {
            input.classList.remove('is-invalid');
        }
    });

    // Email validation
    const emailInput = document.querySelector('[name="email"]');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(emailInput.value.trim())) {
        hasError = true;
        emailInput.classList.add('is-invalid');
    }

    if (hasError) {
        e.preventDefault();
    }
});
</script>
</body>
</html>

