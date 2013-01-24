--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `pp_trx_id` varchar(30) DEFAULT NULL,
  `amount` float DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `account_id` int(11) DEFAULT NULL,
  `trx_timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `email_address` varchar(255) DEFAULT NULL,
  `balance` float DEFAULT '0',
  `admin` tinyint(1) DEFAULT '0',
  `active` tinyint(1) DEFAULT '1',
  UNIQUE KEY `id` (`id`)
);