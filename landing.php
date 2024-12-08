<?php
session_start();
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_sdshoppe";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
  header("Location: haveacc.php"); // Redirect to login if not logged in
  exit;
}

$admin_id = $_SESSION['admin_id'];
// Prepare and bind statement
$stmt = $conn->prepare("SELECT name, position, pics FROM admins WHERE adminID = ?");
$stmt->bind_param("i", $admin_id); 
$stmt->execute();

// Fetch result
$result = $stmt->get_result();
$admin = $result->fetch_assoc(); 

$dataPoints = array();

// Fetch the sum of grand_total from bulk_order_details for the past 7 days
$sql_bulk_order_details = "
    SELECT 
        DATE(order_date) AS order_day, 
        SUM(grand_total) AS total_grand_total
    FROM bulk_order_details 
    WHERE order_date >= CURDATE() - INTERVAL 7 DAY 
    GROUP BY order_day";
$result_bulk_order_details = $conn->query($sql_bulk_order_details);

$daily_grand_totals = array_fill_keys(
    [date('Y-m-d', strtotime('today - 6 days')), date('Y-m-d', strtotime('today - 5 days')), date('Y-m-d', strtotime('today - 4 days')),
     date('Y-m-d', strtotime('today - 3 days')), date('Y-m-d', strtotime('today - 2 days')), date('Y-m-d', strtotime('today - 1 day')), date('Y-m-d', strtotime('today'))], 
    0
); // Initialize an array with the last 7 days

if ($result_bulk_order_details->num_rows > 0) {
    while ($row = $result_bulk_order_details->fetch_assoc()) {
        $daily_grand_totals[$row['order_day']] = (float)$row['total_grand_total'];
    }
}

// Fetch the sum of total_price from order_details for the past 7 days
$sql_order_details = "
    SELECT 
        DATE(order_date) AS order_day, 
        SUM(total_price) AS total_order_price
    FROM order_details 
    WHERE order_date >= CURDATE() - INTERVAL 7 DAY 
    GROUP BY order_day";
$result_order_details = $conn->query($sql_order_details);

$daily_order_totals = array_fill_keys(
    [date('Y-m-d', strtotime('today - 6 days')), date('Y-m-d', strtotime('today - 5 days')), date('Y-m-d', strtotime('today - 4 days')),
     date('Y-m-d', strtotime('today - 3 days')), date('Y-m-d', strtotime('today - 2 days')), date('Y-m-d', strtotime('today - 1 day')), date('Y-m-d', strtotime('today'))], 
    0
); // Initialize an array with the last 7 days

if ($result_order_details->num_rows > 0) {
    while ($row = $result_order_details->fetch_assoc()) {
        $daily_order_totals[$row['order_day']] = (float)$row['total_order_price'];
    }
}

// Sum the totals from both tables for each day and format for the chart
foreach ($daily_grand_totals as $day => $grand_total) {
  $total_price = isset($daily_order_totals[$day]) ? $daily_order_totals[$day] : 0;
    $total_sum = $grand_total + $total_price; // Add the totals for each day
    $dataPoints[] = [
        "label" => date('l', strtotime($day)), // Get the name of the day (e.g., Monday, Tuesday)
        "y" => $total_sum
    ];
}



$stmtPayment = $conn->prepare("
    SELECT COUNT(*) AS count_not_confirmed
    FROM order_details od
    JOIN payment p ON od.payment = p.payment_id
    WHERE p.confirmation = 'Not Yet Confirmed'
");

if ($stmtPayment === false) {
    die("Error preparing count statement: " . $conn->error);
}

// Execute the statement
if (!$stmtPayment->execute()) {
    die("Error executing count statement: " . $stmtPayment->error);
}

// Retrieve the count result
$result = $stmtPayment->get_result();
$count_data = $result->fetch_assoc();
$count_not_confirmed = $count_data['count_not_confirmed'];

$stmtPayment = $conn->prepare("
    SELECT 
        od.order_num, 
        p.payment_id, p.customer_name, p.confirmation
    FROM order_details od
    JOIN payment p ON od.payment= p.payment_id
    WHERE p.confirmation = 'Not Yet Confirmed'
");

if ($stmtPayment === false) {
    die("Error preparing statement: " . $conn->error);
}

// Execute the statement
$stmtPayment->execute();
$result = $stmtPayment->get_result();
$payment_data = $result->fetch_all(MYSQLI_ASSOC);

//order_details
$stmtOrder = $conn->prepare("
    SELECT 
        od.order_num, od.total_price, od.status, 
        oi.product_name, oi.color, oi.quantity,
        pr.product_image
      FROM order_details od
    JOIN order_items oi ON od.order_num = oi.order_num
    JOIN products pr ON oi.product_id = pr.product_id
    WHERE od.status = 'To Pay' or od.status = 'To Ship' GROUP BY od.order_num
    ");

    if ($stmtOrder === false) {
      die("Error preparing statement: " . $conn->error);
  }
  
  $stmtOrder->execute();
$result = $stmtOrder->get_result();
$order_data = $result->fetch_all(MYSQLI_ASSOC);


//bulk order details
$stmtbulkOrder = $conn->prepare("
    SELECT 
        bod.bulk_order_id, bod.grand_total, bod.status, 
        boi.product_name, boi.color, boi.yards, boi.rolls,
        pr.product_image
      FROM bulk_order_details bod
    JOIN bulk_order_items boi ON bod.bulk_order_id = boi.bulk_order_id
    JOIN products pr ON boi.product_id = pr.product_id
    WHERE bod.status = 'To Pay'  GROUP BY bod.bulk_order_id
    ");

    if ($stmtOrder === false) {
      die("Error preparing statement: " . $conn->error);
  }
  
  $stmtbulkOrder->execute();
$result = $stmtbulkOrder->get_result();
$bulk_order_data = $result->fetch_all(MYSQLI_ASSOC);

//recent order_details
$stmtRecentOrders = $conn->prepare("
    SELECT 
        od.order_num, od.total_price, od.status, 
        oi.product_name, oi.color, oi.quantity,
       
        pr.product_image
      FROM order_details od
    JOIN order_items oi ON od.order_num = oi.order_num
    JOIN products pr ON oi.product_id = pr.product_id
    
    WHERE DATE(od.order_date) = CURDATE()
    ");

    if ($stmtRecentOrders === false) {
      die("Error preparing statement: " . $conn->error);
  }
  
  $stmtRecentOrders->execute();
$result = $stmtRecentOrders->get_result();
$recent_data = $result->fetch_all(MYSQLI_ASSOC);

//top selling product
$stmtTopSell = $conn->prepare("
    SELECT 
        od.order_num, od.total_price, od.status, 
        oi.product_name, oi.color, oi.quantity,
        pr.product_image, pr.product_id, pr.category, pr.status,
        pre.total_revenue, pre.product_id
      FROM order_details od
    JOIN order_items oi ON od.order_num = oi.order_num
    JOIN products pr ON oi.product_id = pr.product_id
    JOIN product_revenue pre ON pr.product_id = pre.product_id
    GROUP BY pre.product_id ORDER BY total_revenue DESC 
    ");

    if ($stmtTopSell === false) {
      die("Error preparing statement: " . $conn->error);
  }
  
  $stmtTopSell->execute();
$result = $stmtTopSell->get_result();
$topsell_data = $result->fetch_all(MYSQLI_ASSOC);

//product overview
$stmtProductOverview = $conn->prepare("
    SELECT  
        pr.product_image, pr.product_id, pr.price, pr.status, pr.category, pr.product_name,
        pc.rolls, pc.yards,
        pre.total_revenue, pre.product_id
      FROM products pr
    JOIN product_revenue pre ON pr.product_id = pre.product_id
    JOIN product_colors pc ON pr.product_id = pc.product_id
    GROUP BY pr.product_id
    ");

    if ($stmtProductOverview === false) {
      die("Error preparing statement: " . $conn->error);
  }
  
  $stmtProductOverview->execute();
$result = $stmtProductOverview->get_result();
$overview_data = $result->fetch_all(MYSQLI_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
      crossorigin="anonymous"
    />
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    />
    <link rel="stylesheet" />
    <link rel="icon" href="\SnD_Shoppe-main\PIC\sndlogo.png" type="logo" />
    <title>S&D Fabrics</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@100..900&family=Playfair+Display+SC:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');

        body {
    background-blend-mode: multiply;
    background-position: center;
    background-size: cover;
    background-repeat: no-repeat;
    min-height: 100vh; 
    overflow-y: auto; 
    margin: 0; 
    padding: 0; 
    font-family: "Playfair Display", serif;
}

/* Navbar and Dropdown Styling */
.navbar {
    background-color: #f1e8d9;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}

.nav-link-black {
    color: #1e1e1e !important;
}

.nav-link-black:hover {
    color: #e044a5;
}

/* Account Dropdown Styling */
.navbar .dropdown-menu {
    border-radius: 8px;
    padding: 0;
    min-width: 150px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}

/* Account Dropdown Styling */
.navbar .dropdown-menu {
    border-radius: 11px; 
    padding: 0;
    min-width: 150px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    overflow: hidden; 
}

/* Dropdown Item Styling */
.navbar .dropdown-item {
    padding: 20px 16px;
    font-size: 14px;
    color: #1e1e1e;
    transition: background-color 0.3s;
}

/* Hover Effect with Matching Border Radius */
.navbar .dropdown-item:hover {
    background-color: #f1e8d9;
    border-radius: 0;
}

/* Logout Text */
.dropdown-item.text-danger {
    color: #dc3545;
    font-weight: bold;
}

/* Dropdown Divider */
.dropdown-divider {
    margin: 5;
}

.input-group-text {
    background-color: #f1e8d9; 
    border: 1px solid #d9b65d; 
    border-radius: 20px 0 0 20px; 
}

.form-control {
    border: 1px solid #d9b65d;
    border-radius: 0 20px 20px 0; 
    text-align: center; 
}

h1{
    font-family: "Playfair Display SC", serif;
    font-size: 50px;
    color: #1e1e1e;
}

.dropdown-item:hover {
    background-color: #f1e8d9;
}

.dropdown-item.text-danger {
    color: #dc3545;
    font-weight: bold;
}

.dropdown-divider {
    margin: 0;
}


/* Cards */
.card {
    background-color: white;
    border: 1px solid #d1b88a;
    border-radius: 5px;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
}

/* Sidebar Styling */
.sidebar {
    background-color: #f1e8d9;
    padding: 1.5rem;
    position: relative;
    z-index: 1;
}

/* List Group Item Styling */
.list-group-item {
    color: #1e1e1e;
    background-color: #f1e8d9;
    border: 2px solid #d9b65d;
    border-radius: 8px;
    font-size: 14px;
    padding: 8px 16px;
    margin-bottom: 8px; 
    transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    position: relative;
    z-index: 2; 
}

/* List Group Item Hover */
.list-group-item:hover {
    background-color: #e2d1b3;
    color: #1e1e1e;
    cursor: pointer;
    border-color: #d9b65d;
}


.list-group-item.active {
    background-color: #e2d1b3;
    color: #1e1e1e;
    border-color: #d9b65d;
}


.list-group-item:not(.active):active {
    background-color:  !important; 
    color: #1e1e1e;
    border-color: #d9b65d;
}

.list-group-item:focus, .list-group-item:active {
    outline: none; 
    box-shadow: none; 
}

.list-group-item:not(.active):focus {
    background-color: #f1e8d9;  
    color: #1e1e1e;
    border-color: #d9b65d;
}

/* Handle active item focus */
.list-group-item.active:focus {
    background-color: #e2d1b3;
    color: #1e1e1e;
    border-color: #d9b65d;
}

.list-group-item:active {
    background-color: #e2d1b3;
    color: #1e1e1e;
    border-color: #d9b65d;
}






    
/* Media Query for Sidebar Responsiveness */
@media (max-width: 768px) {
    .sidebar {
        padding: 1rem;
    
    }


    .sidebar h5 {
        font-size: 1.1em;
    }

    .list-group-item {
        font-size: 13px;
    }
    
}

.col-10.p-4 {
    flex: 1;
    padding: 20px;
    background-color: #FFF8DC;
}

.col-10 p-4 h1 {
    font-size: 24px;
    margin-bottom: 15px;
    color: #4b4b4b;
}

.add-new {
    background-color: #ffecd0;
    
}

.product-search{
    display: flex;
  justify-content: flex-end; 
  gap: 10px; 
  margin-bottom: 20px;
}

.product-table {
    width: 100%;
    border-collapse: collapse;
}

.product-table th, .product-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.product-table th {
    background-color: #fdf6e0;
}

.product-table tbody tr:nth-child(odd) {
    background-color: #f0e2c4;
}

.product-table tbody tr:nth-child(even) {
    background-color: #fdf6e0;
}

.container_addproduct{
    width: 600px;
    background-color: #fdf2d9;
    padding: 20px;
    align-items: center; 
    justify-content: center; 
    border-radius: 10px;
    max-width: 800px;margin: auto;
  }
  
  h1 {
    font-size: 24px;
    margin-bottom: 20px;
    text-transform: uppercase;
    font-family: "Playfair Display SC", serif;
    font-size: 50px;
    color: #1e1e1e;
  }
  
  .section {
    background-color: #fef8e6;
    
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
  }

  .hidden{
    display: none;
  }
 
    </style>
  </head>
  <body class="vh-100">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
      <div
        class="container-fluid d-flex justify-content-between align-items-center"
      >
        <a class="navbar-brand fs-4" href="/pages/homepage.html">
          <img src="/SnD_Shoppe-main/PIC/sndlogo.png" width="70px" alt="Logo" /> 
        </a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarTogglerDemo01"
          aria-controls="navbarTogglerDemo01"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>
        
            <img src="<?= htmlspecialchars($admin['pics']) ?>" alt="admin" style="width: 50px; height:45px;  border: 2px solid ; border-radius: 50%; margin-left:1250px;">
              <div class="text-center">
                <a
                  class="nav-link nav-link-black"
                  href="#"
                  role="button"
                  data-bs-toggle="dropdown"
                  aria-expanded="false"
                >
                  <div><?php echo htmlspecialchars($admin['name']); ?></div>
                  <div class="text-muted" style="font-size: 0.85em"><?php echo htmlspecialchars($admin['position']); ?></div>
                </a>
              </div>
              <a
                class="nav-link nav-link-black ms-2"
                href="#"
                id="accountDropdownToggle"
                role="button"
                
                aria-expanded="false"
              >
                <i class="bi bi-list"></i>
              </a>

          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid"  >
      <div class="row vh-100">
        <!-- Main Content Area -->
        <div class="col-10 p-4" style="">
          <h1>Admin Dashboard</h1>
            <!-- Dashboard Cards -->
            <div class="row g-3">
              <div class="col-md-3">
                <div class="card p-3" style="max-height: 250px; width: 840px; margin-left:300px;  ">
                    <div id ="chartContainer" style="height: 200px; width: 800px; border: 3px solid #d1b88a; border-radius: 5px;"> </div>  
                </div>

              </div>
            <div class="row g-3">
              <div class="col-md-3">
                <div class="card p-3" style="max-height: 300px; width: 800px; overflow: auto;margin-left:50px; ">
                  <h4 style="color:#dcaa2e;">Pending Single Orders</h4>
                  <table class="table text-center order-table" style="width: 800px; border-collapse: collapse;">
                    <thead>
                      <tr>
                        <th><h5>Image</h5></th>
                        <th><h5>Order #</h5></th>
                        <th><h5>Details</h5></th>
                        <th><h5>Status</h5></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($order_data as $details): ?>
                        <tr>
                          <td>
                            <img src="<?= htmlspecialchars($details['product_image']) ?>" alt="Product Image" style="width: 70px; height: 70px;">
                          </td>
                          <td>
                          <a href="order_details.php?order_num=<?= urlencode($details['order_num']) ?>">
                              <?= htmlspecialchars($details['order_num']) ?>
                          </a>
                          </td>
                          <td>
                            <?= htmlspecialchars($details['product_name']) ?><br>
                            <?= htmlspecialchars($details['color']) ?><br>
                            <?= htmlspecialchars($details['quantity']) ?> Yards<br>
                            ₱<?= htmlspecialchars($details['total_price']) ?>
                          </td>
                          <td>
                            <?= htmlspecialchars($details['status']) ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
                      </div>
                      
            <div class="col-md-3">
              <div class="card p-3" style="max-height: 300px; width: 600px; overflow: auto; margin-left:500px;"><?php echo '<h4 style="color:#dcaa2e;">Pending Payments: ' . htmlspecialchars($count_not_confirmed) . '</h4>'; ?>
                
                
                <table class="table table-bordered text-center order-table" style="width: 560px; border-collapse: collapse;>
                  <thead class="table-light">
                <tr>
                      <th scope="col">Payment ID</th>
                      <th scope="col">Order Number</th>
                </tr>
                </thead>
                  <tbody>
                <?php
                foreach ($payment_data as $payment) {
                  echo "<tr>";
                    echo "<td>" . htmlspecialchars($payment['payment_id'])   . "</td>";
                    echo "<td><a href='order_details.php?order_num=" . urlencode($payment['order_num']) . "'>" . htmlspecialchars($payment['order_num']) . "</a></td>";
                    echo "</tr>";
                }
                ?>
              </div>
              </tbody>
            </table>
        </div>
            </div>
            <div class="col-md-3">
            </div> 
              </div>
              </div>

          <!-- Larger Cards for Main Content -->
          <div class="row mt-3">
            <div class="col-md-6">
              <!--lagay bulk-->
              <div class="card p-3" style="max-height: 300px; width: 700px; overflow: auto;margin-left:50px; ">
                  <h4 style="color:#dcaa2e;">Pending Bulk Orders</h4>
                  <table class="table text-center order-table" style="width: 800px; border-collapse: collapse;">
                    <thead>
                      <tr>
                        <th><h5>Image</h5></th>
                        <th><h5>Order #</h5></th>
                        <th><h5>Details</h5></th>
                        <th><h5>Status</h5></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($bulk_order_data as $details): ?>
                        <tr>
                          <td>
                            <img src="<?= htmlspecialchars($details['product_image']) ?>" alt="Product Image" style="width: 70px; height: 70px;">
                          </td>
                          <td>
                          <a href="bulkorder_details.php?bulk_order_id=<?= urlencode($details['bulk_order_id']) ?>">
                              <?= htmlspecialchars($details['bulk_order_id']) ?>
                          </a>
                          </td>
                          <td>
                            <?= htmlspecialchars($details['product_name']) ?><br>
                            <?= htmlspecialchars($details['color']) ?><br>
                            <?= htmlspecialchars($details['yards']) ?> Yards<br>
                            <?= htmlspecialchars($details['rolls']) ?> Rolls<br>
                            ₱<?= htmlspecialchars($details['grand_total']) ?>
                          </td>
                          <td>
                            <?= htmlspecialchars($details['status']) ?>
                          </td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                  </table>
                </div>
                        </div>
            <div class="col-md-6">
              <div class="card p-4" style="max-height: 300px; width: 700px;  overflow: auto; margin-left:20px;"><h4 style="color:#dcaa2e;">Top Selling Products</h4>
                <table class="table text-center order-table" style="width: 900px; border-collapse: collapse; ">
                        <thead>
                          <tr>
                            <th><h5>Product</h5></th>
                            <th><h5>       </h5></th>
                            <th><h5>Category</h5></th>
                            <th><h5>Total Sales</h5></th>
                            <th><h5>Stock</h5></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($topsell_data as $topsell_details): ?>
                            <tr>
                              <td>
                                <img src="<?= htmlspecialchars($topsell_details['product_image']) ?>" alt="Product Image" style="width: 70px; height: 70px;">
                              </td>
                              <td>
                                <?= htmlspecialchars($topsell_details['product_name']) ?><br>
                                <?= htmlspecialchars($topsell_details['color']) ?><br>
                                <?= htmlspecialchars($topsell_details['status']) ?> Yards<br>
                                ₱<?= htmlspecialchars($topsell_details['total_price']) ?>
                              </td>
                              <td>
                                <?= htmlspecialchars($topsell_details['category']) ?>
                              </td>
                              <td>
                              ₱<?= htmlspecialchars($topsell_details['total_revenue']) ?>
                              </td>
                              <td>
                                <?= htmlspecialchars($topsell_details['status']) ?>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
          </div>
          <div class="row mt-3">
            <div class="col-md-12">
              <div class="card p-4" style="max-height: 300px; width: 1450px;  overflow: auto; margin-left:20px;"><h4 style="color:#dcaa2e;">Product Overview</h4>
              <a href="product_list.php" style="font-size: 14px; color:#d1b88a; text-decoration: none; margin-left: 95%">
                  View More >>
              </a>
                <table class="table text-center order-table" style="width: 1450px; border-collapse: collapse; ">
                        <thead>
                          <tr>
                            <th><h5>Name</h5></th>
                            <th><h5>        </h5></th>
                            <th><h5>Product ID</h5></th>
                            <th><h5>Category</h5></th>
                            <th><h5>Price</h5></th>
                            <th><h5>Yards</h5></th>
                            <th><h5>Rolls</h5></th>
                            <th><h5>Total Sales</h5></th>
                            <th><h5>Stock</h5></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php foreach ($overview_data as $overview_details): ?>
                            <tr>
                              <td>
                                <img src="<?= htmlspecialchars($overview_details['product_image']) ?>" alt="Product Image" style="width: 70px; height: 70px;">
                              </td>
                              <td>
                                <?= htmlspecialchars($overview_details['product_name']) ?><br>
                              </td>
                              <td>
                                <?= htmlspecialchars($overview_details['product_id']) ?>
                              </td>
                              <td>
                                <?= htmlspecialchars($overview_details['category']) ?>
                              </td>
                              <td>
                              ₱<?= htmlspecialchars($overview_details['price']) ?>
                              </td>
                              <td>
                                <?= htmlspecialchars($overview_details['yards']) ?>
                              </td>
                              <td>
                                <?= htmlspecialchars($overview_details['rolls']) ?>
                              </td>
                              <td>
                              ₱<?= htmlspecialchars($overview_details['total_revenue']) ?>
                              </td>
                              <td>
                                <?= htmlspecialchars($overview_details['status']) ?>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        </tbody>
                  </table>
              </div>
            </div>
          </div>
          
        </div>

        <div class="col-2 sidebar p-3 hidden">
          <div class="list-group">
            <a href="landing.php" class="list-group-item list-group-item-action active">
              <!-- SVG Icon for Dashboard -->
              <img
                src="/SnD_Shoppe-main/Assets/svg(icons)/speedometer.svg"
                alt="Dashboard Icon"
                class="sidebar-icon"
              />
              Dashboard
            </a>
            <a></a>
            <a
              href="product_list.php"
              class="list-group-item list-group-item-action"
            >
              <!-- SVG Icon for Product -->
              <img
                src="/SnD_Shoppe-main/Assets/svg(icons)/basket.svg"
                alt="Product Icon"
                class="sidebar-icon"
              />
              Product
            </a>
            <a></a>
            <a
              href="orders.php"
              class="list-group-item list-group-item-action"
            >
              <!-- SVG Icon for Order -->
              <img
                src="/SnD_Shoppe-main/Assets/svg(icons)/bag-fill.svg"
                alt="Order Icon"
                class="sidebar-icon"
              />
              Order
            </a>
            <a></a>
            <a
              href="user_list.php"
              class="list-group-item list-group-item-action"
            >
              <!-- SVG Icon for User -->
              <img
                src="/SnD_Shoppe-main/Assets/svg(icons)/person-fill.svg"
                alt="User Icon"
                class="sidebar-icon"
              />
              User
            </a>
            <!-- Logout-->
            <a></a>
            <a
              href="logout.php"
              class="list-group-item list-group-item-action"
            >
            <img
                src="\SnD_Shoppe-main\Assets\svg(icons)\logout.png"
                alt="User Icon"
                class="sidebar-icon"
                style="width: 20px; height: 20px;"
              />
              <span style = "color: #dc3545; font-weight: bold;">
              Logout </span>
            </a>
          </div>
        </div>
      </div>
    </div>
              </div>
    <script> //css jaVA SCRIPT
document.addEventListener("DOMContentLoaded", () => {
  const toggleButton = document.getElementById("accountDropdownToggle");
  const sidebar = document.querySelector(".sidebar");

  toggleButton.addEventListener("click", () => {
    sidebar.classList.toggle("hidden");
  });
});


</script>

<script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
<script>//charts
window.onload = function () {

var chart = new CanvasJS.Chart("chartContainer", {
    animationEnabled: true,
    theme: "light2",
    title: {
        text: "Total Sales Per Week"
    },
    axisX: {
        title: "Days",
        interval: 1, // 7-day interval
    },
    axisY: {
        title: "Sales",
        includeZero: true,
    },
    data: [{
        type: "splineArea", 
        color: "#6599FF",
        yValueFormatString: "#,##0",
        dataPoints: <?php echo json_encode($dataPoints, JSON_NUMERIC_CHECK); ?>
    }]
});

chart.render();
}
  
</script>

  </body>
</html>