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

namespace imcger\imgupload\migrations;

class install_settings extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['imcger_imgupload_version']);
	}

	public static function depends_on()
	{
		return ['\imcger\imgupload\migrations\install_acp_module'];
	}

	public function update_data()
	{
		return [
			['config.update', ['max_filesize', 0]],
			['config.update', ['img_max_width', 0]],
			['config.update', ['img_max_height', 0]],

			['config.add', ['imcger_imgupload_version', '0.1.0']],
		];
	}
}
