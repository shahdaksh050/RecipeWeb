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
            font-family: Arial, sans-serif;
            background-color: #0e1b30;
            color: white;
            margin: 20px;
        }
        .navbar .nav-link,
        .navbar .navbar-brand,
        .navbar .profile-icon {
            color: white !important;
        }
        .recipe-header {
            text-align: center;
        }
        .recipe-img {
            max-width: 400px;
            border-radius: 15px;
            margin: 20px auto;
            display: block;
        }
        h2 {
            border-bottom: 2px solid #5c8df6;
            padding-bottom: 5px;
            margin-top: 40px;
        }
        ul, ol {
            max-width: 600px;
            margin: 0 auto 20px auto;
            padding-left: 20px;
        }
        .section-title {
      font-size: 1.8rem;
      font-weight: 600;
      margin-top: 50px;
      margin-bottom: 20px;
    }
    .rating-badge {
      background: #21244c;
      padding: 5px 10px;
      border-radius: 12px;
      font-size: 0.8rem;
    }
    .favorite-icon {
      position: absolute;
      top: 15px;
      right: 15px;
      color: white;
      font-size: 1.2rem;
      cursor: pointer;
      transition: color 0.3s ease;
    }
    .favorite-icon.liked {
      color: #ff4d6d;
    }
    .navbar .profile-icon {
      font-size: 1.5rem;
      color: white;
    }
    .navbar-nav .nav-link {
      margin-left: 10px;
    }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="homepage.php">FoodieHub</a>
      <button class="navbar-toggler text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon bg-light rounded"></span>
      </button>
      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <ul class="navbar-nav">
          <li class="nav-item">
            <a class="nav-link" href="homepage.php">Homepage</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#cuisine-section">Cuisines</a>
          </li>
          <li class="nav-item">
            <!-- Conditionally display profile or login based on session -->
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']): ?>
              <a class="nav-link profile-icon" href="profile.php" title="Profile">
                <i class="fas fa-user-circle"></i>
              </a>
            <?php else: ?>
              <a class="nav-link profile-icon" href="auth.php?action=login" title="Login">
                <i class="fas fa-user-circle"></i>
              </a>
            <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>
    <div class="recipe-header">
        <h1><?php echo htmlspecialchars($recipe['title']); ?></h1>
        <p>Prep time: <?php echo htmlspecialchars($recipe['prep_time']); ?> mins</p>
        <?php if ($recipe['image']): ?>
            <img src="<?php echo htmlspecialchars($recipe['image']); ?>" alt="<?php echo htmlspecialchars($recipe['title']); ?>" class="recipe-img" />
        <?php endif; ?>
    </div>

    <h2>Ingredients</h2>
    <ul>
        <?php foreach ($ingredients as $ingredient): ?>
            <li><?php echo htmlspecialchars($ingredient); ?></li>
        <?php endforeach; ?>
    </ul>

    <h2>Instructions</h2>
    <ol>
        <?php foreach ($instructions as $instruction): ?>
            <li><?php echo htmlspecialchars($instruction); ?></li>
        <?php endforeach; ?>
    </ol>
    <?php if ($message): ?>
        <p style="text-align:center; color: yellow; font-weight: bold;"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <div style="text-align:center; margin: 20px;">
        <?php if (isset($_SESSION['id'])): ?>
            <form method="post" action="">
                <?php if ($is_favourited): ?>
                    <button type="submit" name="remove_favourite" style="background-color:#d9534f; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer;">
                        Remove from Favourites
                    </button>
                <?php else: ?>
                    <button type="submit" name="add_favourite" style="background-color:#5c8df6; color:white; border:none; padding:10px 20px; border-radius:5px; cursor:pointer;">
                        Add to Favourites
                    </button>
                <?php endif; ?>
            </form>
        <?php else: ?>
            <p><a href="login.php" style="color:#5c8df6;">Log in</a> to add this recipe to your favourites.</p>
        <?php endif; ?>
    </div>
</body>
</html>
