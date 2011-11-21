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
	// absolute path of the original image
	private $image_absolute_filename;

	// image type
	private $image_type;

	// filename of the generated thumbnail
	private $thumbnail_filename;

	/**
     * Returns a list of functions
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
		return 'thumbnailExtension';
	}

	/**
	 * 	return the new thumbnail path
	 *
	 * eg: filename = image1.jpg
	 * $size can be:
	 *
	 * 1) array( 'width'=>100, 'height'=>50 )	=> resize the image to this fixed dimension
	 * 																					 thumb filename = image1_w100_h50.jpg
	 * 2) array( 'width'=>100 )								=> calculate the corresponding height and then resize the image
	 * 																					 thumb filename = image1_w100.jpg
	 * 3) array( 'height'=>50 )								=> calculate the corresponding width and then resize the image
	 * 																					 thumb filename = image1_h50.jpg
	 * 4) array( 'scale'=>30 )								=> resize the image with 30% width and 30% height of the original image
	 * 																					 thumb filename = image1_s30.jpg
	 *
	 * the thumbnail file is created in the same path containing the original image
	 *
	 * @param String $absolute_filename the absolute path of the original image
	 * @param array $size
	 */
	public function thumbnail($absolute_filename, Array $size = array())
	{
		try
		{
			$this->createThumbnail($absolute_filename, $size);
		}
		catch(Exception $e)
		{}

		// print the path of the thumbnail
		echo $this->thumbnail_filename;
	}

	private function createThumbnail($absolute_filename, Array $size)
	{
		// if original file does not exist or it is not a file
		if (!file_exists($absolute_filename) || !is_file($absolute_filename))
		throw new InvalidArgumentException();
			
		// absolute path of the original image
		$this->image_absolute_filename = $absolute_filename;
			
		// create the name of the thumbnail
		$this->setThumbnailName($size);
			
		// if thumbnail does not already exists, create id
		if (!file_exists($this->thumbnail_filename))
		{
			// recognize image type
			$image_info = getimagesize($this->image_absolute_filename);
			$this->image_type = $image_info[2];

			if( $this->image_type == IMAGETYPE_JPEG )
			{
				$this->image_absolute_filename = imagecreatefromjpeg($this->image_absolute_filename);
			} elseif( $this->image_type == IMAGETYPE_GIF )
			{
				$this->image_absolute_filename = imagecreatefromgif($this->image_absolute_filename);
			} elseif( $this->image_type == IMAGETYPE_PNG )
			{
				$this->image_absolute_filename = imagecreatefrompng($this->image_absolute_filename);
			}
			else
			{
				throw new InvalidArgumentException();
			}
				

			// actually resize the image depengind by $size
			if (isset($size['width']) && isset($size['height']))
			{
				$this->resize($size['width'], $size['height']);
			}
			else if (isset($size['width']))
			{
				$this->resizeToWidth($size['width']);
			}
			else if (isset($size['height']))
			{
				$this->resizeToHeight($size['height']);
			}
			else if (isset($size['scale']))
			{
				$this->scale($size['scale']);
			}
			else
			{
				throw new InvalidArgumentException();
			}

			// salva
			try
			{
				$this->save();
			}
			catch (Exception $e)
			{
				throw $e;
			}
		}
			
		// calculate the relative path from the document root
		$this->thumbnail_filename = str_replace(realpath($_SERVER['DOCUMENT_ROOT']), '', realpath($this->thumbnail_filename));
	}

	/**
	 * calculate the name of the thumbnail
	 *
	 * @param array $size
	 */
	private function setThumbnailName(Array $size)
	{
		// info about the orifinal image
		$filename_info = pathinfo($this->image_absolute_filename);
		$filename		= $filename_info['filename'];
		$extension	= $filename_info['extension'];
		$dirname		= $filename_info['dirname'];

		// $thumbnail_filename = original filename; if any errors occur, it return <img> tag with the original image
		$thumbnail_filename = $filename;

		// switch by content of $size
		if (isset($size['width']) && isset($size['height']))
		{
			$thumbnail_filename = $filename.'_w'.$size['width'].'_h'.$size['height'];
		}
		else if (isset($size['width']))
		{
			$thumbnail_filename = $filename.'_w'.$size['width'];
		}
		else if (isset($size['height']))
		{
			$thumbnail_filename = $filename.'_h'.$size['height'];
		}
		else if (isset($size['scale']))
		{
			$thumbnail_filename = $filename.'_s'.$size['scale'];
		}

		// set class variable $this->thumbnail_filename
		$this->thumbnail_filename = $dirname.'/'.$thumbnail_filename.'.'.$extension;
	}

	/**
	 * get the width of the original image
	 */
	private function getWidth()
	{
		return imagesx($this->image_absolute_filename);
	}

	/**
	 * get the height of the original image
	 */
	private function getHeight()
	{
		return imagesy($this->image_absolute_filename);
	}

	/**
	 * save the genrated thumbnail
	 *
	 * @param string $image_type
	 * @param integer $compression
	 * @param integer $permissions
	 */
	private function save($image_type = IMAGETYPE_JPEG, $compression = 100, $permissions = 0755)
	{
		$filename = $this->thumbnail_filename;

		try
		{
			if( $image_type == IMAGETYPE_JPEG )
			{
				imagejpeg($this->image_absolute_filename,$filename,$compression);
			}
			else if( $image_type == IMAGETYPE_GIF )
			{
				imagegif($this->image_absolute_filename,$filename);
			}
			else if( $image_type == IMAGETYPE_PNG )
			{
				imagepng($this->image_absolute_filename,$filename);
			}

			if( $permissions != null)
			{
				chmod($filename,$permissions);
			}
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}

	/**
	 * resize the original image to a fixed height, calculating corresponding width
	 *
	 * @param integer $height
	 */
	function resizeToHeight($height)
	{
		$ratio	= $height / $this->getHeight();
		$width	= $this->getWidth() * $ratio;

		$this->resize($width,$height);
	}

	/**
	 * resize the original image to a fixed width, calculating corresponding height
	 *
	 * @param integer $width
	 */
	function resizeToWidth($width)
	{
		$ratio	= $width / $this->getWidth();
		$height	= $this->getheight() * $ratio;

		$this->resize($width,$height);
	}

	/**
	 * scale the original image to a fixed percentage value, calculating corresponding width and height
	 *
	 * @param integer $scale
	 */
	function scale($scale)
	{
		$width	= $this->getWidth() * $scale/100;
		$height	= $this->getheight() * $scale/100;

		$this->resize($width,$height);
	}

	/**
	 * actually resize the original image to a fixed width and height
	 *
	 * @param integer $width
	 * @param integer $height
	 */
	function resize($width,$height)
	{
		$thumbnail = imagecreatetruecolor($width, $height);

		imagecopyresampled($thumbnail, $this->image_absolute_filename, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());

		// set the class variable $this->image_absolute_filename
		$this->image_absolute_filename = $thumbnail;
	}

}