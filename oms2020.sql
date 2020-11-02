/*
Navicat MySQL Data Transfer

Source Server         : 192.168.3.128
Source Server Version : 50731
Source Host           : 192.168.3.128:3306
Source Database       : oms2020

Target Server Type    : MYSQL
Target Server Version : 50731
File Encoding         : 65001

Date: 2020-10-04 23:41:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for fa_admin
-- ----------------------------
DROP TABLE IF EXISTS `fa_admin`;
CREATE TABLE `fa_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `nickname` varchar(45) DEFAULT '' COMMENT '昵称',
  `salt` varchar(32) DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) DEFAULT '' COMMENT '头像',
  `email` varchar(60) DEFAULT '' COMMENT '电子邮箱',
  `loginfailure` tinyint(1) DEFAULT '0' COMMENT '失败次数',
  `logintime` int(11) DEFAULT NULL COMMENT '登录时间',
  `loginip` char(15) DEFAULT '' COMMENT '登录IP',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(11) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(60) DEFAULT '' COMMENT 'Session标识',
  `status` char(10) NOT NULL DEFAULT 'normal' COMMENT '状态',
  `logincount` int(11) DEFAULT '0' COMMENT '登录次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';

-- ----------------------------
-- Records of fa_admin
-- ----------------------------
INSERT INTO `fa_admin` VALUES ('1', 'admin', '7d94549fca3bf84b76e8a26697dafdfc', 'admin', 'p6b2Z0', 'assets/img/avatar.png', 'admin@fastadmin.net', '0', '1601910005', '192.168.3.1', '1596873301', '1601910005', '6e76f77c-8af5-469d-9d97-eb34f66cb409', 'normal', '7');
INSERT INTO `fa_admin` VALUES ('2', 'admin1', 'a67f33d38e803f8d8876e3a1cd97178c', 'admin1', 'PhMAR2', '', 'admin1@fastadmin.net', '0', null, '', '1596973450', '1596973709', '', 'normal', '0');

-- ----------------------------
-- Table structure for fa_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `fa_admin_log`;
CREATE TABLE `fa_admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `username` varchar(45) NOT NULL DEFAULT '' COMMENT '管理员名字',
  `url` varchar(1500) NOT NULL DEFAULT '' COMMENT '操作页面',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '日志标题',
  `content` text NOT NULL COMMENT '内容',
  `ip` varchar(255) NOT NULL DEFAULT '' COMMENT 'IP',
  `useragent` varchar(255) NOT NULL DEFAULT '' COMMENT 'User-Agent',
  `createtime` int(11) DEFAULT '0' COMMENT '操作时间',
  PRIMARY KEY (`id`),
  KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COMMENT='管理员日志表';

-- ----------------------------
-- Records of fa_admin_log
-- ----------------------------
INSERT INTO `fa_admin_log` VALUES ('9', '1', 'admin', '/admin/index/login.html?url=%2Fadmin%2Findex%2Flogout.html', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"drze\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36', '1596896841');
INSERT INTO `fa_admin_log` VALUES ('10', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"ntmb\",\"keeplogin\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36', '1596897470');
INSERT INTO `fa_admin_log` VALUES ('11', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"tphj\",\"keeplogin\":\"1\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36', '1596959571');
INSERT INTO `fa_admin_log` VALUES ('12', '1', 'admin', '/admin/auth.admin/add?ids=&dialog=1', '添加管理员', '{\"username\":\"admin1\",\"salt\":\"VBca9T\",\"password\":\"bca716203cf96efb90aa5d9c365e6200\",\"email\":\"admin1@fastadmin.net\",\"nickname\":\"admin1\",\"status\":\"normal\",\"createtime\":1596973450}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36', '1596973450');
INSERT INTO `fa_admin_log` VALUES ('13', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"uaxu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597027111');
INSERT INTO `fa_admin_log` VALUES ('14', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"7xgg\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597029932');
INSERT INTO `fa_admin_log` VALUES ('15', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"ydpq\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597046923');
INSERT INTO `fa_admin_log` VALUES ('16', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"imgc\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597113286');
INSERT INTO `fa_admin_log` VALUES ('17', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"kffg\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597126387');
INSERT INTO `fa_admin_log` VALUES ('18', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"vuwu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597194173');
INSERT INTO `fa_admin_log` VALUES ('19', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"uwmk\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597212780');
INSERT INTO `fa_admin_log` VALUES ('20', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"bumt\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597217392');
INSERT INTO `fa_admin_log` VALUES ('21', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"MAVV\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597222255');
INSERT INTO `fa_admin_log` VALUES ('22', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"d3eu\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597281511');
INSERT INTO `fa_admin_log` VALUES ('23', '1', 'admin', '/admin/index/login.html?url=%2Fadmin%2Fauth.rule%2Findex.html%3Fref%3Daddtabs', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"aamo\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.25 Safari/537.36 Core/1.70.3775.400 QQBrowser/10.6.4208.400', '1597284887');
INSERT INTO `fa_admin_log` VALUES ('24', '1', 'admin', '/admin/index/login.html', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"xwxj\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597287082');
INSERT INTO `fa_admin_log` VALUES ('25', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"654c\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597287342');
INSERT INTO `fa_admin_log` VALUES ('26', '1', 'admin', '/admin/index/login.html?url=%2Fadmin%2Fauth.admin%2Findex.html%3Fref%3Daddtabs', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"zbsi\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597299204');
INSERT INTO `fa_admin_log` VALUES ('27', '1', 'admin', '/admin/index/login.html?url=%2Fadmin%2Fauth.admin%2Findex.html%3Fref%3Daddtabs', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"wzqe\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597301239');
INSERT INTO `fa_admin_log` VALUES ('28', '1', 'admin', '/admin/index/login.html?url=%2Fadmin%2Fgeneral.config%2Findex.html%3Fref%3Daddtabs', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"reei\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597302826');
INSERT INTO `fa_admin_log` VALUES ('29', '1', 'admin', '/admin/index/login.html?url=%2Fadmin%2Fgeneral.config%2Findex.html%3Fref%3Daddtabs', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"4twf\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.105 Safari/537.36', '1597308184');
INSERT INTO `fa_admin_log` VALUES ('30', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"anad\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.125 Safari/537.36', '1597368157');
INSERT INTO `fa_admin_log` VALUES ('31', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"pv2e\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.125 Safari/537.36', '1597368553');
INSERT INTO `fa_admin_log` VALUES ('32', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"hdgy\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.125 Safari/537.36', '1597373090');
INSERT INTO `fa_admin_log` VALUES ('33', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"mpa8\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.125 Safari/537.36', '1597387130');
INSERT INTO `fa_admin_log` VALUES ('34', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"pca5\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.125 Safari/537.36', '1597468429');
INSERT INTO `fa_admin_log` VALUES ('35', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"m6de\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.125 Safari/537.36', '1597483016');
INSERT INTO `fa_admin_log` VALUES ('36', '1', 'admin', '/admin/index/login.html', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"Qutz\"}', '192.168.231.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36', '1598022324');
INSERT INTO `fa_admin_log` VALUES ('37', '1', 'admin', '/admin/index/login.html?url=%2Fadmin', '登录', '{\"username\":\"admin\",\"password\":\"123456\",\"captcha\":\"k6bk\"}', '192.168.3.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.116 Safari/537.36', '1601910005');

-- ----------------------------
-- Table structure for fa_adv
-- ----------------------------
DROP TABLE IF EXISTS `fa_adv`;
CREATE TABLE `fa_adv` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告位id',
  `pid` int(11) NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '广告标题',
  `advurl` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `status` char(10) NOT NULL DEFAULT 'normal' COMMENT '状态',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '10',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='广告表';

-- ----------------------------
-- Records of fa_adv
-- ----------------------------

-- ----------------------------
-- Table structure for fa_advposition
-- ----------------------------
DROP TABLE IF EXISTS `fa_advposition`;
CREATE TABLE `fa_advposition` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL DEFAULT '' COMMENT '广告位名称',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '广告位描述',
  `status` char(10) NOT NULL DEFAULT 'normal' COMMENT '状态',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COMMENT='广告位表';

-- ----------------------------
-- Records of fa_advposition
-- ----------------------------
INSERT INTO `fa_advposition` VALUES ('1', '网站首页', '2', 'normal', '1597395828', '1597395828', '0');
INSERT INTO `fa_advposition` VALUES ('2', '软件侧栏', '', 'normal', '1597473012', null, '0');

-- ----------------------------
-- Table structure for fa_attachment
-- ----------------------------
DROP TABLE IF EXISTS `fa_attachment`;
CREATE TABLE `fa_attachment` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员ID',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '物理路径',
  `imagewidth` varchar(30) NOT NULL DEFAULT '' COMMENT '宽度',
  `imageheight` varchar(30) NOT NULL DEFAULT '' COMMENT '高度',
  `imagetype` varchar(30) NOT NULL DEFAULT '' COMMENT '图片类型',
  `imageframes` int(255) NOT NULL DEFAULT '0' COMMENT '图片帧数',
  `filesize` int(11) NOT NULL DEFAULT '0' COMMENT '文件大小',
  `mimetype` varchar(100) NOT NULL DEFAULT '' COMMENT 'mime类型',
  `extparam` varchar(255) NOT NULL DEFAULT '' COMMENT '透传数据',
  `createtime` int(11) DEFAULT NULL COMMENT '创建日期',
  `updatetime` int(11) DEFAULT NULL COMMENT '更新时间',
  `uploadtime` int(11) DEFAULT NULL COMMENT '上传时间',
  `storage` varchar(100) NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `sha1` varchar(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COMMENT='附件表';

-- ----------------------------
-- Records of fa_attachment
-- ----------------------------
INSERT INTO `fa_attachment` VALUES ('4', '1', '0', '/storage/uploads/20200814/0424c075dfd7012e31fa49aa2f9318c8.png', '260', '260', 'png', '0', '15218', 'image/png', '{\"name\":\"904217f18840f40443ce8baf6309623.png\"}', '1597391810', '1597391810', '1597391810', 'local', '518bf5e830f83d6103ce276ca73bb989c156fa9f');
INSERT INTO `fa_attachment` VALUES ('7', '1', '0', '/storage/uploads/20200815/2cf0983ba5fc52506d723e1ce31d52b3.png', '3601', '2391', 'png', '0', '485160', 'image/png', '{\"name\":\"578c8eff7a94d3c73dd2192fa055001.png\"}', '1597475001', '1597475001', '1597475001', 'local', '08e9bdc70546355a4839c275e77a0bafc0c7b010');
INSERT INTO `fa_attachment` VALUES ('9', '1', '0', '/storage/uploads/20200816/3c845612f7c481123fd3aacb5af93b36.jpg', '3840', '2160', 'jpg', '0', '1676908', 'image/jpeg', '{\"name\":\"317928.jpg\"}', '1597588657', '1597588657', '1597588657', 'local', '2d6fcfcc3c7f4b42be570adcb0f346f2b3e585e5');
INSERT INTO `fa_attachment` VALUES ('10', '1', '0', '/storage/uploads/20200816/1c1609bd962eed2341549a983c25ef55.jpg', '1920', '1080', 'jpg', '0', '446306', 'image/jpeg', '{\"name\":\"319709.jpg\"}', '1597589095', '1597589095', '1597589095', 'local', '8aceaa0c520ea38a9b25ae4ed07bc6c152d917a3');

-- ----------------------------
-- Table structure for fa_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `fa_auth_group`;
CREATE TABLE `fa_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '组名',
  `rules` text NOT NULL COMMENT '规则ID',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  `status` char(30) NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='角色表';

-- ----------------------------
-- Records of fa_auth_group
-- ----------------------------
INSERT INTO `fa_auth_group` VALUES ('1', '0', '超级管理员', '*', '1596786007', '1596786007', 'normal');
INSERT INTO `fa_auth_group` VALUES ('2', '1', '网站管理员', '1,12,13,14,15,16,2,9,17,18,19,20,21,10,11,4,3,5,22,23,24,25,26,6,27,28,29,7,31,32,33,30,8,34,35,36,37', '1596865550', '1596973162', 'normal');
INSERT INTO `fa_auth_group` VALUES ('3', '1', '软件管理员', '', '1596972036', '1596972036', 'normal');

-- ----------------------------
-- Table structure for fa_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `fa_auth_group_access`;
CREATE TABLE `fa_auth_group_access` (
  `uid` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限分组表';

-- ----------------------------
-- Records of fa_auth_group_access
-- ----------------------------
INSERT INTO `fa_auth_group_access` VALUES ('1', '1');
INSERT INTO `fa_auth_group_access` VALUES ('2', '2');

-- ----------------------------
-- Table structure for fa_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `fa_auth_rule`;
CREATE TABLE `fa_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('menu','file') NOT NULL DEFAULT 'file' COMMENT 'menu为菜单,file为权限节点',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(60) NOT NULL DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '图标',
  `condition` varchar(255) NOT NULL DEFAULT '' COMMENT '条件',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(1) NOT NULL COMMENT '是否为菜单',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  `weigh` int(11) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` char(15) NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of fa_auth_rule
-- ----------------------------
INSERT INTO `fa_auth_rule` VALUES ('1', 'file', '0', 'dashboard', '控制台', 'fa fa-dashboard', '', '', '1', '1596786800', '1596786800', '146', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('2', 'file', '0', 'general', '常规管理', 'fa fa-cogs', '', '', '1', '1596786876', '1596786876', '137', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('3', 'file', '0', 'auth', '权限管理', 'fa fa-group', '', '', '1', '1596786925', '1596899902', '99', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('4', 'file', '0', 'article', '文章管理', 'fa fa-paste', '', '', '1', '1596798467', '1596899777', '123', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('5', 'file', '3', 'auth/admin', '管理员管理', 'fa fa-users', '', '', '1', '1596899890', '1596899890', '118', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('6', 'file', '3', 'auth/adminlog', '管理员日志', 'fa fa-list-alt', '', 'Admin log tips', '1', '1596899977', '1596899977', '113', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('7', 'file', '3', 'auth/group', '角色组', 'fa fa-group', '', 'Group tips', '1', '1596900026', '1596900026', '109', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('8', 'file', '3', 'auth/rule', '规则管理', 'fa fa-bars', '', 'Rule tips', '1', '1596900068', '1596900068', '104', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('9', 'file', '2', 'general/config', '系统配置', 'fa fa-cog', '', 'Config tips', '1', '1596900139', '1596900139', '60', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('10', 'file', '2', 'general/attachment', '附件管理', 'fa fa-file-image-o', '', 'Attachment tips', '1', '1596900184', '1596900184', '53', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('11', 'file', '2', 'general/profile', '个人配置', 'fa fa-users', '', '', '1', '1596900229', '1596900229', '34', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('12', 'file', '1', 'dashboard/index', 'View', 'fa fa-circle-o', '', '', '0', '1596900341', '1596900341', '136', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('13', 'file', '1', 'dashboard/add', 'Add', 'fa fa-circle-o', '', '', '0', '1596900426', '1596900426', '135', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('14', 'file', '1', 'dashboard/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1596900485', '1596900485', '133', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('15', 'file', '1', 'dashboard/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1596900519', '1596900536', '132', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('16', 'file', '1', 'dashboard/multi', 'Multi', 'fa fa-circle-o', '', '', '0', '1596900613', '1596900613', '132', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('17', 'file', '9', 'general/config/index', 'View', 'fa fa-circle-o', '', '', '0', '1596900657', '1596900657', '52', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('18', 'file', '5', 'auth/admin/index', 'View', 'fa fa-circle-o', '', '', '0', '1598079285', '1598079285', '118', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('19', 'file', '5', 'auth/admin/add', 'Add', 'fa fa-circle-o', '', '', '0', '1598079308', '1598079308', '118', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('20', 'file', '5', 'auth/admin/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1598079366', '1598079366', '118', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('21', 'file', '5', 'auth/admin/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1598079366', '1598079366', '118', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('22', 'file', '6', 'auth/adminlog/index', 'View', 'fa fa-circle-o', '', '', '0', '1598079437', '1598079437', '113', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('23', 'file', '6', 'auth/adminlog/detail', 'Detail', 'fa fa-circle-o', '', '', '0', '1598079437', '1598079437', '113', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('24', 'file', '6', 'auth/adminlog/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1598079437', '1598079437', '113', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('25', 'file', '7', 'auth/group/index', 'View', 'fa fa-circle-o', '', '', '0', '1598079598', '1598079598', '109', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('26', 'file', '7', 'auth/group/add', 'Add', 'fa fa-circle-o', '', '', '0', '1598079598', '1598079598', '109', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('27', 'file', '7', 'auth/group/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1598079598', '1598079598', '109', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('28', 'file', '7', 'auth/group/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1598079598', '1598079598', '109', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('29', 'file', '8', 'auth/rule/index', 'View', 'fa fa-circle-o', '', '', '0', '1598080094', '1598080094', '104', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('30', 'file', '8', 'auth/rule/add', 'Add', 'fa fa-circle-o', '', '', '0', '1598080118', '1598080118', '104', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('31', 'file', '8', 'auth/rule/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1598080118', '1598080118', '104', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('32', 'file', '8', 'auth/rule/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1598080118', '1598080118', '104', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('33', 'file', '9', 'general/config/add', 'Add', 'fa fa-circle-o', '', '', '0', '1598080118', '1598080118', '52', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('34', 'file', '9', 'general/config/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1598080118', '1598080118', '52', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('35', 'file', '9', 'general/config/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1598080118', '1598080118', '52', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('36', 'file', '10', 'general/attachment/index', 'View', 'fa fa-circle-o', '', '', '0', '1598080598', '1598080598', '53', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('37', 'file', '10', 'general/attachment/select', 'Select attachment', 'fa fa-circle-o', '', '', '0', '1598080598', '1598080598', '53', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('38', 'file', '10', 'general/attachment/add', 'Add', 'fa fa-circle-o', '', '', '0', '1598080598', '1598080598', '53', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('39', 'file', '10', 'general/attachment/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1598080598', '1598080598', '53', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('40', 'file', '10', 'general/attachment/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1598080598', '1598080598', '53', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('41', 'file', '10', 'general/attachment/multi', 'Multi', 'fa fa-circle-o', '', '', '0', '1598080598', '1598080598', '53', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('42', 'file', '11', 'general/profile/index', 'View', 'fa fa-circle-o', '', '', '0', '1598080797', '1598080797', '34', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('43', 'file', '11', 'general/profile/add', 'Add', 'fa fa-circle-o', '', '', '0', '1598080797', '1598080797', '34', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('44', 'file', '11', 'general/profile/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1598080797', '1598080797', '34', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('45', 'file', '11', 'general/profile/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1598080797', '1598080797', '34', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('46', 'file', '11', 'general/profile/multi', 'Multi', 'fa fa-circle-o', '', '', '0', '1598080797', '1598080797', '34', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('47', 'file', '11', 'general/profile/update', 'Update profile', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '34', 'normal');

-- ----------------------------
-- Table structure for fa_config
-- ----------------------------
DROP TABLE IF EXISTS `fa_config`;
CREATE TABLE `fa_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '变量名',
  `group` varchar(30) NOT NULL DEFAULT '' COMMENT '分组',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '变量标题',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '变量描述',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
  `value` text NOT NULL COMMENT '变量值',
  `content` text NOT NULL COMMENT '变量字典数据',
  `rule` varchar(100) NOT NULL DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) NOT NULL DEFAULT '' COMMENT '扩展属性',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COMMENT='系统配置';

-- ----------------------------
-- Records of fa_config
-- ----------------------------
INSERT INTO `fa_config` VALUES ('1', 'web_name', 'basic', '网站标题', '网站标题前台显示标题', 'string', '陕西天屿懿德科技网络有限公司', '', 'required', '');
INSERT INTO `fa_config` VALUES ('2', 'web_desc', 'basic', '网站描述', '网站搜索引擎描述', 'string', '', '', '', '');
INSERT INTO `fa_config` VALUES ('3', 'configgroup', 'dictionary', '配置分组', '', 'array', '{\"basic\":\"Basic\",\"email\":\"Email\",\"dictionary\":\"Dictionary\",\"user\":\"User\",\"example\":\"Example\"}', '', '', '');
INSERT INTO `fa_config` VALUES ('4', 'web_key', 'basic', '网站关键字', '网站搜索引擎关键字', 'string', '', '', '', '');
INSERT INTO `fa_config` VALUES ('5', 'categorytype', 'dictionary', '分类类型', '', 'array', '{\"default\":\"Default\",\"page\":\"Page\",\"article\":\"Article\",\"test\":\"Test\"}', '', '', '');
INSERT INTO `fa_config` VALUES ('6', 'cdnurl', 'basic', 'Cdn url', '如果静态资源使用第三方云储存请配置该值', 'string', '', '', '', '');
INSERT INTO `fa_config` VALUES ('7', 'version', 'basic', '版本号', '如果静态资源有变动请重新配置该值', 'string', '1.0.1', '', 'required', '');
INSERT INTO `fa_config` VALUES ('8', 'timezone', 'basic', '时区', '', 'string', 'Asia/Shanghai', '', 'required', '');
INSERT INTO `fa_config` VALUES ('9', 'forbiddenip', 'basic', '禁止IP', '一行一条记录', 'text', '', '', '', '');
INSERT INTO `fa_config` VALUES ('10', 'languages', 'basic', '模块语言', '', 'array', '{\"backend\":\"zh-cn\",\"frontend\":\"zh-cn\"}', '', '', '');
INSERT INTO `fa_config` VALUES ('11', 'web_close', 'basic', '关闭站点', '关闭站点', 'switch', '1', '', '', '');
INSERT INTO `fa_config` VALUES ('12', 'web_logo', 'basic', '网站logo', '', 'image', '/storage/uploads/20200814/9fd7a33c5507cff04bf4b9a5fdbbfa8a.png', '', '', '');
