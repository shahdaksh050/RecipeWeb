-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2025 at 10:59 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `foodiehub`
--

-- --------------------------------------------------------

--
-- Table structure for table `cuisines`
--

CREATE TABLE `cuisines` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dietary_preferences`
--

CREATE TABLE `dietary_preferences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vegetarian` tinyint(1) DEFAULT 0,
  `lactose_intolerant` tinyint(1) DEFAULT 0,
  `allergies` text DEFAULT NULL,
  `spice_level` enum('Low','Medium','High') DEFAULT 'Medium',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dietary_preferences`
--

INSERT INTO `dietary_preferences` (`id`, `user_id`, `vegetarian`, `lactose_intolerant`, `allergies`, `spice_level`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Peanuts', 'Medium', '2025-04-21 19:59:46', '2025-04-21 19:59:46');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `recipe_id`, `created_at`) VALUES
(1, 1, 8, '2025-04-21 20:35:04'),
(3, 1, 9, '2025-04-21 20:35:34'),
(4, 1, 6, '2025-04-21 20:37:37'),
(5, 1, 18, '2025-04-21 20:37:44');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total` decimal(10,2) NOT NULL,
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `recipe_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `cuisine_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `rating` decimal(2,1) DEFAULT 0.0,
  `prep_time` int(11) DEFAULT NULL,
  `difficulty` enum('Easy','Medium','Hard') DEFAULT 'Easy',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `title`, `cuisine_id`, `description`, `image`, `rating`, `prep_time`, `difficulty`, `created_at`) VALUES
(1, 'Cheesy Quesadilla', NULL, 'Ingredients:\n2 flour tortillas\n1 cup shredded cheese (cheddar or mozzarella)\n1/4 cup chopped bell peppers\n1/4 cup chopped onions\n1 tbsp butter\nSalsa or sour cream (optional)\n\nInstructions:\nHeat butter in a pan over medium heat.\nPlace one tortilla in the pan and top with cheese, peppers, and onions.\nCover with the second tortilla.\nCook until golden brown on both sides and cheese melts.\nSlice and serve with salsa or sour cream.', 'photos/quesadilla.jpg', 0.0, 10, 'Easy', '2025-04-21 19:45:53'),
(2, 'Apple Pie', NULL, 'Ingredients:\n2 cups all-purpose flour\n1/2 cup butter\n1/4 cup ice-cold water\n6 medium apples, peeled and sliced\n3/4 cup sugar\n1 tbsp lemon juice\n1/2 tsp cinnamon\n1/4 tsp nutmeg\n1 tbsp cornstarch\nPinch of salt\n\nInstructions:\nMix flour, butter, and water to make the pie dough. Refrigerate for 30 minutes.\nCombine sliced apples with sugar, lemon juice, cinnamon, nutmeg, cornstarch, and salt.\nRoll out the dough and line a pie dish. Fill with apple mixture and cover with another layer of dough.\nCut slits on top to let steam escape, then bake at 375°F for 40 minutes or until golden.\nLet cool and serve with vanilla ice cream or whipped cream.', 'photos/apple pie1.jpg', 0.0, 20, 'Easy', '2025-04-21 19:45:53'),
(3, 'Burrito', NULL, 'Ingredients:\n2 large flour tortillas\n1 cup cooked rice\n1/2 cup black beans\n1/2 cup cooked chicken or beef (optional)\n1/4 cup salsa\n1/4 cup shredded cheese\n1/4 cup chopped lettuce\n2 tbsp sour cream or guacamole\n\nInstructions:\nWarm the tortillas slightly in a pan.\nLayer rice, beans, meat, salsa, cheese, and lettuce in the center of each tortilla.\nAdd sour cream or guacamole.\nFold in the sides and roll up tightly into a burrito shape.\nOptional: Grill the burrito for a crispy exterior.', 'photos/burrito.jpg', 0.0, 20, 'Easy', '2025-04-21 19:45:53'),
(4, 'Cheeseburger', NULL, 'Ingredients:\n2 burger buns\n2 beef patties\n2 slices of cheddar cheese\nLettuce\nTomato, sliced\nOnion rings\nKetchup and mustard\nPickles\n\nInstructions:\nCook the beef patties in a pan or grill until desired doneness.\nPlace a slice of cheese on each patty and let it melt.\nToast the burger buns and assemble the burger with lettuce, tomato, onion, and pickles.\nServe with ketchup and mustard on the side.', 'photos/cheeseburger.jpg', 0.0, 15, 'Easy', '2025-04-21 19:45:53'),
(5, 'Chole Bhature', NULL, 'Ingredients:\n1 cup dried chickpeas (soaked overnight)\n2 onions, chopped\n2 tomatoes, chopped\n1 tsp ginger-garlic paste\n2 tsp chole masala\n1/2 tsp turmeric powder\n1/2 tsp red chili powder\nSalt to taste\n2 tbsp oil\nFresh coriander to garnish\n\nInstructions:\nBoil soaked chickpeas until soft.\nHeat oil and sauté onions until golden brown.\nAdd ginger-garlic paste, tomatoes, and spices. Cook well.\nAdd boiled chickpeas and simmer for 10-15 minutes.\nGarnish with coriander and serve hot with puri or bhature.', 'photos/chole_bhature.jpg', 0.0, 15, 'Easy', '2025-04-21 19:45:53'),
(6, 'Crepes', NULL, 'Ingredients:\n1 cup flour\n2 eggs\n1/2 cup milk\n1 tbsp melted butter\n1 tsp vanilla extract\nPinch of salt\nButter for cooking\n\nInstructions:\nMix flour, eggs, milk, melted butter, vanilla, and salt into a smooth batter.\nHeat a non-stick pan with butter. Pour in a small amount of batter.\nCook for 1-2 minutes per side, flipping to brown both sides.\nServe with your favorite fillings like fruits or Nutella.', 'photos/crepes.jpg', 0.0, 5, 'Easy', '2025-04-21 19:45:53'),
(7, 'Masala Dosa', NULL, 'Ingredients:\n2 cups dosa batter\n2 boiled potatoes, mashed\n1 onion, sliced\n1/2 tsp mustard seeds\n1 green chili, chopped\n1/2 tsp turmeric powder\n1 tbsp oil\nSalt to taste\n\nInstructions:\nHeat oil, add mustard seeds, onions, chili, turmeric. Sauté well.\nAdd mashed potatoes and salt. Cook the filling.\nPour dosa batter on hot tawa, spread thin.\nPlace filling in center. Fold dosa and serve with chutney.', 'photos/dosa.jpg', 0.0, 20, 'Easy', '2025-04-21 19:45:53'),
(8, 'Vegetable Fried Rice', NULL, 'Ingredients:\r\n2 cups cooked rice (cold)\r\n1/2 cup carrots, beans, and capsicum (chopped)\r\n2 tbsp soy sauce\r\n1 tbsp sesame oil\r\n1/2 tsp black pepper\r\n1 tsp garlic (chopped)\r\nSpring onions for garnish\r\n\r\nInstructions:\r\nHeat oil, add garlic and sauté till golden.\r\nAdd vegetables, stir-fry on high flame for 2–3 mins.\r\nAdd rice, soy sauce, pepper, and toss well.\r\nGarnish with spring onions and serve hot.', 'photos/fried_rice.jpeg\r\n', 4.5, 60, 'Medium', '2025-04-21 19:45:53'),
(9, 'Hot & Sour Soup', NULL, 'Ingredients:\n2 cups vegetable broth\n1/4 cup soy sauce\n1 tbsp rice vinegar\n1 tbsp chili paste\n1 cup mushrooms, sliced\n1/2 cup tofu, cubed\n1/2 cup bamboo shoots\n1/2 cup carrots, julienned\n1 tsp ginger, grated\n2 tbsp cornstarch\n2 cups water\nSpring onions for garnish\n\nInstructions:\nIn a pot, bring vegetable broth, soy sauce, rice vinegar, and chili paste to a boil.\nAdd mushrooms, tofu, bamboo shoots, carrots, and ginger. Cook for 10 minutes.\nMix cornstarch with water and add it to the soup to thicken it.\nSimmer for an additional 5 minutes. Garnish with spring onions and serve hot.', 'photos/soup.jpg', 0.0, 15, 'Easy', '2025-04-21 19:45:53'),
(10, 'Macrons', NULL, 'Ingredients:\n1 3/4 cups powdered sugar\n1 cup almond flour\n3 large egg whites\n1/4 cup granulated sugar\n1/4 tsp cream of tartar\nButtercream or jam for filling\n\nInstructions:\nPreheat the oven to 300°F (150°C). Line baking sheets with parchment paper.\nWhisk egg whites with cream of tartar until soft peaks form, then add sugar.\nFold in almond flour and powdered sugar mixture.\nPipe the batter into circles on the prepared baking sheets.\nBake for 15-20 minutes. Cool before filling with buttercream or jam.', 'photos/macrons.jpeg', 0.0, 30, 'Easy', '2025-04-21 19:45:53'),
(11, 'Loaded Nachos', NULL, 'Ingredients:\nTortilla chips\n1 cup shredded cheddar cheese\n1/2 cup black beans\n1/2 cup chopped tomatoes\n1/4 cup sliced olives\n1/4 cup sliced jalapeños\nSour cream and guacamole for topping\n\nInstructions:\nPreheat oven to 180°C (350°F).\nArrange tortilla chips on a baking tray.\nSprinkle cheese, beans, tomatoes, olives, and jalapeños over the chips.\nBake for 10–15 minutes until cheese melts.\nTop with sour cream and guacamole before serving.', 'photos/nachos.jpg', 0.0, 10, 'Easy', '2025-04-21 19:45:53'),
(12, 'Noodles and Manchurian', NULL, 'Ingredients:\n1 cup noodles\n1/2 cup mixed vegetables (carrot, beans, peas)\n2 tbsp soy sauce\n1 tbsp vinegar\n1 tbsp chili sauce\n2 tbsp cornflour\n1/2 cup water\nOil for frying\n\nInstructions:\nCook noodles according to package instructions and set aside.\nFor Manchurian, mix vegetables, cornflour, and water to form a batter.\nDeep fry the batter in small portions to form Manchurian balls.\nIn a pan, sauté the noodles with soy sauce, vinegar, chili sauce, and cooked Manchurian balls.\nServe hot and enjoy your meal!', 'photos/noodles.jpg', 0.0, 20, 'Easy', '2025-04-21 19:45:53'),
(13, 'Nutella Strawberry Pancakes', NULL, 'Ingredients:\n1 cup flour\n1 egg\n1/2 cup milk\n1 tbsp sugar\n1/2 tsp baking powder\nNutella\nFresh strawberries, sliced\nButter for cooking\n\nInstructions:\nWhisk together flour, egg, milk, sugar, and baking powder.\nHeat a pan with butter and pour batter to form pancakes.\nCook until golden brown on both sides.\nSpread Nutella on the pancakes, and top with fresh strawberries.\nServe warm and enjoy!', 'photos/pancake.jpg', 0.0, 15, 'Easy', '2025-04-21 19:45:53'),
(14, 'Paneer Butter Masala & Biryani', NULL, 'Ingredients:\n200g paneer, cubed\n2 onions, chopped\n2 tomatoes, pureed\n1 tsp ginger-garlic paste\n2 tbsp butter\n1/2 cup cream\n1 tsp garam masala\n1 tsp red chili powder\nSalt to taste\n1 tsp kasuri methi\n\nInstructions:\nSauté onions in butter until golden brown.\nAdd ginger-garlic paste, then tomato puree and spices. Cook well.\nAdd cream and paneer. Simmer 5–7 minutes.\nSprinkle kasuri methi and serve hot.', 'photos/paneer.jpg', 0.0, 20, 'Easy', '2025-04-21 19:45:53'),
(15, 'Pasta & Garlic Bread', NULL, 'Ingredients:\n200g pasta (penne or spaghetti)\n2 tbsp olive oil\n1/2 cup chopped onions\n1/2 cup chopped bell peppers\n2 cloves garlic (minced)\n1 cup tomato puree\n1 tsp oregano & chili flakes\nSalt to taste\n1/4 cup grated cheese (optional)\n4 slices French bread\n2 tbsp butter mixed with minced garlic & herbs\n\nInstructions:\nBoil pasta in salted water until al dente. Drain and keep aside.\nHeat olive oil, sauté onions, bell peppers, and garlic until soft.\nAdd tomato puree, salt, oregano, chili flakes and simmer for 5-7 minutes.\nMix in the pasta and cook for another 2-3 minutes. Top with cheese.\nFor garlic bread: spread garlic butter on bread slices and toast until golden.\nServe hot pasta with crispy garlic bread on the side.', 'photos/pasta.jpg', 0.0, 15, 'Easy', '2025-04-21 19:45:53'),
(16, 'Pav Bhaji', NULL, 'Ingredients:\n4 pav buns\n2 tbsp butter\n2 potatoes, boiled and mashed\n1/2 cup green peas\n1 onion, finely chopped\n1 tomato, chopped\n1/2 capsicum, chopped\n2 tsp pav bhaji masala\n1 tsp red chili powder\nLemon, coriander leaves\n\nInstructions:\nHeat butter and sauté onions till golden. Add tomatoes and capsicum and cook.\nAdd mashed potatoes, peas, masala and mix well. Mash and simmer with water.\nToast pav with butter on a tawa.\nServe hot bhaji with buttered pav, lemon, and chopped onions.', 'photos/pav bhaji.jpg', 0.0, 15, 'Easy', '2025-04-21 19:45:53'),
(17, 'Classic Margherita Pizza', NULL, 'Ingredients:\n1 pizza base (store-bought or homemade)\n1/2 cup pizza sauce\n1 cup shredded mozzarella cheese\nFresh basil leaves\n1 tomato, sliced\n1 tsp olive oil\nOregano and chili flakes to taste\n\nInstructions:\nPreheat the oven to 220°C (425°F).\nPlace the pizza base on a baking tray.\nSpread pizza sauce evenly on the base.\nSprinkle mozzarella cheese over the sauce.\nAdd tomato slices and fresh basil leaves.\nDrizzle with olive oil and sprinkle oregano/chili flakes.\nBake in the oven for 12–15 minutes or until the cheese is melted and bubbly.\nSlice and serve hot!', 'photos/pizza.jpg', 0.0, 20, 'Easy', '2025-04-21 19:45:53'),
(18, 'Quiche Lorraine', NULL, 'Ingredients:\n1 pie crust\n6 eggs\n1 cup heavy cream\n1/2 cup milk\n1/2 cup cooked bacon, crumbled\n1/2 cup shredded cheese (Gruyère or Swiss)\nSalt & pepper to taste\n\nInstructions:\nPreheat the oven to 350°F (175°C).\nWhisk eggs, cream, milk, salt, and pepper together.\nPlace bacon and cheese into the pie crust, then pour the egg mixture on top.\nBake for 40-45 minutes, or until set and golden brown.', 'photos/quiche_lorraine.jpg', 0.0, 15, 'Easy', '2025-04-21 19:45:53'),
(19, 'Ratatouille', NULL, 'Ingredients:\n1 eggplant, thinly sliced\n1 zucchini, thinly sliced\n1 yellow squash, thinly sliced\n1 red bell pepper, thinly sliced\n1 yellow bell pepper, thinly sliced\n1 cup crushed tomatoes\n2 cloves garlic, minced\n1 tbsp olive oil\n1 tsp dried thyme\nSalt and pepper to taste\nFresh basil for garnish\n\nInstructions:\nPreheat the oven to 375°F (190°C).\nSpread crushed tomatoes on the bottom of a baking dish. Add garlic, thyme, salt, and pepper.\nLayer sliced vegetables in alternating colors in a spiral or row pattern over the tomato base.\nDrizzle olive oil on top and cover with foil. Bake for 30 minutes.\nUncover and bake for another 10 minutes until veggies are tender and slightly golden.\nGarnish with fresh basil and serve warm with crusty bread.', 'photos/ratatouille.jpg', 0.0, 20, 'Easy', '2025-04-21 19:45:53'),
(20, 'Creamy Mushroom Risotto', NULL, 'Ingredients:\n1 cup arborio rice\n2 tbsp olive oil\n1 small onion, chopped\n1 cup mushrooms, sliced\n3 cups vegetable broth\n1/4 cup white wine (optional)\n2 tbsp butter\n1/4 cup grated parmesan cheese\nSalt & pepper to taste\n\nInstructions:\nHeat oil, sauté onion and mushrooms.\nAdd arborio rice, stir for 2 mins.\nPour wine (optional), let it absorb.\nAdd broth gradually, stirring constantly, until rice is creamy and cooked.\nStir in butter and cheese. Serve warm.', 'photos/risotto.avif', 0.0, 10, 'Easy', '2025-04-21 19:45:53'),
(21, 'Spring Roll', NULL, 'Ingredients:\nSpring roll wrappers\n1 cup cabbage, shredded\n1/2 cup carrots, shredded\n1/4 cup bean sprouts\n2 tbsp soy sauce\n1/4 tsp pepper\nOil for frying\n\nInstructions:\nMix cabbage, carrots, bean sprouts, soy sauce, and pepper in a bowl.\nPlace the mixture in the spring roll wrappers and roll them tightly.\nHeat oil in a pan and deep fry the rolls until golden brown.\nServe with dipping sauce of your choice.', 'photos/spring roll.jpg', 0.0, 20, 'Easy', '2025-04-21 19:45:53'),
(22, 'Tacos', NULL, 'Ingredients:\n8 small corn tortillas\n1 cup cooked shredded chicken or beef\n1/2 cup chopped onions\n1/2 cup chopped tomatoes\n1/4 cup chopped cilantro\n1 lime, cut into wedges\nSalsa and sour cream for serving\n\nInstructions:\nWarm the tortillas in a pan or microwave.\nFill each tortilla with cooked meat.\nTop with onions, tomatoes, and cilantro.\nDrizzle with salsa or sour cream if desired.\nServe with lime wedges on the side.', 'photos/tacos.jpg', 0.0, 15, 'Easy', '2025-04-21 19:45:53'),
(23, 'Classic Tiramisu', NULL, 'Ingredients:\n6 oz ladyfinger biscuits\n1 cup brewed espresso or strong coffee, cooled\n3 eggs, separated\n1/2 cup sugar\n1 cup mascarpone cheese\n1 cup heavy cream\nUnsweetened cocoa powder (for dusting)\n\nInstructions:\nBeat egg yolks with sugar until pale and creamy.\nFold in mascarpone until smooth.\nIn a separate bowl, whip cream until stiff peaks form and fold into the mascarpone mixture.\nDip ladyfingers briefly into coffee and layer in a dish.\nSpread half of the mascarpone mixture over the ladyfingers.\nRepeat layers and finish with mascarpone on top.\nCover and refrigerate for at least 4 hours or overnight.\nDust with cocoa powder before serving.', 'photos/tiramisu.jpg', 0.0, 25, 'Easy', '2025-04-21 19:45:53'),
(24, 'Cheese Sandwich ', NULL, 'Ingredients:\r\n4 slices bread\r\n2 tbsp butter\r\n1 small onion, finely chopped\r\n1 small tomato, finely chopped\r\n1/2 cup grated cheese\r\n1/2 tsp red chili powder\r\n1/4 tsp turmeric powder\r\nSalt to taste\r\nFresh coriander leaves\r\n\r\nInstructions:\r\nSpread butter on one side of each bread slice.\r\nIn a pan, sauté onions and tomatoes with spices until soft.\r\nSpread the mixture on one slice of bread and sprinkle cheese on top.\r\nCover with the other slice and toast in a pan until golden brown.\r\nServe hot, garnished with coriander leaves.', 'photos/sandwhich.jpeg', 0.0, 30, 'Medium', '2025-04-21 20:17:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Daksh Shah', 'shahdaksh050@gmail.com', '$2y$10$ncJwAi4ppwGtKOYAJtnW9.CNxyv2VBSpeHYmZtXelxfgbKhsrSp5G', 'Earth', '2025-04-21 19:59:14', '2025-04-21 19:59:35');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cuisines`
--
ALTER TABLE `cuisines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dietary_preferences`
--
ALTER TABLE `dietary_preferences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`recipe_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `recipe_id` (`recipe_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cuisine_id` (`cuisine_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cuisines`
--
ALTER TABLE `cuisines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dietary_preferences`
--
ALTER TABLE `dietary_preferences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dietary_preferences`
--
ALTER TABLE `dietary_preferences`
  ADD CONSTRAINT `dietary_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`cuisine_id`) REFERENCES `cuisines` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
