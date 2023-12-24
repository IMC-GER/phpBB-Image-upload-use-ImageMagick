<?php
/**
 * Image upload use ImageMagick
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Thorsten Ahlers
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace imcger\imgupload\acp;

/**
 * ImageMagick ACP module info.
 */
class main_info
{
	public function module()
	{
		return [
			'filename'	=> '\imcger\imgupload\acp\main_module',
			'title'		=> 'ACP_IMCGER_IMGUPLOAD_TITLE',
			'modes'		=> [
				'settings'	=> [
					'title'	=> 'ACP_IMCGER_SETTINGS',
					'auth'	=> 'ext_imcger/imgupload && acl_a_board',
					'cat'	=> ['ACP_IMCGER_IMGUPLOAD_TITLE']
				],
			],
		];
	}
}
