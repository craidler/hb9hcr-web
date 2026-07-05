<?php

session_start();

$code = substr(str_shuffle("ABCDEFGHJKLMNPQRSTUVWXYZ23456789"), 0, 5);
$_SESSION['captcha'] = $code;

$image = (object)[
    'height' => 20,
    'width' => 100,
];

$image->instance = imagecreatetruecolor($image->width, $image->height);
imagealphablending($image->instance, false); 
$color = imagecolorallocatealpha($image->instance, 0, 0, 0, 127);
imagefill($image->instance, 0, 0, $color);
imagealphablending($image->instance, true);
imagesavealpha($image->instance, true);

// Colors (First color allocated becomes the background)

// Add random distortion lines

$color = imagecolorallocatealpha($image->instance, 0, 0, 0, 100);
for ($i = 0; $i < 20; $i++) imageline($image->instance, rand(0, $image->width), rand(0, $image->height), rand(0, $image->width), rand(0, $image->height), $color);

imagestring($image->instance, 5, 26, 3, $code, imagecolorallocatealpha($image->instance, 0, 0, 0, 40));

// Output the image
header("Content-Type: image/png");
imagepng($image->instance);
imagedestroy($image->instance);