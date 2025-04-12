<?php
session_start();
include('includes/config.php');

if (!isset($_SESSION['user_id'])) {
    echo "<p>Please <a href='login.php'>log in</a> to leave a review.</p>";
    exit;
}

$vehicle_id = $_GET['vehicle_id'];
$user_id = $_SESSION['user_id'];

// Check if user booked this car
$check_booking = $conn->prepare("SELECT * FROM tblbooking WHERE user_id = ? AND VehicleId = ?");
$check_booking->execute([$user_id, $vehicle_id]);
$booking = $check_booking->fetch();

if (!$booking) {
    echo "<p>You need to book this car before leaving a review.</p>";
    exit;
}

// Check if user already reviewed this car
$check_review = $conn->prepare("SELECT * FROM tblreviews WHERE user_id = ? AND vehicle_id = ?");
$check_review->execute([$user_id, $vehicle_id]);
$review = $check_review->fetch();

if ($review) {
    echo "<p>You have already reviewed this car.</p>";
    exit;
}
?>

<form id="reviewForm">
    <input type="hidden" name="vehicle_id" value="<?php echo $vehicle_id; ?>">
    <label>Rating (1-5):</label>
    <select name="rating">
        <option value="1">1 - Poor</option>
        <option value="2">2 - Fair</option>
        <option value="3">3 - Good</option>
        <option value="4">4 - Very Good</option>
        <option value="5">5 - Excellent</option>
    </select>
    <label>Review:</label>
    <textarea name="review" required></textarea>
    <button type="submit">Submit Review</button>
</form>

<div id="message"></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
    $("#reviewForm").submit(function(e){
        e.preventDefault();
        $.ajax({
            type: "POST",
            url: "submit_review.php",
            data: $(this).serialize(),
            success: function(response){
                $("#message").html(response);
                $("#reviewForm")[0].reset();
            }
        });
    });
});
</script>
