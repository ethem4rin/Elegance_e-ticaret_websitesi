-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 10 May 2026, 22:07:30
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `eticaretdb`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `categories`
--

INSERT INTO `categories` (`id`, `category_name`) VALUES
(1, 'Erkek'),
(2, 'Kadın'),
(3, 'Aksesuarlar'),
(4, 'Çanta'),
(5, 'Çocuk'),
(6, 'Ayakkabı');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('Pending','Shipped','Delivered') DEFAULT 'Pending',
  `shipping_name` varchar(150) DEFAULT NULL,
  `shipping_email` varchar(150) DEFAULT NULL,
  `shipping_address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_at_purchase` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(500) DEFAULT '',
  `stock_quantity` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `stock_quantity`, `category_id`, `created_at`) VALUES
(1, 'Merino Yün Blazer', '100% merino yününden üretilmiş, özenle dikilmiş bir blazer. Hem resmi hem de şık-günlük kullanımlar için idealdir. Yapılandırılmış omuzlara, dar kesime ve iki düğmeli kapanışa sahiptir.', 289.00, '', 15, 1, '2026-05-10 19:36:43'),
(2, 'Kaşmir Balıkçı Yaka', 'Rahat kalıpta, son derece yumuşak kaşmir balıkçı yaka. Eşsiz sıcaklık ve konfor için A kalite Moğolistan kaşmirinden üretilmiştir.', 195.00, 'https://i.pinimg.com/1200x/c8/fe/ce/c8fece064b9495cd5b6da9766b7fd5b7.jpg', 20, 1, '2026-05-10 19:36:43'),
(3, 'Dar Kesim Terzi Pantolon', 'Premium yün karışımı kumaşta hassas kesimli dar pantolon. Düz ön tasarım, yan dikiş cepler ve sade paça detayıyla tamamlanır.', 165.00, '', 18, 1, '2026-05-10 19:36:43'),
(4, 'İpek Abiye Elbise', 'Saf ipek şarmeuzdan, yere kadar uzanan zarif bir abiye. Drapeli yaka, akıcı hareket için verev kesim etek ve ince bir yırtmaç içerir. Siyah kravat etkinlikleri için idealdir.', 450.00, '', 8, 2, '2026-05-10 19:36:43'),
(5, 'Keten Kruvaze Elbise', '100% Avrupa keteninden zahmetsiz şık kruvaze elbise. Rahat siluet belde kuşakla toparlanır. Gündüzden geceye sorunsuz geçiş sağlar.', 225.00, 'https://image.hm.com/assets/hm/1f/81/1f8199b1937a7ab82d48cc527f270169bb52afd3.jpg?imwidth=2160', 14, 2, '2026-05-10 19:36:43'),
(6, 'Yün Krep Blazer', 'Lüks yün krep kumaştan yapılandırılmış blazer. Çentikli yaka, tek düğme kapanış ve biyeli ceplere sahiptir. Gardırobun temel parçalarından.', 310.00, '', 10, 2, '2026-05-10 19:36:43'),
(7, 'Deri Çapraz Askılı Çanta', 'Tam taneli İtalyan derisinden, yumuşak eskitme patinalı el işçiliği. Üstten fermuar, iç kart bölmeleri ve ayarlanabilir omuz askısı içerir.', 175.00, 'https://www.getchostore.com/cdn/shop/files/nasbag-kadin-capraz-askili-canta-ns628-63835_fdc338ab-0042-469c-bccf-72ec96dafc47.jpg?v=1772009206&width=1000', 25, 4, '2026-05-10 19:36:43'),
(8, 'İpek Cep Mendili', 'Zarif geometrik desenli, el rulosu ipek cep mendili. Parlak bitiş için saf dut ipeğinden üretilmiştir. Her resmi görünümü yükseltir.', 65.00, 'https://static.ticimax.cloud/cdn-cgi/image/width=0,quality=0/6811/uploads/urunresimleri/buyuk/coolmenclubipek-cep-mendili--3773.jpg', 40, 3, '2026-05-10 19:36:43'),
(9, 'Süet Chelsea Bot', 'Kolay giyme için lastikli yan panelli, el dikişi süet Chelsea bot. Deri astar ve hafif yükselti için katmanlı topuk içerir.', 340.00, 'https://static.ticimax.cloud/cdn-cgi/image/width=1865,quality=85/33164/uploads/urunresimleri/buyuk/2218c502-f405-4808-a59c-fe4fe6006bcf-2544ad.jpg', 12, 6, '2026-05-10 19:36:43'),
(10, 'Kanvas Shopper Çanta', 'Geniş iç hacimli, çift saplı kanvas shopper. Günlük kullanım için hafif ve dayanıklı tasarım. İç cep ve mıknatıslı kapama içerir.', 120.00, 'https://image.hm.com/assets/hm/dc/c3/dcc32cc792c2ecd78cac9687c27375249e7ef009.jpg?imwidth=2160', 30, 4, '2026-05-10 19:36:43'),
(11, 'Renkli Çocuk Hırka', 'Yumuşak pamuk karışımı iplikle örülmüş, düğmeli çocuk hırkası. Rahat kalıp ve cilt dostu doku.', 85.00, 'https://img01.ztat.net/article/spp-media-p1/2a00e55ac65c4bebb58b8f564ddaaf0a/6a93b5de6a38405e959b4d7e15b926b3.jpg?imwidth=762', 22, 5, '2026-05-10 19:36:43'),
(12, 'Oversize Hoodie', 'Ağır gramajlı pamuklu kumaştan, bol kesim hoodie. Ayarlanabilir kapüşon ve kanguru cep detayına sahip.', 160.00, 'https://cdn.dsmcdn.com/mnresize/420/620/ty1770/prod/QC_PREP/20251013/16/6e9951be-d068-3cb3-957b-0b52a7c10f19/1_org_zoom.jpg', 18, 1, '2026-05-10 19:36:43'),
(13, 'Deri Şehir Sneaker', 'Yumuşak deri üst yüzeyli, hafif tabanlı şehir sneaker. Gün boyu konfor için nefes alabilir astar.', 210.00, 'https://productimages.hepsiburada.net/s/777/960-1280/110001166505604.jpg', 16, 6, '2026-05-10 19:36:43'),
(14, 'Klasik Hakiki Deri Kemer', 'İtalyan dana derisinden el işçiliğiyle üretilmiş, mat gümüş tokalı klasik tasarım. Kumaş ve keten pantolonlarla kusursuz bir uyum sağlar.', 100.00, 'https://cdn.myikas.com/images/7192db85-6857-4822-b9e8-3e7dcc6d6371/9931582b-daee-4de6-8322-4247ebfafba4/3840/bd02393-1.webp', 15, 3, '2026-05-10 19:47:09'),
(15, 'Minimalist Deri Kartlık Cüzdan', 'İnce yapısı sayesinde cepte kesinlikle iz yapmayan, 6 kart kapasiteli ve RFID (temassız hırsızlık) korumalı %100 hakiki deri cüzdan.', 50.00, 'https://difami.com/wp-content/uploads/2024/06/Minimal-Deri-Cuzdan-Kartlik-Siyah.png', 25, 3, '2026-05-10 19:47:41'),
(16, 'Yün Keçe Fedora Şapka', '%100 yün keçeden geleneksel yöntemlerle kalıplanmış, grogren kurdele detaylı ikonik fedora şapka. Tarzınıza anında sofistike bir hava katar.', 75.00, 'https://static.ticimax.cloud/cdn-cgi/image/width=-,quality=85/32532/uploads/urunresimleri/buyuk/kulah-indiana-jones-erkek-yun-kece-fot-5-4db1.jpg', 10, 3, '2026-05-10 19:48:06'),
(17, 'Kapitone Deri Omuz Çantası', 'Yumuşak kuzu derisinden el yapımı kapitone işçiliği ve altın rengi zincir askısıyla gece ve gündüz şıklığınıza eşlik eder.', 1500.00, 'https://static.ticimax.cloud/cdn-cgi/image/width=-,quality=85/72015/uploads/urunresimleri/buyuk/azaliya-siyah-kadin-kapitone-deri-omuz-241-80.jpg', 10, 4, '2026-05-10 19:48:42'),
(18, 'Hasır Detaylı Yazlık Bez Çanta', 'Doğal hasır tabanlı, geniş iç hacimli dayanıklı kanvas çanta. Tatil kombinlerinin ve sahil şıklığının vazgeçilmezi.', 800.00, 'https://cdn.dsmcdn.com/mnresize/420/620/ty1800/prod/QC_ENRICHMENT/20251222/05/5d3cdd66-172c-3baf-8083-d43197650789/1_org_zoom.jpg', 15, 4, '2026-05-10 19:49:19'),
(19, 'Örgü Detaylı İnce Deri Kemer', 'El örgüsü hakiki deri ve zarif gold toka. Elbiselerin beline vurgu yapmak veya yüksek bel pantolonlarla kullanmak için tasarlanmıştır.', 300.00, 'https://farktorcdn.com/Library/Upl/5500191/Product/VTK24-119-44-3-1.jpg', 10, 3, '2026-05-10 19:49:45'),
(20, 'Mat Tokalı Süet Kemer', 'Kadifemsi dokuya sahip premium süet deri ve modern mat siyah metal toka. Chino pantolonlar ve casual giyim için mükemmel bir detay.', 300.00, 'https://manukacdn.com/product/cache/1200x1800_-69771-12-B.jpg', 10, 3, '2026-05-10 19:50:13'),
(21, 'Beyaz Deri Sneaker', 'Üzerinde hiçbir logo veya abartılı desen barındırmayan, tamamen beyaz napa derisinden üretilmiş sade sneaker. Kumaş pantolonların ve ceketlerin altına bile giyilebilecek kadar şık ve rahattır.', 700.00, 'https://static.ticimax.cloud/cdn-cgi/image/width=-,quality=99,format=webp/41550/uploads/urunresimleri/buyuk/100-hakiki-deri-beyaz-kadin-ayakkabi-4f-4d5.jpg', 15, 6, '2026-05-10 19:50:40'),
(22, 'Klasik Deri Oxford Ayakkabı', 'El işçiliği ile şekillendirilmiş, kapalı bağcık sistemine sahip %100 parlak dana derisi Oxford. Resmi iş toplantılarının ve takım elbiselerin tartışmasız en asil tamamlayıcısı.', 2500.00, 'https://tr.wessi.com/cdn/shop/files/hakiki-deri-erkek-oxford-ayakkabi-822ma051-205950.jpg?v=1742821833&width=1800', 5, 6, '2026-05-10 19:51:00'),
(23, 'Süet Topuklu Diz Altı Çizme', 'Birinci sınıf İtalyan süetinden esnek yapılı, blok topuklu rahat çizme. Triko elbiseler ve eteklerle kış aylarında sıcak, iddialı ve feminen bir görünüm sunar.', 3000.00, 'https://static.ticimax.cloud/cdn-cgi/image/width=-,quality=85/12157/uploads/urunresimleri/buyuk/kadin-dizalti-strec-topuklu-cizme-siya-579650.jpg', 5, 6, '2026-05-10 19:51:22'),
(24, 'Kalın Tabanlı Klasik Deri Loafer', 'Maskülen tarzın feminen dokunuşla buluştuğu, tırtıklı kalın tabanlı hakiki deri loafer. Sokak modasının en trend, en rahat ve ikonik parçası.', 3500.00, 'https://static.ticimax.cloud/cdn-cgi/image/width=-,quality=85/12079/uploads/urunresimleri/buyuk/cg-2856-erkek-ayakkabi-kahve-celal-gul--4fb5-.jpg', 10, 6, '2026-05-10 19:51:48'),
(25, 'Süet Western Kovboy Çizmesi', 'Taba rengi premium süet üzeri ton sür ton nakış işlemeli. Hem bohem yazlık elbiselerle hem de kışın skinny jeanlerle kullanabileceğiniz dört mevsimlik bir stil ikonu.', 3000.00, 'https://derimod.com.tr/cdn/shop/files/25AFD152910_5637145360_1_2048x2048.jpg?v=1771880875', 10, 6, '2026-05-10 19:52:11'),
(26, 'Küt Burunlu Streç Deri Bot', 'Ayak bileğini bir çorap gibi saran yumuşak streç deri yapısı ve modern küt burun tasarımı. Kısa paça pantolonlar ve midi etekler için en ideal kışlık parça.', 2750.00, 'https://cdn.mos.cms.futurecdn.net/whowhatwear/posts/290329/hm-premium-quality-items-290329-1606152172899-main-768-80.jpg.webp', 15, 6, '2026-05-10 19:52:42'),
(27, 'Saten Midi Slip Elbise', 'Vücudu nazikçe saran saf ipek saten kumaş, ince ip askılar ve zarif inek yaka detayı. Hem stiletto\'larla geceye hem de blazer ceketlerle gündüze uyarlanabilen zamansız bir parça.', 850.00, 'https://i.pinimg.com/736x/8a/1c/62/8a1c62e206104bd8a120d6ac39a06ae6.jpg', 20, 2, '2026-05-10 19:53:16'),
(28, 'Triko Midi Kalem Elbise', 'Esnek ve toparlayıcı premium triko kumaş. Uzun kollu, kare yaka ve yırtmaç detayıyla kış aylarında çizmelerle mükemmel bir uyum yakalar.', 650.00, '', 20, 2, '2026-05-10 19:53:41'),
(29, 'Çiçek Desenli Şifon Şal Yaka Elbise', 'Uçuş uçuş şifon kumaş üzerine pastel tonlarda zarif çiçek desenleri. Beli kuşaklı, şal yaka tasarımıyla ilkbahar ve yaz davetlerinin favorisi.', 800.00, '', 10, 2, '2026-05-10 19:53:57'),
(30, 'İtalyan Kesim Yün Kumaş Pantolon', 'İnce yün kumaştan, önü tek pileli ve duble paçalı klasik tasarım. Özel terzilik detaylarıyla maskülen ve güçlü bir silüet çizer.', 900.00, 'https://editorialist.com/thumbnails/300/2025/4/035/280/752/35280752~brown_1751558157323_0.webp', 15, 1, '2026-05-10 20:00:05'),
(31, 'Hakiki Süet Bomber Ceket', 'Yumuşacık, birinci sınıf taba rengi keçi süetinden el işçiliğiyle üretilmiş klasik bomber. Hem spor hem de şık tarzı tek bir parçada eritir.', 1250.00, 'https://static.ticimax.cloud/cdn-cgi/image/width=1050,quality=95,format=webp/72015/uploads/urunresimleri/buyuk/vitale-kahve-erkek-suet-deri-bomber-ce-18b0-4.jpg', 15, 1, '2026-05-10 20:00:23'),
(32, 'Kaşmir Karışımlı Uzun Kaban', 'Diz kapağına kadar uzanan, gizli düğmeli, minimalist ve jilet gibi bir kalıp. Takım elbiselerin üzerinde asil bir zırh gibi durur.', 3350.00, 'https://riseandfall.co/cdn/shop/files/rise-and-fall-navy-cashmere-merino-double-faced-wool-coat-close-up-cutout.jpg', 5, 1, '2026-05-10 20:00:58'),
(33, 'Saf Kaşmir Bisiklet Yaka Kazak', 'İnanılmaz derecede hafif ve pürüzsüz saf Moğol kaşmiri. Gömlek üzerine veya tek başına giyildiğinde cildinize lüksü hissettirir.', 1500.00, 'https://cache.hemington.com.tr/product/cache/540x810_saf-kasmir-bisiklet-yaka-camel-kazak-hemington-67596-58-B.webp?5828', 20, 1, '2026-05-10 20:01:18'),
(34, 'Drapeli Asimetrik Saten Bluz', 'Asimetrik kesim ve omuzdan dökülen drape detayları. Akşam yemekleri ve partiler için göz alıcı bir tasarım.', 500.00, 'https://image.hm.com/assets/hm/2e/06/2e060049175d858812333d39e0144f87ed95c421.jpg', 30, 2, '2026-05-10 20:02:27'),
(35, 'Yüksek Bel Pileli Kumaş Pantolon', 'Dökümlü yün karışımlı kumaş, belde çift pile detayı ve bol paça (wide-leg) kesim. Bacak boyunu maksimum uzunlukta gösterir.', 900.00, 'https://www.chicwish.com/media/catalog/product/cache/aaa138aafda2e32295dbe3b94f267e44/2/4/240625cc.504.jpg', 25, 2, '2026-05-10 20:02:49'),
(36, 'Klasik Kruvaze Gabardin Trençkot', 'Su itici özellikli tok gabardin kumaş, omuz apoletleri ve belde toka detaylı kemer. Bahar aylarının tartışmasız en ikonik dış giyim parçası.', 2850.00, 'https://image.hm.com/assets/hm/23/19/23190e35ba2f81bdb20e5dbdf18e3f90aba95332.jpg', 10, 2, '2026-05-10 20:03:10'),
(37, 'Kuşaklı Uzun Kaşe Kaban', '%80 yün, %20 kaşmir karışımlı, ayak bileğine kadar uzanan lüks kaban. Şal yaka kesimi ve kalın kuşağıyla soğuk havalarda üst düzey koruma ve zarafet.', 3250.00, 'https://b2c-media.maxmara.com/sys-master/m0/MM/2026/1/9011046106/072/s3details/9011046106072-y-smmluna_thumbnail.jpg', 5, 2, '2026-05-10 20:03:32'),
(38, 'Biyeli Saten Midi Etek', 'Vücuda yapışmadan dökülen, verev kesim parlak saten etek. Kazaklarla zıt bir doku yaratarak veya ipek bluzlarla takım halinde kullanılabilir.', 750.00, 'https://cdn.dsmcdn.com/ty1790/prod/QC_PREP/20251113/13/469e04a1-8f57-3a24-80f7-688ab34785a7/1_org_zoom.jpg', 25, 2, '2026-05-10 20:03:56'),
(39, 'Asimetrik Kesim Pileli Şifon Etek', 'Yürürken hareket eden ince ince işlenmiş kalıcı pileler ve asimetrik etek ucu kesimi. Romantik kombinlerin vazgeçilmezi.', 750.00, 'https://i.pinimg.com/736x/53/d0/f4/53d0f4535bb5cba3eacb5e41d5a7b3a8.jpg', 15, 2, '2026-05-10 20:04:26');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `address`, `role`, `created_at`) VALUES
(1, 'Yönetici Kullanıcı', 'admin@example.com', '$2y$10$f9fZGCVNC1pRCDbbZFbFMut9zeaJyj1ZvItXPokP72DGlKt3WmixK', '123 Yönetim Sokak, Şehir', 'admin', '2026-05-10 19:36:43');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_cart_user` (`user_id`),
  ADD KEY `fk_cart_product` (`product_id`);

--
-- Tablo için indeksler `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_order_user` (`user_id`);

--
-- Tablo için indeksler `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orderitem_order` (`order_id`),
  ADD KEY `fk_orderitem_product` (`product_id`);

--
-- Tablo için indeksler `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_category` (`category_id`);

--
-- Tablo için indeksler `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Tablo için AUTO_INCREMENT değeri `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_cart_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_orderitem_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_orderitem_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Tablo kısıtlamaları `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
