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

class v144 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return version_compare($this->config['imcger_imgupload_version'], '1.4.4', '>=');
	}

	public static function depends_on()
	{
		return ['\imcger\imgupload\migrations\v121'];
	}

	public function update_data()
	{
		return [
			['config.update', ['imcger_imgupload_version', '1.4.4']],
			['custom', [[$this, 'reset_max_filesize']]],
		];
	}

	public function reset_max_filesize()
	{
		$sql = 'UPDATE ' . EXTENSION_GROUPS_TABLE . ' SET max_filesize = 0 WHERE cat_id = 1;';
		$this->db->sql_query($sql);
	}
}
