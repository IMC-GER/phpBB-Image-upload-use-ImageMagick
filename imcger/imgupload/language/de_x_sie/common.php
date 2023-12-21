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
	'ACP_IMCGER_LANG_DESC'			=> 'Deutsch (Sie)',
	'ACP_IMCGER_LANG_EXT_VER' 		=> '1.4.0',
	'ACP_IMCGER_LANG_AUTHOR' 		=> 'IMC-Ger',

	// Messages
	'ACP_IMCGER_SETTINGS_SAVED'		=> 'Einstellungen wurden erfolgreich gespeichert.',

	// ACP settings
	'ACP_IMCGER_IMGUPLOAD_TITLE'	=> 'Image upload use ImageMagick',
	'ACP_IMCGER_IMGUPLOAD_DESC'		=> 'Die Erweiterung verwendet die PHP Imagick Klasse, um hochgeladene Bilder, Thumbnails und Avatars zu verändern. Wenn die im ACP eingestellten Werte von der Bilddatei überschritten werden, wird die Größe des Bildes von der Erweiterung angepasst. Die Erweiterung unterstützt JPEG, WEBP, GIF und PNG Bilder. Andere Bildformate, z. B. BMP, werden beim Ändern der Größe in JPEG umgewandelt. Diese Erweiterung kann die Bildgröße und/oder die Größe der Bilddatei ändern. Es dreht Bilder, Thumbnails und Avatare entsprechend ihrer EXIF Informationen und kann die EXIF Daten aus JPEG und WEBP Dateien entfernen.',

	// Attachment settings
	'ACP_IMCGER_THUMB_QUALITY'			=> 'Vorschaubilder Komprimierungsqualität',
	'ACP_IMCGER_THUMB_QUALITY_DESC'		=> 'Legen sie einen Wert zwischen 50% (kleinere Dateigröße) und 90% (höhere Qualität) fest. Werte größer als 90% erhöhen die Dateigröße und sind daher deaktiviert. Durch niedrigere Werte werden kleinere Dateien erzeugt.',
	'ACP_IMCGER_IMAGE_INLINE'			=> 'Bild einfügen',
	'ACP_IMCGER_IMAGE_INLINE_DESC'		=> 'Der Bildanhang kann mit dem BBCode „[img]url[/img]“ in dem Beitragseditor mittels eines Button eingefügt werden. Die Auswahl „Vorschaubild erstellen“ muss aktiviert sein.',
	'ACP_IMCGER_IMG_MAX_THUMB_WIDTH'	  => 'Maximale Breite/Höhe der Vorschaubilder',
	'ACP_IMCGER_IMG_MAX_THUMB_WIDTH_DESC' => 'Maximale Breite/Höhe in Pixel, mit der Bilder in der Box für Dateianhänge angezeigt werden. Bei der Eingabe von 0 wird die Größe nicht verändert.',

	// Image settings
	'ACP_IMCGER_SETTINGS_IMAGE'			=> 'Bild Einstellungen',
	'ACP_IMCGER_IMAGE_QUALITY'			=> 'Bild Komprimierungsqualität',
	'ACP_IMCGER_IMAGE_QUALITY_DESC'		=> 'Legen sie einen Wert zwischen 50% (kleinere Dateigröße) und 90% (höhere Qualität) fest. Werte größer als 90% erhöhen die Dateigröße und sind daher deaktiviert. Durch niedrigere Werte werden kleinere Dateien erzeugt.',
	'ACP_IMCGER_MAX_SIZE'				=> 'Maximale Bildgröße',
	'ACP_IMCGER_MAX_SIZE_DESC'			=> 'Einstellung der maximalen Bildgröße in Pixel. Die Bilder werden auf die maximale Breite bzw. Höhe verkleinert. Die jeweils andere Seite wird proportional angepasst. Bei der Angabe von 0px x 0px wird keine Veränderung der Bildgröße durchgeführt.',
	'ACP_IMCGER_MAX_FILESIZE'			=> 'Maximale Dateigröße',
	'ACP_IMCGER_MAX_FILESIZE_DESC'		=> 'Einstellung der maximalen Dateigröße in Byte. Bei der Angabe von 0 findet keine Größenbeschränkung statt. Überschreitet die Größe der Bilddatei den eingestellten Wert wird diese angenähert verkleinert.',
	'ACP_IMCGER_DEL_EXIF'				=> 'Entfernt Metadaten (JPEG & WEBP)',
	'ACP_IMCGER_DEL_EXIF_DESC'			=> 'Entfernt die Exif-Metadaten wie Name des Autors, GPS-Koordinaten und Kamera-Details.',

	// Avatar settings
	'ACP_IMCGER_SETTINGS_AVATAR'		=> 'Avatar Einstellungen',
	'ACP_IMCGER_AVATAR_RESIZE'			=> 'Avatar Größe anpassen',
	'ACP_IMCGER_AVATAR_RESIZE_DESC'		=> 'Die Bilddatei des Avatar wird beim Hochladen automatisch verkleinert.',
	'ACP_IMCGER_AVATAR_FILESIZE_ISSET'	=> 'Für diese Funktion muss die "Maximale Dateigröße" des Avatars auf 0 gesetzt werden.',
]);
