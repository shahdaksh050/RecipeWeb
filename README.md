# RecipeWeb

RecipeWeb is a web app designed to help you find and manage recipes from a range of world cuisines. It is built using PHP, HTML, CSS, MySQL, and Bootstrap, with a focus on simplicity and a pleasant user experience.

---

## Features

- **Cuisines:** Browse recipes grouped by cuisine—Indian, Chinese, Italian, Mexican, American, French, and more.
- **Easy Authentication:** Log in with your own credentials or quickly sign in using your Google account (OAuth integration).
- **Detailed Recipes:** Each recipe page provides ingredients, step-by-step instructions, and beautiful images.
- **User Profiles:** Edit your details and set your personal preferences.
- **Mobile-Friendly:** The layout is responsive and works well on all screen sizes, powered by Bootstrap.

---

## Technologies

- **Frontend:** HTML, CSS, Bootstrap
- **Backend:** PHP
- **Database:** MySQL
- **Auth:** Native PHP authentication & Google OAuth

---

## Project Structure

```
RecipeWeb/
├── auth/                      # Login, signup, Google OAuth
│   ├── login.php
│   ├── signup.php
│   └── google_auth.php
├── photos/                    # All image assets
├── american_cuisine.html
├── chineese_cuisine.html
├── config.php                 # DB connection settings
├── edit-preferences.php       # Edit user preferences
├── edit-profile.php           # Edit profile info
├── foodiehub.sql              # Database schema
├── french_cuisine.html
├── homepage.php               # App landing page after login
├── image.png                  # Sample image
├── index.php                  # Main entry point / router
├── indian_cuisine.html
├── italian_cuisine.html
├── logout.php                 # Ends user session
├── mexican_cuisine.html
├── profile.php                # User profile
├── recipe.php                 # Single recipe details
└── README.md                  # This file
```

---

## Key Files Explained

- **index.php**  
  Boots up the project and routes users to login or homepage depending on their session.

- **homepage.php**  
  Displays featured cuisines and an overview for logged-in users.

- **auth/login.php & auth/signup.php**  
  Handle user authentication (with input validation and error handling).

- **auth/google_auth.php**  
  Lets users log in with their Google account using OAuth 2.0 (seamless and secure).

- **config.php**  
  Contains your MySQL connection info. All DB queries connect through here.

- **recipe.php**  
  Loads a particular recipe from the database and shows its details.

- **profile.php**  
  Lets users view and update their profile and preferences.

- **edit-profile.php & edit-preferences.php**  
  Simple forms for updating user info and favorite cuisines.

- **logout.php**  
  Safely logs the user out and destroys the session.

- **[cuisine]_cuisine.html**  
  Pages listing recipes for each cuisine (e.g., "indian_cuisine.html").

---

## Google Login

RecipeWeb supports Google sign-in for a quick and secure way to access your recipes.  
To enable Google OAuth:

1. Set up a Google Cloud project and create OAuth credentials.
2. Download the credentials and set your client ID/secret in `auth/google_auth.php`.
3. Make sure your redirect URI matches what is set in Google Cloud.

When enabled, users will see a "Sign in with Google" button on the login page.

---

## Getting Started

1. **Requirements:**  
   - PHP 7 or newer  
   - MySQL  
   - Web server (Apache, Nginx, XAMPP, etc.)

2. **Install:**  
   - Clone the repo:  
     ```bash
     git clone https://github.com/shahdaksh050/RecipeWeb.git
     cd RecipeWeb
     ```
   - Import `foodiehub.sql` into your MySQL server.
   - Update `config.php` with your DB credentials:
     ```php
     <?php
     $conn = new mysqli("localhost", "your_username", "your_password", "recipeweb");
     if ($conn->connect_error) {
         die("Connection failed: " . $conn->connect_error);
     }
     ?>
     ```
   - Place the directory in your web server’s root (e.g., `htdocs` for XAMPP).

3. **Run:**  
   - Open `http://localhost/RecipeWeb/homepage.php` in your browser.
