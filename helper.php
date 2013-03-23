<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_random_image
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class modRandomImageHelper
{
	/**
	 * @var	params	the params array for the module
	 */
	protected $params;
	
	/**
	 * @var	cms	the glue object for talking to the cms
	 */
	protected $cms;
	
	/**
	 *
	 * Constructor.
	 *
	 * @param	params	JRegistry		params
	 * @param	cms		CMS Glue object	glue for cms API
	 *
	 * @return	modRandomImageHelper object
	 *
	 * @since Unspecified Possible Future Version
	 */
	public function __construct( $params, $cms )
	{
		$this->params = $params;
		$this->cms = $cms;
	}
	
	public function getRandomImage($images)
	{
		$width	= $this->params->get('width');
		$height	= $this->params->get('height');
		$i			= count($images);
		$random		= mt_rand(0, $i - 1);
		$image		= $images[$random];
		$size		= getimagesize(JPATH_BASE . '/' . $image->folder . '/' . $image->name);


		if ($width == '') {
			$width = 100;
		}

		if ($size[0] < $width) {
			$width = $size[0];
		}

		$coeff = $size[0]/$size[1];
		if ($height == '') {
			$height = (int) ($width/$coeff);
		} else {
			$newheight = min ($height, (int) ($width/$coeff));
			if ($newheight < $height) {
				$height = $newheight;
			} else {
				$width = $height * $coeff;
			}
		}

		$image->width	= $width;
		$image->height	= $height;
		$image->folder	= str_replace('\\', '/', $image->folder);

		return $image;
	}

	public function getImages($theFolder, $type)
	{
		$folder = $this->getFolder($theFolder);
		$files	= array();
		$images	= array();

		$dir = JPATH_BASE . '/' . $folder;

		// check if directory exists
		if (is_dir($dir))
		{
			if ($handle = opendir($dir)) {
				while (false !== ($file = readdir($handle))) {
					if ($file != '.' && $file != '..' && $file != 'CVS' && $file != 'index.html') {
						$files[] = $file;
					}
				}
			}
			closedir($handle);

			$i = 0;
			foreach ($files as $img)
			{
				if (!is_dir($dir . '/' . $img))
				{
					if (preg_match('/'.$type.'/', $img)) {
						$images[$i] = new stdClass;

						$images[$i]->name	= $img;
						$images[$i]->folder	= $folder;
						$i++;
					}
				}
			}
		}

		return $images;
	}

	public function getFolder($theFolder)
	{ 
		// if folder includes absolute path, remove
		if ($this->cms->strpos($folder, JPATH_SITE) === 0) {
			$folder= str_replace(JPATH_BASE, '', $folder);
		}
		$folder = $this->removeLiveSite($theFolder);
		$folder = str_replace('\\', DIRECTORY_SEPARATOR, $folder);
		$folder = str_replace('/', DIRECTORY_SEPARATOR, $folder);

		return $folder;
	}
	/**
	 *
	 * createOutput.
	 *
	 * This method outputs through the selected template the results of the
	 * module.
	 *
	 * @return	none
	 *
	 * @since Unspecified Possible Future Version
	 */
	public function createOutput()
	{
 		$link	= $this->params->get('link');
		$moduleclass_sfx = $this->params->get('moduleclass_sfx');

		$images	= $this->getImages($this->params->get('folder'),
			$this->params->get('type', 'jpg')
		);

		if (!count($images)) {
			echo $this->cms->getTranslatedText('MOD_RANDOM_IMAGE_NO_IMAGES');
		} else {
			$image = $this->getRandomImage($images);
			require $this->cms->getLayoutPath($this->params->get('layout'));
		}
	}
	/**
	 *	removeLiveSite
	 *
	 *	Removes the current live site from the folder string, if present.
	 *
	 *	@param	path	String	the path/uri to the folder
	 *	@private
	 *
	 *	@return	string
	 */
	 private function removeLiveSite( $path )
	 {
		$LiveSite	= $this->cms->getBaseURL();

		// if folder includes livesite info, remove
		if ($this->cms->strpos($path, $LiveSite) === 0) {
			$folder = str_replace($LiveSite, '', $path);
		}
		
		return $path;
	 }
}
