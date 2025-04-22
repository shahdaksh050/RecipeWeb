<?php
session_start();
require_once 'config.php';


if (!isset($_GET['title'])) {
    die("Recipe title not specified.");
}

$title = $_GET['title'];

// Connect to database
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message and check if recipe is favourited
$message = '';
$user_id = $_SESSION['id'] ?? null;
$recipe_id = null;
$is_favourited = false;

if ($user_id) {
    $stmt = $conn->prepare("SELECT id FROM recipes WHERE title = ?");
    $stmt->bind_param("s", $title);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $recipe_id = $row['id'];

        // Check if already favourited
        $fav_check = $conn->prepare("SELECT 1 FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $fav_check->bind_param("ii", $user_id, $recipe_id);
        $fav_check->execute();
        $fav_result = $fav_check->get_result();
        $is_favourited = $fav_result->num_rows > 0;
        $fav_check->close();
    }
    $stmt->close();
}

// Handle add to favourites POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_favourite'])) {
    if (!$user_id) {
        $message = "You must be logged in to add favourites.";
    } elseif ($recipe_id) {
        $insert_stmt = $conn->prepare("INSERT IGNORE INTO favorites (user_id, recipe_id) VALUES (?, ?)");
        $insert_stmt->bind_param("ii", $user_id, $recipe_id);
        if ($insert_stmt->execute()) {
            if ($insert_stmt->affected_rows > 0) {
                $message = "Recipe added to your favourites.";
                $is_favourited = true;
            } else {
                $message = "Recipe is already in your favourites.";
            }
        } else {
            $message = "Error adding to favourites: " . $conn->error;
        }
        $insert_stmt->close();
    }
}

// Handle remove from favourites POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_favourite'])) {
    if (!$user_id) {
        $message = "You must be logged in to remove favourites.";
    } elseif ($recipe_id) {
        $delete_stmt = $conn->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $delete_stmt->bind_param("ii", $user_id, $recipe_id);
        if ($delete_stmt->execute()) {
            if ($delete_stmt->affected_rows > 0) {
                $message = "Recipe removed from your favourites.";
                $is_favourited = false;
            } else {
                $message = "Recipe was not in your favourites.";
            }
        } else {
            $message = "Error removing from favourites: " . $conn->error;
        }
        $delete_stmt->close();
    }
}

/* Removed duplicate recipe fetch since we already fetched id above */
if (!$recipe_id) {
    die("Recipe not found.");
}

$recipe = [
    'id' => $recipe_id,
    'title' => $title,
    'prep_time' => '',
    'image' => '',
    'description' => ''
];

// Fetch other recipe details
$stmt = $conn->prepare("SELECT prep_time, image, description FROM recipes WHERE id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Recipe not found.");
}

$row = $result->fetch_assoc();
$recipe['prep_time'] = $row['prep_time'];
$recipe['image'] = $row['image'];
$recipe['description'] = $row['description'];

$stmt->close();
$conn->close();

// Parse description into ingredients and instructions
$description = $recipe['description'];
$ingredients = [];
$instructions = [];

$parts = preg_split('/Ingredients:\s*|\n\nInstructions:\s*/', $description, -1, PREG_SPLIT_NO_EMPTY);

if (count($parts) == 2) {
    $ingredients = explode("\n", trim($parts[0]));
    $instructions = explode("\n", trim($parts[1]));
} else {
    // fallback: treat whole description as instructions
    $instructions = explode("\n", trim($description));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?php echo htmlspecialchars($recipe['title']); ?> - FoodieHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0e1b30 0%, #1a2a4c 100%);
            color: #f0f0f0;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background-color: #0e1126;
        }
        .navbar .nav-link,
        .navbar .navbar-brand {
            color: white !important;
            background-color: #0e1126 !important;
        }
        .navbar .nav-item {
            margin-left: 15px;
        }
        .btn-purple {
            background-color: #0e1126;
            color: white;
        }
        .navbar .nav-link:hover,
        .navbar .navbar-brand:hover {
            color: white !important;
            background-color: #0e1126 !important;
        }
        .navbar .profile-icon {
            display: none;
        }
        .navbar-toggler-icon {
            background-color: #0e1126 !important;
            border-radius: 0.25rem !important;
        }
        .container {
            flex: 1 0 auto;
            padding: 40px 20px;
            max-width: 900px;
            margin: 0 auto;
        }
        .recipe-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .recipe-title {
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 0 8px rgba(92,141,246,0.7);
        }
        .prep-time {
            font-size: 1.2rem;
            color: #a0a8c0;
            margin-bottom: 20px;
        }
        .recipe-img {
            max-width: 100%;
            max-height: 400px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(92,141,246,0.5);
            margin-bottom: 30px;
            object-fit: cover;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            margin-top: 50px;
            margin-bottom: 20px;
            border-bottom: 3px solid #5c8df6;
            padding-bottom: 8px;
            text-align: center;
            text-shadow: 0 0 6px rgba(92,141,246,0.6);
        }
        ul, ol {
            max-width: 700px;
            margin: 0 auto 30px auto;
            padding-left: 25px;
            font-size: 1.1rem;
            line-height: 1.6;
            background: rgba(20, 40, 80, 0.6);
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            padding: 20px 30px;
            color: #e0e6ff;
        }
        ul li, ol li {
            margin-bottom: 10px;
        }
        .message {
            text-align: center;
            color: #ffcc00;
            font-weight: 700;
            font-size: 1.1rem;
            margin-top: 20px;
            text-shadow: 0 0 5px #ffcc00;
        }
        .fav-btn {
            display: inline-block;
            background-color: #5c8df6;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 4px 12px rgba(92,141,246,0.6);
            margin: 20px auto;
            display: block;
            width: 220px;
            text-align: center;
        }
        .fav-btn:hover {
            background-color: #3a5bcc;
            box-shadow: 0 6px 18px rgba(58,91,204,0.8);
        }
        .fav-btn.remove {
            background-color: #d9534f;
            box-shadow: 0 4px 12px rgba(217,83,79,0.6);
        }
        .fav-btn.remove:hover {
            background-color: #b03531;
            box-shadow: 0 6px 18px rgba(176,53,49,0.8);
        }
        footer {
            flex-shrink: 0;
            text-align: center;
            padding: 15px 10px;
            background-color: #142850;
            color: #a0a8c0;
            font-size: 0.9rem;
            box-shadow: 0 -2px 8px rgba(0,0,0,0.5);
            margin-top: 40px;
        }
    </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg">
      <a class="navbar-brand" href="homepage.php">FoodieHub</a>
      <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon rounded"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
          <a class="nav-link navbar-brand" href="homepage.php" style="padding-left: 0;">Homepage</a>
      </div>
  </nav>
    <div class="container">
        <div class="recipe-header">
            <h1 class="recipe-title"><?php echo htmlspecialchars($recipe['title']); ?></h1>
            <p class="prep-time">Prep time: <?php echo htmlspecialchars($recipe['prep_time']); ?> mins</p>
            <?php if ($recipe['image']): ?>
                <img src="<?php echo htmlspecialchars($recipe['image']); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>" class="recipe-img" />
            <?php endif; ?>
        </div>

        <h2 class="section-title">Ingredients</h2>
        <ul>
            <?php foreach ($ingredients as $ingredient): ?>
                <li><?php echo htmlspecialchars($ingredient); ?></li>
            <?php endforeach; ?>
        </ul>

        <h2 class="section-title">Instructions</h2>
        <ol>
            <?php foreach ($instructions as $instruction): ?>
                <li><?php echo htmlspecialchars($instruction); ?></li>
            <?php endforeach; ?>
        </ol>
        <?php if ($message): ?>
            <p class="message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <div style="text-align:center;">
            <?php if (isset($_SESSION['id'])): ?>
                <form method="post" action="">
                    <?php if ($is_favourited): ?>
                        <button type="submit" name="remove_favourite" class="fav-btn remove">
                            Remove from Favourites
                        </button>
                    <?php else: ?>
                        <button type="submit" name="add_favourite" class="fav-btn">
                            Add to Favourites
                        </button>
                    <?php endif; ?>
                </form>
            <?php else: ?>
                <p><a href="login.php" style="color:#5c8df6; font-weight: 600;">Log in</a> to add this recipe to your favourites.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
