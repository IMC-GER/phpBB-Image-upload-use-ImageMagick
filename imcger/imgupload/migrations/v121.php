<?php
/**
 * Image upload use ImageMagick
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Thorsten Ahlers
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace imcger\imgupload\migrations;

class v121 extends \phpbb\db\migration\migration
{
	public function effectively_installed(): bool
	{
		return version_compare($this->config['imcger_imgupload_version'], '1.2.1', '>=');
	}

	public static function depends_on(): array
	{
		return ['\imcger\imgupload\migrations\v120'];
	}

	public function update_data(): array
	{
		return [
			['config.update', ['imcger_imgupload_version', '1.2.1']],
			['config.remove', ['imcger_imgupload_image_inline_maxwidth', 0]],
			['config.add', ['imcger_imgupload_img_max_thumb_width', $this->config['img_max_thumb_width']]],
		];
	}
}
