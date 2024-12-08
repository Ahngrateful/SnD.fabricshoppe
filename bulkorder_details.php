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
$result = $stmt->get_result();
$admin = $result->fetch_assoc(); 
// Check if order number is provided
if (isset($_GET['bulk_order_id'])) {
    $bulk_order_id = $conn->real_escape_string($_GET['bulk_order_id']);
    
    // Query to get order details
    $sql = "
    SELECT 
        bod.*, 
        boi.*, 
        uc.firstname, uc.lastname, uc.address, uc.subdivision, uc.barangay, uc.city, uc.postal, uc.place, phone,
        pr.price, pr.product_image,
        COALESCE(bp.method, cdp.method) AS method,
        COALESCE(bp.confirmation, cdp.confirmation) AS confirmation,
        bp.number AS number,bp.ref_num AS ref_num, bp.acc_name AS acc_name, bp.proof as proof, bp.payment_id as payment_id
    FROM bulk_order_details bod
    JOIN bulk_order_items boi ON bod.bulk_order_id = boi.bulk_order_id
    JOIN users_credentials uc ON bod.customer_id = uc.id
    LEFT JOIN cod_payment cdp ON bod.payment_id = cdp.cod_payment_id
    LEFT JOIN bulk_payment bp ON bod.payment_id = bp.payment_id
    JOIN products pr ON boi.product_id = pr.product_id
    WHERE bod.bulk_order_id = '$bulk_order_id'
    ";

    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $bulk_order_items = [];
        $bulk_order_details = null;

        // Fetch all rows
        while ($row = $result->fetch_assoc()) {
            if (!$bulk_order_details) {
                // Set general order details (only once)
                $bulk_order_details = [
                    'bulk_order_id' => $row['bulk_order_id'],
                    'sub_total' => $row['item_subtotal'],
                    'total_price' => $row['grand_total'],
                    //'shipping_fee' => $row['shipping_fee'],
                    'order_date' => $row['order_date'],
                    'buyer' => $row['firstname'] . ' ' . $row['lastname'],
                    'address' => $row['address'] . ', ' . $row['subdivision'] . ', ' . $row['barangay'] . ', ' . $row['city'] . ', ' . $row['postal'] . ', ' . $row['place'],
                    'payment_method' => $row['method'],
                    'payment_number' => $row['number'],
                    'ref_number' => $row['ref_num'],
                    'proof' => $row['proof'],
                    'confirmation' => $row['confirmation'],
                    'payment_id' => $row['payment_id'],
                    'status' => $row['status'],
                    'rolls' => $row['rolls'],
                    'yards' => $row['yards'],
                    'delivery_method' => $row['delivery_method'],
                    'acc_name' => $row['acc_name'],
                    'phone' => $row['phone'],
                ];
            }

            // Add item details to the list
            $bulk_order_items[] = [
                'product_name' => $row['product_name'],
                'color' => $row['color'],
                'price' => $row['price'],
                'product_image' => $row['product_image'],
                'item_subtotal' => $row['item_subtotal'],
                
            ];
        }
    } else {
       
    }
} else {
    echo "<p>No order number provided.</p>";
    exit;
}


if (isset($_POST['confirm']) && strtolower($_POST['confirm']) === 'confirmed') {
    if (isset($bulk_order_details['payment_id'])) {
        $confirmation = $_POST['confirm'];
        $payment_id = $bulk_order_details['payment_id'];

        // Prepare and execute update query
        $query_confirm = "UPDATE bulk_payment SET confirmation = ? WHERE payment_id = ?";
        $stmt_confirm = $conn->prepare($query_confirm);
        if ($stmt_confirm) {
            $stmt_confirm->bind_param("si", $confirmation, $payment_id);
            if ($stmt_confirm->execute()) {
                echo "<script>alert('Payment confirmed successfully!');</script>";
            } else {
                echo "<script>alert('Failed to confirm payment.');</script>";
            }
            $stmt_confirm->close();
        } else {
            echo "<script>alert('Failed to prepare confirmation query.');</script>";
        }
    } else {
        echo "<p>Error: Payment ID not found.</p>";
        exit;
    }
}


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
    <link rel="stylesheet" href="/admin_pbl/order_d.css" />
    <link rel="icon" href="\SnD_Shoppe-main\PIC\sndlogo.png" type="logo" />
    <title>S&D Fabrics</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@100..900&family=Playfair+Display+SC:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');

/* Body Styling */
body {
    background: url(/Assets/images/) rgba(0, 0, 0, 0.3);
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
    padding: 10px 16px;
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
    margin: 0;
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

h1 {
    font-family: "Playfair Display SC", serif;
    font-size: 100px;
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
    background-color: #faf4e1;
    border: 1px solid #d1b88a;
    border-radius: 5px;
    font-weight: bold;
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
    background-color: #f1e8d9 !important; 
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

    /* Adjust Product Search layout */
    .product-search {
        flex-direction: column;
        gap: 5px;
        justify-content: left;
    }
}

.col-10.p-4 {
    flex: 1;
    padding: 20px;
    background-color: #FFF8DC;
}

.col-10.p-4 h1 {
    font-size: 24px;
    margin-bottom: 15px;
    color: #4b4b4b;
}

.add-new {
    background-color: #f1e8d9;
}

/* Product Search Bar Styling */
.product-search {
    display: flex;
    justify-content: flex-end; 
    gap: 10px; 
    margin-bottom: 20px;
}

/* Product Table Styling */
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

/* Add Product Container Styling */
.container_addproduct {
    width: 600px;
    background-color: #fdf2d9;
    padding: 20px;
    align-items: center; 
    justify-content: center; 
    border-radius: 10px;
    max-width: 800px;
    margin: auto;
}

.container_addproduct h1 {
    font-size: 24px;
    margin-bottom: 20px;
    text-transform: uppercase;
    font-family: "Playfair Display SC", serif;
    color: #1e1e1e;
}

.section {
    background-color: #fef8e6;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.hidden {
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

    <div class= "print">
    <div class="container-fluid">
      <div class="row vh-100">
        <!-- Main Content Area -->
        <div class="col-10 p-4" style="margin-left: auto">
          <div class="container my-4">
            <h1>Order # <?php echo htmlspecialchars($bulk_order_details['bulk_order_id']); ?> </h1>

            <!-- Order List Search Bar (Matching Navbar Search Bar) -->
            <div class="d-flex justify-content-between mb-3">
              <div class="input-group w-25">
                <!--span class="input-group-text" id="basic-addon1">
                  <i class="bi bi-search search-icon"></i>
                </span>
                  <input type="search" 
                    class="form-control" 
                    placeholder="Search..." 
                    id="orderSearchInput" 
                    onkeyup="filterOrders()" /-->
              </div>

              <!-- print receipt-->
              <button id="receipt" class="btn btn-warning">
                Print Receipt
              </button>
            </div>

            <!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Receipt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered text-center order-table">
                        <thead class="table-light">
                            <tr>
                                <th colspan="2">
                                    <img src="/SnD_Shoppe-main/PIC/sndlogo.png" width="70px" alt="Logo" />
                                    <p>Manila</p>
                                    <p>Send Date: <?= $bulk_order_details['order_date']; ?></p>
                                    <p>Order ID: <?= $bulk_order_details['bulk_order_id']; ?></p>
                                </th>
                            </tr>
                            <tr>
                                <th colspan="2">
                                    <p>Track Number: Not Available</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Buyer</td>
                                <td>
                                    <p><?= $bulk_order_details['buyer']; ?> - <?= $bulk_order_details['phone']; ?></p>
                                    <p><?= $bulk_order_details['address']; ?></p>
                                </td>
                            </tr>
                            <tr>
                                <td>Seller</td>
                                <td>
                                    <p>S&D Fabric Shoppe - +639183116920</p>
                                    <p>Divisoria, Manila</p>
                                </td>
                            </tr>
                            <tr>
                                <td>Product Details</td>
                                <td>
                                    <?php foreach ($bulk_order_items as $item): ?>
                                        <p><?= $item['product_name']; ?> (<?= $item['color']; ?>) - <?= $bulk_order_details['yards']; ?> yards, <?= $bulk_order_details['rolls'];?> rolls</p>
                                    <?php endforeach; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>Payment Info</td>
                                <td>
                                    <p>Method: <?= $bulk_order_details['payment_method']; ?></p>
                                    <p>Total Amount: ₱<?= $bulk_order_details['total_price']; ?></p>
                                    
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">Thank you for shopping with us!</td>
                            </tr>
                        </tbody>
                    </table>
                    <button id="downloadReceipt" class="btn btn-success">
                        Download as PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

            <div class="row">
              <!-- Left Side: Order Items -->
              <div class="col-md-8">
                <h4 class="mb-3">Bulk All Items</h4>

                <!-- Order Items -->
<div class="card mb-3">
    <?php 
    if (!empty($bulk_order_items)) {
        foreach ($bulk_order_items as $item) { 
    ?>
        <div class="row g-0 align-items-center">
            <div class="col-md-3">
                <img src="<?= htmlspecialchars($item['product_image']) ?>" class="img-fluid rounded-start" alt="Product Image"> <!-- its not showing what the fuck. even hard code-->
            </div>
            <div class="col-md-9">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($item['product_name'] . ' (' . $item['color'] . ')'); ?></h5>
                    <p class="mb-1">BUYER: <?php echo htmlspecialchars($bulk_order_details['buyer']); ?></p>
                    <p class="mb-1">Yards: <?php echo htmlspecialchars($bulk_order_details['yards']); ?></p>
                    <p class="mb-1">Rolls: <?php echo htmlspecialchars($bulk_order_details['rolls']); ?></p>
                    <p class="fw-bold">Price: P<?php echo htmlspecialchars($item['item_subtotal']); ?></p>
                </div>
            </div>
        </div>
    <?php 
        } 
    } else {
        echo "<p>No items found for this order.</p>";
    }
    ?>
                </div>
                <!-- Cart Totals -->
                <div class="mt-4 p-3 rounded">
                  <h5>Cart Totals</h5>
                  <ul class="list-group">
                    <li class="list-group-item d-flex justify-content-between">
                    <span>Subtotal:</span><span>P <?php echo htmlspecialchars($bulk_order_details['sub_total']); ?></span>
                    </li>
                    <a></a>
                    <!--li class="list-group-item d-flex justify-content-between">
                    <span>Shipping Fee:</span><span>P ?php echo htmlspecialchars($order_details['shipping_fee']); ?></span>
                    </li-->
                    <a></a>
                    <li
                      class="list-group-item d-flex justify-content-between fw-bold"
                    >
                    <span>Total Price:</span><span>P <?php echo htmlspecialchars($bulk_order_details['total_price']); ?></span>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Right Side: Order Summary -->
              <div class="col-md-4">
                <div class="p-3 rounded mb-4">
                  <h5>Summary</h5>
                  <ul class="list-group">
                    <li class="list-group-item">
                    Order ID: <span class="fw-bold"><?php echo htmlspecialchars($bulk_order_details['bulk_order_id']); ?></span>
                    </li>
                    <a></a>
                    <li class="list-group-item">
                    Date: <span><?php echo htmlspecialchars($bulk_order_details['order_date']); ?></span>
                    </li>
                    <a></a>
                    <li class="list-group-item">
                    Total: <span class="fw-bold text-danger">P <?php echo htmlspecialchars($bulk_order_details['total_price']); ?></span>
                    </li>
                  </ul>
                </div>
                <div class="col-md-11">
                  <!-- Shipping Address -->
                  <div
                    class="p-3 rounded mb-4"
                    style="background-color: #f1e8d9; border: 2px solid #d9b65d"
                  >
                    <h3 class="mb-3">Shipping Information </h3>
                    <h5 class="mb-3">Shipping Address</h5>
                    <p class="m-0"><?php echo htmlspecialchars($bulk_order_details['address']) ?></p></br>
                    <h5 class="mb-0">Delivery Method: <?php echo htmlspecialchars($bulk_order_details['delivery_method']) ?> 
                    </h5>
                   
  </div>

                  <!-- Payment Method -->
                  <div
                    class="p-3 rounded"
                    style="background-color: #f1e8d9; border: 2px solid #d9b65d"
                  >
                    <h3 class="mb-3">Payment Information</h3>
                    <p class="m-0"><span style="font-weight: bold;">E-wallet: </span><?php echo htmlspecialchars($bulk_order_details['payment_method']); ?></p>
                    <p class="m-0"><span style="font-weight: bold;">Account Name: </span><?php echo htmlspecialchars($bulk_order_details['acc_name']); ?></p>
                    <p class="m-0"><span style="font-weight: bold;">E-wallet Number: </span><?php echo htmlspecialchars($bulk_order_details['payment_number']); ?></p>
                    <p class="m-0"><span style="font-weight: bold;">Reference # </span><?php echo htmlspecialchars($bulk_order_details['ref_number']); ?></p>
                    <p class="m-0"><span style="font-weight: bold;">Status: </span><?php echo htmlspecialchars($bulk_order_details['confirmation']); ?></p>
                    <img src="<?= htmlspecialchars($bulk_order_details['proof']) ?>" alt="Proof Image" style="width: 300px; height: 350px; justify-content: left; "></br>
                    <form method="POST" action="">
                        
                        <button id="confirm" name="confirm" value="Confirmed" class="btn btn-warning">Confirm payment</button>
                    </form>
   
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        
  
        <!-- Sidebar -->
        <div class="col-2 sidebar p-3 hidden">
          <div class="list-group">
            <a href="landing.php" class="list-group-item">
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
              class="list-group-item list-group-item-action list-group-item-action active"
            >
              <img
                src="/SnD_Shoppe-main//Assets/svg(icons)/bag-fill.svg"
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
              <img
                src="/SnD_Shoppe-main/Assets/svg(icons)/person-fill.svg"
                alt="User Icon"
                class="sidebar-icon"
              />
              User List
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<script> //css jaVA SCRIPT
document.addEventListener("DOMContentLoaded", () => {
  const toggleButton = document.getElementById("accountDropdownToggle");
  const sidebar = document.querySelector(".sidebar");

  toggleButton.addEventListener("click", () => {
    sidebar.classList.toggle("hidden");
  });
});
</script>
<script>
  // Download the modal as PDF
document.getElementById("downloadReceipt").addEventListener("click", function () {
    // Select the table only
    const tableContent = document.querySelector("#receiptModal .table-responsive");

    // Create a new jsPDF instance
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Use html2canvas to render the table into an image for jsPDF
    html2canvas(tableContent).then((canvas) => {
        const imgData = canvas.toDataURL("image/png");

        // Add the image to the PDF
        doc.addImage(imgData, "PNG", 10, 10, 190, 0); // Fit the image in the PDF
        doc.save("receipt-table.pdf"); // Save the PDF
    });
});

document.getElementById("receipt").addEventListener("click", function () {
    const currentStatus = this.getAttribute("data-status"); // Get the status from the button

        const receiptModal = new bootstrap.Modal(document.getElementById("receiptModal"));
        receiptModal.show();
  
});

  document.querySelectorAll('.dropdown-item').forEach(item => {
    item.addEventListener('click', function (e) {
        e.preventDefault();
        const selectedValue = this.getAttribute('data-value');
        const dropdownType = this.getAttribute('data-type'); // To distinguish between dropdowns

        if (dropdownType === 'confirmation') {
            const confirmbutton = document.getElementById('confirmation');
            confirmbutton.textContent = this.textContent; // Update button text
            confirmbutton.dataset.value = selectedValue; // Store the selected value
        } else if (dropdownType === 'status') {
            const statusbutton = document.getElementById('status');
            statusbutton.textContent = this.textContent; // Update button text
            statusbutton.dataset.value = selectedValue; // Store the selected value
        }

        // Prepare data to send
        const data = {
            payment_id: '<?php echo $bulk_order_details["payment_id"]; ?>',
            confirmation: document.getElementById('confirmation').dataset.value || '',
            bulk_order_id: '<?php echo $bulk_order_details["bulk_order_id"]; ?>',
            status: document.getElementById('status').dataset.value || '',
        };

        // Send the selected values to the server
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Update successful!');
            } else {
                alert('Failed to update: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating.');
        });
    });
});



</script>
  </body>
</html>