<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Compress Text to .PNG images and vice versa
 *
 *
 * LICENSE: Redistribution and use in source and binary forms, with or
 * without modification, are permitted provided that the following
 * conditions are met: Redistributions of source code must retain the
 * above copyright notice, this list of conditions and the following
 * disclaimer. Redistributions in binary form must reproduce the above
 * copyright notice, this list of conditions and the following disclaimer
 * in the documentation and/or other materials provided with the
 * distribution.
 *
 * THIS SOFTWARE IS PROVIDED ``AS IS'' AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN
 * NO EVENT SHALL CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS
 * OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR
 * TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH
 * DAMAGE.
 *
 * @category
 * @package     PNGCompress
 * @author      Phillip Dornauer <phillip@dornauer.cc>
 * @copyright   2010 Phillip Dornauer
 * @version     CVS: $Id: Convert.php,v 0.1 2011/09/15 22:00:17 dornauer Exp $
 * @license     http://www.opensource.org/licenses/bsd-license.php
 * @link        http://github.com/phillipdornauer/pngcompress
 */


class PNGCompress{
	private $handle;
	private $size;
	
	public function __destruct(){
		imagedestroy($this->handle);
	}
	
	public function from_file( $file ){
		return $this->from_string(
			file_get_contents(
				$file
			)
		);
	}
	public function from_string( $str ){
		$len = strlen( $str );
		
		$this->size = ceil( sqrt( $len ) );
		
		$lines = str_split( $str, $this->size );
		
		
		$this->create( $this->size, $this->size);
		
		foreach( $lines as $y => $line )
			for( $x = 0; $x < strlen( $line ); $x++ )
				$this->set_pixel( substr( $line, $x, 1 ), $x, $y );
		
		return $this;		
	}
	public function restore( $file ){
		$this->handle = imagecreatefrompng( $file );
		
		$w = imagesx( $this->handle );
		$h = imagesy( $this->handle );
		
		$b = "";
		
		for( $y = 0; $y < $h; $y++)
			for( $x = 0; $x < $w; $x++ )
				$b .= $this->get_pixel( $x, $y );
		
		return $b;
	}
	
	public function show(){
		header ('Content-type: image/png');
		imagepng($this->handle);
		return $this;
	}
	public function save( $file ){
		imagepng($this->handle, $file);
		return $this;
	}
	
	private function create( $width, $height ){
		$this->handle = imagecreatetruecolor( $width, $height );
		imagefill( $this->handle, 0, 0, 
			imagecolorallocate( $this->handle, 255, 255, 255 )
		);
	}
	
	private function get_pixel( $x, $y ){
		$rgba = $this->int2rgba(
			imagecolorat( $this->handle, $x, $y )
		);
		if( $rgba["r"] == "255" ) return "";
		
		$str = "";
		$str .= $rgba["r"] ? chr( $rgba["r"] ) : "";
		$str .= $rgba["g"] ? chr( $rgba["g"] ) : "";
		$str .= $rgba["b"] ? chr( $rgba["b"] ) : "";
		
		return urldecode( $str );
	}
	private function int2rgba( $int ){
		$a = ($int >> 24) & 0xFF;
		$r = ($int >> 16) & 0xFF;
		$g = ($int >> 8) & 0xFF;
		$b = $int & 0xFF;
		return array('r'=>$r, 'g'=>$g, 'b'=>$b, 'a'=>$a); 
	}
	
	private function set_pixel( $char, $x, $y ){
		imagesetpixel( $this->handle, $x, $y, $this->get_color( $char ) );
	}
	
	private function get_color( $chr ){		
		$chr = urlencode( $chr );
		
		$rgb = array(0,0,0);
		foreach( str_split( $chr, 1 ) as $k => $c )
			$rgb[$k] = ord($c);
		
		return imagecolorallocate( $this->handle, $rgb[0], $rgb[1], $rgb[2] );
	}
}
