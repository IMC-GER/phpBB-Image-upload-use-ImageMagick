<?php
/**
 * Image upload use ImageMagick
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Thorsten Ahlers
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace imcger\imgupload\core;

use Maestroerror\HeicToJpg;

if (!class_exists(HeicToJpg::class))
{
	require_once __DIR__ . '/../vendor/maestroerror/php-heic-to-jpg/src/HeicToJpg.php';
}

class iul_image_converter
{
	public function __construct()
	{
		protected \phpbb\config\config $config,
	}

	public function convert(array &$filedata): bool
	{
		// get file path
		$file_path = join('/', [trim($this->config['upload_path'], '/'), trim($filedata['physical_filename'], '/')]);


		clearstatcache();
		$filesize = @filesize($file_path);

		$filedata['mimetype']		= 'image/jpeg';
		$filedata['extension']		= 'jpg';
		$filedata['real_filename'] .= '.jpg';
		$filedata['filesize']		= $filesize;
	}

	public function heic_to_jpg(string $heic_path, string $target_path): string
	{
		if (!file_exists($heic_path))
		{
			throw new \RuntimeException('HEIC file not found: ' . $heic_path);
		}

		// Datei konvertieren und als JPG speichern
		HeicToJpg::convert ($heic_path)->saveAs($target_path);

		return $target_path;
	}

	public function is_heic(string $heic_path): bool
	{
		return HeicToJpg::isHeic($heic_path);
	}
}
