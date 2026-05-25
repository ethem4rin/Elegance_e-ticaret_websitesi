-- Élégance E-Commerce Database Schema
-- Run: mysql -u root -p < database.sql

CREATE DATABASE IF NOT EXISTS eticaretdb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eticaretdb;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    address TEXT,
    role ENUM('admin','user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Products table
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image_url VARCHAR(500) DEFAULT '',
    stock_quantity INT DEFAULT 0,
    category_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_product_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    CONSTRAINT fk_cart_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_cart_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('Pending','Shipped','Delivered') DEFAULT 'Pending',
    shipping_name VARCHAR(150),
    shipping_email VARCHAR(150),
    shipping_address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price_at_purchase DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_orderitem_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT fk_orderitem_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- SAMPLE DATA
-- =============================================

-- Admin user (password: Admin123!)
-- Hash generated with: password_hash('Admin123!', PASSWORD_DEFAULT)
INSERT INTO users (full_name, email, password, address, role) VALUES
('Yönetici Kullanıcı', 'admin@example.com', '$2y$10$f9fZGCVNC1pRCDbbZFbFMut9zeaJyj1ZvItXPokP72DGlKt3WmixK', '123 Yönetim Sokak, Şehir', 'admin');

-- Categories
INSERT INTO categories (category_name) VALUES
('Erkek'),
('Kadın'),
('Aksesuarlar'),
('Çanta'),
('Çocuk'),
('Ayakkabı');

-- Products
INSERT INTO products (name, description, price, image_url, stock_quantity, category_id) VALUES
('Merino Yün Blazer', '100% merino yününden üretilmiş, özenle dikilmiş bir blazer. Hem resmi hem de şık-günlük kullanımlar için idealdir. Yapılandırılmış omuzlara, dar kesime ve iki düğmeli kapanışa sahiptir.', 289.00, '', 15, 1),
('Kaşmir Balıkçı Yaka', 'Rahat kalıpta, son derece yumuşak kaşmir balıkçı yaka. Eşsiz sıcaklık ve konfor için A kalite Moğolistan kaşmirinden üretilmiştir.', 195.00, '', 20, 1),
('Dar Kesim Terzi Pantolon', 'Premium yün karışımı kumaşta hassas kesimli dar pantolon. Düz ön tasarım, yan dikiş cepler ve sade paça detayıyla tamamlanır.', 165.00, '', 18, 1),
('İpek Abiye Elbise', 'Saf ipek şarmeuzdan, yere kadar uzanan zarif bir abiye. Drapeli yaka, akıcı hareket için verev kesim etek ve ince bir yırtmaç içerir. Siyah kravat etkinlikleri için idealdir.', 450.00, '', 8, 2),
('Keten Kruvaze Elbise', '100% Avrupa keteninden zahmetsiz şık kruvaze elbise. Rahat siluet belde kuşakla toparlanır. Gündüzden geceye sorunsuz geçiş sağlar.', 225.00, '', 14, 2),
('Yün Krep Blazer', 'Lüks yün krep kumaştan yapılandırılmış blazer. Çentikli yaka, tek düğme kapanış ve biyeli ceplere sahiptir. Gardırobun temel parçalarından.', 310.00, '', 10, 2),
('Deri Çapraz Askılı Çanta', 'Tam taneli İtalyan derisinden, yumuşak eskitme patinalı el işçiliği. Üstten fermuar, iç kart bölmeleri ve ayarlanabilir omuz askısı içerir.', 175.00, '', 25, 3),
('İpek Cep Mendili', 'Zarif geometrik desenli, el rulosu ipek cep mendili. Parlak bitiş için saf dut ipeğinden üretilmiştir. Her resmi görünümü yükseltir.', 65.00, '', 40, 3),
('Süet Chelsea Bot', 'Kolay giyme için lastikli yan panelli, el dikişi süet Chelsea bot. Deri astar ve hafif yükselti için katmanlı topuk içerir.', 340.00, '', 12, 3),
('Kanvas Shopper Çanta', 'Geniş iç hacimli, çift saplı kanvas shopper. Günlük kullanım için hafif ve dayanıklı tasarım. İç cep ve mıknatıslı kapama içerir.', 120.00, '', 30, 4),
('Renkli Çocuk Hırka', 'Yumuşak pamuk karışımı iplikle örülmüş, düğmeli çocuk hırkası. Rahat kalıp ve cilt dostu doku.', 85.00, '', 22, 5),
('Oversize Hoodie', 'Ağır gramajlı pamuklu kumaştan, bol kesim hoodie. Ayarlanabilir kapüşon ve kanguru cep detayına sahip.', 160.00, '', 18, 1),
('Deri Şehir Sneaker', 'Yumuşak deri üst yüzeyli, hafif tabanlı şehir sneaker. Gün boyu konfor için nefes alabilir astar.', 210.00, '', 16, 6);
