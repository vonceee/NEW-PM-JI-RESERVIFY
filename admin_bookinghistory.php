<?php
session_start();
include_once 'database.php';
// Assuming the admin's name is stored in the session after login
$admin_name = isset($_SESSION['fullname']) ? $_SESSION['fullname'] : 'Admin';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin_login.php');
    exit();
}

// Get all reservations and their associated booking details
$sql = "
    SELECT r.reservation_id, r.status, 
           bs.first_name, bs.middle_name, bs.last_name, bs.email, 
           bs.event_type, bs.event_place, bs.photo_size_layout, 
           bs.start_time, bs.end_time,
           bs.payment_method
    FROM reservation r
    INNER JOIN booking_summary bs ON r.reservation_id = bs.reservation_id
    ORDER BY r.reservation_id ASC
";
$result = $conn->query($sql);

// Handle database error
if (!$result) {
    echo "Error: " . $conn->error;
    exit();
}

$bookingData = []; // Array to store the booking details
while ($row = $result->fetch_assoc()) {
    // Format reservation number (Example: PMJI-YYYYMMDD-CUST001)
    $reservation_number = "PMJI-" . date("Ymd") . "-CUST" . str_pad($row['reservation_id'], 3, "0", STR_PAD_LEFT);
    $bookingData[$reservation_number] = [
        'email' => $row['email'],
        'event_type' => $row['event_type'],
        'event_place' => $row['event_place'],
        'photo_size_layout' => $row['photo_size_layout'],
        'start_time' => $row['start_time'],
        'end_time' => $row['end_time'],
        'payment_method' => $row['payment_method'],
        'status' => $row['status']
    ];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin PM&JI Reservify</title>
    <link rel="stylesheet" href="admin_dashboard.css?v=1.1">
    <link rel="stylesheet" href="admin_bookinghistory.css">
    <link rel="stylesheet" href="admin_profile.css?v=1.1">
    <link rel="stylesheet" href="admin_bookings.css?v=1.1">
    <link rel="stylesheet" href="admin_payments.css?v=1.1">
    <!-- Include FontAwesome CDN (if not already included) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="admin-dashboard">
    <aside class="sidebar">
        <div class="logo">
            <img src="images/reservify_logo.png" alt="Reservify Logo">
            <p>Hello, Admin!</p>
        </div>
        <nav>
            <ul>
                <li class="dashboard-item">
                    <a href="admin_dashboard.php" style="display: flex; align-items: center; gap: 7px;">
                        <img src="images/home.png" alt="Home Icon">
                        <span style="margin-left: 1px; margin-top: 4px; color: black;">Dashboard</span>
                    </a>
                </li>
            </ul>
            <hr class="divider">
            <ul>
                <li>
                    <a href="admin_bookings.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                    <span>Bookings</span>
                    <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
                <li>
                    <a href="admin_payments1.php" style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                    <span>Payments</span>
                    <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
                <li>
                    <a href="admin_payments.php"style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                    <span>Payment Records</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
                <li>
                    <a href="admin_bookinghistory.php"style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                    <span>Booking History</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
                <li>
                    <a href="admin_managefeedback.php"style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                    <span>Manage Feedback</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
                <li>
                    <a href="admin_calendar.php"style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                    <span>Calendar</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
            </ul>
            <hr class="divider">
            <ul>
                <li>
                    <a href="admin_manageinq.php"style="text-decoration: none; color: black; display: flex; justify-content: space-between; align-items: center;">
                    <span>Manage Inquiries</span>
                        <img class="click-here" src="images/click_here.png.png" alt="Click Here">
                    </a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="content">
        <header>
            <!-- Header Section -->
            <div class="header">
                <!-- Booking History Title -->
                <h1 style="color: black;">Booking History</h1>
                <!-- Notification Bell and Profile -->
                <div class="header-right">
                    <div class="search-bar-container">
                        <input type="text" id="searchBar" placeholder="Search bookings..." onkeyup="searchBookings()">
                    </div>
                    <!-- Notification part -->
                    <div class="notification-container">
                        <a href="admin_view_notification.php">
                            <i class="fas fa-envelope" id="notif-icon"></i>
                        </a>
                    </div>
                    <!-- Profile Icon -->
                    <div class="profile-container">
                        <img class="profile-icon" src="images/user_logo.png" alt="Profile Icon" onclick="toggleDropdown()">
                        <div id="profile-dropdown" class="dropdown">
                            <p class="dropdown-header"><?php echo htmlspecialchars($admin_name); ?></p>
                            <hr>
                            <ul>
                                <li><a href="admin_profile.php">Profile</a></li>
                                <li><a href="admin_activitylog.php">Activity Log</a></li>
                            </ul>
                            <hr>
                            <a class="logout" href="?logout">Logout</a>
                        </div>
                    </div>
                </div>

                <!-- Bootstrap Modal -->
                <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h2 class="modal-title" id="bookingModalLabel">Booking Details</h2>
                            </div>
                            <div class="modal-body">
                                <p><strong>Email:</strong> <span id="modalEmail"></span></p>
                                <p><strong>Event Type:</strong> <span id="modalEventType"></span></p>
                                <p><strong>Event Place:</strong> <span id="modalEventPlace"></span></p>
                                <p><strong>Start Time:</strong> <span id="modalStartTime"></span></p>
                                <p><strong>End Time:</strong> <span id="modalEndTime"></span></p>
                                <p><strong>Payment Method:</strong> <span id="modalPaymentMethod"></span></p>
                                <p><strong>Status:</strong> <span id="modalStatus"></span></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Booking List -->
        <div class="booking-list">
            <?php foreach ($bookingData as $reservation_number => $details) { ?>
                <div class="booking-item" onclick="showDetails('<?php echo $reservation_number; ?>')">
                    <?php echo $reservation_number; ?>
                </div>
            <?php } ?>
        </div>
    </main>
</div>
<script>
    // Booking details data
    const bookingData = <?php echo json_encode($bookingData); ?>;

    // Helper function: format a date/time string to 12-hour format (e.g. "April 2, 2025, 11:00 AM")
    function formatTo12Hour(dateString) {
        if (!dateString) return 'N/A';  // fallback if empty
        let dateObj = new Date(dateString);
        
        // Kung hindi valid ang dateObj (hal. maling format), ibabalik na lang ang original string
        if (isNaN(dateObj.getTime())) {
            return dateString;
        }

        let options = {
            month: 'long',
            day: 'numeric',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        };
        return dateObj.toLocaleString('en-US', options);
    }

    function showDetails(reservationNumber) {
        const bookingDetails = bookingData[reservationNumber];
        if (bookingDetails) {
            // Convert start_time and end_time to 12-hour format
            let startFormatted = formatTo12Hour(bookingDetails.start_time);
            let endFormatted   = formatTo12Hour(bookingDetails.end_time);

            // Populate modal fields with booking details
            document.getElementById("modalEmail").textContent       = bookingDetails.email;
            document.getElementById("modalEventType").textContent   = bookingDetails.event_type;
            document.getElementById("modalEventPlace").textContent  = bookingDetails.event_place;
            document.getElementById("modalStartTime").textContent   = startFormatted;
            document.getElementById("modalEndTime").textContent     = endFormatted;
            document.getElementById("modalPaymentMethod").textContent = bookingDetails.payment_method;
            document.getElementById("modalStatus").textContent       = bookingDetails.status;

            // Show the Bootstrap modal
            let bookingModal = new bootstrap.Modal(document.getElementById("bookingModal"));
            bookingModal.show();
        } else {
            alert("No details found for this reservation.");
        }
    }

    function toggleDropdown() {
        const dropdown = document.getElementById('profile-dropdown');
        dropdown.classList.toggle('show');
    }

    window.onclick = function(event) {
        if (!event.target.matches('.profile-icon')) {
            const dropdown = document.getElementById('profile-dropdown');
            if (dropdown && dropdown.classList.contains('show')) {
                dropdown.classList.remove('show');
            }
        }
    };

    // Updated search function para sa booking list
    function searchBookings() {
        const input = document.getElementById("searchBar").value.toUpperCase();
        const bookingItems = document.getElementsByClassName("booking-item");

        for (let i = 0; i < bookingItems.length; i++) {
            const textValue = bookingItems[i].textContent || bookingItems[i].innerText;
            bookingItems[i].style.display = textValue.toUpperCase().indexOf(input) > -1 ? "" : "none";
        }
    }
</script>


<style>
/* Modal Styling */
.modal-content {
    border-radius: 12px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    background: #f9f9f9;
}

.modal-header {
    color: white;
    font-weight: bold;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
    text-align: center;
}
.modal-title {
    text-align: center;
    width: 100%;
    color: black !important;
}
.modal-body {
    padding: 20px;
    font-size: 16px;
    color: #333;
}

.modal-footer {
    border-top: none;
}

.modal-footer .btn {
    background-color: #007bff;
    color: white;
    border-radius: 8px;
    padding: 8px 16px;
}

.modal-footer .btn:hover {
    background-color: #0056b3;
}
#notif-icon {
    color: black;
    font-size: 24px;
}
</style>
</body>
</html>
