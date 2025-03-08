let currentIndex = 0;

function showSlides() {
    const slides = document.querySelectorAll(".slide");
    const dots = document.querySelectorAll(".dot");

    // Hide all slides and remove active class from dots
    slides.forEach(slide => (slide.style.display = "none"));
    dots.forEach(dot => dot.classList.remove("active"));

    // Show current slide and highlight corresponding dot
    currentIndex++;
    if (currentIndex > slides.length) currentIndex = 1;
    slides[currentIndex - 1].style.display = "block";
    dots[currentIndex - 1].classList.add("active");

    setTimeout(showSlides, 5000); // Change slide every 5 seconds
}

function currentSlide(index) {
    currentIndex = index - 1;
    showSlides();
}

function changeSlide(n) {
    const slides = document.querySelectorAll(".slide");
    const dots = document.querySelectorAll(".dot");

    currentIndex += n;
    if (currentIndex < 1) currentIndex = slides.length;
    if (currentIndex > slides.length) currentIndex = 1;

    slides.forEach(slide => (slide.style.display = "none"));
    dots.forEach(dot => dot.classList.remove("active"));

    slides[currentIndex - 1].style.display = "block";
    dots[currentIndex - 1].classList.add("active");
}

document.addEventListener("DOMContentLoaded", function() {
    // Show slides
    showSlides();

    // Define the event types
    const eventTypes = [
        "Wedding",
        "Reunion",
        "Baptism",
        "Birthday",
        "Company Event",
        "Others"
    ];

    // Get the select element and the textarea container
    const selectElement = document.getElementById("eventType");
    const otherEventBox = document.getElementById("otherEventBox");

    // Loop through the event types and create an option for each
    eventTypes.forEach(function(event) {
        const option = document.createElement("option");
        option.value = event.toLowerCase().replace(/\s+/g, ''); // Convert to lowercase and remove spaces for value
        option.textContent = event;
        selectElement.appendChild(option);
    });

    // Show or hide the 'Other' event textarea
    selectElement.addEventListener("change", function() {
        if (selectElement.value === "others") {
            otherEventBox.style.display = "block";
        } else {
            otherEventBox.style.display = "none";
        }
    });

    // Notification bell functionality
    const notificationBell = document.querySelector(".notification-bell");
    const notificationDropdown = document.querySelector(".notification-dropdown");

    // Make an AJAX call to get the customer's notifications
    fetch('get_notifications.php') // Assuming you have a PHP script to fetch notifications
        .then(response => response.json())
        .then(notifications => {
            // Populate the notification dropdown with real messages from the backend
            notifications.forEach(function(notification) {
                const notificationElement = document.createElement("div");
                notificationElement.classList.add("notification");
                notificationElement.innerHTML = `${notification.message} <span class="time">${new Date(notification.created_at).toLocaleTimeString()}</span>`;
                notificationDropdown.appendChild(notificationElement);
            });
        })
        .catch(error => {
            console.error('Error fetching notifications:', error);
        });

    // Toggle the notification dropdown when the bell is clicked
    notificationBell.addEventListener("click", function() {
        notificationDropdown.classList.toggle("show");
    });
});
