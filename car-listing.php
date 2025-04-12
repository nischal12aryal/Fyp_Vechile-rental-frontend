<?php 
session_start();
include('includes/config.php');
error_reporting(0);
?>

<!DOCTYPE HTML>
<html lang="en">
<head>

<title>Car Rental  | Car Listing</title>
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

        
<!-- Fav and touch icons -->
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

<!--Page Header-->
<section class="page-header listing_page">
  <div class="container">
    <div class="page-header_wrap">
      <div class="page-heading">
        <h1>Car Listing</h1>
      </div>
      <ul class="coustom-breadcrumb">
        <li><a href="#">Home</a></li>
        <li>Car Listing</li>
      </ul>
    </div>
  </div>
  <!-- Dark Overlay-->
  <div class="dark-overlay"></div>
</section>
<!-- /Page Header--> 

<!--Listing-->
<section class="listing-page">
  <div class="container">
    <div class="row">
      <div class="col-md-9 col-md-push-3">
        <div class="result-sorting-wrapper">
          <div class="sorting-count">
<?php 
//Query for Listing count
$sql = "SELECT id from tblvehicles";
$query = $dbh -> prepare($sql);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=$query->rowCount();
?>
<p><span><?php echo htmlentities($cnt);?> Listings</span></p>
</div>
</div>

<?php 
// Retrieve filter values from POST request
$brand = (!empty($_POST['brand']) && $_POST['brand'] !== 'Select Brand') ? $_POST['brand'] : null;
$fueltype = (!empty($_POST['fueltype']) && $_POST['fueltype'] !== 'Select Fuel Type') ? $_POST['fueltype'] : null;
$price = (!empty($_POST['price']) && is_numeric($_POST['price'])) ? $_POST['price'] : null;
$model_year = (!empty($_POST['model_year']) && is_numeric($_POST['model_year'])) ? $_POST['model_year'] : null;

// Convert checkboxes from "on" → 1, ignore unchecked ones
$features = [
    'AirConditioner' => isset($_POST['ac']) ? 1 : null,
    'PowerDoorLocks' => isset($_POST['powerdoorlocks']) ? 1 : null,
    'AntiLockBrakingSystem' => isset($_POST['antilockbraking']) ? 1 : null,
    'BrakeAssist' => isset($_POST['brakeassist']) ? 1 : null,
    'PowerSteering' => isset($_POST['powersteering']) ? 1 : null,
    'DriverAirbag' => isset($_POST['driverairbag']) ? 1 : null,
    'PassengerAirbag' => isset($_POST['passengerairbag']) ? 1 : null,
    'PowerWindows' => isset($_POST['powerwindows']) ? 1 : null,
    'CDPlayer' => isset($_POST['cdplayer']) ? 1 : null,
    'CentralLocking' => isset($_POST['centrallocking']) ? 1 : null,
    'CrashSensor' => isset($_POST['crashsensor']) ? 1 : null,
    'LeatherSeats' => isset($_POST['leatherseats']) ? 1 : null
];

// Start the SQL query
$sql = "SELECT tblvehicles.*, tblbrands.BrandName, tblbrands.id as bid 
        FROM tblvehicles 
        JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand 
        WHERE 1=1";  // Always true, allows dynamic conditions

$params = [];

// Add brand filter if selected
if (!is_null($brand)) {
    $sql .= " AND tblvehicles.VehiclesBrand = :brand";
    $params[':brand'] = $brand;
}

// Add fuel type filter if selected
if (!is_null($fueltype)) {
    $sql .= " AND tblvehicles.FuelType = :fueltype";
    $params[':fueltype'] = $fueltype;
}

// Add price filter if selected
if (!is_null($price)) {
  $sql .= " AND tblvehicles.PricePerDay <= :price";
  $params[':price'] = $price;
}

// Add model year filter if selected
if (!is_null($model_year)) {
  $sql .= " AND tblvehicles.ModelYear = :model_year";
  $params[':model_year'] = $model_year;
}

// Add additional features only if they are checked
foreach ($features as $feature => $value) {
    if (!is_null($value)) {
        $sql .= " AND tblvehicles.$feature = 1";
    }
}


// Prepare and execute the query
$query = $dbh->prepare($sql);
$query->execute($params);
$results = $query->fetchAll(PDO::FETCH_OBJ);
$cnt = count($results);

if ($cnt > 0) {
    foreach ($results as $result) { ?>
        <div class="product-listing-m gray-bg">
          <div class="product-listing-img"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1);?>" class="img-responsive" alt="Image" /> </a> 
          </div>
          <div class="product-listing-content">
            <h5><a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></a></h5>
            <p class="list-price">$<?php echo htmlentities($result->PricePerDay);?> Per Day</p>
            <ul>
              <li><i class="fa fa-user" aria-hidden="true"></i><?php echo htmlentities($result->SeatingCapacity);?> seats</li>
              <li><i class="fa fa-calendar" aria-hidden="true"></i><?php echo htmlentities($result->ModelYear);?> model</li>
              <li><i class="fa fa-car" aria-hidden="true"></i><?php echo htmlentities($result->FuelType);?></li>
            </ul>
            <a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>" class="btn">View Details <span class="angle_arrow"><i class="fa fa-angle-right" aria-hidden="true"></i></span></a>
          </div>
        </div>
    <?php }
} else { ?>
    <p>No vehicles match your selected filters.</p>
<?php } ?>


         </div>
      
      <!--Side-Bar-->
      <aside class="col-md-3 col-md-pull-9">
        <div class="sidebar_widget">
          <div class="widget_heading">
            <h5><i class="fa fa-filter" aria-hidden="true"></i> Find Your  Car </h5>
          </div>
          <div class="sidebar_filter">
  <form action="" method="post">
    <div class="form-group select">
      <select class="form-control" name="brand">
        <option>Select Brand</option>
        <?php 
        $sql = "SELECT * from tblbrands";
        $query = $dbh -> prepare($sql);
        $query->execute();
        $brands = $query->fetchAll(PDO::FETCH_OBJ);
        foreach($brands as $brand) { ?>
            <option value="<?php echo htmlentities($brand->id);?>" <?php if ($brand->id == $brand) echo 'selected'; ?>>
                <?php echo htmlentities($brand->BrandName);?>
            </option>
        <?php } ?>
      </select>
    </div>
    <div class="form-group select">
      <select class="form-control" name="fueltype">
        <option>Select Fuel Type</option>
        <option value="Petrol" <?php if ($fueltype == 'Petrol') echo 'selected'; ?>>Petrol</option>
        <option value="Diesel" <?php if ($fueltype == 'Diesel') echo 'selected'; ?>>Diesel</option>
        <option value="CNG" <?php if ($fueltype == 'CNG') echo 'selected'; ?>>CNG</option>
      </select>
    </div>

    <!-- Price Filter -->
    <div class="form-group">
      <label for="price">Price</label>
      <input type="number" class="form-control" name="price" id="price" placeholder="Enter price" value="<?php echo isset($_POST['price']) ? $_POST['price'] : ''; ?>">
    </div>
    
    <label for="price">Model</label>
    <div class="form-group select">

      <select class="form-control" name="model_year">
        <option>Select Model Year</option>
        <?php 
        for ($year = 2000; $year <= date("Y"); $year++) {
            echo "<option value='$year' " . ($year == $model_year ? "selected" : "") . ">$year</option>";
        }
        ?>
      </select>
    </div>
              

              <div class="form-group">
  <h6><i class="fa fa-filter" aria-hidden="true"></i> Additional Features </h6>
  <label><input type="checkbox" name="ac" <?php if (isset($_POST['ac'])) echo 'checked'; ?>> Air Conditioner</label>
</div>
<div class="form-group">
  <label><input type="checkbox" name="powerdoorlocks" <?php if (isset($_POST['powerdoorlocks'])) echo 'checked'; ?>> Power Door Locks</label>
</div>
<div class="form-group">
  <label><input type="checkbox" name="antilockbraking" <?php if (isset($_POST['antilockbraking'])) echo 'checked'; ?>> Anti-Lock Braking System</label>
</div>
<div class="form-group">
  <label><input type="checkbox" name="brakeassist" <?php if (isset($_POST['brakeassist'])) echo 'checked'; ?>> Brake Assist</label>
</div>
<div class="form-group">
  <label><input type="checkbox" name="powersteering" <?php if (isset($_POST['powersteering'])) echo 'checked'; ?>> Power Steering</label>
</div>
<div class="form-group">
  <label><input type="checkbox" name="driverairbag" <?php if (isset($_POST['driverairbag'])) echo 'checked'; ?>> Driver Airbag</label>
</div>
<div class="form-group">
  <label><input type="checkbox" name="passengerairbag" <?php if (isset($_POST['passengerairbag'])) echo 'checked'; ?>> Passenger Airbag</label>
</div>
<div class="form-group">
  <label><input type="checkbox" name="powerwindows" <?php if (isset($_POST['powerwindows'])) echo 'checked'; ?>> Power Windows</label>
</div>
<div class="form-group">
  <label><input type="checkbox" name="cdplayer" <?php if (isset($_POST['cdplayer'])) echo 'checked'; ?>> CD Player</label>
</div>
<div class="form-group">
  <label><input type="checkbox" name="centrallocking" <?php if (isset($_POST['centrallocking'])) echo 'checked'; ?>> Central Locking</label>
</div>
<div class="form-group">
  <label><input type="checkbox" name="crashsensor" <?php if (isset($_POST['crashsensor'])) echo 'checked'; ?>> Crash Sensor</label>
</div>
<div class="form-group">
  <label><input type="checkbox" name="leatherseats" <?php if (isset($_POST['leatherseats'])) echo 'checked'; ?>> Leather Seats</label>
</div>

              <div class="form-group">
                <button type="submit" class="btn btn-block"><i class="fa fa-search" aria-hidden="true"></i> Search Car</button>
              </div>
            </form>
          </div>
        </div>

        <div class="sidebar_widget">
          <div class="widget_heading">
            <h5><i class="fa fa-car" aria-hidden="true"></i> Recently Listed Cars</h5>
          </div>
          <div class="recent_addedcars">
            <ul>
              <?php 
              $sql = "SELECT tblvehicles.*, tblbrands.BrandName, tblbrands.id as bid 
                      FROM tblvehicles 
                      JOIN tblbrands ON tblbrands.id = tblvehicles.VehiclesBrand 
                      ORDER BY id DESC LIMIT 4";
              $query = $dbh -> prepare($sql);
              $query->execute();
              $recent_results = $query->fetchAll(PDO::FETCH_OBJ);
              foreach($recent_results as $result) { ?>
                <li class="gray-bg">
                  <div class="recent_post_img"> <a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>"><img src="admin/img/vehicleimages/<?php echo htmlentities($result->Vimage1);?>" alt="image"></a> </div>
                  <div class="recent_post_title"> <a href="vehical-details.php?vhid=<?php echo htmlentities($result->id);?>"><?php echo htmlentities($result->BrandName);?> , <?php echo htmlentities($result->VehiclesTitle);?></a>
                    <p class="widget_price">$<?php echo htmlentities($result->PricePerDay);?> Per Day</p>
                  </div>
                </li>
              <?php } ?>
            </ul>
          </div>
        </div>
      </aside>
      <!--/Side-Bar--> 
    </div>
  </div>
</section>
<!-- /Listing--> 

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

<!-- Scripts --> 
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<!--Switcher-->
<script src="assets/switcher/js/switcher.js"></script>
<!--bootstrap-slider-JS--> 
<script src="assets/js/bootstrap-slider.min.js"></script> 
<!--Slider-JS--> 
<script src="assets/js/slick.min.js"></script> 
<script src="assets/js/owl.carousel.min.js"></script>

</body>
</html>
