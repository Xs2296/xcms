/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : xcms

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2018-02-06 16:48:06
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for xcms_admin
-- ----------------------------
DROP TABLE IF EXISTS `xcms_admin`;
CREATE TABLE `xcms_admin` (
  `uuid` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `salt` varchar(30) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL,
  `status` tinyint(2) DEFAULT '1' COMMENT '1正常、2禁用',
  `reg_ip` varchar(100) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  `last_login_time` int(11) DEFAULT NULL,
  `isdel` tinyint(2) DEFAULT '1' COMMENT '0删除、1正常',
  PRIMARY KEY (`uuid`),
  KEY `admin_uuid` (`uuid`) USING BTREE,
  KEY `admin_name` (`name`),
  KEY `admin_uuid_name` (`uuid`,`name`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员表';

-- ----------------------------
-- Records of xcms_admin
-- ----------------------------
INSERT INTO `xcms_admin` VALUES ('242caf5d0818c695', '123456', '123456', '41dVUprD', 'e6a6977fbb9864ffa63036d213c8da26', '1', '127.0.0.1', '1517900818', '1517905810', '1');
INSERT INTO `xcms_admin` VALUES ('d7f5f3e16823c370', 'admin', 'admin@qq.com', 'agGZx4dt', 'fb9a1ca8c74e3bec63ef5d32cc9cc077', '1', '127.0.0.1', '1514436502', '1517905533', '1');

-- ----------------------------
-- Table structure for xcms_article
-- ----------------------------
DROP TABLE IF EXISTS `xcms_article`;
CREATE TABLE `xcms_article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cates` int(11) DEFAULT '0',
  `title` varchar(225) DEFAULT NULL,
  `litpic` varchar(225) DEFAULT NULL,
  `content` mediumtext,
  `status` tinyint(2) DEFAULT '1' COMMENT '0禁用、1启用',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `isdel` tinyint(2) DEFAULT '1' COMMENT '0已删除、1正常',
  PRIMARY KEY (`id`),
  KEY `article_id` (`id`),
  KEY `article_cates` (`cates`),
  KEY `article_id_cates` (`id`,`cates`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文章模型';

-- ----------------------------
-- Records of xcms_article
-- ----------------------------

-- ----------------------------
-- Table structure for xcms_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `xcms_auth_group`;
CREATE TABLE `xcms_auth_group` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` char(100) NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `rules` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `group_id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='管理组别';

-- ----------------------------
-- Records of xcms_auth_group
-- ----------------------------
INSERT INTO `xcms_auth_group` VALUES ('1', '超级管理员', '1', '');
INSERT INTO `xcms_auth_group` VALUES ('2', '管理员', '1', '10,11,12,13,14,23,24,25,26,27,28,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,70,71,72,73,74');

-- ----------------------------
-- Table structure for xcms_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `xcms_auth_group_access`;
CREATE TABLE `xcms_auth_group_access` (
  `uuid` varchar(30) NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  KEY `access_group_id` (`group_id`) USING BTREE,
  KEY `access_uuid` (`uuid`) USING BTREE,
  KEY `access_uuid_group_id` (`group_id`,`uuid`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='授权分组表';

-- ----------------------------
-- Records of xcms_auth_group_access
-- ----------------------------
INSERT INTO `xcms_auth_group_access` VALUES ('d7f5f3e16823c370', '1');
INSERT INTO `xcms_auth_group_access` VALUES ('242caf5d0818c695', '2');

-- ----------------------------
-- Table structure for xcms_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `xcms_auth_rule`;
CREATE TABLE `xcms_auth_rule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `group` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `type` varchar(255) DEFAULT '1',
  `status` tinyint(2) DEFAULT '1' COMMENT '0不启用、1已启用',
  `condition` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `rule_id` (`id`),
  KEY `rule_group` (`group`),
  KEY `rule_name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xcms_auth_rule
-- ----------------------------
INSERT INTO `xcms_auth_rule` VALUES ('1', 'Access', 'admin/Access/index', 'index', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('2', 'Access', 'admin/Access/add', 'add', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('3', 'Access', 'admin/Access/edit', 'edit', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('4', 'Access', 'admin/Access/changeStatus', 'changeStatus', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('5', 'Access', 'admin/Access/enable', 'enable', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('6', 'Access', 'admin/Access/disable', 'disable', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('7', 'Access', 'admin/Access/delete', 'delete', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('8', 'Access', 'admin/Access/upRules', 'upRules', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('9', 'Access', 'admin/Access/rules', 'rules', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('10', 'Category', 'admin/Category/index', 'index', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('11', 'Category', 'admin/Category/add', 'add', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('12', 'Category', 'admin/Category/edit', 'edit', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('13', 'Category', 'admin/Category/changeStatus', 'changeStatus', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('14', 'Category', 'admin/Category/delete', 'delete', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('15', 'Config', 'admin/Config/index', 'index', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('16', 'Config', 'admin/Config/add', 'add', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('17', 'Config', 'admin/Config/edit', 'edit', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('18', 'Config', 'admin/Config/changeStatus', 'changeStatus', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('19', 'Config', 'admin/Config/changeSort', 'changeSort', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('20', 'Config', 'admin/Config/enable', 'enable', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('21', 'Config', 'admin/Config/disable', 'disable', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('22', 'Config', 'admin/Config/delete', 'delete', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('23', 'Database', 'admin/Database/index', 'index', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('24', 'Database', 'admin/Database/export', 'export', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('25', 'Database', 'admin/Database/import', 'import', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('26', 'Database', 'admin/Database/optimize', 'optimize', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('27', 'Database', 'admin/Database/repair', 'repair', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('28', 'Database', 'admin/Database/delete', 'delete', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('29', 'Fields', 'admin/Fields/index', 'index', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('30', 'Fields', 'admin/Fields/add', 'add', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('31', 'Fields', 'admin/Fields/edit', 'edit', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('32', 'Fields', 'admin/Fields/changeStatus', 'changeStatus', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('33', 'Fields', 'admin/Fields/changeSort', 'changeSort', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('34', 'Fields', 'admin/Fields/enable', 'enable', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('35', 'Fields', 'admin/Fields/disable', 'disable', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('36', 'Fields', 'admin/Fields/delete', 'delete', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('37', 'Hooks', 'admin/Hooks/index', 'index', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('38', 'Hooks', 'admin/Hooks/add', 'add', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('39', 'Hooks', 'admin/Hooks/edit', 'edit', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('40', 'Hooks', 'admin/Hooks/changeStatus', 'changeStatus', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('41', 'Hooks', 'admin/Hooks/changeSort', 'changeSort', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('42', 'Hooks', 'admin/Hooks/enable', 'enable', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('43', 'Hooks', 'admin/Hooks/disable', 'disable', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('44', 'Hooks', 'admin/Hooks/delete', 'delete', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('45', 'Index', 'admin/Index/index', 'index', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('46', 'Index', 'admin/Index/clearCache', 'clearCache', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('47', 'Index', 'admin/Index/lock', 'lock', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('48', 'Index', 'admin/Index/unlock', 'unlock', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('49', 'Login', 'admin/Login/index', 'index', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('50', 'Login', 'admin/Login/logout', 'logout', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('51', 'Module', 'admin/Module/index', 'index', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('52', 'Module', 'admin/Module/add', 'add', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('53', 'Module', 'admin/Module/edit', 'edit', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('54', 'Module', 'admin/Module/changeStatus', 'changeStatus', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('55', 'Module', 'admin/Module/enable', 'enable', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('56', 'Module', 'admin/Module/disable', 'disable', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('57', 'Module', 'admin/Module/delete', 'delete', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('58', 'System', 'admin/System/index', 'index', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('59', 'Upload', 'admin/Upload/pic', 'pic', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('60', 'Upload', 'admin/Upload/waterMark', 'waterMark', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('61', 'Upload', 'admin/Upload/makeThumb', 'makeThumb', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('62', 'Upload', 'admin/Upload/remove', 'remove', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('63', 'User', 'admin/User/index', 'index', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('64', 'User', 'admin/User/add', 'add', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('65', 'User', 'admin/User/edit', 'edit', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('66', 'User', 'admin/User/changeStatus', 'changeStatus', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('67', 'User', 'admin/User/enable', 'enable', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('68', 'User', 'admin/User/disable', 'disable', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('69', 'User', 'admin/User/delete', 'delete', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('70', 'Xcms', 'admin/Xcms/index', 'index', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('71', 'Xcms', 'admin/Xcms/add', 'add', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('72', 'Xcms', 'admin/Xcms/edit', 'edit', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('73', 'Xcms', 'admin/Xcms/changeStatus', 'changeStatus', '1', '1', null);
INSERT INTO `xcms_auth_rule` VALUES ('74', 'Xcms', 'admin/Xcms/delete', 'delete', '1', '1', null);

-- ----------------------------
-- Table structure for xcms_category
-- ----------------------------
DROP TABLE IF EXISTS `xcms_category`;
CREATE TABLE `xcms_category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '上级id',
  `moduleid` int(11) DEFAULT '0' COMMENT '所属模型id',
  `catname` varchar(255) DEFAULT NULL COMMENT '栏目名称',
  `catdir` varchar(255) DEFAULT NULL COMMENT '栏目目录',
  `seo_title` varchar(255) DEFAULT NULL COMMENT 'seo标题',
  `seo_key` varchar(255) DEFAULT NULL COMMENT 'seo关键词',
  `seo_desc` varchar(255) DEFAULT NULL COMMENT 'seo描述',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `status` tinyint(2) DEFAULT '1' COMMENT '0禁用、1启用',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `isdel` tinyint(2) DEFAULT '1' COMMENT '0已删除、1正常',
  PRIMARY KEY (`id`),
  KEY `category_id` (`id`),
  KEY `category_pid` (`pid`),
  KEY `category_catname` (`catname`),
  KEY `category_catdir` (`catdir`),
  KEY `category_moduleid` (`moduleid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='网站栏目分类表';

-- ----------------------------
-- Records of xcms_category
-- ----------------------------
INSERT INTO `xcms_category` VALUES ('1', '0', '1', '新闻', 'news', '', '', '', '0', '1', '1517902918', '1517902918', '1');
INSERT INTO `xcms_category` VALUES ('2', '0', '1', '产品', '', '', '', '', '0', '1', '1517902930', '1517902930', '1');
INSERT INTO `xcms_category` VALUES ('3', '1', '1', '国内', 'guonei', '', '', '', '0', '1', '1517902944', '1517902944', '1');
INSERT INTO `xcms_category` VALUES ('4', '1', '1', '国际', 'guoji', '', '', '', '0', '1', '1517902956', '1517902956', '1');

-- ----------------------------
-- Table structure for xcms_config
-- ----------------------------
DROP TABLE IF EXISTS `xcms_config`;
CREATE TABLE `xcms_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) DEFAULT NULL COMMENT '标题',
  `name` varchar(50) DEFAULT NULL COMMENT '名称',
  `group` varchar(32) DEFAULT NULL COMMENT '配置分组',
  `type` varchar(32) DEFAULT NULL COMMENT '类型',
  `value` text COMMENT '值',
  `options` text COMMENT '选项',
  `tips` varchar(255) DEFAULT NULL COMMENT '提示',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `status` tinyint(2) DEFAULT '1' COMMENT '0禁用、1启用',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `isdel` tinyint(2) DEFAULT '1' COMMENT '0已删除、1正常',
  PRIMARY KEY (`id`),
  KEY `config_id` (`id`),
  KEY `config_name` (`name`),
  KEY `config_group` (`group`),
  KEY `config_id_name_group` (`id`,`name`,`group`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xcms_config
-- ----------------------------
INSERT INTO `xcms_config` VALUES ('1', '配置分组', 'groups', 'system', 'textarea', 'system:系统\r\nbase:基本\r\nupload:上传\r\ndatabase:数据库\r\n', '', '', '7', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('2', '显示页面Trace', 'trace', 'system', 'switch', '0', '', '', '1', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('3', '配置类型', 'items', 'system', 'textarea', 'text:单行文本\r\nhidden:隐藏框\r\ntextarea:多行文本\r\npassword:密码\r\ncheckbox:复选框\r\nradio:单选按钮\r\ndate:日期\r\nswitch:开关\r\nselect:下拉框\r\nselects:多选下拉框\r\nimage:单张图片\r\nimages:多张图片\r\nfile:单个文件\r\nfiles:多个文件\r\ntags:标签\r\nueditor:编辑器', '', '', '8', '1', '1515045261', '1515749851', '1');
INSERT INTO `xcms_config` VALUES ('4', '分页数量', 'pagesize', 'system', 'text', '15', '', '分页数', '4', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('5', '后台配色方案', 'themes', 'system', 'radio', 'amethyst', 'default:Default\r\namethyst:Amethyst\r\ncity:City\r\nflat:Flat\r\nmodern:Modern\r\nsmooth:Smooth', '', '6', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('6', '清除缓存类型', 'cache', 'system', 'checkbox', 'TEMP_PATH,LOG_PATH,CACHE_PATH', 'TEMP_PATH:应用缓存\r\nLOG_PATH:应用日志\r\nCACHE_PATH:项目模板缓存', '清除缓存的类型', '5', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('7', '后台验证码开关', 'captcha', 'system', 'switch', '0', '', '后台登录验证码', '3', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('8', '开发模式', 'develop', 'system', 'switch', '0', '', '', '2', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('9', '站点开关', 'site_status', 'base', 'switch', '0', '', '站点关闭后将不能访问，后台可正常登录', '7', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('10', '站点标题', 'site_title', 'base', 'text', 'XiaoCms', '', '调用方式：<code>config(\'site_title\')</code>', '6', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('11', 'Logo', 'site_logo', 'base', 'image', '', '', '', '5', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('12', '站点描述', 'site_description', 'base', 'textarea', 'XiaoCms、PHP开发框架、后台框架', '', '网站描述，有利于搜索引擎抓取相关信息', '3', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('13', '站点关键词', 'site_keywords', 'base', 'text', 'XiaoCms、PHP开发框架、后台框架', '', '网站搜索引擎关键字', '4', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('14', '版权信息', 'site_copyright', 'base', 'text', 'Copyright © 2017 XiaoCms All rights reserved.', '', '调用方式：<code>config(\'site_description\')</code>', '2', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('15', '备案信息', 'site_icp', 'base', 'text', '', '', '调用方式：<code>config(\'site_icp\')</code>', '1', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('16', '文件上传大小限制', 'upload_file_size', 'upload', 'text', '0', '', '0为不限制大小，单位：kb', '9', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('17', '允许上传的文件后缀', 'upload_file_ext', 'upload', 'tags', 'doc,docx,xls,xlsx,ppt,pptx,pdf,wps,txt,rar,zip,gz,bz2,7z', '', '多个后缀用逗号隔开，不填写则不限制类型', '8', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('18', '图片上传大小限制', 'upload_image_size', 'upload', 'text', '0', '', '0为不限制大小，单位：kb', '11', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('19', '允许上传的图片后缀', 'upload_image_ext', 'upload', 'tags', 'gif,png,jpg', '', '多个后缀用逗号隔开，不填写则不限制类型', '10', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('20', '缩略图尺寸', 'upload_image_thumb', 'upload', 'text', '300,300', '', '不填写则不生成缩略图，如需生成 <code>300x300</code> 的缩略图，则填写 <code>300,300</code> ，请注意，逗号必须是英文逗号', '7', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('21', '缩略图裁剪类型', 'upload_image_thumb_type', 'upload', 'radio', '1', '1:等比例缩放\r\n2:缩放后填充\r\n3:居中裁剪\r\n4:左上角裁剪\r\n5:右下角裁剪\r\n6:固定尺寸缩放', '该项配置只有在启用生成缩略图时才生效', '6', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('22', '添加水印', 'upload_thumb_water', 'upload', 'switch', '0', '', '', '5', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('23', '水印图片', 'upload_thumb_water_pic', 'upload', 'image', '212', '', '只有开启水印功能才生效', '4', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('24', '水印位置', 'upload_thumb_water_position', 'upload', 'radio', '9', '1:左上角\r\n2:上居中\r\n3:右上角\r\n4:左居中\r\n5:居中\r\n6:右居中\r\n7:左下角\r\n8:下居中\r\n9:右下角', '只有开启水印功能才生效', '3', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('25', '水印透明度', 'upload_thumb_water_alpha', 'upload', 'text', '50', '', '请输入0~100之间的数字，数字越小，透明度越高', '2', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('26', '上传驱动', 'upload_driver', 'upload', 'radio', 'local', 'local:本地', '图片或文件上传驱动', '1', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('27', '数据库备份根路径', 'data_path', 'database', 'text', 'data/', '', '路径必须以 / 结尾', '4', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('28', '数据库备份卷大小', 'data_part_size', 'database', 'text', '20971520', '', '该值用于限制压缩后的分卷最大长度。单位：B；建议设置20M', '3', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('29', '数据库备份文件是否启用压缩', 'data_compress', 'database', 'radio', '1', '0:否\r\n1:是', '压缩备份文件需要PHP环境支持 <code>gzopen</code>, <code>gzwrite</code>函数', '2', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('30', '数据库备份文件压缩级别', 'data_compress_level', 'database', 'radio', '9', '1:最低\r\n4:一般\r\n9:最高', '数据库备份文件的压缩级别，该配置在开启压缩时生效', '1', '1', '1515045261', '1515123576', '1');
INSERT INTO `xcms_config` VALUES ('31', '验证规则', 'rules', 'system', 'textarea', 'number:数字\r\nemail:邮箱\r\narray:数组\r\ndate:日期\r\nalpha:字母\r\nalphaNum:字母+数字\r\nalphaDash:字母+数字+下划线+破折号\r\nchs:中文\r\nchsAlpha:中文+字母\r\nchsAlphaNum:中文+字母+数字\r\nchsDash:汉字+字母+数字+下划线+破折号\r\nactiveUrl:域名或IP\r\nurl:URL\r\nip:IP\r\n', '', '', '7', '1', '1515746777', '1515747130', '1');

-- ----------------------------
-- Table structure for xcms_fields
-- ----------------------------
DROP TABLE IF EXISTS `xcms_fields`;
CREATE TABLE `xcms_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moduleid` int(11) DEFAULT '0' COMMENT '所属模型id',
  `title` varchar(100) DEFAULT NULL COMMENT '标题',
  `name` varchar(100) DEFAULT NULL COMMENT '字段名',
  `tips` varchar(100) DEFAULT NULL COMMENT '提示说明',
  `required` tinyint(2) DEFAULT '0' COMMENT '0非必填、1必填',
  `rules` varchar(100) DEFAULT NULL,
  `type` varchar(32) DEFAULT NULL COMMENT '字段类型',
  `value` text COMMENT '默认值',
  `options` text COMMENT '选项',
  `length` varchar(50) DEFAULT NULL COMMENT '字段长度',
  `sort` int(11) DEFAULT '0' COMMENT '排序',
  `status` tinyint(2) DEFAULT '1' COMMENT '0禁用、1启用',
  `isdel` tinyint(2) DEFAULT '1' COMMENT '0已删除、1正常',
  PRIMARY KEY (`id`),
  KEY `fields_id` (`id`),
  KEY `fields_moduleid` (`moduleid`),
  KEY `fields_id_moduleid` (`id`,`moduleid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of xcms_fields
-- ----------------------------
INSERT INTO `xcms_fields` VALUES ('1', '1', '栏目', 'cates', '', '1', 'number', 'select', '', '', '100', '1', '1', '1');
INSERT INTO `xcms_fields` VALUES ('2', '1', '标题', 'title', '', '1', 'chsDash', 'text', '', '', '100', '2', '1', '1');
INSERT INTO `xcms_fields` VALUES ('3', '1', '缩略图', 'litpic', '', '0', '', 'text', '', '', '100', '3', '1', '1');
INSERT INTO `xcms_fields` VALUES ('4', '1', '内容', 'content', '', '0', '', 'ueditor', '', '', '255', '4', '1', '1');

-- ----------------------------
-- Table structure for xcms_hooks
-- ----------------------------
DROP TABLE IF EXISTS `xcms_hooks`;
CREATE TABLE `xcms_hooks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL COMMENT '钩子描述',
  `name` varchar(100) DEFAULT NULL COMMENT '钩子名称',
  `plugin` varchar(255) DEFAULT NULL COMMENT '插件标识',
  `sort` int(11) DEFAULT '1',
  `status` tinyint(2) DEFAULT '1' COMMENT '0禁用、1启用',
  `create_time` int(11) DEFAULT NULL,
  `isdel` tinyint(2) DEFAULT '1' COMMENT '0已删除、1正常',
  PRIMARY KEY (`id`),
  KEY `hooks_id` (`id`) USING BTREE,
  KEY `hooks_name` (`name`),
  KEY `hooks_id_name` (`id`,`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='钩子、插件表';

-- ----------------------------
-- Records of xcms_hooks
-- ----------------------------
INSERT INTO `xcms_hooks` VALUES ('1', '系统信息', 'dev', 'System', '1', '1', '1512200532', '1');

-- ----------------------------
-- Table structure for xcms_module
-- ----------------------------
DROP TABLE IF EXISTS `xcms_module`;
CREATE TABLE `xcms_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL COMMENT '模型名称',
  `name` varchar(100) DEFAULT NULL COMMENT '表名',
  `desc` varchar(255) DEFAULT NULL,
  `type` tinyint(2) DEFAULT '1' COMMENT '1空表、2普通文章表',
  `status` tinyint(2) DEFAULT '1' COMMENT '0禁用、1启用',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `isdel` tinyint(2) DEFAULT '1' COMMENT '0已删除、1正常',
  PRIMARY KEY (`id`),
  KEY `module_id` (`id`),
  KEY `module_name` (`name`),
  KEY `module_id_name` (`id`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='模型管理表';

-- ----------------------------
-- Records of xcms_module
-- ----------------------------
INSERT INTO `xcms_module` VALUES ('1', '文章模型', 'article', '', '2', '1', '1517902889', '1517902889', '1');

-- ----------------------------
-- Table structure for xcms_pictures
-- ----------------------------
DROP TABLE IF EXISTS `xcms_pictures`;
CREATE TABLE `xcms_pictures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(255) DEFAULT NULL COMMENT '图片路径',
  `name` varchar(255) DEFAULT NULL COMMENT '文件名称',
  `size` int(11) DEFAULT '0' COMMENT '文件大小',
  `ext` varchar(100) DEFAULT NULL COMMENT '文件格式',
  `md5` varchar(255) DEFAULT NULL COMMENT '文件md5',
  `sha1` varchar(255) DEFAULT NULL COMMENT '文件sha1',
  `thumb` varchar(255) DEFAULT NULL COMMENT '缩略图',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `pictures_id` (`id`),
  KEY `pictures_path` (`path`),
  KEY `pictures_id_path` (`id`,`path`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='图片上传';

-- ----------------------------
-- Records of xcms_pictures
-- ----------------------------
