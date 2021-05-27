-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2021-05-27 12:11:59
-- 服务器版本： 5.7.34-log
-- PHP 版本： 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `aidafo_com`
--

-- --------------------------------------------------------

--
-- 表的结构 `article`
--

CREATE TABLE `article` (
  `article_id` int(10) UNSIGNED NOT NULL,
  `article_keys_id` int(10) DEFAULT '0',
  `article_site_id` int(10) NOT NULL DEFAULT '0',
  `article_url` varchar(255) CHARACTER SET utf8 DEFAULT NULL COMMENT '原站URL',
  `article_type_id` int(10) DEFAULT '0',
  `article_title` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `article_des` varchar(1000) CHARACTER SET utf8 DEFAULT NULL,
  `article_tags` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `article_body` mediumtext CHARACTER SET utf8,
  `article_img` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `article_views` int(10) UNSIGNED DEFAULT '0',
  `article_status` tinyint(1) DEFAULT '0',
  `article_times` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `article_body`
--

CREATE TABLE `article_body` (
  `body_id` int(11) NOT NULL,
  `body_article_id` int(11) NOT NULL,
  `body_content` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `dlog`
--

CREATE TABLE `dlog` (
  `dlog_id` int(10) UNSIGNED NOT NULL,
  `dlog_domain` varchar(255) DEFAULT NULL,
  `dlog_success` int(10) UNSIGNED DEFAULT '0',
  `dlog_error` int(10) UNSIGNED DEFAULT '0',
  `dlog_status` tinyint(1) UNSIGNED DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `domain_not`
--

CREATE TABLE `domain_not` (
  `dnot_id` int(10) UNSIGNED NOT NULL,
  `dont_host` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `keys_list`
--

CREATE TABLE `keys_list` (
  `keys_id` int(10) UNSIGNED NOT NULL,
  `keys_site_id` int(10) NOT NULL DEFAULT '0',
  `keys_name` varchar(255) DEFAULT NULL,
  `keys_top_id` int(10) UNSIGNED DEFAULT '0' COMMENT '上级关键词ID',
  `keys_type_id` int(10) DEFAULT '0' COMMENT '当前项目分类ID',
  `keys_length` tinyint(2) DEFAULT '0' COMMENT '关键词长度',
  `keys_type` tinyint(1) DEFAULT '1' COMMENT '采集来源 1：下拉 2：搜索结果下面',
  `keys_last_times` int(10) UNSIGNED DEFAULT '0' COMMENT '最后一次采集时间',
  `keys_search_times` int(10) DEFAULT '0' COMMENT '最后一次搜索时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `keys_type`
--

CREATE TABLE `keys_type` (
  `keyst_id` int(10) UNSIGNED NOT NULL,
  `keyst_site_id` int(10) NOT NULL DEFAULT '0',
  `keyst_title` varchar(255) DEFAULT NULL COMMENT '关键词分类名称 非关键词',
  `keyst_list_required` longtext COMMENT '必须包含的关键词 一行一个',
  `keyst_list_filter` longtext COMMENT '不能包含的关键词 一行一个',
  `keyst_collection_times` int(10) NOT NULL DEFAULT '0',
  `keyst_status` tinyint(2) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `site`
--

CREATE TABLE `site` (
  `site_id` int(10) UNSIGNED NOT NULL,
  `site_name` varchar(255) DEFAULT NULL COMMENT '站点名称',
  `site_title` varchar(255) DEFAULT NULL COMMENT '站点标题',
  `site_des` varchar(255) DEFAULT NULL COMMENT '站点描述',
  `site_keys` varchar(255) DEFAULT NULL COMMENT '站点关键词',
  `site_template` varchar(255) DEFAULT NULL COMMENT '站点模板',
  `site_domain` varchar(255) DEFAULT NULL COMMENT '站点域名',
  `site_status` tinyint(255) UNSIGNED DEFAULT '1' COMMENT '状态',
  `site_task_keys_times` int(10) NOT NULL DEFAULT '0' COMMENT '关键词最后一次采集',
  `site_task_search_times` int(10) NOT NULL DEFAULT '0' COMMENT '搜索引擎最后一次采集',
  `site_task_article_times` int(10) NOT NULL DEFAULT '0' COMMENT '文章最后一次采集'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- 表的结构 `spider`
--

CREATE TABLE `spider` (
  `spider_id` int(10) NOT NULL,
  `spider_site_id` int(10) NOT NULL,
  `spider_name` char(20) NOT NULL,
  `spider_url` char(255) NOT NULL,
  `spider_times` int(10) NOT NULL,
  `spider_ip` char(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `url_search`
--

CREATE TABLE `url_search` (
  `surl_id` int(10) UNSIGNED NOT NULL,
  `surl_site_id` int(10) NOT NULL DEFAULT '0',
  `surl_keys_id` int(10) DEFAULT '0',
  `surl_url` varchar(1000) DEFAULT NULL,
  `surl_create_times` int(10) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- 转储表的索引
--

--
-- 表的索引 `article`
--
ALTER TABLE `article`
  ADD PRIMARY KEY (`article_id`) USING BTREE,
  ADD UNIQUE KEY `article_url` (`article_url`),
  ADD UNIQUE KEY `article_title` (`article_title`),
  ADD KEY `article_keys_id` (`article_keys_id`),
  ADD KEY `article_type_id` (`article_type_id`),
  ADD KEY `article_des` (`article_des`),
  ADD KEY `article_tags` (`article_tags`),
  ADD KEY `article_img` (`article_img`),
  ADD KEY `article_views` (`article_views`),
  ADD KEY `article_status` (`article_status`),
  ADD KEY `article_times` (`article_times`),
  ADD KEY `article_site_id` (`article_site_id`),
  ADD KEY `article_site_id_2` (`article_site_id`,`article_status`);

--
-- 表的索引 `article_body`
--
ALTER TABLE `article_body`
  ADD UNIQUE KEY `body_article_id` (`body_article_id`),
  ADD UNIQUE KEY `body_id` (`body_id`);

--
-- 表的索引 `dlog`
--
ALTER TABLE `dlog`
  ADD PRIMARY KEY (`dlog_id`) USING BTREE,
  ADD KEY `dlog_domain` (`dlog_domain`),
  ADD KEY `dlog_success` (`dlog_success`),
  ADD KEY `dlog_error` (`dlog_error`),
  ADD KEY `dlog_status` (`dlog_status`);

--
-- 表的索引 `domain_not`
--
ALTER TABLE `domain_not`
  ADD PRIMARY KEY (`dnot_id`) USING BTREE,
  ADD KEY `dont_host` (`dont_host`);

--
-- 表的索引 `keys_list`
--
ALTER TABLE `keys_list`
  ADD PRIMARY KEY (`keys_id`) USING BTREE,
  ADD UNIQUE KEY `keys_id` (`keys_id`) USING BTREE,
  ADD UNIQUE KEY `keys_name` (`keys_name`) USING BTREE,
  ADD KEY `keys_top_id` (`keys_top_id`) USING BTREE,
  ADD KEY `keys_type_id` (`keys_type_id`) USING BTREE,
  ADD KEY `keys_length` (`keys_length`) USING BTREE,
  ADD KEY `keys_last_times` (`keys_last_times`) USING BTREE,
  ADD KEY `keys_type` (`keys_type`),
  ADD KEY `keys_site_id` (`keys_site_id`);

--
-- 表的索引 `keys_type`
--
ALTER TABLE `keys_type`
  ADD PRIMARY KEY (`keyst_id`) USING BTREE,
  ADD KEY `keyst_title` (`keyst_title`),
  ADD KEY `keys_site_id` (`keyst_site_id`),
  ADD KEY `keyst_status` (`keyst_status`),
  ADD KEY `keyst_collection_times` (`keyst_collection_times`);

--
-- 表的索引 `site`
--
ALTER TABLE `site`
  ADD PRIMARY KEY (`site_id`) USING BTREE,
  ADD UNIQUE KEY `site_domain` (`site_domain`),
  ADD KEY `site_name` (`site_name`),
  ADD KEY `site_title` (`site_title`),
  ADD KEY `site_status` (`site_status`),
  ADD KEY `site_task_keys_times` (`site_task_keys_times`),
  ADD KEY `site_task_search_times` (`site_task_search_times`),
  ADD KEY `site_task_article_times` (`site_task_article_times`);

--
-- 表的索引 `spider`
--
ALTER TABLE `spider`
  ADD UNIQUE KEY `spider_id` (`spider_id`),
  ADD KEY `spider_site_id` (`spider_site_id`),
  ADD KEY `spider_name` (`spider_name`),
  ADD KEY `spider_times` (`spider_times`);

--
-- 表的索引 `url_search`
--
ALTER TABLE `url_search`
  ADD PRIMARY KEY (`surl_id`) USING BTREE,
  ADD UNIQUE KEY `surl_url` (`surl_url`),
  ADD KEY `surl_keys_id` (`surl_keys_id`),
  ADD KEY `surl_create_times` (`surl_create_times`),
  ADD KEY `surl_site_id` (`surl_site_id`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `article`
--
ALTER TABLE `article`
  MODIFY `article_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `article_body`
--
ALTER TABLE `article_body`
  MODIFY `body_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `dlog`
--
ALTER TABLE `dlog`
  MODIFY `dlog_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `domain_not`
--
ALTER TABLE `domain_not`
  MODIFY `dnot_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `keys_list`
--
ALTER TABLE `keys_list`
  MODIFY `keys_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `keys_type`
--
ALTER TABLE `keys_type`
  MODIFY `keyst_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `site`
--
ALTER TABLE `site`
  MODIFY `site_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `spider`
--
ALTER TABLE `spider`
  MODIFY `spider_id` int(10) NOT NULL AUTO_INCREMENT;

--
-- 使用表AUTO_INCREMENT `url_search`
--
ALTER TABLE `url_search`
  MODIFY `surl_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
