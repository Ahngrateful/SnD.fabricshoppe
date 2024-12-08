
<?php
session_start();

$servername = "localhost";
$dbname = "db_sdshoppe";
$username = "root";  
$password = "";  

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

if (!isset($_SESSION['user_id'])) {
    header("Location: haveacc.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user profile data
$profile_data = [];
$stmt = $pdo->prepare('SELECT lastname, firstname FROM users_credentials WHERE id = :user_id');
$stmt->execute(['user_id' => $user_id]);
$profile_data = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT 
        od.order_num, 
        od.total_price, 
        od.sub_total, 
        od.status, 
        oi.product_name, 
        oi.color, 
        oi.quantity, 
        p.product_image, p.product_id
    FROM order_details od
    JOIN order_items oi ON od.order_num = oi.order_num
    JOIN products p ON oi.product_id = p.product_id
    WHERE od.customer_id = :user_id
");
$stmt->execute([':user_id' => $user_id]);
$order_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("
    SELECT 
        od.bulk_order_id, 
        od.grand_total, 
        od.status, 
        oi.product_name,
        oi.item_subtotal, 
        oi.color, 
        oi.yards, 
        oi.rolls, 
        p.product_image, p.product_id
    FROM bulk_order_details od
    JOIN bulk_order_items oi ON od.bulk_order_id = oi.bulk_order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE od.customer_id = :user_id
");
$stmt->execute([':user_id' => $user_id]);
$bulk_order_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch unread notifications
$query_notifications = "SELECT notif_id, message, created_at FROM notifications WHERE id = ? AND is_read = 0 ORDER BY created_at DESC";
$stmt_notifications = $pdo->prepare($query_notifications);
$stmt_notifications->bindValue(1, $user_id, PDO::PARAM_INT);
$stmt_notifications->execute();
$result_notifications = $stmt_notifications->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['notif_id']) && is_numeric($_GET['notif_id'])) {
    $notif_id = intval($_GET['notif_id']);

    $query_check_notif = "SELECT notif_id FROM notifications WHERE notif_id = ? AND id = ?";
    $stmt_check_notif = $pdo->prepare($query_check_notif);
    $stmt_check_notif->execute([$notif_id, $user_id]);

    if ($stmt_check_notif->rowCount() > 0) {
        // Mark the notification as read
        $query_update_read = "UPDATE notifications SET is_read = 1 WHERE notif_id = ?";
        $stmt_update_read = $pdo->prepare($query_update_read);
        $stmt_update_read->execute([$notif_id]);

        echo json_encode(['status' => 'success', 'message' => 'Notification marked as read']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Notification not found or does not belong to this user']);
    }
}

// Query to count unread notifications
$query_count_unread = "SELECT COUNT(*) FROM notifications WHERE id = ? AND is_read = 0";
$stmt_count_unread = $pdo->prepare($query_count_unread);
$stmt_count_unread->bindValue(1, $user_id, PDO::PARAM_INT);
$stmt_count_unread->execute();
$unread_count = $stmt_count_unread->fetchColumn();


// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btnreview'])) {
    $product_id = $_POST['product_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $rating = $_POST['rating'];

    $stmtUser = $pdo->prepare('SELECT firstname, lastname FROM users_credentials WHERE id = :user_id');
    $stmtUser->execute(['user_id' => $user_id]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    $firstname = $user['firstname'];
    $lastname = $user['lastname'];

    // Insert the review into the database
    $stmt = $pdo->prepare("INSERT INTO product_ratings (product_id, user_id, user_firstname, user_lastname, title, description, rating, time) 
                           VALUES (:product_id, :user_id, :firstname, :lastname, :title, :description, :rating, NOW())");
    $stmt->execute([
        'product_id' => $product_id,
        'user_id' => $user_id,
        'firstname' => $firstname,
        'lastname' => $lastname,
        'title' => $title,
        'description' => $description,
        'rating' => $rating
    ]);


    
    echo '<script type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            // Create the overlay
            const overlay = document.createElement("div");
            overlay.style.position = "fixed";
            overlay.style.top = "0";
            overlay.style.left = "0";
            overlay.style.width = "100%";
            overlay.style.height = "100%";
            overlay.style.background = "linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.3)), url(\'Assets/bgLogin.png\')";
            overlay.style.zIndex = "999"; // Behind the popup but above the content

            // Create the popup
            const popup = document.createElement("div");
            popup.style.position = "fixed";
            popup.style.top = "50%";
            popup.style.left = "50%";
            popup.style.transform = "translate(-50%, -50%)";
            popup.style.padding = "20px";
            popup.style.backgroundColor = "#dcaa2e";
            popup.style.color = "white";
            popup.style.borderRadius = "5px";
            popup.style.boxShadow = "0 4px 8px rgba(0, 0, 0, 0.2)";
            popup.style.zIndex = "1000";
            popup.innerText = "Thank you for taking the time to leave a review! Your insights mean a lot to us, it helps us identify areas for improvement and deliver a better experience for you and all our customers.";

            // Append overlay and popup to the document
            document.body.appendChild(overlay);
            document.body.appendChild(popup);

            // Automatically redirect after 2 seconds
            setTimeout(() => {
                window.location.href = "mypurchase.php";
            }, 1000);
        });
        </script>';
    exit;
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
    <link rel="stylesheet" href="css\mypurchase.css" />
    <link rel="icon" href="PIC/sndlogo.png" type="image/png" />
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
      crossorigin="anonymous"
    ></script>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"
    />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <title>S&D Fabrics Dashboard</title>

    <style>
      body {
        font-family: "Playfair Display", serif;
      }
      .table {
        border-collapse: separate;
        border-spacing: 0 0.4rem; /* Adds spacing between rows */
        background-color: transparent;
      }

      .table thead th {
        font-size: 0.95rem;
        background-color: #B5A888;
        border: none;
      }

      .table img {
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      }

      .table-hover tbody tr:hover {
        background-color: #f1f3f5;
      }
      .badge-to-pay {
        background-color: #dcaa2e;
        color: #fff; 
      }
      .badge-to-ship {
        background-color: #a5a524; 
        color: #fff; 
      }
      .badge-to-receive {
        background-color: #6d9d2e; 
        color: #fff;
      }
      .badge-completed {
        background-color: #008253; 
        color: #fff;
      }
      .badge-cancelled {
        background-color: #B02A2B; 
        color: #fff;
      }
      .badge-other {
        background-color: #89a53c; 
        color: #fff;
      }
      .nav-tabs .nav-link {
        border-radius: 3; 
        color: #1e1e1e; 
      }
      .nav-tabs .nav-link:hover {
        color: #1e1e1e; 
        background-color: #B5A888; 
        border-bottom: 2px solid #007bff; 
      }
      .nav-tabs .nav-link.active {
        color: #1e1e1e; 
        text-decoration: underline;
        background-color: #B5A888; 
        border-bottom: 2px solid #007bff; 
      }
      .nav-tabs {
        border-bottom: none;
      }

      #notification-dropdown {
    position: absolute;
    top: 70px; /* Adjust as per your layout */
    right: 20px;
    width: 500px;
    background-color: white;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    display: none; /* Initially hidden */
    max-height: 300px;
    overflow-y: auto;
}

#notification-dropdown li {
    padding: 10px 16px;
    color: #333;
    cursor: pointer;
}

#notification-dropdown li:hover {
    background-color: #f1e8d9;
}

.badge-danger {
    background-color: #dc3545;
    color: white;
    font-size: 14px;
    padding: 4px 8px;
    border-radius: 50%;
}
#unread-count {
    display: inline-block; /* Ensures it's visible initially */
    background: red;
    color: white;
    border-radius: 50%;
    padding: 2px 5px;
    font-size: 12px;
    position: absolute;
    margin-left:-10px;
}

/* Button used to open the contact form - fixed at the bottom of the page */
.open-button {
    background-color: #dcc07a;
    font-family: "Playfair Display", serif;
    border: 1px solid #000000;
    text-decoration: none;
    padding: 3px;
    color: #1e1e1e;
    width: 100px;
    text-align: center;
}

.open-button:hover {
    background-color: #B5A888;

}

/* The popup form - hidden by default */
.form-popup {
    display: none; /* Will be overridden when visible */
    position: fixed;
    top: 55%;
    left: 50%;
    transform: translate(-50%, 10%); /* Start from below */
    border: 1px solid #dcaa2e;
    z-index: 1060; /* Higher z-index than the overlay */
    background-color: white;
    padding: 10px;
    border-radius: 8px;
    max-width: 700px;
    width: 100%;
    opacity: 0;
    height: auto;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); 
    row-gap: 5px;
    transition: opacity 0.3s ease, transform 0.3s ease;
}

.form-popup.show {
    display: block;
    opacity: 1;
    transform: translate(-50%, -50%); /* Move to its final position */
}

.form-popup h1 {
    font-size: 28px;
    font-weight: bold;
    color: #dcaa2e;
    justify-self: center;
    text-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.buttons button {
    font-family: "Playfair Display", serif;
    border: 1px solid #000000;
    text-decoration: none;
    padding: 3px;
    color: #1e1e1e;
    text-align: center;
    margin-top: 30px;
}

.buttons {
    float: right; /* Floats the button to the right */
    display: flex;
    gap: 10px;
}

.btnReview {
    background-color: #dcc07a;
}

.btnReview:hover {
    background-color: #E2D1A7;
}

.btnCancel {
    background-color: #B5A888;
}
.btnCancel:hover {
    background-color: #BFBAAC;
}

.review-label {
    display: block; 
    text-align: center; 
    font-size: 16px; 
    margin-bottom: 10px; 
    font-weight: bold; 
    color: #333; 
}

.product-rev {
    color: #dcaa2e;
    font-weight: bold;
}

/* Overlay to dim the background */
.overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 1050; /* Ensure overlay appears above other content */ /* Lower than the popup */
    opacity: 0;
    transition: opacity 0.3s ease;
}

/* Show popup and overlay */
.form-popup.show {
    display: block;
    opacity: 1;
}

.overlay.show {
    display: block;
    opacity: 1;
}

/* Add styles to the form container */
.form-container {
    max-height: 100%;
    overflow-y: auto; /* Enable vertical scrolling */
    padding: 10px;
    background-color: white;
    box-sizing: border-box; /* Include padding in width/height */
}

/* Full-width input fields */
.form-container input[type=text] {
    height: 40px;
    width: 100%;
    padding: 15px;
    margin: 3% 0 22px 0;
    border: none;
    background: #f1f1f1;
    box-sizing: border-box; /* Include padding in width/height */
    resize: none; /* Disable resizing for textareas */
    } 

.form-container .box {
    width: 100%;
    padding: 15px;
    margin: 3% 0 22px 0;
    background: #f1f1f1;
    box-sizing: border-box; /* Include padding in width/height */
    resize: none; /* Disable resizing for textareas */
}

.rating-box {
    width: 100%;
    padding: 15px;
    border: none;
    background: #f1f1f1;
    box-sizing: border-box; /* Include padding in width/height */
    resize: none; /* Disable resizing for textareas */
}

/* When the inputs get focus, do something */
.form-container input[type=text]:focus, .rating-box:focus {
  background-color: #ddd;
  outline: none;
}
    </style>
  </head>
  <body class="vh-100">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark main-navbar">
      <div
        class="container-fluid d-flex justify-content-between align-items-center"
      >
        <a class="navbar-brand fs-4" href="homepage.php">
          <img src="PIC/sndlogo.png" width="70px" alt="Logo" />
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

          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a
                class="nav-link nav-link-black active"
                aria-current="page"
                href="cart.php" 
              >
                <img src="/SnD_Shoppe-main/Assets/svg(icons)/shopping_cart.svg" alt="cart" />
              </a>
            </li>
            <li class="nav-item">
                        <a class="nav-link nav-link-black" href="#" id="notification-icon">
                            <img src="Assets/svg(icons)/notifications.svg" alt="notif">
                            <?php if ($unread_count > 0): ?>
                                <span class="badge badge-danger" id="unread-count"><?php echo $unread_count; ?></span>
                            <?php endif; ?>
                        </a>
                        <ul id="notification-dropdown">
                            <?php
                            if (empty($result_notifications)) {
                                echo '<li>No new notifications</li>';
                            } else {
                            foreach ($result_notifications as $notification) {
                                echo '<li data-notif-id="' . htmlspecialchars($notification['notif_id']) . '">' 
                                    . htmlspecialchars($notification['message']) . '</br>' . date('m-d-y', strtotime($notification['created_at'])) .'</li>';
                            } }
                            ?>
                        </ul>
                    </li>

            <!-- Account Dropdown Menu -->
            <li class="nav-item dropdown">
              <a
                class="nav-link nav-link-black dropdown-toggle"
                href="#"
                id="accountDropdown"
                role="button"
                data-bs-toggle="dropdown"
                aria-expanded="false"
              >
                <img
                  src="/SnD_Shoppe-main/Assets/svg(icons)/account_circle.svg"
                  alt="account"
                />
              </a>
              <ul
                class="dropdown-menu dropdown-menu-end"
                aria-labelledby="accountDropdown"
              >
                <li>
                  <a
                    class="dropdown-item"
                    href="accountSettings.php"
                    >My Account</a
                  >
                </li>
                <li>
                  <hr class="dropdown-divider" />
                </li>
                <li>
                   <a class="dropdown-item" href="homepage.php">Home</a>
                </li>
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                  <a class="dropdown-item text-danger" href="logout.php">Logout</a>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Sidebar -->
    <div
      class="d-flex flex-column flex-shrink-0 p-3 text-white bg-dark"
      id="sidebar"
      style="width: 250px; height: 100vh; position: fixed"
    >
      <ul class="nav nav-pills flex-column mb-auto">
        <li>
          <a href="mypurchase.php" class="nav-link custom-active" aria-current="page">
            <i class="bi bi-box-seam"></i> Orders
          </a>
        </li>
        <li>
          <a href="cart.php" class="nav-link text-white">
            <i class="bi bi-heart"></i> Saved Items
          </a>
        </li>
        <li>
          <a
            href="accountSettings.php"
            class="nav-link text-white"
          >
            <i class="bi bi-person"></i> Account Settings
          </a>
        </li>
      </ul>
    </div>

    <!-- Main Dashboard Layout with Content -->
    <div class="d-flex" style="margin-top: 96px; margin-left: 250px">
      <!-- Main Content -->
      <div class="flex-grow-1 p-4">
    <!-- Overview Card -->
      <div class="card" style="background-color: #f1e8d9">
          <div class="card-body">
              <h2 class="card-title">My Purchases</h2>
              <p class="card-text">
                  Welcome back, <span style="font-weight: bold; font-size:large;"><?php echo htmlspecialchars($profile_data['firstname']); ?></span>! Here’s a summary of your recent activities.
              </p>

            <!-- Order Status Tabs and Search Bar -->
            <div class="order-status-container">
              <!-- Tabs for Order Statuses -->
              <ul class="nav nav-tabs" id="orderStatusTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button
                    class="nav-link active"
                    id="all-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#all"
                    type="button"
                    role="tab"
                    aria-controls="all"
                    aria-selected="true"
                    style="border: none; border-bottom: 3px solid transparent; transition: all 0.3s; font-size: 0.9rem;"
                  >
                    All
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button
                    class="nav-link"
                    id="to-pay-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#to-pay"
                    type="button"
                    role="tab"
                    aria-controls="to-pay"
                    aria-selected="false"
                    style="border: none; border-bottom: 3px solid transparent; transition: all 0.3s; font-size: 0.9rem;"
                  >
                    To Pay
                  </button>
                  
                </li>
                <li class="nav-item" role="presentation">
                  <button
                    class="nav-link"
                    id="to-ship-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#to-ship"
                    type="button"
                    role="tab"
                    aria-controls="to-ship"
                    aria-selected="false"
                    style="border: none; border-bottom: 3px solid transparent; transition: all 0.3s; font-size: 0.9rem;"
                  >
                    To Ship
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button
                    class="nav-link"
                    id="to-receive-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#to-receive"
                    type="button"
                    role="tab"
                    aria-controls="to-receive"
                    aria-selected="false"
                    style="border: none; border-bottom: 3px solid transparent; transition: all 0.3s; font-size: 0.9rem;"
                  >
                    Ship Out
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button
                    class="nav-link"
                    id="completed-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#completed"
                    type="button"
                    role="tab"
                    aria-controls="completed"
                    aria-selected="false"
                    style="border: none; border-bottom: 3px solid transparent; transition: all 0.3s; font-size: 0.9rem;"
                  >
                    Completed
                  </button>
                </li>
                
              </ul>

              <!-- Search Bar within the Tab Section -->
              <form class="order-status-search-bar mt-3" role="search">
                <div class="input-group">
                  <span class="input-group-text" id="order-status-search-icon">
                    <i class="bi bi-search"></i>
                  </span>
                  <input
                    class="form-control"
                    type="search"
                    placeholder="Search orders..."
                    aria-label="Search orders"
                    aria-describedby="order-status-search-icon"
                  />
                </div>
              </form>
            </div>
            <!-- Tab Content -->
            <div class="tab-content" id="orderStatusTabContent">
              
              <!-- All Orders -->
              <div
                class="tab-pane fade show active"
                id="all"
                role="tabpanel"
                aria-labelledby="all-tab"
              >
              <h3 class="mt-4">Order Status</h3>
              <div class="table-responsive">
              <table class="table table-striped table-hover align-middle">
                  <thead class="table-dark">
                      <tr>
                          <th></th>
                          <th>Order Number</th>
                          <th>Product</th>
                          <th>Color</th>
                          <th>Yards</th>
                          <th>Item Subtotal</th>
                          <th>Grand Total</th>
                          <th>Status</th>
                      </tr>
                  </thead>
                  <tbody>
                      <?php 
                      $grouped_orders = [];

                      // Group orders by order number
                      foreach ($order_data as $details) {
                          $order_num = $details['order_num'];
                          if (!isset($grouped_orders[$order_num])) {
                              $grouped_orders[$order_num] = [
                                  'order_num' => $details['order_num'],
                                  'status' => $details['status'],
                                  'sub_total' => $details['sub_total'],
                                  'total_price' => $details['total_price'],
                                  'items' => []
                              ];
                          }
                          $grouped_orders[$order_num]['items'][] = $details;
                      }

                      // Display grouped orders
                      foreach ($grouped_orders as $order): ?>
                          <tr>
                              <!-- Product Image and Details -->
                              <td>
                                  <?php 
                                  $displayed_images = []; // Array to track displayed images
                                  foreach ($order['items'] as $item): 
                                      if (!in_array($item['product_image'], $displayed_images)): // Check if the image is not already displayed
                                          $displayed_images[] = $item['product_image']; // Add the image to the displayed list
                                  ?>
                                      <div>
                                        <img src="<?= htmlspecialchars($item['product_image']); ?>" alt="Product Image" class="rounded" style="width: 60px; height: 60px; object-fit: cover; margin-top: 5px; margin-bottom: 5px; margin-left: 5px;">
                                      </div>
                                  <?php 
                                      endif;
                                  endforeach; 
                                  ?>
                              </td>
                              <!-- Order Number -->
                              <td><strong><?= htmlspecialchars($order['order_num']); ?></strong></td>
                              <!-- Product Details -->
                              <td>
                                  <?php foreach ($order['items'] as $item): ?>
                                      <div>
                                          <strong><?= htmlspecialchars($item['product_name']); ?></strong>
                                      </div>
                                  <?php endforeach; ?>
                              </td>
                              <!-- Color -->
                              <td>
                                  <?php foreach ($order['items'] as $item): ?>
                                      <div>
                                          <?= htmlspecialchars($item['color']); ?>
                                      </div>
                                  <?php endforeach; ?>
                              </td>
                              <!-- Yards -->
                              <td>
                                  <?php foreach ($order['items'] as $item): ?>
                                      <div>
                                          <?= htmlspecialchars($item['quantity']); ?>
                                      </div>
                                  <?php endforeach; ?>
                              </td>
                              <!-- Subtotal -->
                              <td style="color: #dcaa2e;">₱<?= htmlspecialchars($order['sub_total']); ?></td>
                              <!-- Grand Total -->
                              <td class="text-success"><strong>₱<?= htmlspecialchars($order['total_price']); ?></strong></td>
                              <!-- Status -->
                              <td>
                                  <span class="badge 
                                      <?= $order['status'] === 'To Pay' ? 'badge-to-pay' : 
                                        ($order['status'] === 'To Ship' ? 'badge-to-ship' : 
                                        ($order['status'] === 'To Receive' ? 'badge-to-receive' : 
                                        ($order['status'] === 'Completed' ? 'badge-completed' : 
                                        ($order['status'] === 'Cancelled' ? 'badge-cancelled' : 'badge-other')))); ?>">
                                      <?= htmlspecialchars($order['status']); ?>
                                  </span>
                                  
                                  <?php if ($order['status'] === 'Completed'): ?>
                                        <?php foreach ($order['items'] as $item): ?>
                                            <button class="open-button" onclick="openForm(<?= htmlspecialchars($item['product_id']); ?>, '<?= htmlspecialchars($item['product_name']); ?>')">
                                                Add Review
                                            </button>
                                        <?php endforeach; ?>

                                        <!-- The overlay -->
                                        <div id="overlay" class="overlay"></div>

                                        <!-- The popup form -->
                                        <div class="form-popup" id="myForm">
                                            <form action="" method="POST" class="form-container">
                                                <h1>Add Review</h1>

                                                <input type="hidden" name="product_id" id="product_id_input" value="" />

                                                <label for="title" class="review-label">
                                                    Review Product:
                                                    <span id="product_name"></span>
                                                </label>

                                                <label for="title">Review Title *</label>
                                                <input type="text" placeholder="Enter Title" name="title" required />

                                                <label for="descript">Review Description</label>
                                                <textarea name="description" class="rating-box" placeholder="Enter Review Description" maxlength="1000" cols="5" rows="3"></textarea>

                                                <label class="descript">Review Rating <span>*</span></label>
                                                <select name="rating" class="rating-box" required>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select>

                                                <div class="buttons">
                                                    <button type="button" class="btnCancel" onclick="closeForm()">Cancel</button>
                                                    <button type="submit" name="btnreview" class="btnReview">Submit Review</button>
                                                </div>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                              </td>
                          </tr>
                      <?php endforeach; ?>
                  </tbody>
              </table>
              </div>
              <h3 class="mt-4">Bulk Order Status</h3>
              <div class="table-responsive">
                  <table class="table table-striped table-hover align-middle">
                      <thead class="table-dark">
                          <tr>
                              <th></th>
                              <th>Order Number</th>
                              <th>Product Details</th>
                              <th>Color</th>
                              <th>Yards</th>
                              <th>Rolls</th>
                              <th>Item Subtotal</th>
                              <th>Grand Total</th>
                              <th>Status</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php 
                          $grouped_bulk_orders = [];

                          // Group bulk orders by bulk_order_id
                          foreach ($bulk_order_data as $details) {
                              $bulk_order_id = $details['bulk_order_id'];
                              if (!isset($grouped_bulk_orders[$bulk_order_id])) {
                                  $grouped_bulk_orders[$bulk_order_id] = [
                                      'bulk_order_id' => $details['bulk_order_id'],
                                      'status' => $details['status'],
                                      'grand_total' => $details['item_subtotal'],
                                      'grand_total' => $details['grand_total'],
                                      'items' => []
                                  ];
                              }
                              $grouped_bulk_orders[$bulk_order_id]['items'][] = $details;
                          }

                          // Display grouped BULK ORDERS
                          foreach ($grouped_bulk_orders as $order): ?>
                              <tr>
                                  <!-- Product Images -->
                                  <td>
                                      <?php 
                                      $displayed_images = []; // Array to track displayed images
                                      foreach ($order['items'] as $item): 
                                          if (!in_array($item['product_image'], $displayed_images)): // Check if the image is not already displayed
                                              $displayed_images[] = $item['product_image']; // Add the image to the displayed list
                                      ?>
                                          <div>
                                              <img src="<?= htmlspecialchars($item['product_image']); ?>" alt="Product Image" class="rounded" style="width: 60px; height: 60px; object-fit: cover; margin-top: 5px; margin-bottom: 5px; margin-left: 5px;">
                                          </div>
                                      <?php 
                                          endif;
                                      endforeach; 
                                      ?>
                                  </td>
                                  <!-- Order Number -->
                                  <td><strong><?= htmlspecialchars($order['bulk_order_id']); ?></strong></td>
                                  <!-- Product Details -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <strong><?= htmlspecialchars($item['product_name']); ?></strong>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>
                                  <!-- Colors -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <?= htmlspecialchars($item['color']); ?>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>   
                                  <!-- Yards -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <?= htmlspecialchars($item['yards']); ?>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>   
                                  <!-- Rolls -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <?= htmlspecialchars($item['rolls']); ?>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>   
                                  <!-- Item Subtotal -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div style="color: #dcaa2e; font-weight: lighter;">
                                              <strong>₱<?= htmlspecialchars($item['item_subtotal']); ?></strong> 
                                          </div>
                                      <?php endforeach; ?>
                                  </td>
                                  <!-- Grand Total -->
                                  <td class="text-success"><strong>₱<?= htmlspecialchars($order['grand_total']); ?></strong></td>
                                  <!-- Status -->
                                  <td>
                                      <span class="badge 
                                          <?= $order['status'] === 'To Pay' ? 'badge-to-pay' : 
                                            ($order['status'] === 'To Ship' ? 'badge-to-ship' : 
                                            ($order['status'] === 'To Receive' ? 'badge-to-receive' : 
                                            ($order['status'] === 'Completed' ? 'badge-completed' : 
                                            ($order['status'] === 'Cancelled' ? 'badge-cancelled' : 'badge-other')))); ?>">
                                          <?= htmlspecialchars($order['status']); ?>
                                      </span>
                                        <?php if ($order['status'] === 'Completed'): ?>
                                            <?php foreach ($order['items'] as $item): ?>
                                                <button class="open-button" onclick="openForm(<?= htmlspecialchars($item['product_id']); ?>, '<?= htmlspecialchars($item['product_name']); ?>')">
                                                    Add Review
                                                </button>
                                            <?php endforeach; ?>

                                            <!-- The overlay -->
                                            <div id="overlay" class="overlay"></div>

                                            <!-- The popup form -->
                                            <div class="form-popup" id="myForm">
                                                <form action="" method="POST" class="form-container">
                                                    <h1>Add Review</h1>

                                                    <input type="hidden" name="product_id" id="product_id_input" value="" />

                                                    <label for="title" class="review-label">
                                                        Review Product:
                                                        <span id="product_name"></span>
                                                    </label>

                                                    <label for="title">Review Title *</label>
                                                    <input type="text" placeholder="Enter Title" name="title" required />

                                                    <label for="descript">Review Description</label>
                                                    <textarea name="description" class="rating-box" placeholder="Enter Review Description" maxlength="1000" cols="5" rows="3"></textarea>

                                                    <label class="descript">Review Rating <span>*</span></label>
                                                    <select name="rating" class="rating-box" required>
                                                        <option value="1">1</option>
                                                        <option value="2">2</option>
                                                        <option value="3">3</option>
                                                        <option value="4">4</option>
                                                        <option value="5">5</option>
                                                    </select>

                                                    <div class="buttons">
                                                        <button type="button" class="btnCancel" onclick="closeForm()">Cancel</button>
                                                        <button type="submit" name="btnreview" class="btnReview">Submit Review</button>
                                                    </div>
                                                </form>
                                            </div>
                                        <?php endif; ?>
                                  </td>
                              </tr>
                          <?php endforeach; ?>
                      </tbody>
                  </table>
              </div>
              </div>

              <!-- To Pay -->
              <div
                class="tab-pane fade"
                id="to-pay"
                role="tabpanel"
                aria-labelledby="to-pay-tab">
                <h3 class="mt-4">Order Status</h3>
                <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th></th>
                            <th>Order Number</th>
                            <th>Product</th>
                            <th>Color</th>
                            <th>Yards</th>
                            <th>Item Subtotal</th>
                            <th>Grand Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grouped_orders = [];

                        // Group orders by order number
                        foreach ($order_data as $details) {
                            $order_num = $details['order_num'];
                            if (!isset($grouped_orders[$order_num])) {
                                $grouped_orders[$order_num] = [
                                    'order_num' => $details['order_num'],
                                    'status' => $details['status'],
                                    'sub_total' => $details['sub_total'],
                                    'total_price' => $details['total_price'],
                                    'items' => []
                                ];
                            }
                            $grouped_orders[$order_num]['items'][] = $details;
                        }

                        // Display grouped orders
                        foreach ($grouped_orders as $order): ?>
                          <?php if ($order['status'] === 'To Pay'): ?>
                            <tr>
                                <!-- Product Image and Details -->
                                <td>
                                    <?php 
                                    $displayed_images = []; // Array to track displayed images
                                    foreach ($order['items'] as $item): 
                                        if (!in_array($item['product_image'], $displayed_images)): // Check if the image is not already displayed
                                            $displayed_images[] = $item['product_image']; // Add the image to the displayed list
                                    ?>
                                        <div>
                                          <img src="<?= htmlspecialchars($item['product_image']); ?>" alt="Product Image" class="rounded" style="width: 60px; height: 60px; object-fit: cover; margin-top: 5px; margin-bottom: 5px; margin-left: 5px;">
                                        </div>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </td>
                                <!-- Order Number -->
                                <td><strong><?= htmlspecialchars($order['order_num']); ?></strong></td>
                                <!-- Product Details -->
                                <td>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div>
                                            <strong><?= htmlspecialchars($item['product_name']); ?></strong>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <!-- Color -->
                                <td>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div>
                                            <?= htmlspecialchars($item['color']); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <!-- Yards -->
                                <td>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div>
                                            <?= htmlspecialchars($item['quantity']); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <!-- Subtotal -->
                                <td style="color: #dcaa2e;">₱<?= htmlspecialchars($order['sub_total']); ?></td>
                                <!-- Grand Total -->
                                <td class="text-success"><strong>₱<?= htmlspecialchars($order['total_price']); ?></strong></td>
                                <!-- Status -->
                                <td>
                                    <span class="badge <?= htmlspecialchars('badge-' . strtolower(str_replace(' ', '-', $order['status']))); ?>">
                                        <?= htmlspecialchars($order['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                </div>
                <h3 class="mt-4">Bulk Order Status</h3>
                <div class="table-responsive">
                  <table class="table table-striped table-hover align-middle">
                      <thead class="table-dark">
                          <tr>
                              <th></th>
                              <th>Order Number</th>
                              <th>Product Details</th>
                              <th>Color</th>
                              <th>Yards</th>
                              <th>Rolls</th>
                              <th>Item Subtotal</th>
                              <th>Grand Total</th>
                              <th>Status</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php 
                          $grouped_bulk_orders = [];

                          // Group bulk orders by bulk_order_id
                          foreach ($bulk_order_data as $details) {
                              $bulk_order_id = $details['bulk_order_id'];
                              if (!isset($grouped_bulk_orders[$bulk_order_id])) {
                                  $grouped_bulk_orders[$bulk_order_id] = [
                                      'bulk_order_id' => $details['bulk_order_id'],
                                      'status' => $details['status'],
                                      'grand_total' => $details['item_subtotal'],
                                      'grand_total' => $details['grand_total'],
                                      'items' => []
                                  ];
                              }
                              $grouped_bulk_orders[$bulk_order_id]['items'][] = $details;
                          }

                          // Display grouped BULK ORDERS
                          foreach ($grouped_bulk_orders as $order): ?>
                            <?php if ($order['status'] === 'To Pay'): ?>
                              <tr>
                                  <!-- Product Images -->
                                  <td>
                                      <?php 
                                      $displayed_images = []; // Array to track displayed images
                                      foreach ($order['items'] as $item): 
                                          if (!in_array($item['product_image'], $displayed_images)): // Check if the image is not already displayed
                                              $displayed_images[] = $item['product_image']; // Add the image to the displayed list
                                      ?>
                                          <div>
                                              <img src="<?= htmlspecialchars($item['product_image']); ?>" alt="Product Image" class="rounded" style="width: 60px; height: 60px; object-fit: cover; margin-top: 5px; margin-bottom: 5px; margin-left: 5px;">
                                          </div>
                                      <?php 
                                          endif;
                                      endforeach; 
                                      ?>
                                  </td>
                                  <!-- Order Number -->
                                  <td><strong><?= htmlspecialchars($order['bulk_order_id']); ?></strong></td>
                                  <!-- Product Details -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <strong><?= htmlspecialchars($item['product_name']); ?></strong>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>
                                  <!-- Colors -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <?= htmlspecialchars($item['color']); ?>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>   
                                  <!-- Yards -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <?= htmlspecialchars($item['yards']); ?>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>   
                                  <!-- Rolls -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <?= htmlspecialchars($item['rolls']); ?>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>   
                                  <!-- Item Subtotal -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div style="color: #dcaa2e; font-weight: lighter;">
                                              <strong>₱<?= htmlspecialchars($item['item_subtotal']); ?></strong> 
                                          </div>
                                      <?php endforeach; ?>
                                  </td>
                                  <!-- Grand Total -->
                                  <td class="text-success"><strong>₱<?= htmlspecialchars($order['grand_total']); ?></strong></td>
                                  <!-- Status -->
                                  <td>
                                      <span class="badge <?= htmlspecialchars('badge-' . strtolower(str_replace(' ', '-', $order['status']))); ?>">
                                          <?= htmlspecialchars($order['status']); ?>
                                      </span>
                                  </td>
                              </tr>
                              <?php endif; ?>
                          <?php endforeach; ?>
                      </tbody>
                  </table>
                </div>
              </div>
              
              <!-- To Ship -->
              <div
                class="tab-pane fade"
                id="to-ship"
                role="tabpanel"
                aria-labelledby="to-ship-tab"
              >
              <h3 class="mt-4">Order Status</h3>
                <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th></th>
                            <th>Order Number</th>
                            <th>Product</th>
                            <th>Color</th>
                            <th>Yards</th>
                            <th>Item Subtotal</th>
                            <th>Grand Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grouped_orders = [];

                        // Group orders by order number
                        foreach ($order_data as $details) {
                            $order_num = $details['order_num'];
                            if (!isset($grouped_orders[$order_num])) {
                                $grouped_orders[$order_num] = [
                                    'order_num' => $details['order_num'],
                                    'status' => $details['status'],
                                    'sub_total' => $details['sub_total'],
                                    'total_price' => $details['total_price'],
                                    'items' => []
                                ];
                            }
                            $grouped_orders[$order_num]['items'][] = $details;
                        }

                        // Display grouped orders
                        foreach ($grouped_orders as $order): ?>
                          <?php if ($order['status'] === 'To Ship'): ?>
                            <tr>
                                <!-- Product Image and Details -->
                                <td>
                                    <?php 
                                    $displayed_images = []; // Array to track displayed images
                                    foreach ($order['items'] as $item): 
                                        if (!in_array($item['product_image'], $displayed_images)): // Check if the image is not already displayed
                                            $displayed_images[] = $item['product_image']; // Add the image to the displayed list
                                    ?>
                                        <div>
                                          <img src="<?= htmlspecialchars($item['product_image']); ?>" alt="Product Image" class="rounded" style="width: 60px; height: 60px; object-fit: cover; margin-top: 5px; margin-bottom: 5px; margin-left: 5px;">
                                        </div>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </td>
                                <!-- Order Number -->
                                <td><strong><?= htmlspecialchars($order['order_num']); ?></strong></td>
                                <!-- Product Details -->
                                <td>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div>
                                            <strong><?= htmlspecialchars($item['product_name']); ?></strong>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <!-- Color -->
                                <td>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div>
                                            <?= htmlspecialchars($item['color']); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <!-- Yards -->
                                <td>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div>
                                            <?= htmlspecialchars($item['quantity']); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <!-- Subtotal -->
                                <td style="color: #dcaa2e;">₱<?= htmlspecialchars($order['sub_total']); ?></td>
                                <!-- Grand Total -->
                                <td class="text-success"><strong>₱<?= htmlspecialchars($order['total_price']); ?></strong></td>
                                <!-- Status -->
                                <td>
                                    <span class="badge <?= htmlspecialchars('badge-' . strtolower(str_replace(' ', '-', $order['status']))); ?>">
                                        <?= htmlspecialchars($order['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endif; ?>
                          <?php endforeach; ?>
                      </tbody>
                  </table>
                </div>
                <h3 class="mt-4">Bulk Order Status</h3>
                <div class="table-responsive">
                  <table class="table table-striped table-hover align-middle">
                      <thead class="table-dark">
                          <tr>
                              <th></th>
                              <th>Order Number</th>
                              <th>Product Details</th>
                              <th>Color</th>
                              <th>Yards</th>
                              <th>Rolls</th>
                              <th>Item Subtotal</th>
                              <th>Grand Total</th>
                              <th>Status</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php 
                          $grouped_bulk_orders = [];

                          // Group bulk orders by bulk_order_id
                          foreach ($bulk_order_data as $details) {
                              $bulk_order_id = $details['bulk_order_id'];
                              if (!isset($grouped_bulk_orders[$bulk_order_id])) {
                                  $grouped_bulk_orders[$bulk_order_id] = [
                                      'bulk_order_id' => $details['bulk_order_id'],
                                      'status' => $details['status'],
                                      'grand_total' => $details['item_subtotal'],
                                      'grand_total' => $details['grand_total'],
                                      'items' => []
                                  ];
                              }
                              $grouped_bulk_orders[$bulk_order_id]['items'][] = $details;
                          }

                          // Display grouped BULK ORDERS
                          foreach ($grouped_bulk_orders as $order): ?>
                            <?php if ($order['status'] === 'To Ship'): ?>
                              <tr>
                                  <!-- Product Images -->
                                  <td>
                                      <?php 
                                      $displayed_images = []; // Array to track displayed images
                                      foreach ($order['items'] as $item): 
                                          if (!in_array($item['product_image'], $displayed_images)): // Check if the image is not already displayed
                                              $displayed_images[] = $item['product_image']; // Add the image to the displayed list
                                      ?>
                                          <div>
                                              <img src="<?= htmlspecialchars($item['product_image']); ?>" alt="Product Image" class="rounded" style="width: 60px; height: 60px; object-fit: cover; margin-top: 5px; margin-bottom: 5px; margin-left: 5px;">
                                          </div>
                                      <?php 
                                          endif;
                                      endforeach; 
                                      ?>
                                  </td>
                                  <!-- Order Number -->
                                  <td><strong><?= htmlspecialchars($order['bulk_order_id']); ?></strong></td>
                                  <!-- Product Details -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <strong><?= htmlspecialchars($item['product_name']); ?></strong>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>
                                  <!-- Colors -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <?= htmlspecialchars($item['color']); ?>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>   
                                  <!-- Yards -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <?= htmlspecialchars($item['yards']); ?>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>   
                                  <!-- Rolls -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <?= htmlspecialchars($item['rolls']); ?>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>   
                                  <!-- Item Subtotal -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div style="color: #dcaa2e; font-weight: lighter;">
                                              <strong>₱<?= htmlspecialchars($item['item_subtotal']); ?></strong> 
                                          </div>
                                      <?php endforeach; ?>
                                  </td>
                                  <!-- Grand Total -->
                                  <td class="text-success"><strong>₱<?= htmlspecialchars($order['grand_total']); ?></strong></td>
                                  <!-- Status -->
                                  <td>
                                      <span class="badge <?= htmlspecialchars('badge-' . strtolower(str_replace(' ', '-', $order['status']))); ?>">
                                          <?= htmlspecialchars($order['status']); ?>
                                      </span>
                                  </td>
                              </tr>
                              <?php endif; ?>
                          <?php endforeach; ?>
                      </tbody>
                  </table>
                </div>
              </div>

              <!-- To Receive -->
              <div
                class="tab-pane fade"
                id="to-receive"
                role="tabpanel"
                aria-labelledby="to-receive-tab"
              >
              <h3 class="mt-4">Order Status</h3>
                <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th></th>
                            <th>Order Number</th>
                            <th>Product</th>
                            <th>Color</th>
                            <th>Yards</th>
                            <th>Item Subtotal</th>
                            <th>Grand Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grouped_orders = [];

                        // Group orders by order number
                        foreach ($order_data as $details) {
                            $order_num = $details['order_num'];
                            if (!isset($grouped_orders[$order_num])) {
                                $grouped_orders[$order_num] = [
                                    'order_num' => $details['order_num'],
                                    'status' => $details['status'],
                                    'sub_total' => $details['sub_total'],
                                    'total_price' => $details['total_price'],
                                    'items' => []
                                ];
                            }
                            $grouped_orders[$order_num]['items'][] = $details;
                        }

                        // Display grouped orders
                        foreach ($grouped_orders as $order): ?>
                          <?php if ($order['status'] === 'To Receive'): ?>
                            <tr>
                                <!-- Product Image and Details -->
                                <td>
                                    <?php 
                                    $displayed_images = []; // Array to track displayed images
                                    foreach ($order['items'] as $item): 
                                        if (!in_array($item['product_image'], $displayed_images)): // Check if the image is not already displayed
                                            $displayed_images[] = $item['product_image']; // Add the image to the displayed list
                                    ?>
                                        <div>
                                          <img src="<?= htmlspecialchars($item['product_image']); ?>" alt="Product Image" class="rounded" style="width: 60px; height: 60px; object-fit: cover; margin-top: 5px; margin-bottom: 5px; margin-left: 5px;">
                                        </div>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </td>
                                <!-- Order Number -->
                                <td><strong><?= htmlspecialchars($order['order_num']); ?></strong></td>
                                <!-- Product Details -->
                                <td>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div>
                                            <strong><?= htmlspecialchars($item['product_name']); ?></strong>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <!-- Color -->
                                <td>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div>
                                            <?= htmlspecialchars($item['color']); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <!-- Yards -->
                                <td>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div>
                                            <?= htmlspecialchars($item['quantity']); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <!-- Subtotal -->
                                <td style="color: #dcaa2e;">₱<?= htmlspecialchars($order['sub_total']); ?></td>
                                <!-- Grand Total -->
                                <td class="text-success"><strong>₱<?= htmlspecialchars($order['total_price']); ?></strong></td>
                                <!-- Status -->
                                <td>
                                    <span class="badge <?= htmlspecialchars('badge-' . strtolower(str_replace(' ', '-', $order['status']))); ?>">
                                        <?= htmlspecialchars($order['status']); ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endif; ?>
                          <?php endforeach; ?>
                      </tbody>
                  </table>
                </div>
                <h3 class="mt-4">Bulk Order Status</h3>
                <div class="table-responsive">
                  <table class="table table-striped table-hover align-middle">
                      <thead class="table-dark">
                          <tr>
                              <th></th>
                              <th>Order Number</th>
                              <th>Product Details</th>
                              <th>Color</th>
                              <th>Yards</th>
                              <th>Rolls</th>
                              <th>Item Subtotal</th>
                              <th>Grand Total</th>
                              <th>Status</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php 
                          $grouped_bulk_orders = [];

                          // Group bulk orders by bulk_order_id
                          foreach ($bulk_order_data as $details) {
                              $bulk_order_id = $details['bulk_order_id'];
                              if (!isset($grouped_bulk_orders[$bulk_order_id])) {
                                  $grouped_bulk_orders[$bulk_order_id] = [
                                      'bulk_order_id' => $details['bulk_order_id'],
                                      'status' => $details['status'],
                                      'grand_total' => $details['item_subtotal'],
                                      'grand_total' => $details['grand_total'],
                                      'items' => []
                                  ];
                              }
                              $grouped_bulk_orders[$bulk_order_id]['items'][] = $details;
                          }

                          // Display grouped BULK ORDERS
                          foreach ($grouped_bulk_orders as $order): ?>
                            <?php if ($order['status'] === 'To Receive'): ?>
                              <tr>
                                  <!-- Product Images -->
                                  <td>
                                      <?php 
                                      $displayed_images = []; // Array to track displayed images
                                      foreach ($order['items'] as $item): 
                                          if (!in_array($item['product_image'], $displayed_images)): // Check if the image is not already displayed
                                              $displayed_images[] = $item['product_image']; // Add the image to the displayed list
                                      ?>
                                          <div>
                                              <img src="<?= htmlspecialchars($item['product_image']); ?>" alt="Product Image" class="rounded" style="width: 60px; height: 60px; object-fit: cover; margin-top: 5px; margin-bottom: 5px; margin-left: 5px;">
                                          </div>
                                      <?php 
                                          endif;
                                      endforeach; 
                                      ?>
                                  </td>
                                  <!-- Order Number -->
                                  <td><strong><?= htmlspecialchars($order['bulk_order_id']); ?></strong></td>
                                  <!-- Product Details -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <strong><?= htmlspecialchars($item['product_name']); ?></strong>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>
                                  <!-- Colors -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <?= htmlspecialchars($item['color']); ?>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>   
                                  <!-- Yards -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <?= htmlspecialchars($item['yards']); ?>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>   
                                  <!-- Rolls -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <?= htmlspecialchars($item['rolls']); ?>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>   
                                  <!-- Item Subtotal -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div style="color: #dcaa2e; font-weight: lighter;">
                                              <strong>₱<?= htmlspecialchars($item['item_subtotal']); ?></strong> 
                                          </div>
                                      <?php endforeach; ?>
                                  </td>
                                  <!-- Grand Total -->
                                  <td class="text-success"><strong>₱<?= htmlspecialchars($order['grand_total']); ?></strong></td>
                                  <!-- Status -->
                                  <td>
                                      <span class="badge <?= htmlspecialchars('badge-' . strtolower(str_replace(' ', '-', $order['status']))); ?>">
                                          <?= htmlspecialchars($order['status']); ?>
                                      </span>
                                  </td>
                              </tr>
                              <?php endif; ?>
                          <?php endforeach; ?>
                      </tbody>
                  </table>
                </div>
              </div>

              <!-- Completed -->
              <div
                class="tab-pane fade"
                id="completed"
                role="tabpanel"
                aria-labelledby="completed-tab"
              >
              <h3 class="mt-4">Order Status</h3>
                <div class="table-responsive">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th></th>
                            <th>Order Number</th>
                            <th>Product</th>
                            <th>Color</th>
                            <th>Yards</th>
                            <th>Item Subtotal</th>
                            <th>Grand Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grouped_orders = [];

                        // Group orders by order number
                        foreach ($order_data as $details) {
                            $order_num = $details['order_num'];
                            if (!isset($grouped_orders[$order_num])) {
                                $grouped_orders[$order_num] = [
                                    'order_num' => $details['order_num'],
                                    'status' => $details['status'],
                                    'sub_total' => $details['sub_total'],
                                    'total_price' => $details['total_price'],
                                    'items' => []
                                ];
                            }
                            $grouped_orders[$order_num]['items'][] = $details;
                        }

                        // Display grouped orders
                        foreach ($grouped_orders as $order): ?>
                          <?php if ($order['status'] === 'Completed'): ?>
                            <tr>
                                <!-- Product Image and Details -->
                                <td>
                                    <?php 
                                    $displayed_images = []; // Array to track displayed images
                                    foreach ($order['items'] as $item): 
                                        if (!in_array($item['product_image'], $displayed_images)): // Check if the image is not already displayed
                                            $displayed_images[] = $item['product_image']; // Add the image to the displayed list
                                    ?>
                                        <div>
                                          <img src="<?= htmlspecialchars($item['product_image']); ?>" alt="Product Image" class="rounded" style="width: 60px; height: 60px; object-fit: cover; margin-top: 5px; margin-bottom: 5px; margin-left: 5px;">
                                        </div>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </td>
                                <!-- Order Number -->
                                <td><strong><?= htmlspecialchars($order['order_num']); ?></strong></td>
                                <!-- Product Details -->
                                <td>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div>
                                            <strong><?= htmlspecialchars($item['product_name']); ?></strong>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <!-- Color -->
                                <td>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div>
                                            <?= htmlspecialchars($item['color']); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <!-- Yards -->
                                <td>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div>
                                            <?= htmlspecialchars($item['quantity']); ?>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <!-- Subtotal -->
                                <td style="color: #dcaa2e;">₱<?= htmlspecialchars($order['sub_total']); ?></td>
                                <!-- Grand Total -->
                                <td class="text-success"><strong>₱<?= htmlspecialchars($order['total_price']); ?></strong></td>
                                <!-- Status -->
                                <td>
                                    <span class="badge <?= htmlspecialchars('badge-' . strtolower(str_replace(' ', '-', $order['status']))); ?>">
                                        <?= htmlspecialchars($order['status']); ?>
                                    </span>

                                    <?php foreach ($order['items'] as $item): ?>
                                            <button class="open-button" onclick="openForm(<?= htmlspecialchars($item['product_id']); ?>, '<?= htmlspecialchars($item['product_name']); ?>')">
                                                Add Review
                                            </button>
                                        <?php endforeach; ?>

                                        <!-- The overlay -->
                                        <div id="overlay" class="overlay"></div>

                                        <!-- The popup form -->
                                        <div class="form-popup" id="myForm">
                                            <form action="" method="POST" class="form-container">
                                                <h1>Add Review</h1>

                                                <input type="hidden" name="product_id" id="product_id_input" value="" />

                                                <label for="title" class="review-label">
                                                    Review Product:
                                                    <span id="product_name"></span>
                                                </label>

                                                <label for="title">Review Title *</label>
                                                <input type="text" placeholder="Enter Title" name="title" required />

                                                <label for="descript">Review Description</label>
                                                <textarea name="description" class="rating-box" placeholder="Enter Review Description" maxlength="1000" cols="5" rows="3"></textarea>

                                                <label class="descript">Review Rating <span>*</span></label>
                                                <select name="rating" class="rating-box" required>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select>

                                                <div class="buttons">
                                                    <button type="button" class="btnCancel" onclick="closeForm()">Cancel</button>
                                                    <button type="submit" name="btnreview" class="btnReview">Submit Review</button>
                                                </div>
                                            </form>
                                        </div>
                                </td>
                            </tr>
                            <?php endif; ?>
                          <?php endforeach; ?>
                      </tbody>
                  </table>
                </div>
                <h3 class="mt-4">Bulk Order Status</h3>
                <div class="table-responsive">
                  <table class="table table-striped table-hover align-middle">
                      <thead class="table-dark">
                          <tr>
                              <th></th>
                              <th>Order Number</th>
                              <th>Product Details</th>
                              <th>Color</th>
                              <th>Yards</th>
                              <th>Rolls</th>
                              <th>Item Subtotal</th>
                              <th>Grand Total</th>
                              <th>Status</th>
                          </tr>
                      </thead>
                      <tbody>
                          <?php 
                          $grouped_bulk_orders = [];

                          // Group bulk orders by bulk_order_id
                          foreach ($bulk_order_data as $details) {
                              $bulk_order_id = $details['bulk_order_id'];
                              if (!isset($grouped_bulk_orders[$bulk_order_id])) {
                                  $grouped_bulk_orders[$bulk_order_id] = [
                                      'bulk_order_id' => $details['bulk_order_id'],
                                      'status' => $details['status'],
                                      'grand_total' => $details['item_subtotal'],
                                      'grand_total' => $details['grand_total'],
                                      'items' => []
                                  ];
                              }
                              $grouped_bulk_orders[$bulk_order_id]['items'][] = $details;
                          }

                          // Display grouped BULK ORDERS
                          foreach ($grouped_bulk_orders as $order): ?>
                            <?php if ($order['status'] === 'Completed'): ?>
                              <tr>
                                  <!-- Product Images -->
                                  <td>
                                      <?php 
                                      $displayed_images = []; // Array to track displayed images
                                      foreach ($order['items'] as $item): 
                                          if (!in_array($item['product_image'], $displayed_images)): // Check if the image is not already displayed
                                              $displayed_images[] = $item['product_image']; // Add the image to the displayed list
                                      ?>
                                          <div>
                                              <img src="<?= htmlspecialchars($item['product_image']); ?>" alt="Product Image" class="rounded" style="width: 60px; height: 60px; object-fit: cover; margin-top: 5px; margin-bottom: 5px; margin-left: 5px;">
                                          </div>
                                      <?php 
                                          endif;
                                      endforeach; 
                                      ?>
                                  </td>
                                  <!-- Order Number -->
                                  <td><strong><?= htmlspecialchars($order['bulk_order_id']); ?></strong></td>
                                  <!-- Product Details -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <strong><?= htmlspecialchars($item['product_name']); ?></strong>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>
                                  <!-- Colors -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <?= htmlspecialchars($item['color']); ?>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>   
                                  <!-- Yards -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <?= htmlspecialchars($item['yards']); ?>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>   
                                  <!-- Rolls -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div>
                                              <?= htmlspecialchars($item['rolls']); ?>
                                          </div>
                                      <?php endforeach; ?>
                                  </td>   
                                  <!-- Item Subtotal -->
                                  <td>
                                      <?php foreach ($order['items'] as $item): ?>
                                          <div style="color: #dcaa2e; font-weight: lighter;">
                                              <strong>₱<?= htmlspecialchars($item['item_subtotal']); ?></strong> 
                                          </div>
                                      <?php endforeach; ?>
                                  </td>
                                  <!-- Grand Total -->
                                  <td class="text-success"><strong>₱<?= htmlspecialchars($order['grand_total']); ?></strong></td>
                                  <!-- Status -->
                                  <td>
                                      <span class="badge <?= htmlspecialchars('badge-' . strtolower(str_replace(' ', '-', $order['status']))); ?>">
                                          <?= htmlspecialchars($order['status']); ?>
                                      </span>

                                      <?php foreach ($order['items'] as $item): ?>
                                            <button class="open-button" onclick="openForm(<?= htmlspecialchars($item['product_id']); ?>, '<?= htmlspecialchars($item['product_name']); ?>')">
                                                Add Review
                                            </button>
                                        <?php endforeach; ?>

                                        <!-- The overlay -->
                                        <div id="overlay" class="overlay"></div>

                                        <!-- The popup form -->
                                        <div class="form-popup" id="myForm">
                                            <form action="" method="POST" class="form-container">
                                                <h1>Add Review</h1>

                                                <input type="hidden" name="product_id" id="product_id_input" value="" />

                                                <label for="title" class="review-label">
                                                    Review Product:
                                                    <span id="product_name"></span>
                                                </label>

                                                <label for="title">Review Title *</label>
                                                <input type="text" placeholder="Enter Title" name="title" required />

                                                <label for="descript">Review Description</label>
                                                <textarea name="description" class="rating-box" placeholder="Enter Review Description" maxlength="1000" cols="5" rows="3"></textarea>

                                                <label class="descript">Review Rating <span>*</span></label>
                                                <select name="rating" class="rating-box" required>
                                                    <option value="1">1</option>
                                                    <option value="2">2</option>
                                                    <option value="3">3</option>
                                                    <option value="4">4</option>
                                                    <option value="5">5</option>
                                                </select>

                                                <div class="buttons">
                                                    <button type="button" class="btnCancel" onclick="closeForm()">Cancel</button>
                                                    <button type="submit" name="btnreview" class="btnReview">Submit Review</button>
                                                </div>
                                            </form>
                                        </div>
                                  </td>
                              </tr>
                              <?php endif; ?>
                          <?php endforeach; ?>
                      </tbody>
                  </table>
                </div>
              </div>
              <!-- End of Displays -->
            </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <script>
$(document).ready(function () {
    // Handle notification item click
    $('#notification-dropdown').on('click', 'li', function () {
        var notifId = $(this).data('notif-id'); // Get notif_id from the clicked notification

        if (notifId) {
            // Make an AJAX request to mark the notification as read
            $.ajax({
                url: 'homepage.php', // PHP script to handle notification read
                method: 'GET',
                data: { notif_id: notifId },
                success: function (response) {
                    var result = JSON.parse(response);

                    if (result.status === 'success') {
                        // Remove the clicked notification
                        $('li[data-notif-id="' + notifId + '"]').remove();

                        // Update the unread count
                        var unreadCountElement = $('#unread-count');
                        var unreadCount = parseInt(unreadCountElement.text(), 10);

                        if (unreadCount > 1) {
                            unreadCountElement.text(unreadCount - 1);
                        } else {
                            unreadCountElement.fadeOut(); // Hide the badge when count reaches 0
                        }
                    } else {
                        console.error('Error: ' + result.message);
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', error);
                }
            });
        }
    });

    // Toggle notification dropdown visibility
    $('#notification-icon').on('click', function (e) {
        e.preventDefault();
        $('#notification-dropdown').toggle(); // Toggle dropdown visibility
    });

    // Close dropdown when clicking outside
    $(document).on('click', function (e) {
        if (!$('#notification-icon').is(e.target) && $('#notification-icon').has(e.target).length === 0 &&
            !$('#notification-dropdown').is(e.target) && $('#notification-dropdown').has(e.target).length === 0) {
            $('#notification-dropdown').hide();
        }
    });
});
</script>
<script>

function openForm(productId, productName) {
    // Set product ID and name in the form
    document.getElementById("product_id_input").value = productId;
    document.getElementById("product_name").textContent = productName;

    // Show the form with a transition effect
    const form = document.getElementById("myForm");
    const overlay = document.getElementById("overlay");
    form.style.display = "block"; // Make it visible immediately
    setTimeout(() => {
        form.classList.add("show");
    }, 10); // Allow the browser to register the change for transition
    document.getElementById("overlay").classList.add("show");
}

function closeForm() {
    const form = document.getElementById("myForm");
    const overlay = document.getElementById("overlay");
    form.classList.remove("show");
    overlay.classList.remove("show");
    setTimeout(() => {
        form.style.display = "none"; // Hide after transition ends
    }, 300); // Match the transition duration
    document.getElementById("overlay").classList.remove("show");
}




</script>
  </body>
</html>