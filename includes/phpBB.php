<?php
/**
 *
 * @Project: Сървър реклама CS-BG.INFO
 * @Author HybridMind <www.webocean.info>
 * @Version: 0.0.1
 * @File phpBB.php
 * @Created 22.1.2021 г.
 * @License: MIT
 * @Discord: HybridMind#6095
 *
 */

define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : $forumPath;
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require($phpbb_root_path . 'common.' . $phpEx);
require($phpbb_root_path . 'includes/functions_display.' . $phpEx);
require($phpbb_root_path . 'includes/functions_user.' . $phpEx);
$user->session_begin();
$auth->acl($user->data);
$user->setup();
$request->enable_super_globals();

require($forumPath . 'config.php');
$bb_db = $dbname;
$bb_prefix = preg_replace("/[^A-Za-z0-9 ]/", '', $table_prefix);

$bb_is_anonymous = ($user->data['user_id'] == ANONYMOUS);
$bb_session_id = $user->session_id;
$bb_user_id = $user->data['user_id'];
$bb_is_bot = $user->data['is_bot'];
$bb_mail = $user->data['user_email'];
$bb_username = $user->data['username'];
$bb_is_admin = $auth->acl_get('a_user');
$bb_user_ip = $user->ip;
$bb_user_sig = $user->data['user_sig'];
$bb_bbcode_uid = $user->data['user_sig_bbcode_uid'];
$bb_bbcode_bitfield = $user->data['user_sig_bbcode_bitfield'];
$bb_user_last_visit = $user->data['user_lastvisit'];
$bb_user_color = $user->data['user_colour'];
$bb_current_page = $user->page['page'];
$bb_new_pm = $user->data['user_new_privmsg'];
$bb_unread_pm = $user->data['user_unread_privmsg'];
$bb_user_posts = $user->data['user_posts'];
$bb_user_ava = $user->data['user_avatar'];
$bb_group_id = $user->data['group_id'];
$bb_ava_type = $user->data['user_avatar_type'];
$bb_user_warns = $user->data['user_warnings'];

