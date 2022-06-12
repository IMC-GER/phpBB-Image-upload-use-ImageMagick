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

namespace imcger\imgupload;

/**
 * Extension base
 */
class ext extends \phpbb\extension\base
{
	public function is_enableable()
	{
		$language = $this->container->get('language');
		$language->add_lang('imgupload_acp', 'imcger/imgupload');
		$error_message = [];

		/* Imagick library installed? */
		if (!class_exists('Imagick'))
		{
			$error_message += ['error1' => $language->lang('IMCGER_IM_REQUIRE_IMAGICK')];
		}

		/* phpBB version greater equal 3.2.4 */
		$config = $this->container->get('config');
		if (!phpbb_version_compare($config['version'], '3.2.4', '>='))
		{
			$error_message += ['error2' => $language->lang('IMCGER_IM_REQUIRE_324')];
		}

		return empty($error_message) ? true : $error_message;
	}
}
