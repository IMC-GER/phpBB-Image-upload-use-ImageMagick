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
	$lang = [];
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

$lang = array_merge($lang, [

	// Language pack author
	'ACP_IMCGER_LANG_DESC'			=> 'British English',
	'ACP_IMCGER_LANG_EXT_VER' 		=> '1.2.1',
	'ACP_IMCGER_LANG_AUTHOR' 		=> 'IMC-Ger',

	// Messages
	'ACP_IMCGER_SETTINGS_SAVED'		=> 'Settings have been saved successfully.',

	// ACP settings
	'ACP_IMCGER_IMGUPLOAD_TITLE'	=> 'Image upload use ImageMagick',
	'ACP_IMCGER_IMGUPLOAD_DESC'		=> 'The extension uses the PHP Imagick class to modify upload​ed image​s. If the values set in the ACP are exceeded by the image file, the image ​is being ​resized by the extension.<br>The extension sup​port​s JPEG, WEBP, GIF and PNG images. Other image formats, for example BMP, ​are ​convert​ed​ to JPEG when resize​d.<br>This extension can change the image size and/or the image file size. It rotate images, thumbnails and avatars according to their EXIF information and it can remove the EXIF data from JPEG and WEBP files.',

	// Attachment settings
	'ACP_IMCGER_THUMB_QUALITY'			=> 'Thumbnail compression quality',
	'ACP_IMCGER_THUMB_QUALITY_DESC'		=> 'Specify value between 50% (smaller file size) and 90% (higher quality). Quality higher than 90% increases filesize and is disabled. Lower values will generate smaller files.',
	'ACP_IMCGER_IMAGE_INLINE'			=> 'Insert full size image',
	'ACP_IMCGER_IMAGE_INLINE_DESC'		=> 'Image attachment can be inserted with the BBCode "[img]url[/img]" in the post editor using a button. The selection "Create thumbnail" must be activated.',
	'ACP_IMCGER_IMG_MAX_THUMB_WIDTH'	  => 'Maximum width/height of thumbnails:',
	'ACP_IMCGER_IMG_MAX_THUMB_WIDTH_DESC' => 'Maximum width/height in pixels with which images are displayed in the file attachment box. If 0 is entered, the size is not changed.',

	// Image settings
	'ACP_IMCGER_SETTINGS_IMAGE'			=> 'Image Settings',
	'ACP_IMCGER_IMAGE_QUALITY'			=> 'Image compression quality',
	'ACP_IMCGER_IMAGE_QUALITY_DESC'		=> 'Specify value between 50% (smaller file size) and 90% (higher quality). Quality higher than 90% increases filesize and is disabled. Lower values will generate smaller files.',
	'ACP_IMCGER_MAX_SIZE'				=> 'Maximum image size',
	'ACP_IMCGER_MAX_SIZE_DESC'			=> 'Setting the maximum image size in pixels. The images are reduced to the maximum width or height. The other side is adjusted proportionally. If 0px by 0px is specified, the image size will not be changed.',
	'ACP_IMCGER_MAX_FILESIZE'			=> 'Maximum file size',
	'ACP_IMCGER_MAX_FILESIZE_DESC'		=> 'Setting of the maximum file size in bytes. If 0 is specified, there is no size limitation. If the size of the image file exceeds the set value, it will be reduced approximately.',
	'ACP_IMCGER_DEL_EXIF'				=> 'Strip image metadata  (JPEG & WEBP)',
	'ACP_IMCGER_DEL_EXIF_DESC'			=> 'Strip Exif metadata, e.g. author name, GPS coordinates and camera details.',

	// Avatar settings
	'ACP_IMCGER_SETTINGS_AVATAR'		=> 'Avatar Settings',
	'ACP_IMCGER_AVATAR_RESIZE'			=> 'Resize avatar',
	'ACP_IMCGER_AVATAR_RESIZE_DESC'		=> 'The image file of the avatar will be automatically resize during the upload.',
	'ACP_IMCGER_AVATAR_FILESIZE_ISSET'	=> 'For this function the "Maximum avatar file size" must be set to 0.',
]);
