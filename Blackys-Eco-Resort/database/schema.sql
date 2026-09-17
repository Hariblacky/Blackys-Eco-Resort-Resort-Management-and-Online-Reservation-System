-- ============================================================
-- Blacky's Eco Resort — Database Schema
-- Import this file into MySQL (phpMyAdmin: Import, or CLI:
--   mysql -u root -p < schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS blackys_eco_resort CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE blackys_eco_resort;

-- ------------------------------------------------------------
-- Admin users
-- ------------------------------------------------------------
CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  username VARCHAR(60) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('superadmin','manager') NOT NULL DEFAULT 'manager',
  status ENUM('active','disabled') NOT NULL DEFAULT 'active',
  last_login DATETIME NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default login: username = admin / password = Admin@123
-- (hash generated with PHP password_hash — change immediately after first login)
INSERT INTO admins (name, username, email, password_hash, role) VALUES
('Resort Administrator', 'admin', 'admin@blackysecoresort.com',
 '$2y$10$2Wjo94tTaMUx7KT0dDtXa.XI1yUmjvIiVI2Smap/knMFZgpdmgsvK', 'superadmin');

-- ------------------------------------------------------------
-- Rooms
-- ------------------------------------------------------------
CREATE TABLE rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(160) NOT NULL UNIQUE,
  category VARCHAR(80) NOT NULL DEFAULT 'Villa',
  description TEXT,
  price_per_night DECIMAL(10,2) NOT NULL DEFAULT 0,
  capacity INT NOT NULL DEFAULT 2,
  size_sqm INT NOT NULL DEFAULT 40,
  bed_type VARCHAR(80) NOT NULL DEFAULT 'King Bed',
  amenities TEXT COMMENT 'comma separated list',
  image VARCHAR(255) NOT NULL,
  gallery_images TEXT COMMENT 'comma separated filenames',
  featured TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO rooms (name, slug, category, description, price_per_night, capacity, size_sqm, bed_type, amenities, image, featured) VALUES
('Canopy Treehouse Villa', 'canopy-treehouse-villa', 'Treehouse', 'Perched above the rainforest canopy with panoramic jungle views, reclaimed timber interiors and a private plunge pool.', 420.00, 2, 65, 'King Bed', 'Private Plunge Pool,Rainforest View,Free WiFi,Organic Minibar,Outdoor Rain Shower', 'https://loremflickr.com/900/650/treehouse,resort', 1),
('Lagoon Water Villa', 'lagoon-water-villa', 'Water Villa', 'Overwater stilted villa with a glass floor panel, direct lagoon access and uninterrupted sunset views.', 560.00, 2, 78, 'King Bed', 'Glass Floor Panel,Direct Lagoon Access,Private Deck,Free WiFi,Air Conditioning', 'https://loremflickr.com/900/650/overwater,bungalow', 1),
('Mangrove Garden Suite', 'mangrove-garden-suite', 'Suite', 'Ground-level suite framed by native mangrove gardens, with an open-air bathroom and private hammock terrace.', 310.00, 3, 55, 'Queen + Single', 'Private Hammock Terrace,Open-Air Bathroom,Garden View,Free WiFi,Ceiling Fan', 'https://loremflickr.com/900/650/eco,cabin', 1),
('Beachfront Eco Bungalow', 'beachfront-eco-bungalow', 'Bungalow', 'Solar-powered bungalow steps from the shoreline, built entirely from sustainably sourced local materials.', 380.00, 2, 48, 'King Bed', 'Beach Access,Solar Powered,Free WiFi,Outdoor Shower,Yoga Mat', 'https://loremflickr.com/900/650/beach,bungalow', 0),
('Rainforest Family Lodge', 'rainforest-family-lodge', 'Family Lodge', 'Spacious two-bedroom lodge designed for families, with a shared veranda overlooking the reforestation valley.', 495.00, 5, 110, '2 Queen + Bunk', 'Two Bedrooms,Shared Veranda,Kitchenette,Free WiFi,Valley View', 'https://loremflickr.com/900/650/lodge,forest', 0),
('Sky Deck Loft Villa', 'sky-deck-loft-villa', 'Loft Villa', 'A modern loft with a private rooftop star-gazing deck and telescope, ideal for a romantic eco getaway.', 445.00, 2, 60, 'King Bed', 'Rooftop Star Deck,Telescope Provided,Free WiFi,Rain Shower,Minibar', 'https://loremflickr.com/900/650/villa,rooftop', 0);

-- ------------------------------------------------------------
-- Restaurant menu items
-- ------------------------------------------------------------
CREATE TABLE menu_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category VARCHAR(60) NOT NULL,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(8,2) NOT NULL DEFAULT 0,
  image VARCHAR(255),
  is_special TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO menu_items (category, name, description, price, image, is_special) VALUES
('Starters', 'Heart of Palm Ceviche', 'Locally foraged heart of palm, citrus, chili and coconut milk.', 14.00, 'https://loremflickr.com/500/380/ceviche', 0),
('Starters', 'Charred Jungle Vegetables', 'Seasonal vegetables from our permaculture garden, smoked over coconut husk.', 12.00, 'https://loremflickr.com/500/380/grilled,vegetables', 0),
('Main Course', 'Wild-Caught Reef Fish', 'Grilled catch of the day with turmeric-lime butter and forest greens.', 32.00, 'https://loremflickr.com/500/380/grilled,fish', 1),
('Main Course', 'Slow-Roasted Jungle Pork', 'Free-range pork shoulder braised in tamarind and native spices.', 28.00, 'https://loremflickr.com/500/380/roasted,pork', 0),
('Main Course', 'Banana Leaf Vegetable Curry', 'Garden vegetables and jackfruit simmered in coconut curry, steamed in banana leaf.', 22.00, 'https://loremflickr.com/500/380/curry,vegetables', 0),
('Desserts', 'Cacao & Passionfruit Tart', 'Single-origin estate cacao with wild passionfruit coulis.', 11.00, 'https://loremflickr.com/500/380/chocolate,tart', 1),
('Desserts', 'Coconut Sticky Rice', 'Steamed sticky rice, palm sugar caramel and toasted coconut.', 9.00, 'https://loremflickr.com/500/380/coconut,dessert', 0),
('Drinks', 'Rainforest Botanical Cooler', 'House-infused botanicals, lemongrass and sparkling water.', 8.00, 'https://loremflickr.com/500/380/mocktail', 0),
('Drinks', 'Estate Cacao Espresso', 'Single-origin coffee grown on the resort''s own hillside estate.', 6.00, 'https://loremflickr.com/500/380/espresso', 0);

-- ------------------------------------------------------------
-- Activities (content-managed via database, extend admin as needed)
-- ------------------------------------------------------------
CREATE TABLE activities (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  duration VARCHAR(60) DEFAULT '2 Hours',
  price DECIMAL(8,2) DEFAULT 0,
  image VARCHAR(255),
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO activities (name, description, duration, price, image) VALUES
('Guided Rainforest Trek', 'Explore ancient canopy trails with our resident naturalist guide.', '3 Hours', 45.00, 'https://loremflickr.com/700/500/rainforest,trail'),
('Sunrise Kayaking', 'Paddle the still lagoon waters as the sun rises over the mangroves.', '2 Hours', 35.00, 'https://loremflickr.com/700/500/kayak,lagoon'),
('Reef Snorkeling Expedition', 'Discover vibrant coral reefs just offshore with certified instructors.', '2.5 Hours', 55.00, 'https://loremflickr.com/700/500/snorkeling,reef'),
('Organic Farm & Cacao Tour', 'Walk our permaculture farm and learn bean-to-bar chocolate making.', '1.5 Hours', 25.00, 'https://loremflickr.com/700/500/organic,farm'),
('Sunset Yoga on the Deck', 'Restorative yoga session overlooking the water as the sun sets.', '1 Hour', 20.00, 'https://loremflickr.com/700/500/yoga,sunset'),
('Night Wildlife Safari', 'Guided evening walk spotting nocturnal rainforest wildlife.', '2 Hours', 40.00, 'https://loremflickr.com/700/500/night,safari');

-- ------------------------------------------------------------
-- Gallery
-- ------------------------------------------------------------
CREATE TABLE gallery (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  category VARCHAR(60) NOT NULL DEFAULT 'Resort',
  image VARCHAR(255) NOT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO gallery (title, category, image) VALUES
('Canopy Villa at Dawn', 'Rooms', 'https://loremflickr.com/600/750/treehouse'),
('The Lagoon Pool', 'Resort', 'https://loremflickr.com/600/450/infinity,pool'),
('Reef Fish Plating', 'Restaurant', 'https://loremflickr.com/600/500/plated,fish'),
('Guided Jungle Trek', 'Activities', 'https://loremflickr.com/600/700/jungle,trail'),
('Overwater Deck Sunset', 'Resort', 'https://loremflickr.com/600/460/sunset,water'),
('Garden Suite Interior', 'Rooms', 'https://loremflickr.com/600/720/eco,interior'),
('Cacao Tasting Table', 'Restaurant', 'https://loremflickr.com/600/440/cacao'),
('Kayaking the Mangroves', 'Activities', 'https://loremflickr.com/600/700/kayak,mangrove'),
('Aerial Resort View', 'Resort', 'https://loremflickr.com/600/460/aerial,resort'),
('Beachfront Bungalow', 'Rooms', 'https://loremflickr.com/600/700/beach,hut'),
('Farm-to-Table Harvest', 'Restaurant', 'https://loremflickr.com/600/440/harvest,vegetables'),
('Sunset Yoga Deck', 'Activities', 'https://loremflickr.com/600/720/yoga,deck');

-- ------------------------------------------------------------
-- Offers
-- ------------------------------------------------------------
CREATE TABLE offers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description TEXT,
  discount_percent DECIMAL(5,2) NOT NULL DEFAULT 0,
  image VARCHAR(255),
  valid_from DATE NOT NULL,
  valid_to DATE NOT NULL,
  status ENUM('active','inactive') NOT NULL DEFAULT 'active',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO offers (title, description, discount_percent, image, valid_from, valid_to) VALUES
('Early Bird Escape', 'Book 60 days in advance and save on any villa category.', 20.00, 'https://loremflickr.com/700/500/sunrise,resort', '2026-01-01', '2026-12-31'),
('Honeymoon Package', 'Complimentary sunset cruise and private dinner for couples.', 15.00, 'https://loremflickr.com/700/500/romantic,dinner', '2026-01-01', '2026-12-31'),
('Extended Stay Retreat', 'Stay 5 nights or more and enjoy a reduced nightly rate.', 25.00, 'https://loremflickr.com/700/500/tropical,resort', '2026-01-01', '2026-12-31');

-- ------------------------------------------------------------
-- Bookings
-- ------------------------------------------------------------
CREATE TABLE bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_ref VARCHAR(20) NOT NULL UNIQUE,
  room_id INT NOT NULL,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(30) NOT NULL,
  check_in DATE NOT NULL,
  check_out DATE NOT NULL,
  guests INT NOT NULL DEFAULT 1,
  special_request TEXT,
  nights INT NOT NULL DEFAULT 1,
  total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
  status ENUM('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_booking_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Contact messages
-- ------------------------------------------------------------
CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  subject VARCHAR(200),
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Site settings (single row)
-- ------------------------------------------------------------
CREATE TABLE settings (
  id INT PRIMARY KEY DEFAULT 1,
  site_name VARCHAR(150) NOT NULL DEFAULT "Blacky's Eco Resort",
  tagline VARCHAR(255) NOT NULL DEFAULT 'Barefoot Luxury. Untouched Nature.',
  phone VARCHAR(40) DEFAULT '+1 555 200 3000',
  whatsapp_number VARCHAR(40) DEFAULT '15552003000',
  email VARCHAR(150) DEFAULT 'stay@blackysecoresort.com',
  address VARCHAR(255) DEFAULT 'Isla Verde Coastal Reserve, Costa Esmeralda',
  logo VARCHAR(255) DEFAULT '',
  hero_video VARCHAR(255) DEFAULT '',
  facebook VARCHAR(255) DEFAULT '#',
  instagram VARCHAR(255) DEFAULT '#',
  twitter VARCHAR(255) DEFAULT '#',
  latitude DECIMAL(10,7) DEFAULT 9.9280900,
  longitude DECIMAL(10,7) DEFAULT -84.0907400,
  currency VARCHAR(5) NOT NULL DEFAULT '$',
  meta_description VARCHAR(300) DEFAULT "Blacky's Eco Resort — a barefoot-luxury rainforest and lagoon retreat with treehouse villas, overwater suites and farm-to-table dining.",
  meta_keywords VARCHAR(300) DEFAULT 'eco resort, luxury resort, treehouse villa, sustainable travel, rainforest resort',
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO settings (id) VALUES (1);
