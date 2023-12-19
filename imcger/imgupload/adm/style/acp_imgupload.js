/*
 Image upload use ImageMagick
------------------------------------- */

$('input[name=img_create_thumbnail]').on('change', function () {
	if ($('input[name=img_create_thumbnail]:checked').last().val() == 1) {
		$('input[name=imcger_imgupload_image_inline]').prop('disabled', false);
	} else {
		$('input[name=imcger_imgupload_image_inline]').attr('type') == 'radio' ?
			$('input[name=imcger_imgupload_image_inline]').last().click() : $('input[name=imcger_imgupload_image_inline]').prop('checked', false);
		$('input[name=imcger_imgupload_image_inline]').prop('disabled', true);
	}
});

$('input[name=imcger_imgupload_image_inline]').trigger('change');

