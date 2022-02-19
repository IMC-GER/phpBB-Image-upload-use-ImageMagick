# phpBB-Image upload use ImageMagick

## Description
This extension us the PHP imagick class to modify upload image for improved quality.
It can change the image size and/or the image file size. Rotate thumbnails according to their exif information. Remove EXIF data.

#### Settings in User Control Panel
- No settings. 

#### Settings in Administration Control Panel
- Thumbnail compression quality. 
- Image compression quality. 
- Maximum image width.
- Maximum image heigth.
- Maximum image file size.
- Remove EXIF data

## Requirements
- php 7.3 or higher
- phpBB 3.2.4 or higher
- php the Imagick class

## Installation
Copy the extension to `phpBB3/ext/imcger/imgupload`.
Go to "ACP" > "Customise" > "Manage extensions" and enable the "Image upload use ImageMagick" extension.
For full functionality "Maximum file size" in "ACP" > "Posting" > "Attachment settings" must be set to 0. This is done automatically during the migration.
For full functionality "Maximum image dimensions" in "ACP" > "Posting" > "Attachment settings" must be set to 0. This is done automatically during the migration.

## Update
For update from v0.01 to v0.0.2 please `disable` the extension and `delete data` before copy the new files to `phpBB3/ext/imcger/imgupload`.

## Changelog

### v0.0.2 (19-02-2022)
Code changes. Takes into account other settings from "Attachment settings".

### v0.0.1 (18-02-2022)

## Uninstallation
- Navigate in the ACP to `Customise -> Manage extensions`.
- Click the `Disable` link for "Image upload use ImageMagick".
- To permanently uninstall, click `Delete Data`, then delete the `imgupload` folder from `phpBB3/ext/imcger/`.

## License
[GPLv2](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)
