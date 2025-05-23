<?php
$error_message = ""; // Initialize the error message variable

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once "database.php";

    // Retrieve form data
    $Email = $_POST['Email']; // Get the email from the form
    $Password = $_POST['newPassword']; // New password
    $Confirm_Password = $_POST['confirmPassword']; // Confirm password

    // Check if the new passwords match
    if ($Password !== $Confirm_Password) {
        $error_message = "Passwords do not match.";
    } else {
        // Hash the new password
        $hashedPassword = password_hash($Password, PASSWORD_DEFAULT);

        // Check if the email exists in the database
        $sqlCheck = "SELECT * FROM test_registration WHERE Email = ?";
        $stmtCheck = mysqli_stmt_init($conn);

        if (mysqli_stmt_prepare($stmtCheck, $sqlCheck)) {
            // Bind and execute the query
            mysqli_stmt_bind_param($stmtCheck, "s", $Email);
            mysqli_stmt_execute($stmtCheck);
            $result = mysqli_stmt_get_result($stmtCheck);

            // Debugging: Check if the email exists in the database
            if (mysqli_num_rows($result) == 0) {
                $error_message = "No user found with this email.";
            } else {
                // Update the password in the database
                $sql = "UPDATE test_registration SET Password = ? WHERE Email = ?";
                $stmt = mysqli_stmt_init($conn);

                if (mysqli_stmt_prepare($stmt, $sql)) {
                    // Bind parameters and execute
                    mysqli_stmt_bind_param($stmt, "ss", $hashedPassword, $Email);
                    if (mysqli_stmt_execute($stmt)) {
                        // Success - Redirect to login page
                        echo "<script>
                                alert('The password has been changed successfully.');
                                window.location.href = 'login.php';
                              </script>";
                        exit(); // Ensure no further code is executed
                    } else {
                        $error_message = "Error updating password. Please try again later.";
                    }
                } else {
                    $error_message = "Error processing the request. Please try again later.";
                }
            }
        } else {
            $error_message = "Error checking email. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PM&JI Reservify - Reset Password</title>
    <link rel="stylesheet" href="newpassword.css">
</head>
<body>
    <form action="" method="POST" id="resetPasswordForm">
        <h1>Reset Password</h1>

        <div class="text">
            <p>Enter your email and new password.</p>
        </div>

        <!-- Manual Email input -->
        <div class="input-box">
            <input type="email" name="Email" placeholder="Enter your email" required>
        </div>

        <div class="input-box">
            <input type="password" name="newPassword" placeholder="New Password" required>
        </div>
        <div class="input-box">
            <input type="password" name="confirmPassword" placeholder="Confirm New Password" required>
        </div>
        <button type="submit" class="btn">Reset Password</button>

        <!-- Error message displayed here -->
        <?php if (!empty($error_message)) : ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
    </form>
</body>
</html>
