<?php
/**
 *
 * Image upload use ImageMagick
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Thorsten Ahlers
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(

	'ACP_IMCGER_SETTINGS_SAVED'		=> 'Settings have been saved successfully.',

	'ACP_IMCGER_IMGUPLOAD_TITLE'	=> 'Image upload use ImageMagick',
	'ACP_IMCGER_SETTINGS'			=> 'Einstellungen',

	'ACP_IMCGER_THUMB_QUALITY'		=> 'Thumbnail compression quality (JPEG only)',
	'ACP_IMCGER_THUMB_QUALITY_DESC'	=> 'This setting adjusts the compression quality for the generated thumbnails. The best display quality is achieved with the setting 100%. Lower values will generate smaller files.',

	'ACP_IMCGER_IMAGE_QUALITY'		=> 'Image compression quality (JPEG only)',
	'ACP_IMCGER_IMAGE_QUALITY_DESC'	=> 'This setting adjusts the compression quality for the resized image. The best display quality is achieved with the setting 100%. Lower values will generate smaller files.',

	'ACP_IMCGER_MAX_WIDTH'			=> 'Maximum image width',
	'ACP_IMCGER_MAX_WIDTH_DESC'		=> 'Setting the maximum image width in pixels. Wide images are reduced in size and the height is adjusted proportionally. If 0 is specified, no change is made.',

	'ACP_IMCGER_MAX_HEIGTH'			=> 'Maximum image height',
	'ACP_IMCGER_MAX_HEIGTH_DESC'	=> 'Setting of the maximum image height in pixels. Higher images are reduced in size and the width is adjusted proportionally. If 0 is specified, no change is made.',

	'ACP_IMCGER_MAX_FILESIZE'		=> 'Maximum file size',
	'ACP_IMCGER_MAX_FILESIZE_DESC'	=> 'Setting of the maximum file size in bytes. If 0 is specified, there is no size limitation. If the size of the image file exceeds the set value, it will be reduced approximately.',

	'ACP_IMCGER_DEL_EXIF'			=> 'Remove image properties',
	'ACP_IMCGER_DEL_EXIF_DESC'		=> 'Cameras store additional information in images, such as geodata. These can be removed for data protection.',
));
