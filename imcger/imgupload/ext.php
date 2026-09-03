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

/**
 * Extension base
 */
class ext extends \phpbb\extension\base
{
	/**
	 * Check the minimum and maximum requirements.
	 *
	 * The return value is a union type array|bool
	 * Not specified for reasons of compatibility with php 7
	 */
	public function is_enableable()
	{
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

		// HEIC file extension defined?
		if ($this->is_extension_defined())
		{
			$error_message[] = $language->lang('IMCGER_REQUIRE_EXT_DEF', 'heic', $language->lang('ACP_IMCGER_IMGUPLOAD_TITLE'));
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

	public function is_extension_defined()
	{
		$db = $this->container->get('dbal.conn');

		$sql_array = [
			'SELECT'	=> '1 as heic_exist',
			'FROM'		=> [EXTENSIONS_TABLE => 'e',	],
			'LEFT_JOIN' => [
				[
					'FROM' => [EXTENSION_GROUPS_TABLE => 'eg', ],
					'ON'   => 'eg.group_name = \'IUL_IMAGES\'',
				],
			],
			'WHERE'		=> '(eg.group_id IS NULL OR eg.group_id <> e.group_id)
						AND LOWER(e.extension) = \'heic\'',
		];

		$sql		= $db->sql_build_query('SELECT', $sql_array);
		$result		= $db->sql_query_limit($sql, 1);
		$heic_exist	= $db->sql_fetchfield('heic_exist');
		$db->sql_freeresult($result);

		return $heic_exist;
	}
}
