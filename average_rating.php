<?php
include('includes/config.php');


$vehicle_id = $_GET['vehicle_id'];

$stmt = $conn->prepare("SELECT AVG(rating) as avg_rating FROM tblreviews WHERE vehicle_id = ?");
$stmt->execute([$vehicle_id]);
$result = $stmt->fetch();

$avg_rating = $result['avg_rating'] ? number_format($result['avg_rating'], 1) : "No ratings yet";
echo "<p>Average Rating: {$avg_rating} / 5</p>";
?>
