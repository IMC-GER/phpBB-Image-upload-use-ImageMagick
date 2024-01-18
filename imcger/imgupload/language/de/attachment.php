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
	'IUL_PLACE_INLINE'		 => 'Vorschaubild im Beitrag anzeigen',
	'IUL_IMAGE_PLACE_INLINE' => 'Bild im Beitrag anzeigen',

	// Confirmbox
	'IUL_CONFIRMBOX_TEXT1'	 => 'Folgende Bilder wurden gedreht, jedoch nicht gespeichert.',
	'IUL_CONFIRMBOX_TEXT2'	 => 'Möchtest du fortfahren ohne die Drehung der Bilder zu speichern?',

	// Errors from XMLHttpRequest
	'IUL_REQUEST_ERROR'		  => 'Bei der Anfrage ist ein Fehler aufgetreten.',
	'IUL_WRONG_PARAM'		  => 'Falsche Parameter gesendet.',
	'IUL_NO_IMG_IN_DATABASE'  => 'Das Bild wurde nicht in der Datenbank gefunden.',
	'IUL_IMG_NOT_EXIST'		  => 'Die Bilddatei existiert nicht.',
	'IUL_THUMB_NOT_EXIST'	  => 'Das erwartete Vorschaubild existiert nicht.',
	'IUL_DATABASE_NOT_UPDATE' => 'Die Datenbank konnte nicht aktualisiert werden.',
]);
