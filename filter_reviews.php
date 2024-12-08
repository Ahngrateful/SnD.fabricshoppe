<?php
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

$product_id = $_GET['product_id'];
$ratingFilter = $_GET['rating'] ?? 0;

// Fetch reviews based on filter
if ($ratingFilter > 0) {
    $stmt = $pdo->prepare("SELECT * FROM product_ratings WHERE product_id = ? AND rating = ?");
    $stmt->execute([$product_id, $ratingFilter]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM product_ratings WHERE product_id = ?");
    $stmt->execute([$product_id]);
}

$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter Reviews</title>
    <style>
        .back-button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 15px;
        }

        .back-button:hover {
            background-color: #0056b3;
        }

        .review-con {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .rating-stars i {
            color: #FFD700; /* Gold color for stars */
        }
    </style>
</head>
<body>

    <?php if (empty($reviews)): ?>
        <p>No reviews found for this filter.</p>
    <?php else: ?>
        <?php foreach ($reviews as $review): ?>
            <div class="review-con">
                <div class="review">
                    <h3><?= htmlspecialchars($review['user_firstname']) ?> 
                        <span><?= htmlspecialchars($review['user_lastname']) ?></span> | 
                        <span class="date"><?= htmlspecialchars($review['time']) ?></span>
                    </h3>
                    <div class="rating-stars">
                        <?php
                        $rating = (int)$review['rating'];
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
                        }
                        ?>
                    </div>
                    <h4><?= htmlspecialchars($review['title']) ?></h4>
                    <p><?= htmlspecialchars($review['description']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>


</body>
</html>
