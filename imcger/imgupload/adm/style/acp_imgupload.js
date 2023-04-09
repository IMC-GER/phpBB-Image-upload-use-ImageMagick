/*
 Image upload use ImageMagick
------------------------------------- */

function imcgerImgInlineDisabled() {
	if (document.getElementById('img_create_thumbnail').checked) {
		document.getElementById('imcger_imgupload_image_inline').disabled = false;
	} else {
		document.getElementById('imcger_imgupload_image_inline').checked = false;
		setTimeout(document.getElementById('imcger_imgupload_image_inline').disabled = true, 1000);
	}
}

document.getElementById('img_create_thumbnail').addEventListener('click', imcgerImgInlineDisabled);
imcgerImgInlineDisabled();
