--
-- 数据库: `am`
--
CREATE DATABASE IF NOT EXISTS `am` DEFAULT CHARACTER SET utf8 COLLATE latin1_swedish_ci;
USE `am`;

-- --------------------------------------------------------

--
-- 表的结构 `am_admin`
--

CREATE TABLE IF NOT EXISTS `am_admin` (
  `name` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`name`)
) DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `am_admin`
--

INSERT INTO `am_admin` (`name`, `password`) VALUES
('admin', 'admin');

-- --------------------------------------------------------

--
-- 表的结构 `am_sign`
--

CREATE TABLE IF NOT EXISTS `am_sign` (
  `log` int(11) NOT NULL AUTO_INCREMENT,
  `id` bigint(20) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log`),
  UNIQUE KEY `id` (`id`,`ip`)
) DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `am_user`
--

CREATE TABLE IF NOT EXISTS `am_user` (
  `id` bigint(20) NOT NULL,
  `name` varchar(15) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
