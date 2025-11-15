<?php
require_once '../../config.php';
require_once '../../business/SessionManager.php';
require_once '../../business/UserManager.php';

// Redirect if already logged in
if (SessionManager::isLoggedIn()) {
    header('Location: ' . BASE_URL);
    exit;
}

$error = '';
$success = '';
$formData = [
    'name' => '',
    'email' => '',
    'role' => 'customer'
];

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formData['name'] = trim($_POST['name'] ?? '');
    $formData['email'] = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $formData['role'] = $_POST['role'] ?? 'customer';
    
    // Basic validation
    if (empty($formData['name']) || empty($formData['email']) || empty($password)) {
        $error = 'Please fill all required fields';
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        // Register user
        $userManager = new UserManager();
        $userData = [
            'name' => $formData['name'],
            'email' => $formData['email'],
            'password' => $password,
            'role' => $formData['role']
        ];
        
        $result = $userManager->registerUser($userData);
        
        if ($result['success']) {
            $success = $result['message'];
            $formData = [
                'name' => '',
                'email' => '',
                'role' => 'customer'
            ];
        } else {
            $error = $result['message'];
        }
    }
}

// Load layout files
require_once '../../presentation/layouts/header.php';
require_once '../../presentation/layouts/navigation.php';
?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-4">
                    <h2 class="text-center mb-4"><i class="fas fa-user-plus me-2"></i>Register</h2>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                            <p class="mt-2 mb-0">You can now <a href="<?php echo BASE_URL; ?>presentation/auth/login.php">login</a> with your credentials.</p>
                        </div>
                    <?php else: ?>
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($formData['name']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($formData['email']); ?>" required>
                                <div class="form-text">We'll never share your email with anyone else.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                    <span class="input-group-text" id="togglePassword" style="cursor:pointer;">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                                <div class="form-text">Password must be at least 6 characters long.</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                    <span class="input-group-text" id="toggleConfirmPassword" style="cursor:pointer;">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Register as</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role" id="roleCustomer" 
                                           value="customer" <?php echo $formData['role'] === 'customer' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="roleCustomer">
                                        Customer - I want to shop for local products
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="role" id="roleVendor" 
                                           value="vendor" <?php echo $formData['role'] === 'vendor' ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="roleVendor">
                                        Vendor - I want to sell my products
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">Register</button>
                            </div>
                        </form>
                    <?php endif; ?>
                    
                    <hr class="my-4">
                    
                    <div class="text-center">
                        <p class="mb-0">Already have an account? <a href="<?php echo BASE_URL; ?>presentation/auth/login.php">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once '../../presentation/layouts/footer.php'; ?>
<script>
    const togglePassword = document.getElementById('togglePassword');
    const password = document.getElementById('password');
    togglePassword.addEventListener('click', function () {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });

    const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
    const confirmPassword = document.getElementById('confirm_password');
    toggleConfirmPassword.addEventListener('click', function () {
        const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPassword.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
    });
</script> 
