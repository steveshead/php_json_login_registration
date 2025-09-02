<?php
// Set the content type to PNG image
header('Content-Type: image/png');

// Create a 200x200 image
$image = imagecreatetruecolor(200, 200);

// Define colors
$bg_color = imagecolorallocate($image, 52, 152, 219); // Blue background
$text_color = imagecolorallocate($image, 255, 255, 255); // White text

// Fill the background
imagefill($image, 0, 0, $bg_color);

// Set the font size and calculate position
$font_size = 80;
$text = isset($_GET['initials']) ? substr($_GET['initials'], 0, 2) : 'U'; // Default to 'U' for User

// Calculate text position to center it
$text_width = imagefontwidth($font_size) * strlen($text);
$text_height = imagefontheight($font_size);
$x = (200 - $text_width) / 2;
$y = (200 - $text_height) / 2 + $text_height;

// Add the text
imagestring($image, 5, $x, $y - 30, $text, $text_color);

// Output the image
imagepng($image);

// Free up memory
imagedestroy($image);
?>