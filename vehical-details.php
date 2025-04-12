<?php 
session_start();
include('includes/config.php');
error_reporting(0);
if(isset($_POST['submit']))
{
$fromdate=$_POST['fromdate'];
$todate=$_POST['todate']; 
$message=$_POST['message'];
$useremail=$_SESSION['login'];
$status=0;
$vhid=$_GET['vhid'];
$bookingno=mt_rand(100000000, 999999999);
$ret="SELECT * FROM tblbooking where (:fromdate BETWEEN date(FromDate) and date(ToDate) || :todate BETWEEN date(FromDate) and date(ToDate) || date(FromDate) BETWEEN :fromdate and :todate) and VehicleId=:vhid";
$query1 = $dbh -> prepare($ret);
$query1->bindParam(':vhid',$vhid, PDO::PARAM_STR);
$query1->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
$query1->bindParam(':todate',$todate,PDO::PARAM_STR);
$query1->execute();
$results1=$query1->fetchAll(PDO::FETCH_OBJ);

if($query1->rowCount()==0)
{

$sql="INSERT INTO  tblbooking(BookingNumber,userEmail,VehicleId,FromDate,ToDate,message,Status) VALUES(:bookingno,:useremail,:vhid,:fromdate,:todate,:message,:status)";
$query = $dbh->prepare($sql);
$query->bindParam(':bookingno',$bookingno,PDO::PARAM_STR);
$query->bindParam(':useremail',$useremail,PDO::PARAM_STR);
$query->bindParam(':vhid',$vhid,PDO::PARAM_STR);
$query->bindParam(':fromdate',$fromdate,PDO::PARAM_STR);
$query->bindParam(':todate',$todate,PDO::PARAM_STR);
$query->bindParam(':message',$message,PDO::PARAM_STR);
$query->bindParam(':status',$status,PDO::PARAM_STR);
$query->execute();
$lastInsertId = $dbh->lastInsertId();
if($lastInsertId)
{
echo "<script>alert('Booking successfull.');</script>";
echo "<script type='text/javascript'> document.location = 'my-booking.php'; </script>";
}
else 
{
echo "<script>alert('Something went wrong. Please try again');</script>";
 echo "<script type='text/javascript'> document.location = 'car-listing.php'; </script>";
} }  else{
 echo "<script>alert('Car already booked for these days');</script>"; 
 echo "<script type='text/javascript'> document.location = 'car-listing.php'; </script>";
}
}
// check booking before requesting
$driver_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_with_driver']) && isset($_POST['vhid'])) {
    $user_email = $_SESSION['login'];
    $vhid = $_POST['vhid']; 
    $from_date = date('Y-m-d');
    $to_date = date('Y-m-d', strtotime('+2 days'));
    $message = 'Requesting a driver';

    // Check if user has a confirmed booking
    $sql_check_booking = "SELECT * FROM tblbooking WHERE useremail = :email AND vehicleId = :vhid AND status = 'Confirmed'";
    $query_check = $dbh->prepare($sql_check_booking);
    $query_check->bindParam(':email', $useremail, PDO::PARAM_STR);
    $query_check->bindParam(':vhid', $vhid, PDO::PARAM_INT);
    $query_check->execute();

    if ($query_check->rowCount() == 0) {
        $driver_message = "You need to book a car first before requesting a driver.";
    } else {
        $sql = "INSERT INTO tbl_driver_requests (user_email, VehicleId, from_date, to_date, message, status) 
                VALUES (:email, :vhid, :from_date, :to_date, :message, 'Pending')";
        $query = $dbh->prepare($sql);
        $query->bindParam(':email', $user_email, PDO::PARAM_STR);
        $query->bindParam(':vhid', $vhid, PDO::PARAM_INT);
        $query->bindParam(':from_date', $from_date, PDO::PARAM_STR);
        $query->bindParam(':to_date', $to_date, PDO::PARAM_STR);
        $query->bindParam(':message', $message, PDO::PARAM_STR);
        $query->execute();

        $_SESSION['driver_sent'] = "Driver has been sent successfully!";
        header("Location: vehical_details.php?vhid=" . $vhid);
        exit();
    }
}

// Show success message if it was set
if (isset($_SESSION['driver_sent'])) {
  echo "<div class='alert alert-success'>" . htmlentities($_SESSION['driver_sent']) . "</div>";
  unset($_SESSION['driver_sent']); // Clear it after displaying
  print_r($_SESSION); // DEBUG — check what session looks like
}

// Grab vehicle ID from URL
if (isset($_GET['vhid'])) {
    $car_id = $_GET['vhid'];
} else {
    echo "Error: Vehicle ID (vhid) is missing!";
    exit();
}
?>


<!DOCTYPE HTML>
<html lang="en">
<head>

<title>Car Rental | Vehicle Details</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<!--Custome Style -->
<link rel="stylesheet" href="assets/css/style.css" type="text/css">
<!--OWL Carousel slider-->
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<!--slick-slider -->
<link href="assets/css/slick.css" rel="stylesheet">
<!--bootstrap-slider -->
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<!--FontAwesome Font Style -->
<link href="assets/css/font-awesome.min.css" rel="stylesheet">

<!-- SWITCHER -->
		<link rel="stylesheet" id="switcher-css" type="text/css" href="assets/switcher/css/switcher.css" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/red.css" title="red" media="all" data-default-color="true" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/orange.css" title="orange" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/blue.css" title="blue" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/pink.css" title="pink" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/green.css" title="green" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/purple.css" title="purple" media="all" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/images/favicon-icon/apple-touch-icon-144-precomposed.png">
<link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/images/favicon-icon/apple-touch-icon-114-precomposed.html">
<link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/images/favicon-icon/apple-touch-icon-72-precomposed.png">
<link rel="apple-touch-icon-precomposed" href="assets/images/favicon-icon/apple-touch-icon-57-precomposed.png">
<link rel="shortcut icon" href="assets/images/favicon-icon/favicon.png">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet">
</head>
<body>
 
<!--Header-->
<?php include('includes/header.php');?>
<!-- /Header --> 

<!--Listing-Image-Slider-->

<?php 
$vhid=intval($_GET['vhid']);
$sql = "SELECT tblvehicles.*,tblbrands.BrandName,tblbrands.id as bid  from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand where tblvehicles.id=:vhid";
$query = $dbh -> prepare($sql);
$query->bindParam(':vhid',$vhid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{  
$_SESSION['brndid']=$result->bid;  
?>  

<section id="listing_img_slider">
  <div><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1);?>" class="img-responsive" alt="image" width="900" height="560"></div>
  <div><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage2);?>" class="img-responsive" alt="image" width="900" height="560"></div>
  <div><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage3);?>" class="img-responsive" alt="image" width="900" height="560"></div>
  <div><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage4);?>" class="img-responsive"  alt="image" width="900" height="560"></div>
  <?php if($result->Vimage5=="")
{

} else {
  ?>
  <div><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage5);?>" class="img-responsive" alt="image" width="900" height="560"></div>
  <?php } ?>
</section>
<!--/Listing-Image-Slider-->

<?php
// Fetch reviews for the specific car
$query = "SELECT r.rating, r.review, r.review_date, u.FullName 
          FROM tblreviews r 
          INNER JOIN tblusers u ON r.user_id = u.id 
          WHERE r.vehicle_id = :car_id";
$stmt = $dbh->prepare($query);
$stmt->bindParam(':car_id', $car_id, PDO::PARAM_INT);
$stmt->execute();

$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!--Listing-detail-->
<section class="listing-detail">
  <div class="container">
    <div class="listing_detail_head row">
      <div class="col-md-9">
        <h2><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></h2>
      </div>
      <div class="col-md-3">
        <div class="price_info">
          <p>$<?php echo htmlentities($result->PricePerDay);?> </p>Per Day 
          </p><h6>Additional $20 if driver is hired
          
         
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-9">
        <div class="main_features">
          <ul>
          
            <li> <i class="fa fa-calendar" aria-hidden="true"></i>
              <h5><?php echo htmlentities($result->ModelYear);?></h5>
              <p>Reg.Year</p>
            </li>
            <li> <i class="fa fa-cogs" aria-hidden="true"></i>
              <h5><?php echo htmlentities($result->FuelType);?></h5>
              <p>Fuel Type</p>
            </li>
       
            <li> <i class="fa fa-user-plus" aria-hidden="true"></i>
              <h5><?php echo htmlentities($result->SeatingCapacity);?></h5>
              <p>Seats</p>
            </li>
          </ul>
        </div>
        <div class="listing_more_info">
          <div class="listing_detail_wrap"> 
            <!-- Nav tabs -->
    <ul class="nav nav-tabs gray-bg" role="tablist">
      <li role="presentation" class="active">
        <a href="#vehicle-overview" aria-controls="vehicle-overview" role="tab" data-toggle="tab">Vehicle Overview</a>
      </li>
      <li role="presentation">
        <a href="#accessories" aria-controls="accessories" role="tab" data-toggle="tab">Accessories</a>
      </li>
      <li role="presentation">
        <a href="#ratings" aria-controls="ratings" role="tab" data-toggle="tab">Ratings</a>
      </li>
    </ul>
              <div class="tab-content">
    <div role="tabpanel" class="tab-pane" id="ratings">
        <h4>User Ratings</h4>
        
        <?php if (empty($reviews)): ?>
            <p>No reviews yet. Be the first to review this car by booking it!</p>
        <?php else: ?>
            <?php foreach ($reviews as $review): ?>
                <div class="review">
                    <strong><?php echo htmlentities($review['FullName']); ?></strong>
                    <span> - <?php echo htmlentities($review['review_date']); ?></span>
                    <div class="rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                          <span class="fa <?php echo $i <= $review['rating'] ? 'fa-star' : 'fa-star-o'; ?>"></span>
                          <?php endfor; ?>
                        </div>
                    <p><?php echo htmlentities($review['review']); ?></p>
                    <hr>
                </div>
            <?php endforeach; ?>
            <?php endif; ?>

        <!-- /* Overall review section styling */ -->
        <style>
          
#ratings {
  margin-top: 40px;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

#ratings h4 {
    font-size: 24px;
    font-weight: 600;
    margin-bottom: 20px;
    color: #333;
  }
  
  .review {
    padding: 15px;
    margin-bottom: 20px;
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

.review strong {
    font-size: 18px;
    color: #333;
  }

.review .date {
    color: #999;
    font-size: 14px;
}

.rating {
    margin-top: 5px;
    color: #ffcc00; /* Gold color for stars */
}

.rating .fa {
  font-size: 18px;
}

.review p {
  margin-top: 10px;
  font-size: 16px;
  color: #555;
}

.review hr {
    border: 0;
    border-top: 1px solid #eee;
    margin-top: 10px;
  }
  
/* Empty star styling */
.fa-star-o {
  color: #d3d3d3;
}

/* Custom padding and margin for the whole ratings tab */
.tab-content {
    padding: 20px;
}

.tab-content .no-reviews {
  text-align: center;
  font-size: 18px;
    color: #888;
}
</style>
    </div>
</div>

            </ul>
            
            <!-- Tab panes -->
            <div class="tab-content"> 
              <!-- vehicle-overview -->
              <div role="tabpanel" class="tab-pane active" id="vehicle-overview">
                
                <p><?php echo htmlentities($result->VehiclesOverview);?></p>
              </div>
              
              
              <!-- Accessories -->
              <div role="tabpanel" class="tab-pane" id="accessories"> 
                <!--Accessories-->
                <table>
                  <thead>
                    <tr>
                      <th colspan="2">Accessories</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Air Conditioner</td>
<?php if($result->AirConditioner==1)
{
?>
                      <td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?> 
   <td><i class="fa fa-close" aria-hidden="true"></i></td>
   <?php } ?> </tr>
   
   <tr>
<td>AntiLock Braking System</td>
<?php if($result->AntiLockBrakingSystem==1)
{
  ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else {?>
  <td><i class="fa fa-close" aria-hidden="true"></i></td>
  <?php } ?>
</tr>

<tr>
  <td>Power Steering</td>
  <?php if($result->PowerSteering==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>
                   

<tr>

<td>Power Windows</td>

<?php if($result->PowerWindows==1)
{
  ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
  <td>CD Player</td>
  <?php if($result->CDPlayer==1)
{
  ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
  <td><i class="fa fa-close" aria-hidden="true"></i></td>
  <?php } ?>
</tr>

<tr>
  <td>Leather Seats</td>
<?php if($result->LeatherSeats==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Central Locking</td>
<?php if($result->CentralLocking==1)
{
  ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
  <td>Power Door Locks</td>
  <?php if($result->PowerDoorLocks==1)
{
  ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>
                    <tr>
                      <td>Brake Assist</td>
                      <?php if($result->BrakeAssist==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php  } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

<tr>
<td>Driver Airbag</td>
<?php if($result->DriverAirbag==1)
{
  ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>
 
<tr>
 <td>Passenger Airbag</td>
 <?php if($result->PassengerAirbag==1)
{
  ?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else {?>
  <td><i class="fa fa-close" aria-hidden="true"></i></td>
  <?php } ?>
</tr>

<tr>
  <td>Crash Sensor</td>
  <?php if($result->CrashSensor==1)
{
?>
<td><i class="fa fa-check" aria-hidden="true"></i></td>
<?php } else { ?>
<td><i class="fa fa-close" aria-hidden="true"></i></td>
<?php } ?>
</tr>

                  </tbody>
                </table>
              </div>
            </div>
          </div>
          
        </div>
<?php }} ?>


</div>
      
<!--Side-Bar-->
    <!--Side-Bar-->
<aside class="col-md-3">
    <div class="share_vehicle">
        <p>Share: 
            <a href="#"><i class="fa fa-facebook-square" aria-hidden="true"></i></a>
            <a href="#"><i class="fa fa-twitter-square" aria-hidden="true"></i></a>
            <a href="#"><i class="fa fa-linkedin-square" aria-hidden="true"></i></a>
            <a href="#"><i class="fa fa-google-plus-square" aria-hidden="true"></i></a>
        </p>
    </div>

    <div class="sidebar_widget">
        <div class="widget_heading">
            <h5><i class="fa fa-envelope" aria-hidden="true"></i>Book Now</h5>
        </div>
        
        <form method="post">
            <div class="form-group">
            <?php
// Get the current date in the required format (YYYY-MM-DD)
  $current_date = date('Y-m-d');
?>
                <label>From Date:</label>
                <input type="date" class="form-control" name="fromdate" placeholder="From Date" required min="<?php echo $current_date; ?>">
            </div>
            <div class="form-group">
                <label>To Date:</label>
                <input type="date" class="form-control" name="todate" placeholder="To Date" required min="<?php echo $current_date; ?>">
            </div>
            <div class="form-group">
                <textarea rows="4" class="form-control" name="message" placeholder="Message" required></textarea>
            </div>

            <?php
            // Check if the user is logged in
            if ($_SESSION['login']) {
                $useremail = $_SESSION['login'];
                
                // Fetch the user's balance from the database
                $sql = "SELECT balance FROM tblusers WHERE EmailId = :useremail";
                $query = $dbh->prepare($sql);
                $query->bindParam(':useremail', $useremail, PDO::PARAM_STR);
                $query->execute();
                $user = $query->fetch(PDO::FETCH_OBJ);

                // Set the required security deposit amount
                define('SECURITY_DEPOSIT_AMOUNT', 100);  // Replace with actual deposit amount

                if ($user) {
                    $current_balance = $user->balance;

                    // Check if the user has enough balance
                    if ($current_balance >= SECURITY_DEPOSIT_AMOUNT) {
                        // User has enough balance, allow booking
                        ?>
                        <div class="form-group">
                            <input type="submit" class="btn" name="submit" value="Pay Later">
                        </div>
                        <?php
                    } else {
                      // User does not have enough balance
                        echo "<div class='alert alert-danger'>❌ You need to deposit at least $" . SECURITY_DEPOSIT_AMOUNT . " to proceed with booking.</div>";
                        ?>
                        <a href="deposit.php" class="btn btn-primary">Deposit Funds</a>
                        <br>
                        <?php
                    }
                }
                
              } else {
                // User is not logged in
                ?>
                <a href="#loginform" class="btn btn-xs uppercase" data-toggle="modal" data-dismiss="modal">Login To Book</a>
                <?php
            }
            ?>
             <form method="POST" action="">
    <input type="hidden" name="vhid" value="<?php echo $car_id; ?>">
    <input type="submit" class="btn" name="book_with_driver" value="Request a Driver">
</form>
              
              <?php if (!empty($driver_message)): ?>
    <div class="alert alert-success"><?php echo htmlentities($driver_message); ?></div>
<?php endif; ?>
        </aside>




<script>
// JavaScript to ensure To Date is after From Date and restrict past dates

document.addEventListener('DOMContentLoaded', function() {
    var fromDateField = document.getElementById('fromdate');
    var toDateField = document.getElementById('todate');
    
    // When From Date is selected, update To Date's min value to be the same or later
    fromDateField.addEventListener('change', function() {
        var fromDate = fromDateField.value;
        toDateField.setAttribute('min', fromDate);
    });

    // Ensure that users cannot manually select past dates
    var today = new Date().toISOString().split('T')[0]; // Current date in YYYY-MM-DD format
    fromDateField.setAttribute('min', today);  // Set min date to today for From Date
    toDateField.setAttribute('min', today);    // Set min date to today for To Date
});
</script>

      <!--/Side-Bar--> 
    </div>
    
    <div class="space-20"></div>
    <div class="divider"></div>
    
    <!--Similar-Cars-->
    <div class="similar_cars">
      <h3>Similar Cars</h3>
      <div class="row">
<?php 
$bid=$_SESSION['brndid'];
$sql="SELECT tblvehicles.VehiclesTitle,tblbrands.BrandName,tblvehicles.PricePerDay,tblvehicles.FuelType,tblvehicles.ModelYear,tblvehicles.id,tblvehicles.SeatingCapacity,tblvehicles.VehiclesOverview,tblvehicles.Vimage1 from tblvehicles join tblbrands on tblbrands.id=tblvehicles.VehiclesBrand where tblvehicles.VehiclesBrand=:bid";
$query = $dbh -> prepare($sql);
$query->bindParam(':bid',$bid, PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $result)
{ ?>      
        <div class="col-md-3 grid_listing">
          <div class="product-listing-m gray-bg">
            <div class="product-listing-img"> <a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1);?>" class="img-responsive" alt="image" /> </a>
            </div>
            <div class="product-listing-content">
              <h5><a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></a></h5>
              <p class="list-price">$<?php echo htmlentities($result->PricePerDay);?></p>
          
              <ul class="features_list">
                
             <li><i class="fa fa-user" aria-hidden="true"></i><?php echo htmlentities($result->SeatingCapacity);?> seats</li>
                <li><i class="fa fa-calendar" aria-hidden="true"></i><?php echo htmlentities($result->ModelYear);?> model</li>
                <li><i class="fa fa-car" aria-hidden="true"></i><?php echo htmlentities($result->FuelType);?></li>
              </ul>
            </div>
          </div>
        </div>
 <?php }} ?>       

      </div>
    </div>
    <!--/Similar-Cars--> 
    
  </div>
</section>
<!--/Listing-detail--> 

<!--Footer -->
<?php include('includes/footer.php');?>
<!-- /Footer--> 

<!--Back to top-->
<div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
<!--/Back to top--> 

<!--Login-Form -->
<?php include('includes/login.php');?>
<!--/Login-Form --> 

<!--Register-Form -->
<?php include('includes/registration.php');?>

<!--/Register-Form --> 

<!--Forgot-password-Form -->
<?php include('includes/forgotpassword.php');?>


<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<script src="assets/switcher/js/switcher.js"></script>
<script src="assets/js/bootstrap-slider.min.js"></script> 
<script src="assets/js/slick.min.js"></script> 
<script src="assets/js/owl.carousel.min.js"></script>

</body>
</html>