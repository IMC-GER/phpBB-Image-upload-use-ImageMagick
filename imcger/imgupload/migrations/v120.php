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

class v120 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return version_compare($this->config['imcger_imgupload_version'], '1.2.0', '>=');
	}

	public static function depends_on()
	{
		return ['\imcger\imgupload\migrations\v100'];
	}

	public function update_data()
	{
		return [
			['config.update', ['imcger_imgupload_version', '1.2.0']],
			['config.add', ['imcger_imgupload_image_inline', 0]],
			['config.add', ['imcger_imgupload_image_inline_maxwidth', 0]],
		];
	}
}
