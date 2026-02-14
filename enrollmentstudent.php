<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'student_enrollment_db');

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
$conn->query($sql);

// Select database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create students table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    course VARCHAR(100) NOT NULL,
    year_level VARCHAR(20) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $student_id = $conn->real_escape_string($_POST['student_id']);
                $first_name = $conn->real_escape_string($_POST['first_name']);
                $last_name = $conn->real_escape_string($_POST['last_name']);
                $email = $conn->real_escape_string($_POST['email']);
                $course = $conn->real_escape_string($_POST['course']);
                $year_level = $conn->real_escape_string($_POST['year_level']);
                
                $sql = "INSERT INTO students (student_id, first_name, last_name, email, course, year_level) 
                        VALUES ('$student_id', '$first_name', '$last_name', '$email', '$course', '$year_level')";
                
                if ($conn->query($sql)) {
                    $message = 'Student added successfully!';
                    $messageType = 'success';
                } else {
                    if ($conn->errno == 1062) {
                        $message = 'Student ID already exists!';
                        $messageType = 'error';
                    } else {
                        $message = 'Error adding student: ' . $conn->error;
                        $messageType = 'error';
                    }
                }
                break;
            
            case 'edit':
                $id = (int)$_POST['id'];
                $student_id = $conn->real_escape_string($_POST['student_id']);
                $first_name = $conn->real_escape_string($_POST['first_name']);
                $last_name = $conn->real_escape_string($_POST['last_name']);
                $email = $conn->real_escape_string($_POST['email']);
                $course = $conn->real_escape_string($_POST['course']);
                $year_level = $conn->real_escape_string($_POST['year_level']);
                
                $sql = "UPDATE students SET 
                        student_id='$student_id', 
                        first_name='$first_name', 
                        last_name='$last_name', 
                        email='$email', 
                        course='$course', 
                        year_level='$year_level' 
                        WHERE id=$id";
                
                if ($conn->query($sql)) {
                    $message = 'Student updated successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error updating student: ' . $conn->error;
                    $messageType = 'error';
                }
                break;
            
            case 'delete':
                $id = (int)$_POST['id'];
                $sql = "DELETE FROM students WHERE id=$id";
                
                if ($conn->query($sql)) {
                    $message = 'Student deleted successfully!';
                    $messageType = 'success';
                } else {
                    $message = 'Error deleting student: ' . $conn->error;
                    $messageType = 'error';
                }
                break;
        }
        header('Location: ' . $_SERVER['PHP_SELF'] . ($message ? '?msg=' . urlencode($message) . '&type=' . $messageType : ''));
        exit;
    }
}

// Get message from URL if exists
if (isset($_GET['msg'])) {
    $message = $_GET['msg'];
    $messageType = $_GET['type'] ?? 'info';
}

// Get student for editing
$editStudent = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $result = $conn->query("SELECT * FROM students WHERE id=$id");
    if ($result && $result->num_rows > 0) {
        $editStudent = $result->fetch_assoc();
    }
}

// Get all students
$students = $conn->query("SELECT * FROM students ORDER BY created_at DESC");
$studentCount = $students->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Enrollment Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #800020 0%, #4a0012 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, #800020 0%, #a0002a 100%);
            color: #FFD700;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
        }

        .logo-container {
            margin-bottom: 15px;
        }

        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #FFD700;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .logo:hover {
            transform: scale(1.05);
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .header p {
            font-size: 1.1em;
            color: #FFF8DC;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
            animation: slideIn 0.3s ease-out;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .card h2 {
            color: #800020;
            margin-bottom: 20px;
            font-size: 1.8em;
            border-bottom: 3px solid #FFD700;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #800020;
            font-weight: 600;
            font-size: 0.95em;
        }

        input, select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1em;
            transition: all 0.3s;
        }

        input:focus, select:focus {
            outline: none;
            border-color: #FFD700;
            box-shadow: 0 0 0 3px rgba(255, 215, 0, 0.2);
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #800020;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
        }

        .btn-edit {
            background: #4CAF50;
            color: white;
            padding: 8px 16px;
            font-size: 0.9em;
            margin-right: 5px;
        }

        .btn-edit:hover {
            background: #45a049;
        }

        .btn-delete {
            background: #f44336;
            color: white;
            padding: 8px 16px;
            font-size: 0.9em;
        }

        .btn-delete:hover {
            background: #da190b;
        }

        .btn-cancel {
            background: #999;
            color: white;
            margin-left: 10px;
        }

        .btn-cancel:hover {
            background: #777;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background: linear-gradient(135deg, #800020 0%, #a0002a 100%);
            color: #FFD700;
        }

        th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.9em;
            letter-spacing: 0.5px;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        tbody tr {
            transition: all 0.3s;
        }

        tbody tr:hover {
            background: #fff8dc;
        }

        .empty-state {
            text-align: center;
            padding: 50px;
            color: #999;
        }

        .empty-state svg {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .actions {
            display: flex;
            gap: 5px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #800020;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
        }

        .stat-card h3 {
            font-size: 2.5em;
            margin-bottom: 5px;
        }

        .stat-card p {
            font-size: 1em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            table {
                font-size: 0.9em;
            }

            .header h1 {
                font-size: 1.8em;
            }

            .actions {
                flex-direction: column;
            }

            .btn-edit, .btn-delete {
                width: 100%;
            }
        }

        /* Loading Overlay Styles */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 5px solid rgba(255, 215, 0, 0.3);
            border-top: 5px solid #FFD700;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        .loading-text {
            color: #FFD700;
            font-size: 1.2em;
            margin-top: 20px;
            font-weight: 600;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
        <div class="loading-text">Processing...</div>
    </div>

    <div class="container">
        <div class="header">
            <div class="logo-container">
                <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 80 80'%3E%3Ccircle cx='40' cy='40' r='38' fill='%23FFD700'/%3E%3Ctext x='40' y='50' font-family='Arial' font-size='30' font-weight='bold' fill='%23800020' text-anchor='middle'%3ESEM%3C/text%3E%3C/svg%3E" alt="Logo" class="logo" id="schoolLogo" title="Click to upload your logo">
            </div>
            <h1>Student Enrollment Management</h1>
            <p>Manage Your Student Records Efficiently</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="stats">
            <div class="stat-card">
                <h3><?php echo $studentCount; ?></h3>
                <p>Total Students</p>
            </div>
        </div>

        <!-- Add/Edit Student Form -->
        <div class="card">
            <h2><?php echo $editStudent ? 'Edit Student Record' : 'Add New Student'; ?></h2>
            <form method="POST" action="">
                <input type="hidden" name="action" value="<?php echo $editStudent ? 'edit' : 'add'; ?>">
                <?php if ($editStudent): ?>
                    <input type="hidden" name="id" value="<?php echo $editStudent['id']; ?>">
                <?php endif; ?>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Student ID *</label>
                        <input type="text" name="student_id" required value="<?php echo $editStudent ? htmlspecialchars($editStudent['student_id']) : ''; ?>" placeholder="e.g., 20232024346">
                    </div>
                    <div class="form-group">
                        <label>First Name *</label>
                        <input type="text" name="first_name" required value="<?php echo $editStudent ? htmlspecialchars($editStudent['first_name']) : ''; ?>" placeholder="Enter first name">
                    </div>
                    <div class="form-group">
                        <label>Last Name *</label>
                        <input type="text" name="last_name" required value="<?php echo $editStudent ? htmlspecialchars($editStudent['last_name']) : ''; ?>" placeholder="Enter last name">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email Address *</label>
                        <input type="email" name="email" required value="<?php echo $editStudent ? htmlspecialchars($editStudent['email']) : ''; ?>" placeholder="student@example.com">
                    </div>
                    <div class="form-group">
                        <label>Course *</label>
                        <input type="text" name="course" required value="<?php echo $editStudent ? htmlspecialchars($editStudent['course']) : ''; ?>" placeholder="e.g., Bachelor of Science in Information Systems">
                    </div>
                    <div class="form-group">
                        <label>Year Level *</label>
                        <select name="year_level" required>
                            <option value="">Select Year</option>
                            <option value="1st Year" <?php echo ($editStudent && $editStudent['year_level'] === '1st Year') ? 'selected' : ''; ?>>1st Year</option>
                            <option value="2nd Year" <?php echo ($editStudent && $editStudent['year_level'] === '2nd Year') ? 'selected' : ''; ?>>2nd Year</option>
                            <option value="3rd Year" <?php echo ($editStudent && $editStudent['year_level'] === '3rd Year') ? 'selected' : ''; ?>>3rd Year</option>
                            <option value="4th Year" <?php echo ($editStudent && $editStudent['year_level'] === '4th Year') ? 'selected' : ''; ?>>4th Year</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editStudent ? '✓ Update Student' : '+ Add Student'; ?>
                    </button>
                    <?php if ($editStudent): ?>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-cancel">Cancel</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Student List -->
        <div class="card">
            <h2>Student Records (<?php echo $studentCount; ?>)</h2>
            
            <?php if ($studentCount === 0): ?>
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <h3>No Students Enrolled Yet</h3>
                    <p>Add your first student using the form above</p>
                </div>
            <?php else: ?>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Course</th>
                                <th>Year Level</th>
                                <th>Enrolled Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($student = $students->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['student_id']); ?></td>
                                    <td><?php echo htmlspecialchars($student['first_name'] . ' ' . $student['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($student['email']); ?></td>
                                    <td><?php echo htmlspecialchars($student['course']); ?></td>
                                    <td><?php echo htmlspecialchars($student['year_level']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($student['created_at'])); ?></td>
                                    <td>
                                        <div class="actions">
                                            <a href="?edit=<?php echo $student['id']; ?>" class="btn btn-edit">Edit</a>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this student record?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $student['id']; ?>">
                                                <button type="submit" class="btn btn-delete">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Allow users to upload their own logo
        document.getElementById('schoolLogo').addEventListener('click', function() {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        document.getElementById('schoolLogo').src = event.target.result;
                        localStorage.setItem('schoolLogo', event.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            };
            input.click();
        });

        // Load saved logo on page load
        window.addEventListener('load', function() {
            const savedLogo = localStorage.getItem('schoolLogo');
            if (savedLogo) {
                document.getElementById('schoolLogo').src = savedLogo;
            }
        });

        // Auto-hide alert messages after 5 seconds
        const alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        }

        // Show loading overlay when form is submitted
        const studentForm = document.querySelector('form[method="POST"]');
        if (studentForm) {
            studentForm.addEventListener('submit', function() {
                document.getElementById('loadingOverlay').classList.add('active');
            });
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>