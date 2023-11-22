<?php
/*
	Event Map marker image generation with number in it
*/

function createImage($number, $url) {

        // $blank = $url."/assets/marker.png";
        $blank = "./assets/marker.png";

        //$image = @imagecreatefrompng($blank);
        $image = LoadPNG($blank);

        // pick color for the text
        $fontcolor = imagecolorallocate($image, 255, 255, 255);

        $font = 12;
        $fontsize = 16;

        $width = imagefontwidth($font) * strlen($number) ;
        $height = imagefontheight($font) ;

        $x = (imagesx($image) - $width) / 2;
        $y = 8;

        //white background
        $backgroundColor = imagecolorallocate ($image, 255, 255, 255);

        //white text
        $textColor = imagecolorallocate($image, 255, 255, 255);

        //  preserves the transparency
        imagesavealpha($image, true);
        imagealphablending($image, false);

        imagestring($image, $font, $x, $y, $number, $textColor);

        // tell the browser that the content is an image
        header('Content-type: image/png');

        // output image to the browser
        imagepng($image);

        // delete the image resource
        imagedestroy($image);
    }

function LoadPNG($imgname) {
        /* Attempt to open */
        $im = imagecreatefrompng($imgname);

        /* See if it failed */
        if(!$im) {
                /* Create a blank image */
                $im  = imagecreatetruecolor(150, 30);
                $bgc = imagecolorallocate($im, 255, 255, 255);
                $tc  = imagecolorallocate($im, 0, 0, 0);

                imagefilledrectangle($im, 0, 0, 150, 30, $bgc);

                /* Output an error message */
                imagestring($im, 1, 5, 5, 'Error loading ' . $imgname, $tc);
        }
        return $im;
}

if(!isset($_GET['url'])) {
    $_GET['url'] = ".";
}
if(!isset($_GET['number'])) {
    $_GET['number'] = "99";
}

createImage($_GET['number'], urldecode($_GET['url']) );
?>