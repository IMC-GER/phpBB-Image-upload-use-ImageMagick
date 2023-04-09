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

namespace imcger\imgupload\controller;

class admin_controller
{
	/** @var config */
	protected $config;

	/** @var template */
	protected $template;

	/** @var language */
	protected $language;

	/** @var request */
	protected $request;

	/** @var \phpbb\extension\manager */
	protected $ext_manager;

	/** @var string Custom form action */
	protected $u_action;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config		$config
	 * @param \phpbb\template\template	$template
	 * @param \phpbb\language\language	$language
	 * @param \phpbb\request\request	$request
	 * @param \phpbb\extension\manager	$ext_manager
	 *
	 */
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\template\template $template,
		\phpbb\language\language $language,
		\phpbb\request\request $request,
		\phpbb\extension\manager $ext_manager
	)
	{
		$this->config		= $config;
		$this->template		= $template;
		$this->language		= $language;
		$this->request		= $request;
		$this->ext_manager	= $ext_manager;
	}

	/**
	 * Display the options a user can configure for this extension
	 *
	 * @return null
	 * @access public
	 */
	public function display_options()
	{
		// Add ACP lang file
		$this->language->add_lang('common', 'imcger/imgupload');
		$this->language->add_lang('acp/attachments');

		add_form_key('imcger/imgupload');

		// Is the form being submitted to us?
		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('imcger/imgupload'))
			{
				trigger_error($this->language->lang('FORM_INVALID') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			// Store the variable to the db
			$this->set_variable();

			trigger_error($this->language->lang('ACP_IMCGER_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}

		$metadata_manager = $this->ext_manager->create_extension_metadata_manager('imcger/imgupload');

		$filesize = get_formatted_filesize($this->config['imcger_imgupload_max_filesize'], false, array('mb', 'kb', 'b'));

		$this->template->assign_vars([
			'U_ACTION'				=> $this->u_action,
			'IMGUPLOAD_TITLE'		=> $metadata_manager->get_metadata('display-name'),
			'IMGUPLOAD_EXT_VER'		=> $metadata_manager->get_metadata('version'),
			'IMCGER_TUM_QUALITY'	=> $this->config['imcger_imgupload_tum_quality'],
			'IMCGER_IMG_QUALITY'	=> $this->config['imcger_imgupload_img_quality'],
			'IMCGER_MAX_WIDTH'		=> $this->config['imcger_imgupload_max_width'],
			'IMCGER_MAX_HEIGHT'		=> $this->config['imcger_imgupload_max_height'],
			'IMCGER_MAX_FILESIZE'	=> $filesize['value'],
			'IMCGER_UNIT'			=> $filesize['si_identifier'],
			'IMCGER_DEL_EXIF'		=> (bool) $this->config['img_strip_metadata'],
			'IMCGER_AVATAR_RESIZE'			=> (bool) $this->config['imcger_imgupload_avatar_resize'],
			'IMCGER_AVATAR_FILESIZE_ISSET'	=> (bool) $this->config['avatar_filesize'],
			'CREATE_THUMBNAIL'				=> (bool) $this->config['img_create_thumbnail'],
			'IMCGER_IMGUPLOAD_IMAGE_INLINE'	=> (bool) $this->config['imcger_imgupload_image_inline'],
			'IMCGER_IMAGE_INLINE_MAXWIDTH'	=>  $this->config['imcger_imgupload_image_inline_maxwidth'],
		]);
	}

	/**
	 * Store the variable to the db
	 *
	 * @return null
	 * @access protected
	 */
	protected function set_variable()
	{
		$size_select  = $this->request->variable('size_select', 'b');
		$max_filesize = $this->request->variable('imcger_imgupload_max_filesize', 0);
		$max_filesize = ($size_select == 'kb') ? round($max_filesize * 1024) : (($size_select == 'mb') ? round($max_filesize * 1048576) : $max_filesize);

		$this->config->set('imcger_imgupload_tum_quality', $this->request->variable('imcger_imgupload_tum_quality', 80));
		$this->config->set('imcger_imgupload_img_quality', $this->request->variable('imcger_imgupload_img_quality', 80));
		$this->config->set('imcger_imgupload_max_width', $this->request->variable('imcger_imgupload_max_width', 0));
		$this->config->set('imcger_imgupload_max_height', $this->request->variable('imcger_imgupload_max_height', 0));
		$this->config->set('imcger_imgupload_max_filesize', $max_filesize);
		$this->config->set('img_strip_metadata', $this->request->variable('imcger_imgupload_del_exif', 0));
		$this->config->set('img_create_thumbnail', $this->request->variable('img_create_thumbnail', 0));
		$this->config->set('imcger_imgupload_image_inline', $this->request->variable('imcger_imgupload_image_inline', 0));
		$this->config->set('imcger_imgupload_image_inline_maxwidth', $this->request->variable('imcger_imgupload_image_inline_maxwidth', 0));
	}

	/**
	 * Set page url
	 *
	 * @param string $u_action Custom form action
	 * @return null
	 * @access public
	 */
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
