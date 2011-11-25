<?php

/**
 * This file is part of Twig.
 * 
 * (c) 2011 Emanuele Gaspari Castelletti
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Emanuele Gaspari Castelletti <inmarelibero@gmail.com>
 * @package Twig
 * @subpackage Twig-extensions
 */
class Twig_Extensions_Extension_Thumbnail extends Twig_Extension
{
	// relative path (to web dir) of the original image
	private $image_relative_path;

	// relative path (to web dir) of the generated thumbnail
	private $thumbnail_relative_path;
	
	// resources of image and thumbnail
	private $image		= null;
	private $thumbnail	= null;
	
	// dirname containing the php script (eg: app.php)
	private $document_root;
	
	/**
	 * Initializes the $document_root internal variable
	 */
	public function __construct()
	{
		$this->document_root = realpath($_SERVER['DOCUMENT_ROOT']);
	}
	
	/**
     * Returns a list of available functions
     *
     * @return array
     */
	public function getFunctions()
	{
		return array(
			'thumbnail' => new Twig_Function_Method($this, 'thumbnail')
		);
	}

	/**
     * Name of this extension
     *
     * @return string
     */
	public function getName()
	{
		return 'Thumbnail';
	}

	/**
	 * Returns the relative path (to web dir) of the created thumbnail file if everithig ok.
	 * Returns the relative path (to web dir) of the original image if any error occurs.
	 *
	 * eg: $image_relative_path = /uploads/image1.jpg
	 * 
	 * $options can be:
	 *
	 * 1) array('width'=>100, 'height'=>50)			=>	resizes the image to this fixed size
	 * 													thumbnail path = /uploads/image1_w100_h50.jpg
	 * 
	 * 2) array('width'=>100, 'permissions'=>755)	=>	calculates the corresponding height and then resize the image
	 * 													applies 755 permissions to the created thumbnail file
	 * 													thumbnail path = /uploads/image1_w100.jpg
	 * 
	 * 3) array('height'=>50, 'quality'=>90)		=>	calculates the corresponding width and then resize the image
	 * 													if the image is jpeg, applies a percentage value (90%) to the thumbnail quality (range 0-100)
	 * 													thumbnail path = /uploads/image1_h50_q90.jpg
	 * 
	 * 4) array('scale'=>30)						=>	resizes the image with 30% width and 30% height of the original image
	 * 													thumbnail path = /uploads/image1_s30.jpg
	 *
	 * Important:
	 * 	1) The thumbnail file is created in the same path containing the original image
	 * 	2) All relative paths are intended from the folder containing the php script (eg: app.php, app_dev.php)
	 *
	 * @param string $image_relative_path relative path (to web dir) of the original image
	 * @param array $options thumbnail size values and more
	 */
	public function thumbnail($image_relative_path, array $options = array())
	{
		// prints the relative path of the thumbnail
		try
		{
			echo $this->createThumbnail($image_relative_path, $options);
		}
		// suppresses internal exceptions and print the original filename if any error occurs
		catch(Exception $e)
		{
			//throw $e;
			echo $image_relative_path;
		}
	}
	
	/**
	 * Internal function to create the thumbnail
	 * 
	 * @param string $image_relative_path relative path (to web dir) of the original image
	 * @param array $options thumbnail size values and more
	 * @throws InvalidArgumentException
	 * @throws Exception
	 * @return string relative path (to web dir) of the generated thumbnail
	 */
	private function createThumbnail($image_relative_path = null, array $options = array())
	{
		// the absolute path of the original image
		$image_absolute_path = $this->document_root.$image_relative_path;
		
		// if original file does not exist or it is not a file
		if (!file_exists($image_absolute_path) || !is_file($image_absolute_path))
			throw new InvalidArgumentException('The original file "'.$image_absolute_path.'" is not a file or does not exist.');

		// sets the relative path class variable of the original image
		$this->image_relative_path = $image_relative_path;
		
		// recognizes image type. required for thumbnail filename
		try
		{
			$image_info = getimagesize($image_absolute_path);
		}
		catch (Exception $e)
		{
			throw new InvalidArgumentException('The original file "'.$image_absolute_path.'" is not an image.');
		}
		
		$image_type = $image_info[2];
		
		if ($image_type == IMAGETYPE_JPEG)
		{
			$this->image = imagecreatefromjpeg($image_absolute_path);
		}
		else if ($image_type == IMAGETYPE_GIF)
		{
			$this->image = imagecreatefromgif($image_absolute_path);
		}
		else if ($image_type == IMAGETYPE_PNG)
		{
			$this->image = imagecreatefrompng($image_absolute_path);
		}
		else
		{
			throw new InvalidArgumentException('Unsupported type of image: '.$image_type);
		}
		
		// generates the relative path of the thumbnail
		try
		{
			$this->thumbnail_relative_path = $this->getThumbnailRelativePath($options, $image_type);
		}
		catch (Exception $e)
		{
			throw $e;
		}
		
		// if thumbnail does not exist yet, creates it
		if (!file_exists($this->document_root.$this->thumbnail_relative_path))
		{
			// resizes the image depending on specified $options
			if (isset($options['width']) && isset($options['height']))
			{
				$this->resize($options['width'], $options['height']);
			}
			else if (isset($options['width']))
			{
				$this->resizeToWidth($options['width']);
			}
			else if (isset($options['height']))
			{
				$this->resizeToHeight($options['height']);
			}
			else if (isset($options['scale']))
			{
				$this->scale($options['scale']);
			}
			else
			{
				throw new InvalidArgumentException('New size not specified. A value for width, height or scale must be set.');
			}
			
			// saves the thumbnail
			$this->save($image_type, $options);
		}
			
		// returns the relative path of the created thumbnail
		return $this->thumbnail_relative_path;
	}

	/**
	 * Calculates the relative path of the thumbnail
	 *
	 * @param array $options thumbnail size values and more
	 * @param unknown_type $image_type
	 * @return string relative path (to web dir) of the generated thumbnail
	 */
	private function getThumbnailRelativePath(array $options = array(), $image_type)
	{
		// info about the original image
		$filename_info	= pathinfo($this->document_root.$this->image_relative_path);
		$filename		= $filename_info['filename'];
		$extension		= $filename_info['extension'];
		$dirname		= $filename_info['dirname'];

		// $thumbnail_filename = original filename; if any error occurs, it returns <img> tag with the original image
		$thumbnail_filename = $filename;

		// switches by content of $options related to the size
		if (isset($options['width']) && isset($options['height']))
		{
			$thumbnail_filename = $filename.'_w'.$options['width'].'_h'.$options['height'];
		}
		else if (isset($options['width']))
		{
			$thumbnail_filename = $filename.'_w'.$options['width'];
		}
		else if (isset($options['height']))
		{
			$thumbnail_filename = $filename.'_h'.$options['height'];
		}
		else if (isset($options['scale']))
		{
			$thumbnail_filename = $filename.'_s'.$options['scale'];
		}
		
		// append the portion of the filename regarding the quality
		if ($image_type == IMAGETYPE_JPEG && isset($options['quality']))
		{
			$thumbnail_filename = $thumbnail_filename.'_q'.$options['quality'];
		}
		
		// returns the generated filename of the thumbnail
		return str_replace($filename, $thumbnail_filename, $this->image_relative_path);
	}

	/**
	 * Returns the width of the original image
	 */
	private function getWidth()
	{
		return imagesx($this->image);
	}

	/**
	 * Returns the height of the original image
	 */
	private function getHeight()
	{
		return imagesy($this->image);
	}

	/**
	 * Saves the generated thumbnail
	 *
	 * $options accepts:	'permisisons'	=> interger (eg: 775, 644, 0755)
	 * 						'quality'		=> integer; 0 <= quality <= 100
	 *
	 * @param unknown_type $image_type original image type
	 * @param integer $options thumbnail size values and more
	 */
	private function save($image_type = IMAGETYPE_JPEG, $options = array())
	{
		try
		{
			// saves as jpeg
			if ($image_type == IMAGETYPE_JPEG)
			{
				$quality = 100;
				// adjusts quality
				if (isset($options['quality']))
				{
					$quality = $options['quality'];
					if ($quality < 0) $quality = 0;
					if ($quality > 100) $quality = 100;
				}
				
				imagejpeg($this->thumbnail, $this->document_root.$this->thumbnail_relative_path, $quality);
			}
			// saves as gif
			else if ($image_type == IMAGETYPE_GIF)
			{
				imagegif($this->thumbnail, $this->document_root.$this->thumbnail_relative_path);
			}
			// saves as png
			else if ($image_type == IMAGETYPE_PNG)
			{
				imagepng($this->thumbnail, $this->document_root.$this->thumbnail_relative_path);
			}
		}
		catch (Exception $e)
		{
			throw $e;
		}
		
		// changes the thumbnail file permissions
		if (isset($options['permissions']) && is_int($options['permissions']))
		{
			chmod($this->document_root.$this->thumbnail_relative_path, $options['permissions']);
		}
	}

	/**
	 * Resizes the original image to a fixed height, calculating corresponding width
	 *
	 * @param integer $height
	 */
	function resizeToHeight($height)
	{
		$ratio	= $height / $this->getHeight();
		$width	= $this->getWidth() * $ratio;

		$this->resize($width, $height);
	}

	/**
	 * Resizes the original image to a fixed width, calculating corresponding height
	 *
	 * @param integer $width
	 */
	function resizeToWidth($width)
	{
		$ratio	= $width / $this->getWidth();
		$height	= $this->getheight() * $ratio;

		$this->resize($width, $height);
	}

	/**
	 * Scales the original image to a fixed percentage value, calculating corresponding width and height
	 *
	 * @param integer $scale
	 */
	function scale($scale)
	{
		$width	= $this->getWidth() * $scale/100;
		$height	= $this->getheight() * $scale/100;

		$this->resize($width, $height);
	}

	/**
	 * Actually resizes the original image to a fixed width and height
	 *
	 * @param integer $width
	 * @param integer $height
	 */
	function resize($width, $height)
	{
		// creates thumbnail resource
		$this->thumbnail = imagecreatetruecolor($width, $height);
		
		// resizes the original image
		imagecopyresampled($this->thumbnail, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
	}

}