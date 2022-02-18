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

	'ACP_IMCGER_SETTINGS_SAVED'		=> 'Einstellungen wurden erfolgreich gespeichert.',

	'ACP_IMCGER_IMGUPLOAD_TITLE'	=> 'Image upload use ImageMagick',
	'ACP_IMCGER_SETTINGS'			=> 'Einstellungen',

	'ACP_IMCGER_THUMB_QUALITY'		=> 'Vorschaubilder Komprimierungsqualität',
	'ACP_IMCGER_THUMB_QUALITY_DESC'	=> 'Mit dieser Einstellung wird die Komprimierungsqualität für die generierten Vorschaubilder eingestellen. Die beste Anzeigequalität wir mit der Einstellung 100% erreicht. Durch niedrigere Werte werden kleinere Dateien erzeugt.',

	'ACP_IMCGER_IMAGE_QUALITY'		=> 'Bild Komprimierungsqualität',
	'ACP_IMCGER_IMAGE_QUALITY_DESC'	=> 'Mit dieser Einstellung wird die Komprimierungsqualität für das größenveränderte Bild eingestellen. Die beste Anzeigequalität wir mit der Einstellung 100% erreicht. Durch niedrigere Werte werden kleinere Dateien erzeugt.',

	'ACP_IMCGER_MAX_WIDTH'			=> 'Maximale Bildbreite',
	'ACP_IMCGER_MAX_WIDTH_DESC'		=> 'Einstellung der maximalen Bildbreite in Pixel. Breite Bilder werden verkleinert und die Höhe proportional angepasst. Bei der Angabe von 0 wird keine Veränderung durchgeführt.',

	'ACP_IMCGER_MAX_HEIGTH'			=> 'Maximale Bildhöhe',
	'ACP_IMCGER_MAX_HEIGTH_DESC'	=> 'Einstellung der maximalen Bildhöhe in Pixel. Höhere Bilder werden verkleinert und die Breite proportional angepasst. Bei der Angabe von 0 wird keine Veränderung durchgeführt.',

	'MAX_EXTGROUP_FILESIZE'			=> 'Maximale Dateigröße',
	'MAX_EXTGROUP_FILESIZE_DESC'	=> 'Einstellung der maximalen Dateigröße in Byte. Bei der Angabe von 0 findet keine Größenbeschränkung statt. Überschreitet die Größe der Bilddatei den eingestellten Wert wird diese angenähert klein gerechnet.',

	'ACP_IMCGER_DEL_EXIF'			=> 'Bildeigenschaften entfernen',
	'ACP_IMCGER_DEL_EXIF_DESC'		=> 'Kameras speichern in Bildern zusätzliche Informationen wie z.B. Geodaten. Diese können zum Datenschutz entfernt werden.',
));
