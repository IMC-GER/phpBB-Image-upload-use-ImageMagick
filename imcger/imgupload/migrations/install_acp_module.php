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

class install_acp_module extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['imcger_imgupload_tum_quality']);
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v32x\v324');
	}

	public function update_data()
	{		
		return array(
			array('config.add', array('imcger_imgupload_tum_quality', 80)),
			array('config.add', array('imcger_imgupload_img_quality', 80)),
			array('config.add', array('imcger_imgupload_max_width', 0)),
			array('config.add', array('imcger_imgupload_max_height', 0)),
			array('config.add', array('imcger_imgupload_max_filesize', 0)),
			array('config.add', array('imcger_imgupload_del_exif', 0)),

			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_IMCGER_IMGUPLOAD_TITLE'
			)),
			array('module.add', array(
				'acp',
				'ACP_IMCGER_IMGUPLOAD_TITLE',
				array(
					'module_basename'	=> '\imcger\imgupload\acp\main_module',
					'modes'				=> array('settings'),
				),
			)),
		);
	}
}
