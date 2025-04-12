<?php
session_start();
include('includes/config.php'); // Database connection

// Ensure the user is logged in
if (!isset($_SESSION['login'])) {
    $_SESSION['message'] = "You must be logged in to submit a review.";
    header("Location: my-booking.php");
    exit();
}

$user_email = $_SESSION['login']; // Get user email
$car_id = $_POST['car_id'] ?? null; // Get car ID from form
$rating = $_POST['rating'];
$review = trim($_POST['review']);

// Validate input
if (!$car_id || !$rating || empty($review)) {
    $_SESSION['message'] = "All fields are required!";
    header("Location: my-booking.php");
    exit();
}

// Get user ID, FullName (username), and email from tblusers
$userQuery = "SELECT id, FullName, emailid FROM tblusers WHERE emailid = :email";
$stmt = $dbh->prepare($userQuery);
$stmt->bindParam(':email', $user_email, PDO::PARAM_STR);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $_SESSION['message'] = "Error: User not found!";
    header("Location: my-booking.php");
    exit();
}

$user_id = $user['id'];
$username = $user['FullName'];
$email = $user['emailid']; // Email from database

// Check if the user booked this car
$checkBookingQuery = "SELECT id FROM tblbooking WHERE userEmail = :email AND VehicleId = :car_id AND Status = 1";
$stmt = $dbh->prepare($checkBookingQuery);
$stmt->bindParam(':email', $user_email, PDO::PARAM_STR);
$stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
$stmt->execute();
$bookingResult = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$bookingResult) {
    $_SESSION['message'] = "You must book the car before you can review it.";
    header("Location: my-booking.php");
    exit();
}

// Check if user already reviewed this car
$checkReviewQuery = "SELECT id FROM tblreviews WHERE user_id = :user_id AND vehicle_id = :car_id";
$stmt = $dbh->prepare($checkReviewQuery);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $_SESSION['message'] = "You have already reviewed this car.";
    header("Location: my-booking.php");
    exit();
}

// Insert the review into tblreviews
$insertQuery = "INSERT INTO tblreviews (user_id, username, user_email, vehicle_id, rating, review, review_date) 
                VALUES (:user_id, :username, :email, :car_id, :rating, :review, NOW())";
$stmt = $dbh->prepare($insertQuery);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindParam(':username', $username, PDO::PARAM_STR);
$stmt->bindParam(':email', $email, PDO::PARAM_STR);
$stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
$stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
$stmt->bindParam(':review', $review, PDO::PARAM_STR);

if ($stmt->execute()) {
    $_SESSION['message'] = "Review submitted successfully!";
} else {
    $_SESSION['message'] = "Error submitting review. Please try again.";
}

header("Location: my-booking.php");
exit();
?>
