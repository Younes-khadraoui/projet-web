-- SEED DATA

-- Dummy Users (password: password123)
INSERT INTO `users` (`name`, `email`, `password`, `role`, `balance`) VALUES
('younes khx', 'younes@example.com', '$2y$12$9QqJuJ9RlLa5iQlfg3aj..dI1kwMfclpkiz7HJ5lEcj1mZA7unDni', 'user', 150.75),
('sidali moh', 'sidali@example.com', '$2y$12$9QqJuJ9RlLa5iQlfg3aj..dI1kwMfclpkiz7HJ5lEcj1mZA7unDni', 'user', 150.75);

-- Categories
INSERT INTO `categories` (`name`) VALUES
('Électronique'),
('Vêtements'),
('Maison'),
('Loisirs'),
('Autres');


-- Ads
INSERT INTO `ads` (`title`, `description`, `price`, `delivery_type`, `category_id`, `seller_id`) VALUES
('Smartphone quasi neuf', 'Un smartphone en excellent état, utilisé pendant 6 mois. Vendu avec sa boîte et ses accessoires d\'origine.', 250.00, 'delivery,hand_delivery', (SELECT id FROM categories WHERE name = 'Électronique'), (SELECT id FROM users WHERE email = 'younes@example.com')),
('Manteau d\'hiver chaud', 'Manteau pour homme, taille L. Très peu porté, idéal pour les grands froids. Marque Célèbre.', 75.50, 'delivery', (SELECT id FROM categories WHERE name = 'Vêtements'), (SELECT id FROM users WHERE email = 'younes@example.com')),
('Table basse en bois', 'Jolie table basse en bois massif. Quelques petites rayures mais en bon état général. Dimensions : 120x60x45cm.', 40.00, 'hand_delivery', (SELECT id FROM categories WHERE name = 'Maison'), (SELECT id FROM users WHERE email = 'sidali@example.com'));