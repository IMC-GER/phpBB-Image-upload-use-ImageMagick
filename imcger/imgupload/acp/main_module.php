<?php
/**
 *
 * Images upload use ImageMagick
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Thorsten Ahlers
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace imcger\imgupload\acp;

/**
 * ImageMagick ACP module.
 */
class main_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	public function main($id, $mode)
	{
		global $phpbb_container;

		/* Add ACP lang file */
		$language = $phpbb_container->get('language');

		/* Get an instance of the admin controller */
		$admin_controller = $phpbb_container->get('imcger.imgupload.admin.controller');

		/* Make the $u_action url available in the admin controller */
		$admin_controller->set_page_url($this->u_action);

		switch ($mode)
		{
			case 'settings':
				/* Load a template from adm/style for our ACP page */
				$this->tpl_name = 'acp_imgupload_body';

				/* Set the page title for our ACP page */
				$this->page_title = $language->lang('ACP_IMCGER_IMGUPLOAD_TITLE');

				/* Load the display options handle in the admin controller */
				$admin_controller->display_options();
			break;
		}
	}
}
