<?php
/**
 *
 * Images upload use ImageMagick
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2021, Thorsten Ahlers
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace imcger\imgupload\acp;

/**
 * ImageMagick Thumbnailer ACP module.
 */
class main_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	public function main($id, $mode)
	{
		global $config, $request, $template, $user;

		$user->add_lang_ext('imcger/imgupload', 'common');
		$this->tpl_name = 'acp_imgupload_body';
		$this->page_title = $user->lang('ACP_IMCGER_IMGUPLOAD_TITLE');
		add_form_key('imcger/imgupload');

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key('imcger/imgupload'))
			{
				trigger_error('FORM_INVALID', E_USER_WARNING);
			}

			$size_select	= $request->variable('size_select', 'b');
			$max_filesize	= $request->variable('imcger_imgupload_max_filesize', 0);
			$max_filesize	= ($size_select == 'kb') ? round($max_filesize * 1024) : (($size_select == 'mb') ? round($max_filesize * 1048576) : $max_filesize);

			$config->set('imcger_imgupload_tum_quality', $request->variable('imcger_imgupload_tum_quality', 80));
			$config->set('imcger_imgupload_img_quality', $request->variable('imcger_imgupload_img_quality', 80));
			$config->set('imcger_imgupload_max_width', $request->variable('imcger_imgupload_max_width', 0));
			$config->set('imcger_imgupload_max_height', $request->variable('imcger_imgupload_max_height', 0));
			$config->set('imcger_imgupload_max_filesize', $max_filesize);
			$config->set('img_strip_metadata', $request->variable('imcger_imgupload_del_exif', 0));

			trigger_error($user->lang('ACP_IMCGER_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}
		
		$filesize = get_formatted_filesize($config['imcger_imgupload_max_filesize'], false, array('mb', 'kb', 'b'));

		$template->assign_vars(array(
			'U_ACTION'				=> $this->u_action,
			'IMCGER_TUM_QUALITY'	=> $config['imcger_imgupload_tum_quality'],
			'IMCGER_IMG_QUALITY'	=> $config['imcger_imgupload_img_quality'],
			'IMCGER_MAX_WIDTH'		=> $config['imcger_imgupload_max_width'],
			'IMCGER_MAX_HEIGHT'		=> $config['imcger_imgupload_max_height'],
			'IMCGER_MAX_FILESIZE'	=> $filesize['value'],
			'IMCGER_UNIT'			=> $filesize['si_identifier'],
			'IMCGER_DEL_EXIF'		=> $config['img_strip_metadata'],
		));
	}
}
