<?php

	ini_set('memory_limit', '-1');
	ini_set('max_execution_time', 300);

    $video = $_GET['video'];
    $fps = $_GET['fps'];
    $width = $_GET['width'];
    $height = $_GET['height'];
    $from = null;
    if (isset($_GET['from'])) {
        $from = $_GET['from'];
    }
    $to = null;
    if (isset($_GET['to'])) {
        $to = $_GET['to'];
    }
    $index = null;
    if (isset($_GET['index'])) {
        $index = $_GET['index'];
    }

    function deleteLocalFiles() {
        $lastimages = glob("[0-9]*.{jpg,png,mp4,flv,mkv,webm}", GLOB_BRACE);
        foreach ($lastimages as $bimages) {
            unlink($bimages);
        }
    }

    function checkType($file) {
    	$allowedTypes = array(".mp4", ".webm", ".ogv", ".gif", ".flv");
    	foreach ($allowedTypes as $at) {
    		if (stripos($file, $at) !== false) return true;
    	}
    	return false;
    }

    function start() {
        $video = $GLOBALS['video'];
        $fps = $GLOBALS['fps'];
        $width = $GLOBALS['width'];
        $height = $GLOBALS['height'];
        $from = $GLOBALS['from'];
        $to = $GLOBALS['to'];
        $index = $GLOBALS['index'];

        if ($video) {
            if (checkType($video)) {
                deleteLocalFiles();
                function getRGB($imgname) {
                    $img = ImageCreateFromPng($imgname);
                    $colorvals = array();
                    for($x = 0; $x < imagesx($img); $x++) {
                        for ($y = 0; $y < imagesy($img); $y++) {
                            $color = imagecolorat($img, $x, $y);
                            $r = ($color >> 16) & 0xFF;
                            $g = ($color >> 8) & 0xFF;
                            $b = $color & 0xFF;
                            array_push($colorvals, $r.', '.$g.', '.$b);
                        }
                    }
                    return $colorvals;
                }

                file_put_contents("video.mp4", fopen($video, 'r'));
                $cmd = sprintf('ffmpeg -i video.mp4 -vf scale=%dx%d,fps=%d', $width, $height, $fps);
                $cmdfull = $cmd." %d.png";
                exec($cmdfull);

                $images = glob("[0-9]*.png");
                sort($images, SORT_NUMERIC);

                if ($from == null or $to == null) {
                    $from = 0;
                    $to = count($images);
                }
                

                if ($index != null) {
                    $from = $index;
                    $to = $index + 1;
                }

                $mArr = array();
                
                for ($i = $from; $i < $to; $i++) {
                    $frameArr = array();

                    $image = $images[$i];
                    $colarray = getRGB($image);
                    foreach ($colarray as $col) {
                        array_push($frameArr, $col);
                    }
                    array_push($mArr, $frameArr);
                }

                echo json_encode($mArr);                

                deleteLocalFiles();

            } else {
                echo "\"".$video."\" Is not one of the accepted video types!";
            }
        } else {
            echo "Missing arguments!";
        }
    }
    start();

?>
