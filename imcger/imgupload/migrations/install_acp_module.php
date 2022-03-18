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

	public static function depends_on()
	{
		return array('\phpbb\db\migration\data\v32x\v324');
	}

	public function update_data()
	{
		global $config;

		$img_quality	= $config['img_quality'];
		$max_filesize	= $config['max_filesize'];
		$img_max_width	= $config['img_max_width'];
		$img_max_height	= $config['img_max_height'];

		return array(
			array('config.add', array('imcger_imgupload_tum_quality', $img_quality)),
			array('config.add', array('imcger_imgupload_img_quality', $img_quality)),
			array('config.add', array('imcger_imgupload_max_width', $img_max_width)),
			array('config.add', array('imcger_imgupload_max_height', $img_max_height)),
			array('config.add', array('imcger_imgupload_max_filesize', $max_filesize)),

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
