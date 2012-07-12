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
	 * @var	width	integer	Number of pixels for image width
	 */
	protected $width;
	
	/**
	 * @var	height	integer	Number of pixels for image height
	 */
	protected $height;
	
	/**
	 * @var	link	string	URL image should link to
	 */
	protected $link;
	
	/**
	 * @var	folder	string	path to image folder
	 */
	protected $folder;
	
	/**
	 * @var	type	string	image type suffix (jpg/png/etc.)
	 */
	protected $type;
	
	/**
	 * @var	moduleclass_sfx	string	Module Class suffix
	 */
	protected $moduleclass_sfx;
	
	/**
	 * @var	layout	string	Name of layout to use for output
	 */
	protected $layout;
	
	/**
	 *
	 * Constructor.
	 *
	 * @param	JRegistry	params
	 *
	 * @return	modRandomImageHelper object
	 *
	 * @since Unspecified Possible Future Version
	 */
	public function __construct( $params )
	{
		$this->width			= $params->get('width');
		$this->height			= $params->get('height');
		$this->link				= $params->get('link');
		$this->folder			= $this->getFolder($params);
		$this->type				= $params->get('type', 'jpg');
		$this->moduleclass_sfx	= htmlspecialchars($params->get('moduleclass_sfx'));
		$this->layout			= $params->get('layout', 'default');
}
	
	public function getRandomImage($images)
	{
		$i			= count($images);
		$random		= mt_rand(0, $i - 1);
		$image		= $images[$random];
		$size		= getimagesize (JPATH_BASE . '/' . $image->folder . '/' . $image->name);


		if ($this->width == '') {
			$this->width = 100;
		}

		if ($size[0] < $this->width) {
			$this->width = $size[0];
		}

		$coeff = $size[0]/$size[1];
		if ($this->height == '') {
			$this->height = (int) ($this->width/$coeff);
		} else {
			$newheight = min ($this->height, (int) ($this->width/$coeff));
			if ($newheight < $this->height) {
				$this->height = $newheight;
			} else {
				$this->width = $this->height * $coeff;
			}
		}

		$image->width	= $this->width;
		$image->height	= $this->height;
		$image->folder	= str_replace('\\', '/', $image->folder);

		return $image;
	}

	public function getImages()
	{
		$files	= array();
		$images	= array();

		$dir = JPATH_BASE . '/' . $this->folder;

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
					if (preg_match('/'.$this->type.'/', $img)) {
						$images[$i] = new stdClass;

						$images[$i]->name	= $img;
						$images[$i]->folder	= $this->folder;
						$i++;
					}
				}
			}
		}

		return $images;
	}

	public function getFolder(&$params)
	{
		$folder	= $params->get('folder');

		$LiveSite	= JURI::base();

		// if folder includes livesite info, remove
		if (JString::strpos($folder, $LiveSite) === 0) {
			$folder = str_replace($LiveSite, '', $folder);
		}
		// if folder includes absolute path, remove
		if (JString::strpos($folder, JPATH_SITE) === 0) {
			$folder= str_replace(JPATH_BASE, '', $folder);
		}
		$folder = str_replace('\\', DIRECTORY_SEPARATOR, $folder);
		$folder = str_replace('/', DIRECTORY_SEPARATOR, $folder);

		return $folder;
	}
	/**
	 *
	 * creasteOutput.
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
 		$link	= $this->link;
		$moduleclass_sfx = $this->moduleclass_sfx;

		$images	= $this->getImages();

		if (!count($images)) {
			echo JText::_('MOD_RANDOM_IMAGE_NO_IMAGES');
		} else {
			$image = $this->getRandomImage($images);
			require JModuleHelper::getLayoutPath('mod_random_image', $this->layout);
		}
	}
}
