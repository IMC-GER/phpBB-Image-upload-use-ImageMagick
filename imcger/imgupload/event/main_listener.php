<?php
/**
 *
 * Image upload use ImageMagick
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022, Thorsten Ahlers
 * @copyright (c) 2018, canonknipser, ImageMagick Thumbnailer, http://canonknipser.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace imcger\imgupload\event;

/**
 * @ignore
 */
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * ImageMagick Thumbnailer Event listener.
 */
class main_listener implements EventSubscriberInterface
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\language\language */
	protected $language;

	/**
	* Constructor for listener
	*
	* @param \phpbb\config\config		$config		phpBB config
	* @param \phpbb\language\language	$language	phpBB language
	*
	* @access public
	*/
	public function __construct
	(
		\phpbb\config\config $config,
		\phpbb\language\language $language
	)
	{
		$this->config = $config;
		$this->language = $language;
	}

	public static function getSubscribedEvents()
	{
		return [
			'core.ucp_display_module_before'			 => 'new_profil_avatar_text',
			'core.thumbnail_create_before'				 => 'imcger_create_tumbnail',
			'core.modify_uploaded_file'					 => 'imcger_modify_uploaded_file',
			'core.avatar_driver_upload_move_file_before' => 'imcger_modify_uploaded_avatar',
		];
	}

	/**
	 * Add language file for avatar upload page
	 */
	public function new_profil_avatar_text()
	{
		if ($this->config['imcger_imgupload_avatar_resize'])
		{
			$this->language->add_lang('ucp','imcger/imgupload');
		}
	}

	/**
	 * Create a thumbnail using IMagick
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function imcger_create_tumbnail($event)
	{
		/* create a new instance of ImageMagick and load current image */
		$thumbnail = new \Imagick(realpath($event['source']));

		/* rotate the image according it's orientation flag */
		$thumb = $this->image_auto_rotate($thumbnail);

		/* changed the image heigth and width when it rotate */
		if ($thumb['rotate'])
		{
			$new_width = $event['new_height'];
			$new_height = $event['new_width'];
		}
		else
		{
			$new_width = $event['new_width'];
			$new_height = $event['new_height'];
		}

		/* resize the Image */
		$thumbnail->resizeImage($new_width, $new_height, \Imagick::FILTER_LANCZOS, 1);

		/* set image format */
		$image_format = $this->set_image_format($thumbnail, $event['mimetype']);

		/* compression quality is read from config, set in ACP */
		$this->set_image_compression($thumbnail, $this->config['imcger_imgupload_tum_quality']);

		/* strip EXIF data and image profile */
		if ($this->config['imcger_imgupload_del_exif'] && ($image_format == 'JPEG' || $image_format == 'WEBP'))
		{
			$thumbnail->stripImage();
		}

		/* store the image */
		$thumbnail_created = $thumbnail->writeImage($event['destination']);
		$thumbnail->clear();

		/* if image format change to JPEG set mimetype to jpeg */
		if ($image_format == 'JPEG' && $event['mimetype'] != 'image/jpeg' && $thumbnail_created)
		{
			$event['mimetype'] = 'image/jpeg';
		}

		/* set return value */
		$event['thumbnail_created'] = $thumbnail_created;
	}

	/**
	 * Modify upload image using IMagick
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	public function imcger_modify_uploaded_file($event)
	{
		if ($event['is_image'])
		{
			$write_image		= false; // set to true wenn image attribute changed
			$image_max_width	= $this->config['imcger_imgupload_max_width'];
			$image_max_height	= $this->config['imcger_imgupload_max_height'];
			$image_quality		= $this->config['imcger_imgupload_img_quality'];
			$image_del_exif		= $this->config['img_strip_metadata'];
			$image_max_filesize = $this->config['imcger_imgupload_max_filesize'];
			$filesize			= $event['filedata']['filesize'];

			/* get file path */
			$file_path = join('/', [trim($this->config['upload_path'], '/'), trim($event['filedata']['physical_filename'], '/')]);

			/* get image dimension and type */
			$dimension = @getimagesize($file_path);

			if ($dimension === false)
			{
				return false;
			}

			list($width, $height, $img_type, ) = $dimension;

			/* if image no jpeg or webp and image properties smaller then maximum values do nothing */
			if (!(($img_type == IMAGETYPE_JPEG || $img_type == IMAGETYPE_WEBP ) && $image_del_exif) &&
				(!$image_max_width || $image_max_width >= $width) &&
				(!$image_max_height || $image_max_height >= $height) &&
				(!$image_max_filesize || $image_max_filesize >= $filesize)
				)
			{
				return true;
			}

			/* create a new instance of ImageMagick and load current image */
			$image = new \Imagick($file_path);

			/* rotate the image according it's orientation flag */
			$img = $this->image_auto_rotate($image);

			/* resize the image */
			$write_image = $this->resize_image($image, $image_max_width, $image_max_height);

			/* set image format */
			$image_format = $this->set_image_format($image, $event['filedata']['mimetype']);

			/* when not resize don`t changed image quality when it less then quality set in ACP */
			if (($image_format == 'JPEG' || $image_format == 'WEBP') && ($write_image || $image_quality < $image->getImageCompressionQuality()))
			{
				/* set compression quality is read from config */
				$this->set_image_compression($image, $image_quality);

				$write_image = true;
			}

			if ($image_format == 'PNG')
			{
				/* set compression quality, second parameter isn't importent for PNG */
				$this->set_image_compression($image);
			}

			/* strip EXIF data and image profile */
			if ($image_del_exif && ($image_format == 'JPEG' || $image_format == 'WEBP'))
			{
				$image->stripImage();

				$write_image = true;
			}

			/* shrink file size wenn greater then set in ACP */
			if ($image_max_filesize && $filesize > $image_max_filesize)
			{
				/* set compression quality is read from config */
				$this->set_image_compression($image, $image_quality);

				/* set the max file size for the image */
				$filesize = $this->image_auto_length($image, $image_max_filesize);

				$write_image = true;
			}

			/* store the image when attribute change */
			if ($write_image || $img['changed'])
			{
				$write = $image->writeImage($file_path);

				/* if image store set new filedata */
				if ($write)
				{
					/* set return value new file size and filedata*/
					$filedata_array = $event['filedata'];

					/* set new file size */
					$filedata_array['filesize'] = $filesize ? $filesize : $image->getImageBlob();

					/* if image format change to JPEG set filedata to JPEG */
					if ($image_format == 'JPEG' && $event['filedata']['mimetype'] != 'image/jpeg')
					{
						$filedata_array['mimetype'] = 'image/jpeg';
						$filedata_array['extension'] = 'jpg';
						$filedata_array['real_filename'] .= '.jpg';
					}

					$event['filedata'] = $filedata_array;
				}

				$image->clear();
			}
		}
	}

	/**
	 * Modify upload avatar image using IMagick
	 *
	 * @param \phpbb\event\data	$event	Event object
	 */
	function imcger_modify_uploaded_avatar($event)
	{
		if ($this->config['imcger_imgupload_avatar_resize'])
		{
			$avatar_max_width	 = $this->config['avatar_max_width'];
			$avatar_max_height	 = $this->config['avatar_max_height'];
			$avatar_file		 = $event['filedata']['filename'];

			$dimension = @getimagesize($avatar_file);

			if ($dimension === false)
			{
				return false;
			}

			list($width, $height, ) = $dimension;

			/* when avatar image file to great modify it */
			if ($width > $avatar_max_width || $height > $avatar_max_height)
			{
				/* create a new instance of ImageMagick and load current image */
				$avatar = new \Imagick($avatar_file);

				/* rotate the image according it's orientation flag */
				$this->image_auto_rotate($avatar);

				/* resize the image */
				$this->resize_image($avatar, $avatar_max_width, $avatar_max_height);

				/* set compression quality is read from config */
				$this->set_image_compression($avatar);

				/* set image format */
				$image_format = $this->set_image_format($avatar, $event['filedata']['mimetype']);

				/* strip EXIF data and image profile */
				if ($image_format == 'JPEG' || $image_format == 'WEBP')
				{
					$avatar->stripImage();
				}

				/* store the avatar */
				$write = $avatar->writeImage($avatar_file);

				if($write === false)
				{
					return false;
				}

				/* set return value new file size*/
				$filedata_array = $event['filedata'];
				$filedata_array['filesize'] = $avatar->getImageBlob();
				$event['filedata'] = $filedata_array;

				$avatar->clear();
			}
		}
	}

	/**
	 * resize image if image to large
	 *
	 * @param object	$image	image object
	 *
	 * @return bool		$img_resize	images is resize
	 */
	function resize_image($image, $max_width, $max_height)
	{
		$img_resize = false;

		/* get image dimensions */
		$img_geo = $image->getImageGeometry();
		list('width' => $width, 'height' =>  $height, ) = $img_geo;

		/* image aspect ratio */
		$side_ratio = $width / $height;

		/* set new images width */
		if ($max_width && $max_width < $width)
		{
			$width = $max_width;
			$height = (int) ($width / $side_ratio);

			$img_resize = true;
		}

		/* set new images height */
		if ($max_height && $max_height < $height)
		{
			$height = $max_height;
			$width = (int) ($height * $side_ratio);

			$img_resize = true;
		}

		/* when changed dimensions resize the Image */
		if ($img_resize)
		{
			$image->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1, false);
		}

		return $img_resize;
	}

	/**
	 * set the image format for the generated image
	 *
	 * @param object	$image		image object
	 * @param string	$mimetype	mimetype of the image in phpBB internal format
	 *
	 * @return string	$imageformat images format
	 */
	function set_image_format($image, $mimetype)
	{
		/* Check the mimetype and set the appropriate type for the image */
		switch ($mimetype)
		{
			case 'image/jpeg':
				$imageformat = 'JPEG';
				break;
			case 'image/png':
				$imageformat = 'PNG';
				break;
			case 'image/gif':
				$imageformat = 'GIF';
				break;
			case 'image/webp':
				$imageformat = 'WEBP';
				break;
			default:
				$imageformat = 'JPEG';
				break;
		}

		$image->setImageFormat($imageformat);

		return $imageformat;
	}

	/**
	 * set the compression value for the generated image
	 *
	 * @param object	$image		image object
	 * @param integer	$quality	image quality value
	 */
	function set_image_compression($image, $quality = 80)
	{
		$image_format = $image->getImageFormat();

		switch ($image_format)
		{
			case 'JPEG':
				$image->setImageCompression(\Imagick::COMPRESSION_JPEG);
				$image->setImageCompressionQuality($quality);
				break;

			case 'PNG':
				$image->setOption('png:compression-method', 0);
				$image->setOption('png:compression-filter', 0);
				$image->setOption('png:compression-level', 9);
				break;

			case 'WEBP':
				$image->setOption('webp:alpha-compression', 1);
				$image->setOption('webp:method', 6);
				$image->setImageCompressionQuality($quality);
				break;

			default:
				/* do nothing */
				break;
		}
	}

	/**
	 * rotate the generated image
	 *
	 * @param object	$image		image object
	 *
	 * @return array	changed		orientation changed
	 * 					rotate		rotate 90 degree
	 */
	function image_auto_rotate($image)
	{
		$is_rotate	= false; // true, when orientation change between portrait and landscape
		$is_changed = true;  // true, when orientation change

		/* read the orientation from the image */
		if (!($image_orientation = $image->getImageOrientation()))
		{
			/* orientation flag not available */
			$image_orientation = \Imagick::ORIENTATION_UNDEFINED;
		}

		switch ($image_orientation)
		{
			case \Imagick::ORIENTATION_UNDEFINED:
				/* do nothing */
				$is_changed = false;
				break;
			case \Imagick::ORIENTATION_TOPLEFT:
				/* do nothing */
				$is_changed = false;
				break;
			case \Imagick::ORIENTATION_TOPRIGHT:
				$image->flopImage();
				break;
			case \Imagick::ORIENTATION_BOTTOMRIGHT:
				$image->rotateImage("#000", 180);
				break;
			case \Imagick::ORIENTATION_BOTTOMLEFT:
				$image->flipImage();
				break;
			case \Imagick::ORIENTATION_LEFTTOP:
				$image->transposeImage();
				$is_rotate = true;
				break;
			case \Imagick::ORIENTATION_RIGHTTOP:
				$image->rotateImage("#000", 90);
				$is_rotate = true;
				break;
			case \Imagick::ORIENTATION_RIGHTBOTTOM:
				$image->transverseImage();
				$is_rotate = true;
				break;
			case \Imagick::ORIENTATION_LEFTBOTTOM:
				$image->rotateImage("#000", 270);
				$is_rotate = true;
				break;
			default:
				/* do nothing */
				$is_changed = false;
				break;
		}

		/* set the orientation from the Image */
		$image->setImageOrientation(\Imagick::ORIENTATION_TOPLEFT);

		return ['changed' => $is_changed, 'rotate' => $is_rotate];
	}

	/**
	 * resize the image file
	 *
	 * @param object	$image			image object
	 * @param integer	$new_image_size	new size of the image
	 *
	 * @return integer	$filesize		file size of the image after shrink
	 */
	function image_auto_length($image, $new_image_size)
	{
		/* get image dimensions */
		$img_geo = $image->getImageGeometry();
		list('width' => $width, 'height' =>  $height, ) = $img_geo;

		$filesize = strlen($image->getImageBlob());

		/* image aspect ratio */
		$side_ratio = $width / $height;

		while ($filesize >  $new_image_size)
		{
			/* calculate the image side in relation to the image area */
			$size_ratio = $filesize / $new_image_size;
			$xh = sqrt($width * $height / $size_ratio / $side_ratio);

			/* calculate the different to new height */
			$dh = ($height - $xh) * 0.9;
			$dh = $dh > 10 ? $dh : 10;

			/* set image dimensions */
			$height -= $dh;
			$width	 = $height * $side_ratio;

			/* resize the image */
			$image->resizeImage((int) $width, (int) $height, \Imagick::FILTER_LANCZOS, 1, false);

			/* get the file size */
			$filesize = strlen($image->getImageBlob());
		}
		return $filesize;
	}
}
