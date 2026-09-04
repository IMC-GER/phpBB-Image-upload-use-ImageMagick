<?php
/**
 * Image upload use ImageMagick
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Thorsten Ahlers
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace imcger\imgupload;

class ext extends \phpbb\extension\base
{
	public function is_enableable()
	{
		// If phpBB version 3.2 or less cancel
		if (phpbb_version_compare(PHPBB_VERSION, '3.3.0', '<'))
		{
			return false;
		}

		$language = $this->container->get('language');
		$language->add_lang('info_acp_imgupload', 'imcger/imgupload');

		$error_message 	= [];

		// Imagick library installed?
		if (!class_exists('Imagick'))
		{
			$error_message[] = $language->lang('IMCGER_REQUIRE_IMAGICK');
		}

		// Check php and phpBB extension requirments
		$ext_requirements = new \imcger\imgupload\core\imcger_ext_requirements($this->extension_name);
		$requirements	  = $ext_requirements->check();

		if ($requirements !== true)
		{
			$error_message = array_merge($error_message, $requirements);
		}

		return empty($error_message) ? true : $error_message;
	}
}
