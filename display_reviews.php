<?php
include('includes/config.php');

if (!isset($_GET['vhid']) || !is_numeric($_GET['vhid'])) {
    echo "<p>No reviews found for this vehicle.</p>";
    exit;
}

$vehicle_id = intval($_GET['vhid']);

$sql = "SELECT username, rating, review, review_date FROM tblreviews WHERE vehicle_id = :vehicle_id ORDER BY review_date DESC";
$query = $dbh->prepare($sql);
$query->bindParam(':vehicle_id', $vehicle_id, PDO::PARAM_INT);
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

if ($query->rowCount() > 0) {
    echo '<div class="review-list">';
    foreach ($results as $review) {
        echo '<div class="single-review">';
        echo '<h4>' . htmlentities($review->username) . '</h4>';
        echo '<p><strong>Rating:</strong> ' . str_repeat("⭐", $review->rating) . '</p>';
        echo '<p>' . htmlentities($review->review) . '</p>';
        echo '<small><em>' . date('F j, Y', strtotime($review->review_date)) . '</em></small>';
        echo '</div><hr>';
    }
    echo '</div>';
} else {
    echo '<p>No reviews available for this vehicle.</p>';
}
?>
