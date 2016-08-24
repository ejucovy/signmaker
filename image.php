<?php
include("include/settings.php");

$images = file_get_contents($vardir . "images.json");
$images = json_decode($images, true);

$formats = file_get_contents($vardir . "formats.json");
$formats = json_decode($formats, true);

$config = $images[$_GET['t']];
if (!$config) {
  exit(); // @@TODO 400
}

$placement  = $config['placement'];
if (!$placement) {
  $placement = Array(
                Array("x"=>$config['x'],
                      "y"=>$config['y'],
                      "max_width"=>$config['max_width'],
                      "max_height"=>$config['max_height']
                      ));
}
$fontfile   = $vardir . "fonts/".$config['font'].".ttf";
$red        = $config['color'][0];
$green      = $config['color'][1];
$blue       = $config['color'][2];
$imagefile  = $vardir . "images/".$config['image'].".png";
$fontsize   = $config['fontsize'];
$i18n       = $config['i18n'];

function resize($fontsize, $angle, $fontfile, $string, $width) {
  $testbox = imagettfbbox($fontsize, $angle, $fontfile, $string);
  while($testbox[2] > $width) {
    $fontsize--;
    $testbox = imagettfbbox($fontsize, $angle, $fontfile, $string);
  }
  return Array($fontsize, $testbox[2], abs($testbox[5] + $testbox[3]));
}

header ("Content-Type: image/png");
if ($_GET['download'] == "true") {
  header("Content-Disposition: attachment; filename=\"sign.png\"");
}

$image = imagecreatefrompng($imagefile);
$text_color = imagecolorallocate($image, $red, $green, $blue);//black text
$angle = 0;

if ($i18n == "ar") {
  require('./I18N/Arabic.php');
  $Arabic = new I18N_Arabic('Glyphs');
}

if (count($placement) == 1) {
  $message = $_GET["message"];
  if ($i18n == "ar") {
    $message = $Arabic->utf8Glyphs($message);
  }

  $p = $placement[0];
  list($fontsize, $realwidth, $realheight) = resize($fontsize, $angle, $fontfile, $message, $p['max_width']);
  $p['x'] = $p['x'] + ($p['max_width'] - $realwidth) / 2;
  $p['y'] = $p['y'] - ($p['max_height'] - $realheight) / 2;
  imagettftext($image, $fontsize, $angle, $p['x'], $p['y'],
               $text_color, $fontfile, $message);
} else {
  for ($i = 1; $i <= count($placement); $i++) {
    $message = $_GET['m'.$i];
    $p = $placement[$i-1];
    if ($i18n == "ar") {
      $message = $Arabic->utf8Glyphs($message);
    }
    $fontsize   = $config['fontsize'];    
    list($fontsize, $realwidth, $realheight) = resize($fontsize, $angle, $fontfile, $message, $p['max_width']);
    $p['x'] = $p['x'] + ($p['max_width'] - $realwidth) / 2;
    $p['y'] = $p['y'] - ($p['max_height'] - $realheight) / 2;
    imagettftext($image, $fontsize, $angle, $p['x'], $p['y'],
                 $text_color, $fontfile, $message);
  }
}  

if ( $_GET['resize'] && $formats[ $_GET['resize'] ] ) {
  $format = $formats[ $_GET['resize'] ];

  $fx = $format['w'];
  $fy = $format['h'];
  $size = getimagesize($imagefile);

  if ($fx > $fy) {
    $ratio = $format['w'] / $format['h'];
    $rx = $size[0] * $ratio;
    $ry = $size[1];
  } else {
    $ratio = $format['h'] / $format['w'];
    $rx = $size[0];
    $ry = $size[1] * $ratio;
  }
  $rred = $config['fillcolor'][0];
  $rgreen = $config['fillcolor'][1];
  $rblue = $config['fillcolor'][2];

  $resized = imagecreatetruecolor($rx, $ry);
  $rcolor = imagecolorallocate($resized, $rred, $rgreen, $rblue);
  imagefill($resized, 0, 0, $rcolor);

  $start_x = ($rx - 1282) / 2;
  $start_y = ($ry - 1282) / 2;
  imagecopy($resized, $image, $start_x, $start_y, 0, 0, 1282, 1282);

  imagedestroy($image);

  $final = imagecreatetruecolor($fx, $fy);
  imagecopyresampled($final, $resized, 0, 0, 0, 0, $fx, $fy, $rx, $ry);

  imagedestroy($resized);

  if ($config['fillbordercolor']) {
    $bordercolor = imagecolorallocate($final, $config['fillbordercolor'][0], $config['fillbordercolor'][1], $config['fillbordercolor'][2]);
    imagerectangle($final, 0, 0, $fx - 1, $fy - 1, $bordercolor);
  }

  imagepng($final);
  imagedestroy($final);
  return;
}
imagepng($image);
imagedestroy($image);
?>
