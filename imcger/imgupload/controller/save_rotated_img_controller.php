<?php
/**
 * Image upload use ImageMagick
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Thorsten Ahlers
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

namespace imcger\imgupload\controller;

/**
 * @ignore
 */

/**
 * Main controller
 */
class save_rotated_img_controller
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\request\request */
	private $request;

	/** @var \phpbb\db\driver\driver_interface */
	private $db;

	/** @var \phpbb\auth\auth */
	private $auth;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\extension\manager */
	protected $ext_manager;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/**
	 * @param \phpbb\config\config				$config
	 * @param \phpbb\user						$user
	 * @param \phpbb\request\request			$request
	 * @param \phpbb\db\driver\driver_interface	$db
	 * @param \phpbb\auth\auth					$auth
	 * @param \phpbb\language\language			$language
	 * @param \phpbb\extension\manager			$ext_manager
	 * @param string							$root_path
	 * @param string							$php_ext
	 */
	public function __construct(
		\phpbb\config\config $config,
		\phpbb\user $user,
		\phpbb\request\request $request,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\auth\auth $auth,
		\phpbb\language\language $language,
		\phpbb\extension\manager $ext_manager,
		$root_path,
		$php_ext
	)
	{
		$this->config		= $config;
		$this->user			= $user;
		$this->request		= $request;
		$this->db			= $db;
		$this->auth			= $auth;
		$this->language		= $language;
		$this->ext_manager	= $ext_manager;
		$this->root_path	= $root_path;
		$this->php_ext		= $php_ext;

		// Add language file
		$this->language->add_lang('attachment', 'imcger/imgupload');
	}

	/**
	 * Rotate Image with ImageMagick
	 *
	 * @var 	int		attach_id		contain attach id
	 * @var 	int		img_rotate_deg	contain rotate degree
	 *
	 * @return	array	Json arry with status, old and new attach id or error message
	 */
	public function save_image()
	{
		// No ajax request, redirect to forum index
		if (!$this->request->is_ajax())
		{
			redirect($this->root_path . '/index.' . $this->php_ext);
		}

		// No user logged in, redirect in js to login page
		if ($this->user->data['user_id'] == ANONYMOUS)
		{
			$this->json_response(3);
		}

		// Get name of the extension
		$metadata_manager = $this->ext_manager->create_extension_metadata_manager('imcger/imgupload');
		$ext_display_name = $metadata_manager->get_metadata('display-name');

		// Check form token
		if (!check_form_key('posting'))
		{
			$this->json_response(5, $ext_display_name, $this->language->lang('FORM_INVALID'));
		}

		// Get variable
		$img_attach_id	= intval($this->request->variable('attach_id', ''));
		$img_rotate_deg	= intval($this->request->variable('img_rotate_deg', ''));

		if (!$img_attach_id || !$img_rotate_deg)
		{
			$this->json_response(5, $ext_display_name, $this->language->lang('IUL_WRONG_PARAM'));
		}

		if ($this->auth->acl_gets('u_attach', 'a_attach', 'f_attach'))
		{
			$sql = 'SELECT *
					FROM ' . ATTACHMENTS_TABLE . '
					WHERE attach_id = ' . (int) $img_attach_id;

			$result	  = $this->db->sql_query($sql);
			$img_data = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);
		}

		if (!isset($img_data) || $img_data == false)
		{
			$this->json_response(4, $ext_display_name, $this->language->lang('IUL_NO_IMG_IN_DATABASE'));
		}

		// Get image file path
		$image_file_path = $this->root_path . trim($this->config['upload_path'], '/') . '/' . $img_data['physical_filename'];
		$thumb_file_path = $this->root_path . trim($this->config['upload_path'], '/') . '/' . 'thumb_' . $img_data['physical_filename'];

		if (file_exists($image_file_path))
		{
			$img_data['filesize'] = $this->rotate_image($image_file_path, $img_rotate_deg);
		}
		else
		{
			$this->json_response(4, $ext_display_name, $this->language->lang('IUL_IMG_NOT_EXIST'));
		}

		if ($img_data['thumbnail'] && file_exists($thumb_file_path))
		{
			$this->rotate_image($thumb_file_path, $img_rotate_deg);
		}
		else if ($img_data['thumbnail'])
		{
			$this->json_response(4, $ext_display_name, $this->language->lang('IUL_THUMB_NOT_EXIST'));
		}

		// Update DataBase
		unset($img_data['attach_id']);
		$sql = 'INSERT INTO ' . ATTACHMENTS_TABLE . ' ' . $this->db->sql_build_array('INSERT', $img_data);
		$this->db->sql_query($sql);

		// sql_nextid() to be removed in 4.1.0-a1, in future use sql_last_inserted_id() it exsits since 3.3.11-RC1
		$new_attach_id = $this->db->sql_nextid();

		if ($new_attach_id)
		{
			$sql = 'DELETE FROM ' . ATTACHMENTS_TABLE . ' WHERE attach_id = ' . (int) $img_attach_id;
			$this->db->sql_query($sql);

			$this->json_response(0, $ext_display_name, '', $img_attach_id, $new_attach_id);
		}
		else
		{
			$this->json_response(5, $ext_display_name, $this->language->lang('IUL_DATABASE_NOT_UPDATE'));
		}
	}

	/**
	 * Rotate Image with ImageMagick
	 *
	 * @param 	string	$path	Path to the image file
	 * @param	int		$deg	Rotation angle
	 *
	 * @return	int		Image	file size
	 */
	private function rotate_image($path, $deg)
	{
		$image = new \Imagick($path);
		$image->rotateImage('#000', $deg);
		$image->writeImage($path);
		$filesize = strlen($image->getImageBlob());
		$image->clear();

		return $filesize;
	}

	/**
	 * Generate json string
	 *
	 * @param 	int		$status			Status 0=id's, 3=redirect, 4=file not found, 5=error
	 * @param	string	$title			Messagebox title
	 * @param 	string	$message		Messagebox message
	 * @param	int		$old_attach_id	Previous attachment id
	 * @param 	int		$new_attach_id	New attachment id
	 *
	 * @return	string	$json
	 */
	private function json_response($status, $title = '', $message = '', $old_attach_id = 0, $new_attach_id = 0)
	{
		$json_response = new \phpbb\json_response;
		$json_response->send([
			'status'		=> (int) $status,
			'title'			=> $title,
			'message'		=> $message,
			'oldAttachId'	=> (int) $old_attach_id,
			'newAttachId'	=> (int) $new_attach_id,
		]);
	}
}
