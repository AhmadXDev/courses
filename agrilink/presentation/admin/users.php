<?php
require_once '../../config.php';
require_once '../../business/SessionManager.php';
require_once '../../business/UserManager.php';

// Restrict access to admins only
SessionManager::requireAdmin();

// Initialize manager
$userManager = new UserManager();

// Get filter parameters
$role = isset($_GET['role']) ? $_GET['role'] : '';

// Check for success or error messages
$message = '';
$alertType = '';

if (isset($_GET['success'])) {
    $message = $_GET['success'];
    $alertType = 'success';
} elseif (isset($_GET['error'])) {
    $message = $_GET['error'];
    $alertType = 'danger';
}

// Get all users
$users = $userManager->getAllUsers();

// Apply role filter if specified
if (!empty($role)) {
    $users = array_filter($users, function($user) use ($role) {
        return $user['role'] === $role;
    });
}

// Load layout files
require_once '../../presentation/layouts/header.php';
require_once '../../presentation/layouts/navigation.php';
?>

<main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Admin - Manage Users</h1>
        <div>
            <a href="<?php echo BASE_URL; ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Home
            </a>
        </div>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $alertType; ?> alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    
    <!-- Filter Options -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Filter Users</h5>
        </div>
        <div class="card-body">
            <form action="" method="GET" class="row g-3">
                <!-- Role Filter -->
                <div class="col-md-3">
                    <label for="role" class="form-label">User Role</label>
                    <select class="form-select" id="role" name="role" onchange="this.form.submit()">
                        <option value="">All Roles</option>
                        <option value="customer" <?php echo $role === 'customer' ? 'selected' : ''; ?>>Customers</option>
                        <option value="vendor" <?php echo $role === 'vendor' ? 'selected' : ''; ?>>Vendors</option>
                        <option value="admin" <?php echo $role === 'admin' ? 'selected' : ''; ?>>Administrators</option>
                    </select>
                </div>
                
                <!-- Reset Button -->
                <div class="col-md-3 d-flex align-items-end">
                    <a href="<?php echo BASE_URL; ?>presentation/admin/users.php" class="btn btn-outline-secondary">
                        <i class="fas fa-undo me-2"></i>Reset Filters
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Users List -->
    <div class="card">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Users (<?php echo count($users); ?>)</h5>
        </div>
        <div class="card-body">
            <?php if (empty($users)): ?>
                <p class="text-muted">No users found matching your criteria.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Registration Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?php echo $user['id']; ?></td>
                                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td>
                                        <?php 
                                        $badgeClass = 'bg-secondary';
                                        if ($user['role'] === 'admin') {
                                            $badgeClass = 'bg-danger';
                                        } elseif ($user['role'] === 'vendor') {
                                            $badgeClass = 'bg-primary';
                                        } elseif ($user['role'] === 'customer') {
                                            $badgeClass = 'bg-success';
                                        }
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>">
                                            <?php echo ucfirst($user['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                    <td>
                                        <?php if (SessionManager::getCurrentUserId() != $user['id']): ?>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        data-bs-toggle="modal" data-bs-target="#editModal<?php echo $user['id']; ?>">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" 
                                                        data-bs-toggle="modal" data-bs-target="#deleteModal<?php echo $user['id']; ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            
                                            <!-- Edit Modal -->
                                            <div class="modal fade" id="editModal<?php echo $user['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Edit User</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="user_update.php" method="POST">
                                                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label for="name<?php echo $user['id']; ?>" class="form-label">Name</label>
                                                                    <input type="text" class="form-control" id="name<?php echo $user['id']; ?>" 
                                                                           name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="email<?php echo $user['id']; ?>" class="form-label">Email</label>
                                                                    <input type="email" class="form-control" id="email<?php echo $user['id']; ?>" 
                                                                           name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="role<?php echo $user['id']; ?>" class="form-label">Role</label>
                                                                    <select class="form-select" id="role<?php echo $user['id']; ?>" name="role">
                                                                        <option value="customer" <?php echo $user['role'] === 'customer' ? 'selected' : ''; ?>>Customer</option>
                                                                        <option value="vendor" <?php echo $user['role'] === 'vendor' ? 'selected' : ''; ?>>Vendor</option>
                                                                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="password<?php echo $user['id']; ?>" class="form-label">New Password (leave blank to keep current)</label>
                                                                    <input type="password" class="form-control" id="password<?php echo $user['id']; ?>" 
                                                                           name="password">
                                                                    <div class="form-text">Minimum 6 characters. Leave empty to keep current password.</div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Delete Modal -->
                                            <div class="modal fade" id="deleteModal<?php echo $user['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Confirm Delete</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p>Are you sure you want to delete the user: <strong><?php echo htmlspecialchars($user['name']); ?></strong>?</p>
                                                            <p class="text-danger">This action cannot be undone and will remove all associated data.</p>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <a href="<?php echo BASE_URL; ?>presentation/admin/user_delete.php?id=<?php echo $user['id']; ?>" 
                                                               class="btn btn-danger">Delete</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">Current User</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script>
    // Fix for modal hover issues
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to all modal triggers
        document.querySelectorAll('[data-bs-toggle="modal"]').forEach(trigger => {
            trigger.addEventListener('click', function() {
                // Add a class to the body when a modal is opened
                document.body.classList.add('modal-open-no-hover');
            });
        });
        
        // Remove the class when modals are closed
        document.addEventListener('hidden.bs.modal', function() {
            document.body.classList.remove('modal-open-no-hover');
        });
        
        // Move all modals to the end of body to prevent parent hover effects
        const modals = Array.from(document.querySelectorAll('.modal'));
        modals.forEach(modal => {
            document.body.appendChild(modal);
        });
    });
</script>

<?php require_once '../../presentation/layouts/footer.php'; ?> 