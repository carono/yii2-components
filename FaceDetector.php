<?php
namespace carono\components;

use svay\Exception\NoFaceException;

class FaceDetector extends \svay\FaceDetector
{

    public function cropFaceToJpegOffset($outFileName = null)
    {
        if (empty($this->face)) {
            throw new NoFaceException('No face detected');
        }
        $im_width = imagesx($this->canvas);
        $im_height = imagesy($this->canvas);
        $cords = $this->face;
        $xLeft = $cords["x"];
        $yTop = $cords["y"];
        $xRight = $im_width - $cords["x"] + $cords["w"];
        $yBottom = $im_height - $cords["y"] + $cords["w"];
        $x = min([$xLeft, $yTop, $xRight, $yBottom]);
        $cords["x"] -= $x;
        $cords["y"] -= $x;
        $cords["w"] += $x * 2;
        $canvas = imagecreatetruecolor($cords['w'], $cords['w']);
        imagecopy($canvas, $this->canvas, 0, 0, $cords['x'], $cords['y'], $cords['w'], $cords['w']);
        if ($outFileName === null) {
            header('Content-type: image/jpeg');
        }
        imagejpeg($canvas, $outFileName);
    }
}