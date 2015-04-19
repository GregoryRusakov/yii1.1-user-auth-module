-- phpMyAdmin SQL Dump
-- version 4.2.11
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Мар 03 2015 г., 16:53
-- Версия сервера: 5.6.21
-- Версия PHP: 5.6.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `tradernews`
--

-- --------------------------------------------------------

--
-- Структура таблицы `auth_unsafeip`
--

CREATE TABLE IF NOT EXISTS `auth_unsafeip` (
  `id` int(11) NOT NULL DEFAULT '0',
  `ip_address` varchar(50) NOT NULL,
  `attempts` int(11) NOT NULL,
  `blocked_until` datetime NOT NULL,
  `comments` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `attempts_total` int(11) NOT NULL,
  `last_user_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Структура таблицы `auth_unsafeusers`
--

CREATE TABLE IF NOT EXISTS `auth_unsafeusers` (
`id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `attempts` int(11) NOT NULL,
  `blocked_until` datetime NOT NULL,
  `comments` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `attempts_total` int(11) NOT NULL,
  `last_ip` varchar(50) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Структура таблицы `auth_validations`
--

CREATE TABLE IF NOT EXISTS `auth_validations` (
`id` int(11) NOT NULL,
  `guid` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `exp_datetime` datetime NOT NULL,
  `comments` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(250) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `user_id` int(11) NOT NULL,
  `type` int(1) NOT NULL COMMENT '0 - activate new user, 1 - restore password'
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
`id` int(11) NOT NULL,
  `key` varchar(36) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `email` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `full_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `username` varchar(100) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `date_reg` datetime NOT NULL,
  `comments` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `password_hash` text CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `ip_endorsed` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `activated` tinyint(1) NOT NULL,
  `logintoken` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `date_lastlogin` datetime NOT NULL,
  `blocked` tinyint(1) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `auth_unsafeip`
--
ALTER TABLE `auth_unsafeip`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `auth_unsafeusers`
--
ALTER TABLE `auth_unsafeusers`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `auth_validations`
--
ALTER TABLE `auth_validations`
 ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
 ADD PRIMARY KEY (`id`), ADD KEY `id` (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `auth_unsafeusers`
--
ALTER TABLE `auth_unsafeusers`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT для таблицы `auth_validations`
--
ALTER TABLE `auth_validations`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=11;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
