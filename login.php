<?php
/**
*
* @package phpBB3
* @version $Id: login.php,v 1.0.5 2008/03/09 15:15:06 rxu Exp $
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
* Minimum Requirement: PHP 4.3.3
*/

/*
* This code is an example for board login integration on sites.
*/
ini_set('display_errors', '0');
/**
*/
define('IN_PHPBB', true);

// Set phpBB root folder properly. Change in according to the actual board folder
define('PHPBB_ROOT_PATH', './phpbb/');
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
global $db;

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup('ucp');

// Initialize  login result array
$login = array();

// Handle logouot button if pressed
if(isset($_POST['logout']) && $user->data['user_id'] != ANONYMOUS)
{
	$user->session_kill();
}

// Check if user has submitted login and password and try to log in
if(isset($_POST['login']) && $user->data['user_id'] == ANONYMOUS)
{
	$username = request_var('username', '', true);
	$password = request_var('password', '', true);
	$autologin	= (!empty($_POST['autologin'])) ? true : false;

	$login = $auth->login($username, $password, $autologin);
}
//
if(isset($_POST['register']) && $user->data['user_id'] == ANONYMOUS)
{
	$username = request_var('username', '', true);
	$password = request_var('password', '', true);
	$email = request_var('email', '', true);
	$address = request_var('address', '', true);
	$autologin	= (!empty($_POST['autologin'])) ? true : false;
	$user_row = array(
    'username'              => $username,
    'user_password'         => phpbb_hash($password),
    'user_email'            => $email,
    'group_id'              => 2,
    'user_timezone'         => 'UTC',
    'user_lang'             => 'ru',
	'user_type'				=> 0,
    'user_actkey'           => $user_actkey,
    'user_regdate'          => time(),
    'user_inactive_reason'  => 0,
    'user_inactive_time'    => 0,
    );
	$cp_data = array(
	'pf_phpbb_address' => $address,
	);
    $user_id = user_add($user_row, $cp_data);

    $forum_name = $cp_data['pf_phpbb_address'];

    include ($phpbb_root_path . 'includes/acp/acp_forums.' . $phpEx);
    include ($phpbb_root_path . 'includes/functions_acp.' . $phpEx);
    include ($phpbb_root_path . 'includes/functions_admin.' . $phpEx);

    $forum_data = array(
        'parent_id'                => 0,
        'forum_type'            => FORUM_POST,
        'type_action'            => '',
        'forum_status'            => ITEM_UNLOCKED,
        'forum_parents'            => '',
        'forum_name'            => $forum_name,
        'forum_link'            => '',
        'forum_link_track'        => false,
        'forum_desc'            => '',  
        'forum_desc_uid'        => '',
        'forum_desc_options'    => 7,
        'forum_desc_bitfield'    => '',
        'forum_rules'            => '',
        'forum_rules_uid'        => '',
        'forum_rules_options'    => 7,
        'forum_rules_bitfield'    => '',
        'forum_rules_link'        => '',
        'forum_image'            => '',
        'forum_style'            => 0,
        'display_subforum_list'    => false,
        'display_on_index'        => false,
        'forum_topics_per_page'    => 0,
        'enable_indexing'        => true,
        'enable_icons'            => false,
        'enable_prune'            => false,
        'enable_post_review'    => true,
        'enable_quick_reply'    => false,
        'prune_days'            => 7,
        'prune_viewed'            => 7,
        'prune_freq'            => 1,
        'prune_old_polls'        => false,
        'prune_announce'        => false,
        'prune_sticky'            => false,
        'forum_password'        => '',
        'forum_password_confirm'=> '',
        'forum_password_unset'    => false,
        'forum_options'=> 0,
        'show_active'=> true,
    );

    $sql = 'SELECT forum_name FROM ' . FORUMS_TABLE;
    $result = $db->sql_query($sql);
    $rows = $db->sql_fetchrowset($result);
    $db->sql_freeresult($result);
    $forum_exist = 0;
    foreach ($rows as $row){
        if($row['forum_name'] == $forum_name) {
            $forum_exist = true;
        }
    }

    if(!$forum_exist){
        \acp_forums::update_forum_data($forum_data);
        global $cache;
        $cache->destroy('sql', FORUMS_TABLE);
        $forum_perm_from = 1;
        if ($forum_perm_from) {
            copy_forum_permissions($forum_perm_from, $forum_data['forum_id'], false);
        }
        $auth->acl_clear_prefetch();
		$forum_parent_id = $forum_data['forum_id'];
        global $cache, $auth;
        for($i = 1; $i <= 7; $i++) {
            switch ($i) {
                case 1:
                    $forum_data = array(
                        'parent_id'                => $forum_parent_id,
                        'forum_type'            => FORUM_POST,
                        'type_action'            => '',
                        'forum_status'            => ITEM_UNLOCKED,
                        'forum_parents'            => '',
                        'forum_name'            => "Нужен совет или помощь",
                        'forum_link'            => '',
                        'forum_link_track'        => false,
                        'forum_desc'            => "Здесь вы можете получить совет или помощь по какому-нибудь вопросу.",
                        'forum_desc_uid'        => '',
                        'forum_desc_options'    => 7,
                        'forum_desc_bitfield'    => '',
                        'forum_rules'            => '',
                        'forum_rules_uid'        => '',
                        'forum_rules_options'    => 7,
                        'forum_rules_bitfield'    => '',
                        'forum_rules_link'        => '',
                        'forum_image'            => '',
                        'forum_style'            => 0,
                        'display_subforum_list'    => false,
                        'display_on_index'        => false,
                        'forum_topics_per_page'    => 0,
                        'enable_indexing'        => true,
                        'enable_icons'            => false,
                        'enable_prune'            => false,
                        'enable_post_review'    => true,
                        'enable_quick_reply'    => false,
                        'prune_days'            => 7,
                        'prune_viewed'            => 7,
                        'prune_freq'            => 1,
                        'prune_old_polls'        => false,
                        'prune_announce'        => false,
                        'prune_sticky'            => false,
                        'forum_password'        => '',
                        'forum_password_confirm'=> '',
                        'forum_password_unset'    => false,
                        'forum_options'=> 0,
                        'show_active'=> true,
                    );
					//$forum_data['parent_id'] = $forum_data['forum_id'];
					//$forum_data['forum_name'] = 'Нужен совет или помощь';
					//$forum_data['forum_desc'] = 'Здесь вы можете получить совет или помощь по какому-нибудь вопросу.';
                    \acp_forums::update_forum_data($forum_data);
                    $cache->destroy('sql', FORUMS_TABLE);
                    $forum_perm_from = $forum_parent_id;
                    if ($forum_perm_from) {
                        copy_forum_permissions($forum_perm_from, $forum_data['forum_id'], false);
                    }
                    $auth->acl_clear_prefetch();
                    $sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
                            'user_id'        => $user_id,
                            'forum_id'       => $forum_data['forum_id'],
                            'auth_role_id'   => 15
                        ));

                    $db->sql_query($sql);
                    break;
                case 2:
					$forum_data = array(
                        'parent_id'                => $forum_parent_id,
                        'forum_type'            => FORUM_POST,
                        'type_action'            => '',
                        'forum_status'            => ITEM_UNLOCKED,
                        'forum_parents'            => '',
                        'forum_name'            => "Срочно",
                        'forum_link'            => '',
                        'forum_link_track'        => false,
                        'forum_desc'            => "Здесь вы можете создавать наиболее важные темы, требующие быстрого ответа или решения проблемы.",
                        'forum_desc_uid'        => '',
                        'forum_desc_options'    => 7,
                        'forum_desc_bitfield'    => '',
                        'forum_rules'            => '',
                        'forum_rules_uid'        => '',
                        'forum_rules_options'    => 7,
                        'forum_rules_bitfield'    => '',
                        'forum_rules_link'        => '',
                        'forum_image'            => '',
                        'forum_style'            => 0,
                        'display_subforum_list'    => false,
                        'display_on_index'        => false,
                        'forum_topics_per_page'    => 0,
                        'enable_indexing'        => true,
                        'enable_icons'            => false,
                        'enable_prune'            => false,
                        'enable_post_review'    => true,
                        'enable_quick_reply'    => false,
                        'prune_days'            => 7,
                        'prune_viewed'            => 7,
                        'prune_freq'            => 1,
                        'prune_old_polls'        => false,
                        'prune_announce'        => false,
                        'prune_sticky'            => false,
                        'forum_password'        => '',
                        'forum_password_confirm'=> '',
                        'forum_password_unset'    => false,
                        'forum_options'=> 0,
                        'show_active'=> true,
                    );
                    \acp_forums::update_forum_data($forum_data);
                    $cache->destroy('sql', FORUMS_TABLE);
                    $forum_perm_from = $forum_parent_id;
                    if ($forum_perm_from) {
                        copy_forum_permissions($forum_perm_from, $forum_data['forum_id'], false);
                    }
                    $auth->acl_clear_prefetch();
                    $sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
                            'user_id'        => $user_id,
                            'forum_id'       => $forum_data['forum_id'],
                            'auth_role_id'   => 15
                        ));

                    $db->sql_query($sql);
                    break;
                case 3:
                    $forum_data = array(
                        'parent_id'                => $forum_parent_id,
                        'forum_type'            => FORUM_POST,
                        'type_action'            => '',
                        'forum_status'            => ITEM_UNLOCKED,
                        'forum_parents'            => '',
                        'forum_name'            => "Вопросы ЖКХ",
                        'forum_link'            => '',
                        'forum_link_track'        => false,
                        'forum_desc'            => "Здесь решаются вопросы, касающиеся ЖКХ услуг.",
                        'forum_desc_uid'        => '',
                        'forum_desc_options'    => 7,
                        'forum_desc_bitfield'    => '',
                        'forum_rules'            => '',
                        'forum_rules_uid'        => '',
                        'forum_rules_options'    => 7,
                        'forum_rules_bitfield'    => '',
                        'forum_rules_link'        => '',
                        'forum_image'            => '',
                        'forum_style'            => 0,
                        'display_subforum_list'    => false,
                        'display_on_index'        => false,
                        'forum_topics_per_page'    => 0,
                        'enable_indexing'        => true,
                        'enable_icons'            => false,
                        'enable_prune'            => false,
                        'enable_post_review'    => true,
                        'enable_quick_reply'    => false,
                        'prune_days'            => 7,
                        'prune_viewed'            => 7,
                        'prune_freq'            => 1,
                        'prune_old_polls'        => false,
                        'prune_announce'        => false,
                        'prune_sticky'            => false,
                        'forum_password'        => '',
                        'forum_password_confirm'=> '',
                        'forum_password_unset'    => false,
                        'forum_options'=> 0,
                        'show_active'=> true,
                    );
                    \acp_forums::update_forum_data($forum_data);
                    $cache->destroy('sql', FORUMS_TABLE);
                    $forum_perm_from = $forum_parent_id;
                    if ($forum_perm_from) {
                        copy_forum_permissions($forum_perm_from, $forum_data['forum_id'], false);
                    }
                    $auth->acl_clear_prefetch();
                    $sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
                            'user_id'        => $user_id,
                            'forum_id'       => $forum_data['forum_id'],
                            'auth_role_id'   => 15
                        ));

                    $db->sql_query($sql);
                    break;
                case 4:
                   $forum_data = array(
                        'parent_id'                => $forum_parent_id,
                        'forum_type'            => FORUM_POST,
                        'type_action'            => '',
                        'forum_status'            => ITEM_UNLOCKED,
                        'forum_parents'            => '',
                        'forum_name'            => "Общее собрание",
                        'forum_link'            => '',
                        'forum_link_track'        => false,
                        'forum_desc'            =>"Здесь организуются собрания, сборы, голосования.",
                        'forum_desc_uid'        => '',
                        'forum_desc_options'    => 7,
                        'forum_desc_bitfield'    => '',
                        'forum_rules'            => '',
                        'forum_rules_uid'        => '',
                        'forum_rules_options'    => 7,
                        'forum_rules_bitfield'    => '',
                        'forum_rules_link'        => '',
                        'forum_image'            => '',
                        'forum_style'            => 0,
                        'display_subforum_list'    => false,
                        'display_on_index'        => false,
                        'forum_topics_per_page'    => 0,
                        'enable_indexing'        => true,
                        'enable_icons'            => false,
                        'enable_prune'            => false,
                        'enable_post_review'    => true,
                        'enable_quick_reply'    => false,
                        'prune_days'            => 7,
                        'prune_viewed'            => 7,
                        'prune_freq'            => 1,
                        'prune_old_polls'        => false,
                        'prune_announce'        => false,
                        'prune_sticky'            => false,
                        'forum_password'        => '',
                        'forum_password_confirm'=> '',
                        'forum_password_unset'    => false,
                        'forum_options'=> 0,
                        'show_active'=> true,
                    );
                    \acp_forums::update_forum_data($forum_data);
                    $cache->destroy('sql', FORUMS_TABLE);
                    $forum_perm_from = $forum_parent_id;
                    if ($forum_perm_from) {
                        copy_forum_permissions($forum_perm_from, $forum_data['forum_id'], false);
                    }
                    $auth->acl_clear_prefetch();
                    $sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
                            'user_id'        => $user_id,
                            'forum_id'       => $forum_data['forum_id'],
                            'auth_role_id'   => 15
                        ));

                    $db->sql_query($sql);
                    break;
                case 5:
                    $forum_data = array(
                        'parent_id'                => $forum_parent_id,
                        'forum_type'            => FORUM_POST,
                        'type_action'            => '',
                        'forum_status'            => ITEM_UNLOCKED,
                        'forum_parents'            => '',
                        'forum_name'            => "План работы по обслуживанию дома",
                        'forum_link'            => '',
                        'forum_link_track'        => false,
                        'forum_desc'            =>"Здесь вы можете просмотреть расписание работ по обслуживанию дома",
                        'forum_desc_uid'        => '',
                        'forum_desc_options'    => 7,
                        'forum_desc_bitfield'    => '',
                        'forum_rules'            => '',
                        'forum_rules_uid'        => '',
                        'forum_rules_options'    => 7,
                        'forum_rules_bitfield'    => '',
                        'forum_rules_link'        => '',
                        'forum_image'            => '',
                        'forum_style'            => 0,
                        'display_subforum_list'    => false,
                        'display_on_index'        => false,
                        'forum_topics_per_page'    => 0,
                        'enable_indexing'        => true,
                        'enable_icons'            => false,
                        'enable_prune'            => false,
                        'enable_post_review'    => true,
                        'enable_quick_reply'    => false,
                        'prune_days'            => 7,
                        'prune_viewed'            => 7,
                        'prune_freq'            => 1,
                        'prune_old_polls'        => false,
                        'prune_announce'        => false,
                        'prune_sticky'            => false,
                        'forum_password'        => '',
                        'forum_password_confirm'=> '',
                        'forum_password_unset'    => false,
                        'forum_options'=> 0,
                        'show_active'=> true,
                    );
                    \acp_forums::update_forum_data($forum_data);
                    $cache->destroy('sql', FORUMS_TABLE);
                    $forum_perm_from = $forum_parent_id;
                    if ($forum_perm_from) {
                        copy_forum_permissions($forum_perm_from, $forum_data['forum_id'], false);
                    }
                    $auth->acl_clear_prefetch();
                    $sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
                            'user_id'        => $user_id,
                            'forum_id'       => $forum_data['forum_id'],
                            'auth_role_id'   => 15
                        ));

                    $db->sql_query($sql);
                    break;
                case 6:
                    $forum_data = array(
                        'parent_id'                => $forum_parent_id,
                        'forum_type'            => FORUM_POST,
                        'type_action'            => '',
                        'forum_status'            => ITEM_UNLOCKED,
                        'forum_parents'            => '',
                        'forum_name'            => "Соседи по дому",
                        'forum_link'            => '',
                        'forum_link_track'        => false,
                        'forum_desc'            =>  "Здесь вы можете посмотреть список зарегистрированных жильцов дома",
                        'forum_desc_uid'        => '',
                        'forum_desc_options'    => 7,
                        'forum_desc_bitfield'    => '',
                        'forum_rules'            => '',
                        'forum_rules_uid'        => '',
                        'forum_rules_options'    => 7,
                        'forum_rules_bitfield'    => '',
                        'forum_rules_link'        => '',
                        'forum_image'            => '',
                        'forum_style'            => 0,
                        'display_subforum_list'    => false,
                        'display_on_index'        => false,
                        'forum_topics_per_page'    => 0,
                        'enable_indexing'        => true,
                        'enable_icons'            => false,
                        'enable_prune'            => false,
                        'enable_post_review'    => true,
                        'enable_quick_reply'    => false,
                        'prune_days'            => 7,
                        'prune_viewed'            => 7,
                        'prune_freq'            => 1,
                        'prune_old_polls'        => false,
                        'prune_announce'        => false,
                        'prune_sticky'            => false,
                        'forum_password'        => '',
                        'forum_password_confirm'=> '',
                        'forum_password_unset'    => false,
                        'forum_options'=> 0,
                        'show_active'=> true,
                    );
                    \acp_forums::update_forum_data($forum_data);
                    $cache->destroy('sql', FORUMS_TABLE);
                    $forum_perm_from = $forum_parent_id;
                    if ($forum_perm_from) {
                        copy_forum_permissions($forum_perm_from, $forum_data['forum_id'], false);
                    }
                    $auth->acl_clear_prefetch();
                    $sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
                            'user_id'        => $user_id,
                            'forum_id'       => $forum_data['forum_id'],
                            'auth_role_id'   => 15
                        ));

                    $db->sql_query($sql);
                    break;
                case 7:
                    $forum_data = array(
                        'parent_id'                => $forum_parent_id,
                        'forum_type'            => FORUM_POST,
                        'type_action'            => '',
                        'forum_status'            => ITEM_UNLOCKED,
                        'forum_parents'            => '',
                        'forum_name'            => "Разное",
                        'forum_link'            => '',
                        'forum_link_track'        => false,
                        'forum_desc'            => "Здесь находится информация различного вида",
                        'forum_desc_uid'        => '',
                        'forum_desc_options'    => 7,
                        'forum_desc_bitfield'    => '',
                        'forum_rules'            => '',
                        'forum_rules_uid'        => '',
                        'forum_rules_options'    => 7,
                        'forum_rules_bitfield'    => '',
                        'forum_rules_link'        => '',
                        'forum_image'            => '',
                        'forum_style'            => 0,
                        'display_subforum_list'    => false,
                        'display_on_index'        => false,
                        'forum_topics_per_page'    => 0,
                        'enable_indexing'        => true,
                        'enable_icons'            => false,
                        'enable_prune'            => false,
                        'enable_post_review'    => true,
                        'enable_quick_reply'    => false,
                        'prune_days'            => 7,
                        'prune_viewed'            => 7,
                        'prune_freq'            => 1,
                        'prune_old_polls'        => false,
                        'prune_announce'        => false,
                        'prune_sticky'            => false,
                        'forum_password'        => '',
                        'forum_password_confirm'=> '',
                        'forum_password_unset'    => false,
                        'forum_options'=> 0,
                        'show_active'=> true,
                    );
                    \acp_forums::update_forum_data($forum_data);
                    $cache->destroy('sql', FORUMS_TABLE);
                    $forum_perm_from = $forum_parent_id;
                    if ($forum_perm_from) {
                        copy_forum_permissions($forum_perm_from, $forum_data['forum_id'], false);
                    }
                    $auth->acl_clear_prefetch();
                    $sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
                            'user_id'        => $user_id,
                            'forum_id'       => $forum_data['forum_id'],
                            'auth_role_id'   => 15
                        ));

                    $db->sql_query($sql);
                    break;
            }
        }
		 $sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
            'user_id'        => $user_id,
            'forum_id'       => $forum_parent_id,
            'auth_role_id'   => 15
        ));

    $db->sql_query($sql);
    } else {
        $sql = "SELECT forum_id FROM ". FORUMS_TABLE ." WHERE forum_name = " . "'" . $forum_name ."'";
        $result = $db->sql_query($sql);
        $rows = $db->sql_fetchrowset($result);
        $forum_data['forum_id'] = $rows[0]['forum_id'];
        $db->sql_freeresult($result);
        for($i = 1; $i <= 7; $i++) {
            switch ($i) {
                case 1:
                    $sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
                            'user_id'        => $user_id,
                            'forum_id'       => $forum_data['forum_id']+1,
                            'auth_role_id'   => 15
                        ));

                    $db->sql_query($sql);
                    break;
                case 2:
                    $sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
                            'user_id'        => $user_id,
                            'forum_id'       => $forum_data['forum_id']+2,
                            'auth_role_id'   => 15
                        ));

                    $db->sql_query($sql);
                    break;
                case 3:
                    $sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
                            'user_id'        => $user_id,
                            'forum_id'       => $forum_data['forum_id']+3,
                            'auth_role_id'   => 15
                        ));

                    $db->sql_query($sql);
                    break;
                case 4:
                    $sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
                            'user_id'        => $user_id,
                            'forum_id'       => $forum_data['forum_id']+4,
                            'auth_role_id'   => 15
                        ));

                    $db->sql_query($sql);
                    break;
                case 5:
                    $sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
                            'user_id'        => $user_id,
                            'forum_id'       => $forum_data['forum_id']+5,
                            'auth_role_id'   => 15
                        ));

                    $db->sql_query($sql);
                    break;
                case 6:
                    $sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
                            'user_id'        => $user_id,
                            'forum_id'       => $forum_data['forum_id']+6,
                            'auth_role_id'   => 15
                        ));

                    $db->sql_query($sql);
                    break;
                case 7:
                    $sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
                            'user_id'        => $user_id,
                            'forum_id'       => $forum_data['forum_id']+7,
                            'auth_role_id'   => 15
                        ));

                    $db->sql_query($sql);
                    break;
            }
        }
		$sql = 'INSERT INTO ' . ACL_USERS_TABLE  . $db->sql_build_array('INSERT', array(
            'user_id'        => $user_id,
            'forum_id'       => $forum_data['forum_id'],
            'auth_role_id'   => 15
        ));

    $db->sql_query($sql);
    }
   
}

// Send headers
header('Content-type: text/html; charset=UTF-8');
header('Cache-Control: private, no-cache="set-cookie"');
header('Expires: 0');
header('Pragma: no-cache');

// Check if user has tried to log in and greet him if login is successful
if((!empty($login) && $login['status'] == LOGIN_SUCCESS) || $user->data['user_id'] != ANONYMOUS)
{
	// Reset permissions data if user has just logged in
	if(!empty($login))
	{
		$auth->acl($user->data);
	}
	echo 'Hello, ' . get_username_string('full', $user->data['user_id'], $user->data['username'], $user->data['user_colour']);
	echo '<form method="post" action="login.php">';
	echo '	<input type="submit" name="logout" value="LOGOUT" />';
	echo '</form>';
}
else
{
	// Handle login errors if exist and display error message right above the login form
	if(isset($login['error_msg']) && $login['error_msg'])
	{
		$err = $user->lang[$login['error_msg']];
		// Assign admin contact to some error messages
		if ($login['error_msg'] == 'LOGIN_ERROR_USERNAME' || $login['error_msg'] == 'LOGIN_ERROR_PASSWORD')
		{
			$err = (!$config['board_contact']) ? sprintf($user->lang[$login['error_msg']], '', '') : sprintf($user->lang[$login['error_msg']], '<a href="mailto:' . htmlspecialchars($config['board_contact']) . '">', '</a>');
		}
				
		echo $err . '<br />';
	}
	
	// Show login form
	echo '<form method="post" action="login.php">';
	echo $user->lang['USERNAME'] . ':&nbsp;<input type="text" name="username" id="username" size="10" title="' . $user->lang['USERNAME'] . '" /> ';
	echo $user->lang['PASSWORD'] . ':&nbsp;<input type="password" name="password" id="password" size="10" title="' . $user->lang['PASSWORD'] . '" />';
	echo '	<input type="submit" name="login" value="LOGIN" />';
	if ($config['allow_autologin'])
	{
		echo '  <br /><input type="checkbox" name="autologin" /> ' . $user->lang['LOG_ME_IN'];
	}
	echo '</form>';
}





