-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- 主機： 127.0.0.1
-- 產生時間： 2025 年 04 月 14 日 19:58
-- 伺服器版本： 10.4.32-MariaDB
-- PHP 版本： 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `seat_reservation`
--

-- --------------------------------------------------------

--
-- 資料表結構 `reservation`
--

CREATE TABLE `reservation` (
  `reid` int(11) NOT NULL,
  `usid` int(11) NOT NULL,
  `seid` int(11) NOT NULL,
  `date` date NOT NULL,
  `tsid` int(11) NOT NULL,
  `reserve_time` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `seat`
--

CREATE TABLE `seat` (
  `seid` int(11) NOT NULL,
  `location` varchar(20) NOT NULL,
  `is_socket` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `timeslot`
--

CREATE TABLE `timeslot` (
  `tsid` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `label` varchar(20) NOT NULL,
  `active` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `unavailable`
--

CREATE TABLE `unavailable` (
  `unid` int(11) NOT NULL,
  `seid` int(11) NOT NULL,
  `date` date NOT NULL,
  `tsid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `user`
--

CREATE TABLE `user` (
  `usid` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `account` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `is_admin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- 傾印資料表的資料 `user`
--

INSERT INTO `user` (`usid`, `name`, `account`, `password`, `email`, `is_admin`) VALUES
(1, '管理員', 'admin', '0000', 'M133040097@student.nsysu.edu.tw', 1),
(2, 'Rebecca', 'Rebecca', 'Rebecca', 'add172839@gmail.com', 0),
(3, 'Steven', 'Steven', 'Steven', 'ns96284@gmail.com', 0);

--
-- 已傾印資料表的索引
--

--
-- 資料表索引 `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`reid`),
  ADD UNIQUE KEY `unique_seat` (`seid`,`date`,`tsid`) USING BTREE,
  ADD UNIQUE KEY `unique_user` (`usid`,`date`,`tsid`) USING BTREE;

--
-- 資料表索引 `seat`
--
ALTER TABLE `seat`
  ADD PRIMARY KEY (`seid`),
  ADD UNIQUE KEY `location` (`location`);

--
-- 資料表索引 `timeslot`
--
ALTER TABLE `timeslot`
  ADD PRIMARY KEY (`tsid`);

--
-- 資料表索引 `unavailable`
--
ALTER TABLE `unavailable`
  ADD PRIMARY KEY (`unid`),
  ADD KEY `seid` (`seid`),
  ADD KEY `tsid` (`tsid`);

--
-- 資料表索引 `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`usid`),
  ADD UNIQUE KEY `account` (`account`);

--
-- 在傾印的資料表使用自動遞增(AUTO_INCREMENT)
--

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `reservation`
--
ALTER TABLE `reservation`
  MODIFY `reid` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `seat`
--
ALTER TABLE `seat`
  MODIFY `seid` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `timeslot`
--
ALTER TABLE `timeslot`
  MODIFY `tsid` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `unavailable`
--
ALTER TABLE `unavailable`
  MODIFY `unid` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用資料表自動遞增(AUTO_INCREMENT) `user`
--
ALTER TABLE `user`
  MODIFY `usid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- 已傾印資料表的限制式
--

--
-- 資料表的限制式 `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`seid`) REFERENCES `seat` (`seid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`tsid`) REFERENCES `timeslot` (`tsid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reservation_ibfk_3` FOREIGN KEY (`usid`) REFERENCES `user` (`usid`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 資料表的限制式 `unavailable`
--
ALTER TABLE `unavailable`
  ADD CONSTRAINT `unavailable_ibfk_1` FOREIGN KEY (`seid`) REFERENCES `seat` (`seid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `unavailable_ibfk_2` FOREIGN KEY (`tsid`) REFERENCES `timeslot` (`tsid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
