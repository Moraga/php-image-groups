<?php

header('content-type: text/plain; charset=utf-8');

$img = imagecreatefromjpeg('desk.jpg');
//$img = imagecreatefromgif('german.gif');
//$img = imagecreatefromgif('carid.gif');
//$img = imagecreatefrompng('triangle.png');
//$img = imagecreatefromgif('chessboard.gif');

$w = imagesx($img);
$h = imagesy($img);

$set = 0;
$cnt = 0;
$mtx = [];
$sup = null;
$var = 6;

for ($y = 0; $y < $h; ++$y) {
    $row = [];
    for ($x = 0; $x < $w; ++$x) {
        $rgb = imagecolorat($img, $x, $y);
        $cur = [($rgb >> 16) & 0xFF, ($rgb >> 8) & 0xFF, $rgb & 0xFF];
        if ($x == 0) {
            if ($y == 0) {
                $set = 0;
            } else if (diff($cur, $sup[$x]) > $var) {
                $set = ++$cnt;
            } else {
                $set = $sup[$x][3];
            }
        } else if (diff($cur, $row[$x-1]) > $var) {
            if ($y == 0) {
                $set = 0;
            } else if (diff($cur, $sup[$x]) > $var) {
                if ($x > 0 && diff($cur, $sup[$x-1]) <= $var) {
                    $set = $sup[$x-1][3];
                } else if ($x < $w - 1 && diff($cur, $sup[$x+1]) <= $var) {
                    $set = $sup[$x+1][3];
                } else {
                    $set = ++$cnt;
                }
            } else {
                $set = $sup[$x][3];
            }
        } else {
            $set = $row[$x-1][3];
        }
        $cur[] = $set;
        $row[] = $cur;
    }
    $sup = $row;
    $mtx[] = $row;
}

//exit;

$opt = [];
for ($i = 0; $i <= $cnt; ++$i) {
    do {
        $cor = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
    } while (!$cor);
    $opt[] = $cor;
}

//echo $cnt . ' ' . count($opt); exit;

foreach ($mtx as $y => $row) {
    foreach ($row as $x => $col) {
        imagesetpixel($img, $x, $y, $opt[$col[3]]);
    }
}

header('content-type: image/jpeg'); imagejpeg($img);
//header('content-type: image/png'); imagepng($img);
//header('content-type: image/gif'); imagegif($img);

function diff($a, $b) {
    return (abs($a[0] - $b[0]) + abs($a[1] - $b[1]) + abs($a[2] - $b[2]));
}
