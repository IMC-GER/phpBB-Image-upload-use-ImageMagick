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

class v160 extends \phpbb\db\migration\migration
{
	public function effectively_installed(): bool
	{
		$sql		= 'SELECT 1 as heic_exist FROM ' . EXTENSIONS_TABLE . ' WHERE extension = "heic"';
		$result		= $this->db->sql_query_limit($sql, 1);
		$heic_exist	= $this->db->sql_fetchfield('heic_exist');
		$this->db->sql_freeresult($result);

		return $heic_exist;
	}

	public static function depends_on(): array
	{
		return ['\imcger\imgupload\migrations\v144'];
	}

	public function update_data(): array
	{
		return [
			['config.remove', ['imcger_imgupload_version']],
			['custom', [[$this, 'set_extension_group']]],
		];
	}

	public function revert_data()
	{
		return [
			['config.add', ['imcger_imgupload_version', '1.4.4']],
			['custom', [[$this, 'del_extension_group']]],
		];
	}

	public function set_extension_group(): void
	{
		$sql	= 'SELECT * FROM ' . EXTENSION_GROUPS_TABLE . ' WHERE cat_id = "1"';
		$result	= $this->db->sql_query_limit($sql, 1);
		$row	= $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		unset($row['group_id']);
		$row['cat_id']	   = 0;
		$row['group_name'] = 'IUL_IMAGES';

		// Insert new group
		$sql = 'INSERT INTO ' . EXTENSION_GROUPS_TABLE . ' ' .
				$this->db->sql_build_array('INSERT', $row);
		$this->db->sql_query($sql);

		$group_id = $this->db->sql_last_inserted_id();

		// Add extension heic
		$ext = ['extension' => 'heic', 'group_id' => $group_id];
		$sql = 'INSERT INTO ' . EXTENSIONS_TABLE . ' ' .
				$this->db->sql_build_array('INSERT', $ext);
		$this->db->sql_query($sql);
	}

	public function del_extension_group(): void
	{
		$sql = 'DELETE FROM ' . EXTENSIONS_TABLE . ' WHERE extension = \'heic\'';
		$this->db->sql_query($sql);

		$sql = 'DELETE FROM ' . EXTENSION_GROUPS_TABLE . ' WHERE group_name = \'IUL_IMAGES\'';
		$this->db->sql_query($sql);
	}
}
