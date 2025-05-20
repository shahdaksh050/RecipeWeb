![image](https://github.com/user-attachments/assets/6eaf4534-f377-4241-bed7-d5744145adcb)# RecipeWeb

RecipeWeb is a web application that makes it easy to browse, search, and manage recipes from a variety of world cuisines. Built using PHP, HTML, CSS, MySQL, and Bootstrap, it provides a clean and user-friendly interface — including Google login — for exploring new dishes and managing your own recipe preferences.

---

## Features

- **Browse by Cuisine:** Quickly find recipes grouped under Indian, Chinese, Italian, Mexican, American, and French cuisines.
- **User Authentication:** Secure signup and login system, with the added convenience of Google OAuth.
- **Recipe Pages:** Each recipe shows ingredients, preparation steps, and an image.
- **User Profile & Preferences:** Edit your profile and set your favorite cuisines.
- **Responsive Design:** Looks great on any device, thanks to Bootstrap.

---

## Technologies

- **Frontend:** HTML, CSS, Bootstrap
- **Backend:** PHP
- **Database:** MySQL
- **Authentication:** PHP sessions & Google OAuth

---

## Project Structure & File Overview

### Folders

- **auth/**  
  Contains authentication scripts:
  - `login.php`: Processes user login, checks credentials, starts sessions.
  - `signup.php`: Handles new account creation, input validation, password hashing.

- **photos/**  
  All images for recipes and cuisines.

### Root-Level Files

| File                   | Description                                                                                                    |
|------------------------|----------------------------------------------------------------------------------------------------------------|
| `index.php`            | Redirects to `homepage.php`, main entry point.                                                                 |
| `homepage.php`         | Main landing page, lists cuisines.                                                                             |
| `config.php`           | Sets up MySQL database connection, included in most PHP scripts.                                               |
| `logout.php`           | Logs the user out and redirects to login.                                                                      |
| `edit-profile.php`     | Lets users update profile info like name and email.                                                            |
| `edit-preferences.php` | Lets users set or change preferred cuisines and food types.                                                    |
| `profile.php`          | Displays user info and preferences.                                                                            |
| `recipe.php`           | Shows full recipe details (name, ingredients, image, instructions), loaded dynamically based on selected dish.  |
| `foodiehub.sql`        | SQL dump to set up the project database and populate with starter data.                                        |
### Cuisine Pages

- `indian_cuisine.html`
- `chineese_cuisine.html`
- `italian_cuisine.html`
- `mexican_cuisine.html`
- `american_cuisine.html`
- `french_cuisine.html`

Each cuisine page displays relevant recipes and uses PHP to fetch and render content.

---

## 🧩 Data Flow Diagram (DFD) – Level 1

Below is a simplified Level 1 Data Flow Diagram (DFD) that illustrates the main components and data flows in RecipeWeb:

```
+-----------------+                               
|    Browser      |  <--- (HTML, CSS, JS) --->   [User Interface]
+-----------------+                               
         |                                      
         v                                      
+---------------------+                         
|     index.php       |  <--- (Redirects) --->  +------------------+
+---------------------+                         |  homepage.php    |
         |                                      +------------------+
         v
+---------------------+                         
|   auth/login.php    |  <--- (POST: login) ---+
|   auth/signup.php   |  <--- (POST: signup)   |
+---------------------+                        |
         |                                     |
         v                                     v
+------------------------------------------------------------+
|                      Database (MySQL)                     |
|  - users (login, signup, profile)                          |
|  - recipes (fetch, list, details)                          |
|  - preferences (user food prefs)                           |
+------------------------------------------------------------+
         ^                                     |
         |                                     |
+---------------------+                        |
|   profile.php       | <--- (GET/POST) ------+
|   edit-profile.php  | <--- (GET/POST) ------+
|   edit-preferences.php <--- (GET/POST) -----+
+---------------------+                        |
         |                                     |
         v                                     v
+---------------------+              +----------------------+
|    recipe.php       |  <--- (GET: recipe details) ------  |
+---------------------+              +----------------------+
```

---

## Google Login

RecipeWeb allows users to log in using their Google account for a faster and more secure experience.  
To enable this:

1. Create a Google Cloud project and set up OAuth credentials.
2. Download the credentials and update `auth/google_auth.php` with your client ID and secret.
3. Set the correct redirect URI on the Google Cloud Console and in your code.

A "Sign in with Google" button is visible on the login page after configuration.

---

## Getting Started

### Prerequisites

- PHP 7.x or newer
- MySQL
- Web server (Apache, Nginx, XAMPP, etc.)

### Installation

1. **Clone the repository:**
    ```bash
    git clone https://github.com/shahdaksh050/RecipeWeb.git
    cd RecipeWeb
    ```

2. **Set up the database:**
    - Import `foodiehub.sql` into your MySQL server.

3. **Configure database connection:**
    - Edit `config.php` to match your MySQL credentials:
    ```php
    <?php
    $conn = new mysqli("localhost", "your_username", "your_password", "recipeweb");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    ?>
    ```

4. **Set up Google OAuth (optional but recommended):**
    - Create OAuth credentials in Google Cloud.
    - Set `client_id`, `client_secret`, and redirect URI in `auth/google_auth.php`.

5. **Run the application:**
    - Copy the project folder to your web server’s root (e.g., `htdocs` for XAMPP).
    - Open `http://localhost/RecipeWeb/homepage.php` to get started.

---



