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

class v100 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return version_compare($this->config['imcger_imgupload_version'], '1.0.0', '>=');
	}

	public static function depends_on()
	{
		return array(
			'\imcger\imgupload\migrations\install_settings',
		);
	}

	public function update_data()
	{
		return array(
			array('config.update', array('imcger_imgupload_version', '1.0.0')),
			array('config.update', array('avatar_filesize', 0)),
			array('config.add', array('imcger_imgupload_avatar_resize', 1)),
		);
	}
}
