<?php
/**
 * Image upload use ImageMagick
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Thorsten Ahlers
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

/**
 */

/**
 * @ignore
 */
define('IN_PHPBB', true);
global $user, $auth, $request, $language, $db, $config;
$phpbb_root_path = './../../../../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);

// No ajax request, redirect to forum index
if (!$request->is_ajax())
{
	redirect(generate_board_url() . '/index.' . $phpEx);
}

// No user logged in, redirect in js to login page
if ($user->data['user_id'] == ANONYMOUS)
{
	// The flag E_USER_WARNING trigger a redirect to the login page
	trigger_error('LOGIN_REQUIRED', E_USER_WARNING);
}

// Add language file
$language->add_lang('attachment', 'imcger/imgupload');

$data = $request->variable('data', '');

if (!$data)
{
	trigger_error($language->lang('IUL_NO_DATA_SEND'), E_USER_NOTICE);
}

list($img_attach_id, $img_rotate_deg) = explode(';', $data);

if (!$img_attach_id || !$img_rotate_deg)
{
	trigger_error($language->lang('IUL_WRONG_PARAM'), E_USER_NOTICE);
}

if ($auth->acl_gets('u_attach', 'a_attach', 'f_attach'))
{
	$sql = 'SELECT *
			FROM ' . ATTACHMENTS_TABLE . '
			WHERE attach_id = ' . (int) $img_attach_id;

	$result	  = $db->sql_query($sql);
	$img_data = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);
}

if (!isset($img_data) || $img_data == false)
{
	trigger_error($language->lang('IUL_NO_IMG_IN_DATABASE'), E_USER_NOTICE);
}

// Get image file path
$image_file_path = $phpbb_root_path . trim($config['upload_path'], '/') . '/' . $img_data['physical_filename'];
$thumb_file_path = $phpbb_root_path . trim($config['upload_path'], '/') . '/' . 'thumb_' . $img_data['physical_filename'];

if (file_exists($image_file_path))
{
	$img_data['filesize'] = rotate_image($image_file_path, $img_rotate_deg);
}
else
{
	trigger_error($language->lang('IUL_IMG_NOT_EXIST'), E_USER_NOTICE);
}

if ($img_data['thumbnail'] && file_exists($thumb_file_path))
{
	rotate_image($thumb_file_path, $img_rotate_deg);
}
else if ($img_data['thumbnail'])
{
	trigger_error($language->lang('IUL_THUMB_NOT_EXIST'), E_USER_NOTICE);
}

// Update DataBase
unset($img_data['attach_id']);
$sql = 'INSERT INTO ' . ATTACHMENTS_TABLE . ' ' . $db->sql_build_array('INSERT', $img_data);
$db->sql_query($sql);

// sql_nextid() to be removed in 4.1.0-a1, in future use sql_last_inserted_id() it exsits since 3.3.11-RC1
$new_attachID = $db->sql_nextid();

if ($new_attachID)
{
	$sql = 'DELETE FROM ' . ATTACHMENTS_TABLE . ' WHERE attach_id = ' . (int) $img_attach_id;
	$db->sql_query($sql);

	trigger_error('UPDATE-ROW_' . $img_attach_id . '_' . $new_attachID, E_USER_NOTICE);
}
else
{
	trigger_error($language->lang('IUL_DATABASE_NOT_UPDATE'), E_USER_NOTICE);
}



/**
 * Rotate Image with ImageMagick
 *
 * @param 	string	$path	Path to the image file
 * @param	int		$deg	Rotation angle
 *
 * @return	int		Image	file size
 */
function rotate_image($path, $deg)
{
	$image = new \Imagick($path);
	$image->rotateImage('#000', $deg);
	$image->writeImage($path);
	$filesize = strlen($image->getImageBlob());
	$image->clear();

	return $filesize;
}
