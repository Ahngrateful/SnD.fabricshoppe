<?php
session_start();
// Database connection
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
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

// Function to send an email
function sendEmail($to, $subject, $body) {
    require 'vendor/autoload.php'; // Autoload for PHPMailer

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = "ssl";
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 465;
    $mail->Username = "sndshoppe11@gmail.com";
    $mail->Password = "nmbd uctm myxc pshv";
    $mail->From = "sndshoppe11@gmail.com";
    $mail->FromName = "S&D SHOPPE";
    $mail->AddAddress($to);
    $mail->Subject = $subject;
    $mail->isHTML(true);
    $mail->Body = $body;

    return $mail->send();
}

if (isset($_GET['order_num'])) {
    $order_num = intval($_GET['order_num']);

    // Fetch the order details
    $stmt = $conn->prepare("SELECT order_num, customer_id, status, track_num FROM order_details WHERE order_num = ?");
    $stmt->bind_param("i", $order_num);
    $stmt->execute();
    $result_set = $stmt->get_result();
    $result = $result_set->fetch_assoc();

    if (!$result) {
        echo "<script>alert('Order not found.');</script>";
        exit;
    }

    $customer_id = $result['customer_id'];
$track_num = $result['track_num'];
    // Fetch customer email
    $stmt_email = $conn->prepare("SELECT email FROM users_credentials WHERE id = ?");
    $stmt_email->bind_param("i", $customer_id);
    $stmt_email->execute();
    $customer = $stmt_email->get_result()->fetch_assoc();
    $customer_email = $customer['email'] ?? null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['toPay'])) {
            $status = 'To Pay';
            $message = "Your Order #$order_num is now 'To Pay'.";
        } elseif (isset($_POST['toShip'])) {
            $status = 'To Ship';
            $message = "Your Order #$order_num is now to ship! We're preparing your items for shipment.";
        } elseif (isset($_POST['ship_out'])) {
            $status = 'Ship out';
            $message = "Your Order #$order_num is now on its way! Thank you for your patience.";

            $email_subject = "Your Order #$order_num is on its way!";
            $email_body = "
<body style='font-family: Playfair Display, serif; background-color: #f1e8d9; margin: 0; padding: 20px; color: #1e1e1e; line-height: 1.6;'>
    <div style='max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);'>
        <h3 style='font-size: 24px; color: #1e1e1e;'>
        <b>Good news! Your order #$order_num is on its way.</b></h3>
        <p>Your tracking number is: <b style='color: #d9b65d;'>$track_num</b></p>
        <p>You can use this tracking number to follow your package’s progress on our delivery partners' websites</p>
        <div style='background: #f9f7f3; padding: 15px; border-radius: 8px; margin-top: 10px;'>
            <h4 style='color: #1e1e1e; font-size: 18px;'>Courier Tracking Links:</h4>
            <ul style='list-style: none; padding: 0; margin: 0;'>
                <li><a href=\"https://www.ninjavan.co/en-ph/\" style=\"color: #e044a5; text-decoration: none;\" target=\"_blank\">Ninja Van</a></li>
    <li><a href=\"http://www.lbcexpress.com/track/\" style=\"color: #e044a5; text-decoration: none;\" target=\"_blank\">LBC</a></li>
    <li><a href=\"https://www.jtexpress.ph/trajectoryQuery?flag=1\" style=\"color: #e044a5; text-decoration: none;\" target=\"_blank\">J&T Express</a></li>
                
            </ul>
        </div>
        <p>Simply enter your tracking number on the relevant courier’s website to get the latest updates on your delivery.</p>
        <p style='margin-top: 20px;'>Thank you for shopping with us!</p>
        <p style='font-size: 18px; font-weight: bold;'>S&D Fabrics Shoppe</p>
    </div>
</body>";

        } elseif (isset($_POST['complete'])) {
            $status = 'Completed';
            $message = "Your Order #$order_num is now completed! Thank you for buying our product.";

            // Email content for "Completed"
            $email_subject = "Your Order #$order_num is Complete!";
            $email_body = "<body style='font-family: Playfair Display, serif; background-color: #f1e8d9; margin: 0; padding: 20px; color: #1e1e1e; line-height: 1.6;'>
            <div style='max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);'>
                <h3 style='font-size: 24px; color: #1e1e1e;'>
                <b>Hi valued customer, </b></h3>
                <p>We are excited to let you know that your order #$order_num has been completed and delivered!</p>
                <p>Thank you for choosing S&D Fabrics Shoppe. We hope you enjoy your purchase! If you have any questions or need further assistance, please don't hesitate to contact us.</p>
                <p style='margin-top: 20px;'>Best regards,</p>
                <p style='font-size: 18px; font-weight: bold;'>S&D Fabrics Shoppe</p>
            </div>
        </body>";
        }

        // Update status and notify the user
        $stmt_status = $conn->prepare("UPDATE order_details SET status = ? WHERE order_num = ?");
        $stmt_status->bind_param("si", $status, $order_num);

        if ($stmt_status->execute()) {
            // Insert notification
            $stmt_notification = $conn->prepare("INSERT INTO notifications (id, message) VALUES (?, ?)");
            $stmt_notification->bind_param("is", $customer_id, $message);
            $stmt_notification->execute(); //hindi nag eexecute

            // Send email if required
            if (isset($email_subject, $email_body) && $customer_email) {
                if (sendEmail($customer_email, $email_subject, $email_body)) {
                    echo "<script>alert('Status updated, notification sent, and email delivered!');</script>";
                } else {
                    echo "<script>alert('Status updated, notification sent, but email delivery failed.');</script>";
                }
            } else {
                echo "<script>alert('Order status updated and user notified.');</script>";
            }
        } else {
            echo "<script>alert('Failed to update order status.');</script>";
        }
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
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
    align-items: center;
    justify-content: center;
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
    background-color: #faf4e1;
    border: 1px solid #d1b88a;
    border-radius: 5px;
    font-weight: bold;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
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

  button {
    background-color: #d9b65d;
    color: #ffffff;
    font-size: 1rem;
    font-weight: bold;
    padding: 12px 20px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    width: 48%;
    margin-top: 10px;
}

button:hover {
    background-color: #c49b50;
}

button:active {
    background-color: #b08847;
}
 
    </style>
  </head>
  <body class="vh-100">

    <div class="container-fluid"  >
      <div class="row vh-100">
        <!-- Main Content Area -->
        <div class="col-10 p-4 d-flex flex-column align-items-center justify-content-center">
          <h1>Shipping Status</h1>
            
          <?php if ($result_set && $result_set->num_rows > 0): ?>
        <div class="row g-3">
            <div class="text-center mb-4">
                <h2>Current Status:
                    <?php 
                    if (isset($result['status'])) {
                        echo htmlspecialchars($result['status']); 
                    }
                    ?>
            </h2>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="card p-3" style="max-height: 300px; width: 800px; overflow: auto; margin-left: 50px;">
                <h4 style="color: #dcaa2e;">Update the shipping status</h4>
                <form method="POST">
                <div class="d-flex flex-wrap justify-content-between mt-3">
                <button type="submit" name="toPay" value="To Pay">To Pay</button>
                <button type="submit" name="toShip" value="To Ship">To Ship</button>
                <button type="submit" name="ship_out" value="Ship out">Ship Out</button>
                <button type="submit" name="complete" value="Completed">Completed</button>
            </div>
        </form>
            </div>
        </div>
    </div>
<?php else: ?>
    <p>No results found.</p>
<?php endif; ?>
<button id="back" class="btn" onclick="window.location.href='orders.php';" style="width=50px;">
            Back
        </button>
    <script> 
   
 </script>

  </body>
</html>