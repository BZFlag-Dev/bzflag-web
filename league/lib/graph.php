<?php // $Id: graph.php,v 1.2 2005/03/24 13:27:14 dennismp Exp $ vim:sts=2:et:sw=2

$g = new ImageGraph(400, 300);

$g->setXrange (0, 1);
$g->setYrange (5, 15);

$g->createImage ('jpg', 95);

class ImageGraph {
	var $imgW, $imgH;
	var $img;
	var $colBG;
	var $colGrid;
	var $colLabels;
		

	function ImageGraph ($w, $h){
		$this->imgW = $w;
		$this->imgH = $h;
		$this->img = imagecreate ($w, $h);
	}
	

	function setXrange ($min, $max){
	}
	
	function setYrange ($min, $max){
	}
	

	
	function setCurveData ($col, $arDat){
	}

	
	function setColBG ($r, $g, $b){
	  $this->colBG = imagecolorallocate($this->img, $r, $g, $b);
	}
	function setColGrid ($r, $g, $b){
	  $this->colBG = imagecolorallocate($this->img, $r, $g, $b);
	}
	function setColLabels ($r, $g, $b){
	  $this->colLabels = imagecolorallocate($this->img, $r, $g, $b);
	}

	
	function createImage ($type, $quality=null){
	
	  header("Content-type: image/jpg");
	  imagejpeg($this->img, '', $quality);
	
	}
}



?>
