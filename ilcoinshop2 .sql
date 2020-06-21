-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 19 Haz 2020, 20:12:36
-- Sunucu sürümü: 10.1.39-MariaDB
-- PHP Sürümü: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `ilcoinshop2`
--

DELIMITER $$
--
-- Yordamlar
--
CREATE PROCEDURE `getProducts` (IN `order_id` INT, OUT `prod` TEXT)  BEGIN
    DECLARE productList VARCHAR(50);
	DECLARE tempProducts TEXT;
    DECLARE tempParent INT;
    SET max_sp_recursion_depth = 255;
    SELECT po.orders_id, po.products_id FROM orders AS o LEFT JOIN products_has_orders AS po ON o.id=po.orders_id WHERE o.id=order_id INTO productList, tempParent;
    IF tempParent IS NULL
    THEN
    	SET prod = productList;
    ELSE
    	CALL getProducts(tempParent, tempProducts);
        SET prod = CONCAT(tempProducts, ', ', productList);
    END IF;
END$$

--
-- İşlevler
--
CREATE FUNCTION `getProducts` (`order_id` INT) RETURNS TEXT CHARSET utf8 BEGIN
	DECLARE result TEXT;
    CALL getProducts(order_id, result);
    RETURN result;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cost` decimal(17,8) NOT NULL,
  `recd` decimal(17,8) NOT NULL,
  `pay_id` int(11) NOT NULL,
  `paid` enum('yes','no') NOT NULL DEFAULT 'no',
  `difference` enum('under','above') DEFAULT NULL,
  `complete` enum('yes','no') NOT NULL DEFAULT 'no',
  `ts_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `orders`
--

INSERT INTO `orders` (`id`, `date`, `cost`, `recd`, `pay_id`, `paid`, `difference`, `complete`, `ts_create`, `ts_update`) VALUES
(3, '2020-05-29 20:49:06', '0.15000000', '292050.00200000', 1, 'yes', 'above', 'yes', '2020-05-29 20:49:06', '2020-06-11 16:59:02'),
(4, '2020-05-30 12:54:58', '32.00000000', '311.00000000', 5, 'no', NULL, 'yes', '2020-05-30 12:54:58', '2020-06-06 21:28:48'),
(420, '2020-06-19 13:08:00', '878.32597359', '25.00000000', 3, 'yes', 'under', 'no', '2020-06-19 13:08:00', '2020-06-19 13:11:29'),
(421, '2020-06-19 13:13:51', '0.43933394', '0.00010000', 2, 'yes', 'under', 'no', '2020-06-19 13:13:51', '2020-06-19 13:14:32'),
(422, '2020-06-19 13:19:40', '0.43904069', '0.43904069', 2, 'yes', 'above', 'no', '2020-06-19 13:19:40', '2020-06-19 13:20:15');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `pay`
--

CREATE TABLE `pay` (
  `id` int(11) NOT NULL,
  `address` varchar(45) NOT NULL,
  `private_key` varchar(60) DEFAULT NULL,
  `dispensed` tinyint(1) UNSIGNED DEFAULT '1',
  `ts_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ts_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `pay`
--

INSERT INTO `pay` (`id`, `address`, `private_key`, `dispensed`, `ts_create`, `ts_update`) VALUES
(91, '155tVJB6d6NEQYXgkktTbv3r9dnMmQZpcr', 'L2Y97asTN4MpJjhHCk15PCdhF2g2G1o7uJia9R2xCkyMmej1rNrp', 1, '2020-06-19 18:07:57', '2020-06-19 18:07:57'),
(92, '15pJkpmoATJDLcjgqTSHGhHF5mZvdsmo8r', 'KxNWWPUQyDvWFB6ooSFf73yymHLakaMsmAD7ErbL3KjLcRBUfHBs', 1, '2020-06-19 18:08:10', '2020-06-19 18:08:10');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(45) NOT NULL,
  `price` decimal(11,2) NOT NULL,
  `image` varchar(300) DEFAULT NULL,
  `in_stock` int(1) DEFAULT NULL,
  `description` mediumtext,
  `ts_create` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `ts_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `in_stock`, `description`, `ts_create`, `ts_update`) VALUES
(2, 'Caramel Macchiato', '4.00', 'https://globalassets.starbucks.com/assets/79bfec31ab7447f691b3c48f39cc7661.jpg?impolicy=1by1_wide_1242', 1, '', '2020-05-29 20:08:08', '2020-05-29 20:08:08'),
(3, 'Espresso', '6.00', 'https://encrypted-tbn0.gstatic.com/images?q=tbn%3AANd9GcT4jQygsvMt12bADeiwMB6RZTmsF1jzzyQ2xqVK9M81q2-n6M_i&usqp=CAU', 1, '', '2020-05-29 20:08:08', '2020-05-29 20:08:08'),
(4, 'Latte', '20.00', 'https://www.diyetkolik.com/site_media/media/nutrition_images/latte-yagsiz-sut-ile.jpg', 1, '', '2020-05-29 20:08:08', '2020-05-29 20:08:08'),
(5, 'Coffee', '2.99', 'https://www.kartal24.com/dosyalar/2014/09/kahve.jpg', 1, '', '2020-05-29 20:08:08', '2020-06-09 15:32:14'),
(6, 'Coca Cola', '5.00', 'https://pazarlamasyon.com/wp-content/uploads/2019/05/New-Coke-Stranger-Things.jpg', 1, '', '2020-05-29 20:08:08', '2020-05-29 20:08:08'),
(7, 'Burn', '0.01', 'https://reimg-carrefour.mncdn.com/mnresize/600/600/productimage/30074870/30074870_0_MC/8796776267826_1499977497942.jpg', 1, '', '2020-05-29 20:08:08', '2020-05-30 21:19:52'),
(9, 'Fahim', '10.00', 'https://s3.amazonaws.com/keybase_processed_uploads/09e5c95e4e1e5647978a67f6072f0b05_360_360.jpg', 1, 'testestest', '2020-06-06 16:57:18', '2020-06-18 17:05:45'),
(10, 'Water', '1.00', 'https://i2.wp.com/www.formsante.com.tr/wp-content/uploads/2020/01/su-pet.jpg?fit=700%2C465&ssl=1&resize=1200%2C675', 1, '', '2020-06-09 15:37:30', '2020-06-09 15:37:30'),
(11, 'Hot Chocolatte', '5.89', 'https://i.pinimg.com/originals/81/f5/81/81f581771db56054caf2475a59f0251f.jpg', 1, '', '2020-06-11 14:09:49', '2020-06-11 14:09:49');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `products_has_orders`
--

CREATE TABLE `products_has_orders` (
  `products_id` int(11) DEFAULT NULL,
  `orders_id` int(11) DEFAULT NULL,
  `amount` smallint(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Tablo döküm verisi `products_has_orders`
--

INSERT INTO `products_has_orders` (`products_id`, `orders_id`, `amount`) VALUES

(7, 420, 1),
(4, 420, 1),
(7, 421, 1),
(7, 422, 1);

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pay_id` (`pay_id`) USING BTREE;

--
-- Tablo için indeksler `pay`
--
ALTER TABLE `pay`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `products_has_orders`
--
ALTER TABLE `products_has_orders`
  ADD KEY `products_has_orders_products` (`products_id`),
  ADD KEY `products_has_orders_orders` (`orders_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=423;

--
-- Tablo için AUTO_INCREMENT değeri `pay`
--
ALTER TABLE `pay`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- Tablo için AUTO_INCREMENT değeri `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Dökümü yapılmış tablolar için kısıtlamalar
--

--
-- Tablo kısıtlamaları `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_pay` FOREIGN KEY (`pay_id`) REFERENCES `pay` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Tablo kısıtlamaları `products_has_orders`
--
ALTER TABLE `products_has_orders`
  ADD CONSTRAINT `products_has_orders_orders` FOREIGN KEY (`orders_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `products_has_orders_products` FOREIGN KEY (`products_id`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
