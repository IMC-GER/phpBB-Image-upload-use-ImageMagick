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

	static public function depends_on()
	{
		return array('\imcger\externallinks\migrations\install_acp_module');
	}

	public function update_data()
	{
		global $config;
		
		$max_filesize = $config['max_filesize'];
		$config->set('max_filesize', 0);
		$config->set('imcger_imgupload_max_filesize', $max_filesize);
		
		return array(
			array('config.add', array('imcger_imgupload_version', '0.0.1')),
		);
	}
}
