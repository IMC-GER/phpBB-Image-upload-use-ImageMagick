<?php
/**
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
	// Post editor settings
	'IUL_PLACE_INLINE'		 => 'Place thumbnail inline',
	'IUL_IMAGE_PLACE_INLINE' => 'Place image inline',

	// Confirmbox
	'IUL_CONFIRMBOX_TEXT1'	 => 'The following images were rotated but not saved.',
	'IUL_CONFIRMBOX_TEXT2'	 => 'Do you want to continue without saving the rotation of the images?',

	// Errors from XMLHttpRequest
	'IUL_REQUEST_ERROR'		  => 'The request encountered an error.',
	'IUL_WRONG_PARAM'		  => 'Wrong parameter.',
	'IUL_NO_IMG_IN_DATABASE'  => 'Image not found in DataBase.',
	'IUL_IMG_NOT_EXIST'		  => 'The image file does not exist.',
	'IUL_THUMB_NOT_EXIST'	  => 'The thumbnail file does not exist.',
	'IUL_DATABASE_NOT_UPDATE' => 'Database cannot be updated.',
]);
