-- phpMyAdmin SQL Dump
-- version 4.4.15.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-02-02 14:08:01
-- 服务器版本： 5.7.9-log
-- PHP Version: 7.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mg_wiki`
--

-- --------------------------------------------------------

--
-- 表的结构 `tb_catalog`
--

CREATE TABLE IF NOT EXISTS `tb_catalog` (
  `cat_id` int(10) NOT NULL COMMENT '目录id',
  `cat_name` varchar(20) NOT NULL DEFAULT '' COMMENT '目录名',
  `item_id` int(10) NOT NULL DEFAULT '0' COMMENT '所在的项目id',
  `order` int(10) NOT NULL DEFAULT '99' COMMENT '顺序号。数字越小越靠前。若此值全部相等时则按id排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态(1正常 0删除)',
  `addtime` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='目录表';

-- --------------------------------------------------------

--
-- 表的结构 `tb_item`
--

CREATE TABLE IF NOT EXISTS `tb_item` (
  `item_id` int(10) NOT NULL,
  `item_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '项目类型（0公开 1私人）',
  `item_name` varchar(50) NOT NULL DEFAULT '',
  `item_description` varchar(225) NOT NULL DEFAULT '' COMMENT '项目描述',
  `uid` int(10) NOT NULL DEFAULT '0',
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态(1正常 0删除)',
  `addtime` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='项目表';

-- --------------------------------------------------------

--
-- 表的结构 `tb_item_member`
--

CREATE TABLE IF NOT EXISTS `tb_item_member` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `addtime` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='项目成员表';

-- --------------------------------------------------------

--
-- 表的结构 `tb_item_pwder`
--

CREATE TABLE IF NOT EXISTS `tb_item_pwder` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `addtime` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='项目成员表';

-- --------------------------------------------------------

--
-- 表的结构 `tb_item_viewer`
--

CREATE TABLE IF NOT EXISTS `tb_item_viewer` (
  `uid` int(10) NOT NULL DEFAULT '0',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `addtime` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='项目成员表';

-- --------------------------------------------------------

--
-- 表的结构 `tb_page`
--

CREATE TABLE IF NOT EXISTS `tb_page` (
  `page_id` int(10) NOT NULL,
  `author_uid` int(10) NOT NULL DEFAULT '0' COMMENT '页面作者uid',
  `author_username` varchar(50) NOT NULL DEFAULT '' COMMENT '页面作者名字',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `cat_id` int(10) NOT NULL DEFAULT '0',
  `page_title` varchar(50) NOT NULL DEFAULT '',
  `page_content` text NOT NULL,
  `order` int(10) NOT NULL DEFAULT '99' COMMENT '顺序号。数字越小越靠前。若此值全部相等时则按id排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态(1正常 0删除)',
  `addtime` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章页面表';

-- --------------------------------------------------------

--
-- 表的结构 `tb_page_history`
--

CREATE TABLE IF NOT EXISTS `tb_page_history` (
  `page_history_id` int(10) NOT NULL,
  `page_id` int(10) NOT NULL DEFAULT '0',
  `author_uid` int(10) NOT NULL DEFAULT '0' COMMENT '页面作者uid',
  `author_username` varchar(50) NOT NULL DEFAULT '' COMMENT '页面作者名字',
  `item_id` int(10) NOT NULL DEFAULT '0',
  `cat_id` int(10) NOT NULL DEFAULT '0',
  `page_title` varchar(50) NOT NULL DEFAULT '',
  `page_content` text NOT NULL,
  `order` int(10) NOT NULL DEFAULT '99' COMMENT '顺序号。数字越小越靠前。若此值全部为0则按时间排序',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态(1正常 0删除)',
  `addtime` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='页面历史表';

-- --------------------------------------------------------

--
-- 表的结构 `tb_user`
--

CREATE TABLE IF NOT EXISTS `tb_user` (
  `uid` int(10) NOT NULL,
  `username` varchar(20) NOT NULL DEFAULT '',
  `groupid` tinyint(2) NOT NULL DEFAULT '2' COMMENT '1为超级管理员，2为普通用户',
  `name` varchar(15) DEFAULT '',
  `avatar` varchar(200) DEFAULT '' COMMENT '头像',
  `email` varchar(50) DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态(1正常 0删除)',
  `password` varchar(50) NOT NULL,
  `cookie_token` varchar(50) NOT NULL DEFAULT '' COMMENT '实现cookie自动登录的token凭证',
  `cookie_token_expire` int(11) NOT NULL DEFAULT '0',
  `reg_time` int(11) NOT NULL DEFAULT '0',
  `last_login_time` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='用户表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_catalog`
--
ALTER TABLE `tb_catalog`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `tb_item`
--
ALTER TABLE `tb_item`
  ADD PRIMARY KEY (`item_id`);

--
-- Indexes for table `tb_item_member`
--
ALTER TABLE `tb_item_member`
  ADD UNIQUE KEY `uq_uid_iid` (`uid`,`item_id`);

--
-- Indexes for table `tb_item_pwder`
--
ALTER TABLE `tb_item_pwder`
  ADD UNIQUE KEY `uq_uid_iid` (`uid`,`item_id`);

--
-- Indexes for table `tb_item_viewer`
--
ALTER TABLE `tb_item_viewer`
  ADD UNIQUE KEY `uq_uid_iid` (`uid`,`item_id`);

--
-- Indexes for table `tb_page`
--
ALTER TABLE `tb_page`
  ADD PRIMARY KEY (`page_id`);

--
-- Indexes for table `tb_page_history`
--
ALTER TABLE `tb_page_history`
  ADD PRIMARY KEY (`page_history_id`);

--
-- Indexes for table `tb_user`
--
ALTER TABLE `tb_user`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `username` (`username`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_catalog`
--
ALTER TABLE `tb_catalog`
  MODIFY `cat_id` int(10) NOT NULL AUTO_INCREMENT COMMENT '目录id';
--
-- AUTO_INCREMENT for table `tb_item`
--
ALTER TABLE `tb_item`
  MODIFY `item_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tb_page`
--
ALTER TABLE `tb_page`
  MODIFY `page_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tb_page_history`
--
ALTER TABLE `tb_page_history`
  MODIFY `page_history_id` int(10) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `tb_user`
--
ALTER TABLE `tb_user`
  MODIFY `uid` int(10) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
