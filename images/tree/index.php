<?php 


if(isset($_GET) && isset($_GET['structure']) && preg_replace('/[0-3]/','',$_GET['structure'])==''){
	$basedir = dirname(__FILE__) .'/';
	$structure = $_GET['structure'];
	if(is_numeric($_GET['structure']) && file_exists("$basedir$structure.gif")){
		header("Location: $structure.gif");
		exit();
	}
	if(!is_numeric($_GET['structure'])){
		
		die("Codice struttura invalido");
		
	}
	$l = strlen($structure);
	if($l==0){
		$img  = imagecreate(1, 1);
	}else{
		
		$img  = imagecreate($l*20, 100);
		$color = imagecolorallocate($img,255,255,255);
		imagefill($img,1,1, $color);
		for($i = 0 ; $i<$l; $i++){
			$file = $basedir. substr($structure,$i,1) .'.gif';
			#echo($file);
			if(file_exists($file)){
				$img2 = imagecreatefromgif($file);
				imagecopy($img,$img2,$i*20,0,0,0,20,100);
				imagedestroy($img2);
			}
		}
	}
	imagegif($img, "$basedir$structure.gif");
	imagegif($img);
	imagedestroy($img);
}
?>