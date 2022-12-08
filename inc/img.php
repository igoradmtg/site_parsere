<?php
// »зменить размер картинки
// ѕример
// $img = resize_image("image.jpg", 200, 200);
// imagejpeg($img,"resize.jpg",95);

function resize_image($file, $w, $h, $crop=FALSE,$is_show_msg = false) {
    try {
        $src = @imagecreatefrompng($file);
    } catch (Exception $e) {
        $src = false;
    }
    
    if($src==false) {
        try {
            $src = @imagecreatefromjpeg($file);   //try JPEG
        } catch (Exception $e) {
            $src = false;
        }
    }
    if(!$src) {
        try {
            $src = @imagecreatefromgif($file);   //try gif
        } catch (Exception $e) {
            $src = false;
        }
    }
    if(!$src) {echo "Error read image $file <br>" . PHP_EOL;return false;}
    $width = imagesx($src);
    $height = imagesy($src);
    echo "W: $width H: $height" . PHP_EOL;
    
    $r = $width / $height;
    if ($crop) {
        $width_orig = $width;
        $height_orig = $height;
        if ($width > $height) {
            $width = $height;
        } else {
            $height = $width;
        }
        $newwidth = $w;
        $newheight = $h;
    } else {
        if ($w/$h > $r) {
            $newwidth = intval($h*$r);
            $newheight = $h;
        } else {
            $newheight = intval($w/$r);
            $newwidth = $w;
        }
    }
    echo "W: $width H: $height NW:$newwidth NH: $newheight" . PHP_EOL;
    
    $dst = imagecreatetruecolor($newwidth, $newheight);
    if ($crop) {
      if ($is_show_msg) {
        echo "Original: ($width_orig x $height_orig) Thumb: ($width x $height)" . PHP_EOL;
      }
      $src_x = intval($width_orig / 2 - $width / 2);
      $src_y = intval($height_orig / 2 - $height / 2);
      //echo "$src_x $src_y \r\n";
      imagecopyresampled($dst, $src, 0, 0, $src_x, $src_y, $newwidth, $newheight, $width, $height);
    } else {
      imagecopyresampled($dst, $src, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
    }
    return $dst;
}

// —делать несколько картинок в одну по горизонтали
//$ar_fname=array('img1.jpg','img2.jpg','img3.jpg');$h=200;
//$img=resize_many_image($ar_fname,$h);
//imagejpeg($img,"resize.jpg",95);
function resize_many_image($ar_fname,$h)
{
  $width_all=0;
  foreach ($ar_fname as $fn)
  {
    $ar_size=getimagesize($fn);
    if ($ar_size==false) {echo "Error get image size $fn <br>" . PHP_EOL;return false;}
    list($width, $height) = $ar_size;
    $r = $width / $height;
    $width_all+=intval($h*$r);
  }
  $dst = imagecreatetruecolor($width_all, $h);
  $width_cur=0;
  foreach ($ar_fname as $fn)
  {
    $ar_size=getimagesize($fn);
    if ($ar_size==false) {echo "Error get image size $fn <br>" . PHP_EOL;return false;}
    list($width, $height) = $ar_size;
    $r = $width / $height;
    $width_new=intval($h*$r);
    $src = @imagecreatefromjpeg($fn);
    if(!$src) $src = @imagecreatefromgif ($fn);   //try gif
    if(!$src) $src = @imagecreatefrompng ($fn);   //try png
    if(!$src) {echo "Error read image $fn <br>\r\n";return false;}
    echo "$fn $width $height $width_cur $h $width_new <br>\r\n";
    imagecopyresampled($dst, $src, $width_cur, 0, 0, 0, $width_new, $h, $width, $height);  
    $width_cur+=$width_new;
  }
  return $dst;
}

// ѕолучить размер картинки чтобы она вписывалась в ширину и высоту
function get_resize_image($file, $w, $h) 
{
  $ar_size_image=getimagesize($file);
  if ($ar_size_image==false) {return false;}
  list($width, $height) = $ar_size_image;
  //echo "w = $width h = $height <br>" . PHP_EOL;
  $r = $width / $height;
  if ($w/$h > $r) {
      $newwidth = intval($h*$r);
      $newheight = $h;
  } else {
      $newheight = intval($w/$r);
      $newwidth = $w;
  }
  return array('w'=>$newwidth,'h'=>$newheight);
}




function add_text_to_image($file_src,$file_output,$ar_text,$font_file,$start_size=500) {
  GLOBAL $error_add_text_to_image;
  $img = @imagecreatefromjpeg($file_src);
  if ($img==false) $img = @imagecreatefrompng($file_src);
  if ($img==false) $img = @imagecreatefromgif($file_src);
  if ($img==false) {$error_add_text_to_image="Error create image $GlobalFileName ";return false;}
  $widht=imagesx($img);
  $height=imagesy($img);
  $color_white = imagecolorallocate($img, 255, 255, 255);
  $color_gray = imagecolorallocatealpha($img, 0, 0, 0, 80); // «начение в диапазоне от 0 до 127. 0 означает непрозрачный цвет, 127 означает   
  $text_size=$start_size;
  $is_size_error=true;
  while($is_size_error)
  {
    $text_size--;
    $max_width=0;$max_height=0;
    foreach($ar_text as $text) {
      $text_arsize=imagettfbbox($text_size,0,$font_file,$text);
      if ($text_arsize==false) {$error_add_text_to_image="Error font_size $text_size $font_file $text";return false;}
      $text_width=$text_arsize[4]-$text_arsize[6];
      $text_height=$text_arsize[1]-$text_arsize[5];
      //log_error($text_width.' X '.$text_height.' '.$text_arsize[4].' '.$text_arsize[6].' font size '.$text_size);
      if ($text_width>$max_width) {$max_width=$text_width;$max_height=$text_height;}
    }
    if ($max_width<=$widht) $is_size_error=false;
  }
  imagefilledrectangle($img,0,0,$widht,count($ar_text)*$max_height+10,$color_gray);
  $cnt=0;
  foreach($ar_text as $text) {
    imagettftext($img, $text_size, 0, 0, ($cnt+1)*$max_height, $color_white, $font_file, $text);
    $cnt++;
  }
  imagejpeg($img,$file_output,95);
  imagedestroy($img);
  return true;
}
//$ar_fname=array('1.jpg','2.jpg','3.jpg','4.jpg','5.jpg');$h=400;
//$img=resize_many_image($ar_fname,$h);
//if ($img!=false) imagejpeg($img,"img1.jpg",95);

