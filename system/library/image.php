<?php
class Image {
	private $file;
	private $image;
	private $info;
	public $transparent;

	public function __construct($file) {
// BOF - Zappo - ImagEngine - ONE LINE - Added $file as array, for Text Images
		if (!is_array($file) && file_exists($file)) {
			$this->file = $file;

			$info = getimagesize($file);

			$this->info = array(
				'width'  => $info[0],
				'height' => $info[1],
				'bits'   => isset($info['bits']) ? $info['bits'] : '',
				'mime'   => isset($info['mime']) ? $info['mime'] : ''
			);

// BOF - Zappo - ImagEngine - Calculate needed Memory, and set memory to that value, so we can do needed calculations
			$memoryNeeded = round(($info[0] * $info[1] * $info['bits'] * 4 / 8 + Pow(2, 16)) * 1.65);
			if (function_exists('memory_get_usage') && memory_get_usage() + $memoryNeeded > (int) ini_get('memory_limit') * pow(1024, 2)) {
				ini_set('memory_limit', (int) ini_get('memory_limit') + ceil(((memory_get_usage() + $memoryNeeded) - (int) ini_get('memory_limit') * pow(1024, 2)) / pow(1024, 2)) . 'M');
			}
// EOF - Zappo - ImagEngine - Calculate needed Memory, and set memory to that value, so we can do needed calculations

			$this->image = $this->create($file);
		} else {
// BOF - Zappo - ImagEngine - If image doesn't exist, create one (for text images 'n stuff)
			$w = is_array($file) ? $file[0] : 5;
			$h = is_array($file) ? $file[1] : 5;
			$this->image = imagecreatetruecolor($w,$h);
			if (is_array($file) && isset($file[2]) && $file[2]) {
				$r = hexdec(substr($file[2], 0, 2));
				$g = hexdec(substr($file[2], 2, 2));
				$b = hexdec(substr($file[2], 4, 2));
				$trans_color = imagecolorallocate($this->image, $r, $g, $b);
			} else {
				imagesavealpha($this->image,true);
				$trans_color = imagecolorallocatealpha($this->image, 0, 0, 0, 127);
			}
			imagefill($this->image, 0, 0, $trans_color);
			$this->info = array(
				'width'  => $w,
				'height' => $h,
				'mime'   => 'image/png'
			);
// EOF - Zappo - ImagEngine - If image doesn't exist, create one (for text images 'n stuff)
		}
	}

	private function create($image) {
		$mime = $this->info['mime'];

		if ($mime == 'image/gif') {
			return imagecreatefromgif ($image);
// BOF - Zappo - ImagEngine - Added extra mime type for png, and added bmp files
		} elseif ($mime == 'image/png' || $mime == 'image/x-png') {
			return imagecreatefrompng($image);
		} elseif ($mime == 'image/jpeg') {
			return imagecreatefromjpeg($image);
		} elseif ($mime == 'image/bmp' || $mime == 'image/x-windows-bmp') {
			return $this->ImageCreateFromBMP($image);
// EOF - Zappo - ImagEngine - Added extra mime type for png, and added bmp files
		}
	}

	public function save($file, $quality = 90) {
		$info = pathinfo($file);

		$extension = strtolower($info['extension']);

		if (is_resource($this->image)) {
			if ($extension == 'jpeg' || $extension == 'jpg') {
				imagejpeg($this->image, $file, $quality);
			} elseif ($extension == 'png') {
// BOF - Zappo - ImagEngine - Added PNG Quality
				if ($quality < 10) $quality = 10;
				$quality = round(10 - ($quality / 10));
				imagepng($this->image, $file, $quality);
// EOF - Zappo - ImagEngine - Added PNG Quality
			} elseif ($extension == 'gif') {
				imagegif ($this->image, $file);
			}

			imagedestroy($this->image);
		}
	}

// BOF - Zappo - ImagEngine - Remove transparency
    public function flatten($color='') {
		if ($this->transparent && $this->info['mime'] == 'image/jpeg') {
			if (!$color) $color = 'FFFFFF';
			$image_old = $this->image;
			$this->image = imagecreatetruecolor($this->info['width'], $this->info['height']);
			$r = hexdec(substr($color, 0, 2));
			$g = hexdec(substr($color, 2, 2));
			$b = hexdec(substr($color, 4, 2));
			$transcolor = imagecolorallocate($this->image, $r, $g, $b);
			imagefill($this->image,0,0,$transcolor);
			imagecopyresampled($this->image, $image_old, 0, 0, 0, 0, $this->info['width'], $this->info['height'], $this->info['width'], $this->info['height']);
			$this->transparent = false;
		}
	}
// BOF - Zappo - ImagEngine - Remove transparency

// BOF - Zappo - ImagEngine - Changed Resizing function (Fixed = true, false or 'stretch')
    public function resize($width = 0, $height = 0, $fixed = false, $border = 0) {
    	if (!$this->info['width'] || !$this->info['height']) {
			return;
		}

		if (!$width && !$height) {
			echo '<b style="color:red;">No Width/Height Settings... Did you configure your image sizes?<br/></b>';
			return;
		}
		
		$xpos = $ypos = (int)$border;
		$new = $this->calculateRatios($width,$height);
      		        
       	$image_old = $this->image;
		if ($fixed && $width && $height) {
			if ($fixed == 'stretch') {
				$new['width'] = $width;
				$new['height'] = $height;
			} else {
				$xpos += ($width - $new['width']) / 2;
				$ypos += ($height - $new['height']) / 2;
			}
		} else {
			$width = $new['width'];
			$height = $new['height'];
		}
		$this->image = imagecreatetruecolor(($width + ($border * 2)),($height + ($border * 2)));
		imagealphablending($this->image, false);
		imagesavealpha($this->image, true);
		$transcolor = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
		imagefill($this->image,0,0,$transcolor);
	
		imagecopyresampled($this->image, $image_old, $xpos, $ypos, 0, 0, $new['width'], $new['height'], $this->info['width'], $this->info['height']);
		imagedestroy($image_old);
           
		$this->transparent = true;
		$this->info['width']  = $width;
		$this->info['height'] = $height;
	}
// EOF - Zappo - ImagEngine - Changed Resizing function
    

// BOF - Zappo - ImagEngine - (Re-)Set image size without scaling the image
	public function setsize($width=0, $height=0, $fixed='', $color='') {
		$old = $this->info;
		$new = $this->calculateRatios($width,$height);
		$oldimg = $this->image;
		if (!$fixed || !$width || !$height) {
			$width = $new['width'];
			$height = $new['height'];
		}
       	$this->image = imagecreatetruecolor($width, $height);
		if (!$color) {
			imagesavealpha($this->image,true);
			$transcolor = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
		} else {
			$r = hexdec(substr($color, 0, 2));
			$g = hexdec(substr($color, 2, 2));
			$b = hexdec(substr($color, 4, 2));
			$transcolor = imagecolorallocate($this->image, $r, $g, $b);
		}
		imagefill($this->image, 0, 0, $transcolor);
		$xpos = ($width - $old['width']) / 2;
		$ypos = ($height - $old['height']) / 2;
		$this->merge($oldimg, $xpos, $ypos);
		$this->transparent = true;
		$this->info = array(
			'width' => $width,
			'height' => $height,
			'mime' => $old['mime']
		);
    }
// EOF - Zappo - ImagEngine - (Re-)Set image size without scaling the image

	public function watermark($file, $position = 'bottomright') {
		$watermark = $this->create($file);

		$watermark_width = imagesx($watermark);
		$watermark_height = imagesy($watermark);

		switch($position) {
			case 'topleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = 0;
				break;
			case 'topright':
				$watermark_pos_x = $this->info['width'] - $watermark_width;
				$watermark_pos_y = 0;
				break;
			case 'bottomleft':
				$watermark_pos_x = 0;
				$watermark_pos_y = $this->info['height'] - $watermark_height;
				break;
			case 'bottomright':
				$watermark_pos_x = $this->info['width'] - $watermark_width;
				$watermark_pos_y = $this->info['height'] - $watermark_height;
				break;
		}

		imagecopy($this->image, $watermark, $watermark_pos_x, $watermark_pos_y, 0, 0, 120, 40);

		imagedestroy($watermark);
	}

	public function crop($top_x, $top_y, $bottom_x, $bottom_y) {
		$image_old = $this->image;
		$this->image = imagecreatetruecolor($bottom_x - $top_x, $bottom_y - $top_y);

		imagecopy($this->image, $image_old, 0, 0, $top_x, $top_y, $this->info['width'], $this->info['height']);
		imagedestroy($image_old);

		$this->info['width'] = $bottom_x - $top_x;
		$this->info['height'] = $bottom_y - $top_y;
	}

// BOF - Zappo - ImagEngine - Replaced Function for rotation
	public function rotate($rotate) {
		$RotSrc = $this->image;
		$width = imagesx($RotSrc);
		$height = imagesy($RotSrc);
		// - Zappo - Calculate correct rotated-image size...
		$theta = deg2rad($rotate);
		$CenterX = $width/2;
		$CenterY = $height/2;
		$XMin = $CenterX - 0.5 * ($width*Abs(cos($theta)) + $height*Abs(sin($theta)));
		$XMax = $CenterX + 0.5 * ($width*Abs(cos($theta)) + $height*Abs(sin($theta)));
		$YMin = $CenterY - 0.5 * ($width*Abs(sin($theta)) + $height*Abs(cos($theta)));
		$YMax = $CenterY + 0.5 * ($width*Abs(sin($theta)) + $height*Abs(cos($theta)));
		$newwidth = $XMax - $XMin;
		$newheight = $YMax - $YMin;
		$this->image = imagecreatetruecolor($newwidth, $newheight);
		$color = imagecolorallocate($this->image, 255, 255, 255);
		imagecolortransparent($this->image, $color);
		$this->image = imagerotate($RotSrc, $rotate, $color);
		imagealphablending($this->image, true);
		imagesavealpha($this->image,true);
		$this->transparent = true;
		$this->info['width'] = $newwidth;
		$this->info['height'] = $newheight;
		imagedestroy($RotSrc);
	}
// EOF - Zappo - ImagEngine - Replaced Function for rotation

	private function filter($filter) {
		imagefilter($this->image, $filter);
	}

// BOF - Zappo - ImagEngine / Option Types - Replaced Function for Text Images
	public function text($text, $height, $fontfile, $rotate, $color, $bgcolor, $fontsize=30) {
		if (!$fontsize) $fontsize = 30; // Big font size, sharp image
		if ($color != $bgcolor){
			$color = $this->html2rgb($color);
			$colorb = $this->html2rgb((($bgcolor) ? $bgcolor : '000000'));
		} else {
			$color = $this->html2rgb($color);
			foreach ($color as $i => $c) $colorb[$i] = ($c > 128) ? 0 : 255;
		}
		// Get the boundingbox from imagettfbbox(), and remove whitespace to get the correct dimensions
		if (function_exists('imagettfbbox')) {
			$rect = imagettfbbox($fontsize, 0, $fontfile, $text);
		} else {
			// Image Preview not available!! Need GD + FreeType installed!!
			$rect = false;
		}
		if (!$rect) return false;
		$min_x = min(array($rect[0], $rect[2], $rect[4], $rect[6]));
		$max_x = max(array($rect[0], $rect[2], $rect[4], $rect[6]));
		$min_y = min(array($rect[1], $rect[3], $rect[5], $rect[7]));
		$max_y = max(array($rect[1], $rect[3], $rect[5], $rect[7]));
		$w = ($max_x - $min_x);
		$h = ($max_y - $min_y);
		$left = abs($min_x) + $w;
		$top = abs($min_y) + $h;
		$img = @imagecreatetruecolor($w << 2, $h << 2);
		$white = imagecolorallocate($img, 255, 255, 255);
		$black = imagecolorallocate($img, 0, 0, 0);
		imagefilledrectangle($img, 0, 0, imagesx($img), imagesy($img), $black);
		if (!imagettftext($img, $fontsize, 0, $left, $top, $white, $fontfile, $text)) $this->printErrorImage("Error with ImageTtfText(img, $fontsize, 0, $left, $top, $white, $fontfile, $text)");
		// start scanning (0=> black => empty)
		$rleft = $w4 = $w<<2;
		$rtop = $h4 = $h<<2;
		$rright = $rbottom = 0;
		for($x = 0; $x < $w4; $x++) for($y = 0; $y < $h4; $y++) if (imagecolorat( $img, $x, $y )) {
			$rleft   = min($rleft, $x);
			$rright  = max($rright, $x);
			$rtop    = min($rtop, $y);
			$rbottom = max($rbottom, $y);
		}
		$this->image = @imagecreatetruecolor($rright - $rleft + 1, $rbottom - $rtop + 1);
		// Get and set background Color
		if ($bgcolor) {
			$colorb = imagecolorallocate($this->image, $colorb[0], $colorb[1], $colorb[2]);
		} else {
			$colorb = imagecolorallocatealpha($this->image, 254, 254, 254, 127);
		}
		if ($colorb === false) $this->printErrorImage("Error with imagecolorallocate (Text Background)");
		if (!imagefill($this->image,0,0,$colorb)) $this->printErrorImage("Error with imageFill(image,0,0, $colorb)");
		// Make background Transparent
		if (!$bgcolor && !imagecolortransparent($this->image, $colorb)) $this->printErrorImage("Error with imageColorTransparent(image, $colorb)");
		// Print the Text
		$textcolor = imagecolorallocate($this->image, $color[0], $color[1], $color[2]);
		if ($textcolor === false) $this->printErrorImage("Error with imagecolorallocate(image, ".$color[0].", ".$color[1].", ".$color[2].")");
		if (!imagettftext($this->image, $fontsize, 0, $left - $rleft, $top  - $rtop, $textcolor, $fontfile, $text)) $this->printErrorImage("Error with ImageTtfText(img, $fontsize, 0, ".($left - $rleft).", ".($top - $rtop).", $textcolor, $fontfile, $text)");
		// Scale the image to exact dimentions
		if ($height) {
			$width = ($height / ($rbottom - $rtop + 1)) * ($rright - $rleft + 1);
			$img = $this->image;
			$this->image = @imagecreatetruecolor($width, $height);
			if (!imagefill($this->image,0,0,$colorb)) $this->printErrorImage("Error with imageFill(image,0,0, $colorb)");
			if (!$bgcolor && !imagecolortransparent($this->image, $colorb)) $this->printErrorImage("Error with imageColorTransparent(image, $colorb)");
			if (!imagecopyresampled($this->image,$img,0,0,0,0,$width,$height,($rright - $rleft + 1),($rbottom - $rtop + 1))) $this->printErrorImage("Error in ImageText:\n       ImageCopyResampled(image, $img, 0,0,0,0, $width, $height, ".($rright - $rleft + 1).", ".($rbottom - $rtop + 1).") Failed!!!");
		}
		$this->info['width'] = imagesx($this->image);
		$this->info['height'] = imagesy($this->image);
		imagedestroy($img);
		if ($rotate) $this->rotate($rotate);
    }
// EOF - Zappo - ImagEngine / Option Types - Replaced Function for Text Images

// BOF - Zappo - ImagEngine - Replaced Merge function: Merge (png) image over this->image
	public function merge($merge,$imgX=0,$imgY=0,$opacity=100) {
		if (is_string($merge)) $merge = imagecreatefrompng($merge);
		$width = imagesx($merge);
		$height = imagesy($merge);
		imagealphablending($this->image,true);
		imagealphablending($merge,true);
		imagesavealpha($this->image,true);
		imagesavealpha($merge,false);
		if ($opacity >= 100) {
			imagecopy($this->image, $merge, (int)$imgX, (int)$imgY, 0, 0, $width, $height);
		} else {
			$cut = imagecreatetruecolor($width, $height);
			// copying relevant section from background to the cut resource
			imagecopy($cut, $this->image, 0, 0, $imgX, $imgY, $width, $height);
			// copying relevant section from watermark to the cut resource
			imagecopy($cut, $merge, 0, 0, 0, 0, $width, $height);
			// insert cut resource to destination image
			imagecopymerge($this->image, $cut, $imgX, $imgY, 0, 0, $width, $height, $opacity);
		}
		imagedestroy($merge);
    }
// EOF - Zappo - ImagEngine - Replaced Merge function: Merge a (png) image over this->image

	private function html2rgb($color) {
		if ($color[0] == '#') {
			$color = substr($color, 1);
		}

		if (strlen($color) == 6) {
			list($r, $g, $b) = array($color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5]);
		} elseif (strlen($color) == 3) {
			list($r, $g, $b) = array($color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2]);
		} else {
			return false;
		}

		$r = hexdec($r);
		$g = hexdec($g);
		$b = hexdec($b);

		return array($r, $g, $b);
	}

// BOF - Zappo - ImagEngine - Added image Destroy, for cleanup, and setting memory limit
	public function destroy() {
		imagedestroy($this->image);
		// Set memory back to a decent setting
		if (ini_get("memory_limit") != '16M') ini_set('memory_limit', '16M');
	}
// EOF - Zappo - ImagEngine - Added image Destroy, for cleanup, and setting memory limit

// BOF - Zappo - ImagEngine - Added getInfo function
	public function getInfo($info='') {
		if (!empty($info)) return $this->info[$info];
		return $this->info;
	}
// EOF - Zappo - ImagEngine - Added getInfo function

// BOF - Zappo - ImagEngine - Added getInfo function
	public function getResource() {
		return $this->image;
	}
// EOF - Zappo - ImagEngine - Added getInfo function

// BOF - Zappo - ImagEngine - Added getData function
	public function getData($file='') {
		if ($this->info['mime'] == 'image/png' || $this->info['mime'] == 'image/x-png' || $this->info['mime'] == 'image/gif') {
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
		}
		if (!empty($file)) imagepng($this->image, $file, 5);
		ob_start();
		imagepng($this->image, null, 5);
		$data	= ob_get_contents();
		ob_end_clean();
		imagedestroy($this->image);
		return $data;
	}
// EOF - Zappo - ImagEngine - Added getInfo function

// BOF - Zappo - Calculate Ratios for resizing the images
	public function calculateRatios($newWidth,$newHeight) {
		$xRatio = $newWidth / $this->info['width'];
		$yRatio	= $newHeight / $this->info['height'];

		if (!empty($xRatio) && ($xRatio * $this->info['height']) < $newHeight) { // Resize the image based on width
			$size['height'] = ceil($xRatio * $this->info['height']);
			$size['width'] = $newWidth;
		} else { // Resize the image based on height
			$size['width'] = ceil($yRatio * $this->info['width']);
			$size['height'] = $newHeight;
		}
		return $size;
	}
// EOF - Zappo - Calculate Ratios for resizing the images

// BOF - Zappo - Added Function to import BMP Images.
	private function ImageCreateFromBMP($filename) {
		if (! $f1 = fopen($filename,"rb")) return FALSE;
		$FILE = unpack("vfile_type/Vfile_size/Vreserved/Vbitmap_offset", fread($f1,14));
		if ($FILE['file_type'] != 19778) return FALSE;
		$BMP = unpack('Vheader_size/Vwidth/Vheight/vplanes/vbits_per_pixel'.
					  '/Vcompression/Vsize_bitmap/Vhoriz_resolution'.
					  '/Vvert_resolution/Vcolors_used/Vcolors_important', fread($f1,40));
		$BMP['colors'] = pow(2,$BMP['bits_per_pixel']);
		if ($BMP['size_bitmap'] == 0) $BMP['size_bitmap'] = $FILE['file_size'] - $FILE['bitmap_offset'];
		$BMP['bytes_per_pixel'] = $BMP['bits_per_pixel']/8;
		$BMP['bytes_per_pixel2'] = ceil($BMP['bytes_per_pixel']);
		$BMP['decal'] = ($BMP['width']*$BMP['bytes_per_pixel']/4);
		$BMP['decal'] -= floor($BMP['width']*$BMP['bytes_per_pixel']/4);
		$BMP['decal'] = 4-(4*$BMP['decal']);
		if ($BMP['decal'] == 4) $BMP['decal'] = 0;
		$PALETTE = array();
		if ($BMP['colors'] < 16777216) {
			$PALETTE = unpack('V'.$BMP['colors'], fread($f1,$BMP['colors']*4));
		}
		$IMG = fread($f1,$BMP['size_bitmap']);
		$VIDE = chr(0);
		$res = imagecreatetruecolor($BMP['width'],$BMP['height']);
		$P = 0;
		$Y = $BMP['height']-1;
		while ($Y >= 0) {
			$X=0;
			while ($X < $BMP['width']) {
				if ($BMP['bits_per_pixel'] == 24) {
					$COLOR = unpack("V",substr($IMG,$P,3).$VIDE);
				} elseif ($BMP['bits_per_pixel'] == 16) {
					$COLOR = unpack("n",substr($IMG,$P,2));
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				} elseif ($BMP['bits_per_pixel'] == 8) {
					$COLOR = unpack("n",$VIDE.substr($IMG,$P,1));
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				} elseif ($BMP['bits_per_pixel'] == 4) {
					$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
					if (($P*2)%2 == 0) $COLOR[1] = ($COLOR[1] >> 4) ; else $COLOR[1] = ($COLOR[1] & 0x0F);
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				} elseif ($BMP['bits_per_pixel'] == 1) {
					$COLOR = unpack("n",$VIDE.substr($IMG,floor($P),1));
					if     (($P*8)%8 == 0) $COLOR[1] =  $COLOR[1]        >>7;
					elseif (($P*8)%8 == 1) $COLOR[1] = ($COLOR[1] & 0x40)>>6;
					elseif (($P*8)%8 == 2) $COLOR[1] = ($COLOR[1] & 0x20)>>5;
					elseif (($P*8)%8 == 3) $COLOR[1] = ($COLOR[1] & 0x10)>>4;
					elseif (($P*8)%8 == 4) $COLOR[1] = ($COLOR[1] & 0x8)>>3;
					elseif (($P*8)%8 == 5) $COLOR[1] = ($COLOR[1] & 0x4)>>2;
					elseif (($P*8)%8 == 6) $COLOR[1] = ($COLOR[1] & 0x2)>>1;
					elseif (($P*8)%8 == 7) $COLOR[1] = ($COLOR[1] & 0x1);
					$COLOR[1] = $PALETTE[$COLOR[1]+1];
				} else {
					return FALSE;
				}
				imagesetpixel($res,$X,$Y,$COLOR[1]);
				$X++;
				$P += $BMP['bytes_per_pixel'];
			}
			$Y--;
			$P+=$BMP['decal'];
		}
		fclose($f1);
		return $res;
	}
// EOF - Zappo - Added Function to import BMP Images.

// BOF - Zappo - Print Error Messages as images (to get a clear 'warning' somethings wrong)
	function printErrorImage($string) {
		$string = str_replace("\r","",$string);
		$string = explode("\n",$string);
		$maxlen = 0;
	    foreach ($string as $str){
			if (strlen($str) > $maxlen) {
				$maxlen = strlen($str);
			}
		}
		$font_size = 6;
		$width = imagefontwidth($font_size)*$maxlen; // Create image width dependant on width of the string
		$height = imagefontheight($font_size) * count($string); // Set height to that of the font
		$this->image = imagecreate($width,$height); // Create the image pallette
		$bg = imagecolorallocate($this->image, 205, 255, 255); // Grey background
		$color = imagecolorallocate($this->image, 0, 0, 0); // White font color
		$ypos = 0;
		foreach ($string as $str){
			$len = strlen($str);
			for($i=0;$i<$len;$i++){
				$xpos = $i * imagefontwidth($font_size); // Position of the character horizontally
				imagechar($this->image, $font_size, $xpos, $ypos, $str, $color); // Draw character
				$str = substr($str, 1); // Remove character from string
			}
			$ypos = $ypos + imagefontheight($font_size);
		}
		imagepng($this->image);
	}
// EOF - Zappo - Print Error Messages as images (to get a clear 'warning' somethings wrong)
}
