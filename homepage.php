<?php
session_start();


$servername = "localhost";
$dbname = "db_sdshoppe";
$username = "root";  
$password = "";  

try {
    // Initialize PDO connection
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

 //Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: haveacc.php"); // Redirect to login if not logged in
    exit;
}

// Access user information from the session
$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];

//auto complete searc
if (isset($_GET['query'])) {

    $search_term = "%" . $_GET['query'] . "%";
    $stmt = $pdo->prepare("
        SELECT product_name, category 
        FROM products 
        WHERE product_name LIKE :search_term OR category LIKE :search_term 
        LIMIT 10
    ");
    $stmt->execute([':search_term' => $search_term]);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results);
    exit;
}

// Get the 4 most recently added products
$stmtCategory = $pdo->prepare('SELECT product_id, product_image, product_name, category FROM products GROUP BY category');
$stmtCategory->execute();
$category = $stmtCategory->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare('SELECT product_id, product_image, product_name, category FROM products');
$stmt->execute();
$product = $stmt->fetchAll(PDO::FETCH_ASSOC);


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

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="icon" href="Assets/sndlo.ico">
    <link rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <title>S&D Fabrics</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Kumbh+Sans:wght@100..900&family=Playfair+Display+SC:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap');

body {
    background-color: #f1e8d9;
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

.navbar {
    position: fixed; 
    top: 0; 
    left: 0; 
    right: 0; 
    z-index: 1000; 
    background-color: #f1e8d9; 
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15); 
    overflow: visible; /* Ensures dropdowns can appear outside the navbar */
}

.nav-link-black {
    color: #1e1e1e !important;
}

.nav-link-black:hover {
    color: #e044a5;
}


/* Hamburger icon color */
.navbar-toggler-icon {
    background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(30, 30, 30, 1)' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
}

.navbar-collapse {
    display: flex;
    justify-content: center; /* Center aligns the entire navbar content */
}

.search-bar {
    max-width: 500px; 
    width: 100%; 
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

/* Card background for the category section */
.category-card {
    max-height: 80vh; 
    background-color: #b6b3ae; 
    border-radius: 15px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15); 
    margin: 10px 0;
    padding: 20px;
    text-decoration: none;
}

/* Styling for the Category heading */
h2 {
    font-family: "Playfair Display SC", serif;
    font-weight: 600;
    font-size: 37px;
    margin: 50px 0;
    text-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    padding-top: 10px;
    padding-bottom: -5px;
}

/* Category section styling */
.category-card {
    max-height: 80vh; 
    overflow-y: auto; 
    background: radial-gradient(circle, rgba(255,241,202,1) 0%, rgba(209,194,157,1) 100%);
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    margin: 20px 0;
}

/* Row to display categories in a single line */
.row {
    display: flex;
    justify-content: space-around; 
    flex-wrap: wrap; 
    gap: 15px; 
    padding: 0;
    margin: 0; 
}

/* Individual category styling */
.category {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    flex: 1 1 120px; /* Allow the items to take up space evenly */
    max-width: 120px;
    transition: transform 400ms;
}

.category:hover {
    transform: scale(1.2);
}

.category img {
    width: 150px; 
    height: 150px; 
    border-radius: 50%; 
    border: 4px solid #d9b65d; 
    object-fit: cover;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.category p {
    margin-top: 5px; 
    font-weight: bold;
    font-size: 16px; 
    font-family: "Playfair Display SC", serif;
    color: #1e1e1e;
}

.category a{
    color: #d9b65d;
    text-decoration: none;
}

.category a:hover {
    color: #d9b65d;
    text-decoration: underline #d9b65d;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .category {
        flex: 1 1 80px; 
        max-width: 80px; 
    }

    .category img {
        width: 80px;
        height: 80px; 
    }

    .category p {
        font-size: 14px; 
    }
}

/* Filter button styling */
.filter-buttons {
    display: flex;
    flex-wrap: wrap; 
    gap: 10px; 
    margin-bottom: 20px;
    justify-content: center; 
}

.filter-buttons button {
    padding: 8px 12px; 
    color: #c7a754;
    background-color: transparent;
    cursor: pointer;
    border-radius: 10px; 
    font-weight: bold;
    transition: background-color 0.3s;
    height: auto;
    min-width: 10px; 
    width: auto; 
    flex: 0 0 auto;
    border: none;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    border: 1px solid #d9b65d;
}

/* Hover effect */
.filter-buttons button:hover {
    color: #f1e8d9;
    background-color: #d9b65d; 
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .filter-buttons {
        flex-direction: column; 
        gap: 8px;
        align-items: center; 
    }

    .filter-buttons button {
        width: 150px; 
        padding: 10px; 
        font-size: 14px; 
    }
}

@media (max-width: 480px) {
    .filter-buttons button {
        width: 100%; 
        max-width: 200px; 
        padding: 12px 20px; 
        font-size: 18px; 
    }
}

/* Fabric items grid */
.fabric-items {
    display: flex;
    flex-wrap: wrap; 
    justify-content: center; 
    padding: 20px;
    max-width: 1000px; 
    margin: 0 auto; 
    row-gap: 50px;
    column-gap: 50px;
}

.fabric-card {
    width: 200px; 
    height: auto; 
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    transition: transform 400ms;
    border: 1px solid #d9b65d;
}

.fabric-card:hover {
    transform: scale(1.2);
}

.fabric-content {
    display: flex;
    flex-direction: column; 
    align-items: center; 
}

.fabric-card img {
    width: 100%; 
    height: auto; 
    object-fit: cover; 
    border: 1px solid #d9b65d;
}

.fabric-card p {
    margin-top: 5px;
    font-weight: bold;
    font-size: 14px;
    text-align: center; /* Center text below image */
}

.fabric-card a {
    color: #1e1e1e;
    text-decoration: none;
}

.fabric-card a:hover {
    color: #c7a754;
    text-decoration: underline;
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
.input-group {
    position: relative; /* Ensure positioning context for child elements */
}

.result-box {
    position: absolute;
    top: 67px; /* Position it below the input */
    left: 0;
    width: 463px; /* Match the width of the search bar */
    z-index: 1000;
    margin-left: 522px;
}

.results-list {
    max-height: 200px; /* Prevent it from becoming too tall */
    overflow-y: auto; /* Enable scrolling if results exceed max height */
    background-color: white;
    border: 1px solid #ccc;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    list-style: none;
    margin: 0;
    padding: 0;
}

.results-list li {
    padding: 10px;
    cursor: pointer;
}

.results-list li:hover {
    background-color: #f0f0f0;
}
        </style>
</head>
<body class="vh-100">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #f1e8d9; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <a class="navbar-brand fs-4" href="sndLandingpage.php">
                <img src="Assets/sndlogo.png" width="70px" alt="Logo"/> 
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo01" aria-controls="navbarTogglerDemo01" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
<!--search bar-->
            <div class="collapse navbar-collapse justify-content-center" id="navbarTogglerDemo01">
                <form class="search-bar" name="search" role="search" method="POST" action="search_landing.php">
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="bi bi-search search-icon"></i>
                        </span>
                        <input class="form-control search-input" type="search" name="search_term" placeholder="Search..." aria-label="Search" aria-describedby="basic-addon1">
    
                        <button type="submit" name="search" style="display:none;"></button>
                    </div>
                    <div class="result-box">
                            <li class="results-list"></li>
                        </div>
                </form>
            </div>
            

                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link nav-link-black active" aria-current="page" href="cart.php">
                            <img src="Assets/svg(icons)/shopping_cart.svg" alt="cart">
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


                    <!-- New Account Dropdown Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link nav-link-black dropdown-toggle" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="Assets/svg(icons)/account_circle.svg" alt="account">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="accountDropdown">
                            <li>
                                <a class="dropdown-item" href="mypurchase.php">My Account</a>
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

    <!-- Category Card -->
    <div class="category-card p-4">
    <div class="row justify-content-center" style="margin-top: 75px;">
        <h2 class="text-center mb-4">Category</h2>
        <?php 
        if (!empty($category)) {
            $count = 0;
            foreach ($category as $item):    
                if ($count >= 6) break;
                
                $categoryName = htmlspecialchars($item['category']);
                $productImage = htmlspecialchars($item['product_image']);
        ?>
            <div class="category col-4 col-md-2 text-center">
                <a href="search_landing.php?category=<?= urlencode($categoryName) ?>">
                    <img src="<?= $productImage ?>" alt="Fabric Image" class="rounded-circle">
                    <p><?= htmlspecialchars($item['category']) ?></p>
                </a>
            </div>
        <?php 
                $count++;
            endforeach; 
        } else {
            echo "<p>No categories found.</p>";
        }
        ?>
    </div>
</div>
<div class="filter-buttons">
    <a href="filters.php?filter=allitems"><button>All</button></a>
    <a href="filters.php?filter=newest"><button>Newest</button></a>
    <a href="filters.php?filter=popular"><button>Popular</button></a>
</div>

    <!-- Fabric Items -->
    <div class="fabric-items">
        <?php foreach ($product as $item): ?>
            <div class="fabric-card">
                <div class="fabric-content">
                    <a href="product.php?product_id=<?= htmlspecialchars($item['product_id']) ?>">
                        <img src="<?= htmlspecialchars($item['product_image']) ?>" alt="Fabric Image" style="width: 200px; height: 200px; object-fit: cover; border-radius: 10px;">
                        <p><?= htmlspecialchars($item['product_name']) ?></p>
                    </a>
                    
                </div>
            </div>
        <?php endforeach; ?>
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

// Auto-complete search
document.addEventListener("DOMContentLoaded", () => {
    const searchInput = document.querySelector('.search-input');
    const resultsList = document.querySelector('.results-list');

    searchInput.addEventListener('input', function () {
        const query = this.value.trim();

        if (query.length > 1) {
            fetch(`?query=${encodeURIComponent(query)}`) // Fixed missing parenthesis
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    resultsList.innerHTML = ''; // Clear previous results
                    if (data.length > 0) {
                        data.forEach(item => {
                            const li = document.createElement('li');
                            li.textContent = `${item.product_name} - ${item.category}`;
                            li.addEventListener('click', () => {
                                searchInput.value = item.product_name; // Fill the input with the selected suggestion
                                //lagyan pa clickable?
                                resultsList.innerHTML = ''; // Clear results
                            });
                            resultsList.appendChild(li);
                        });
                    } else {
                        resultsList.innerHTML = '<li>No results found</li>';
                    }
                })
                .catch(err => console.error('Error fetching suggestions:', err));
        } else {
            resultsList.innerHTML = ''; // Clear results if query is too short
        }
    });

    // Close results list when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !resultsList.contains(e.target)) {
            resultsList.innerHTML = ''; // Clear results
        }
    });
});
</script>
</body>
</html>