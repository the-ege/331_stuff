<?php
header("Content-type: image/png");
// 24 hours of data @ 1 point a minute
$points=1440;

// #pixels between each plotted point
$span = 1;

// Image dimensions
$xspan = $span * 25 * 60;
$yspan = 1200;
$xlast = 60;
$ylast = 0;

// generate plot image
$img = @ImageCreateTrueColor($xspan, $yspan) or die ("ImageCreate failed");

// various colors
$back = ImageColorAllocate($img, 0, 0, 0);
$red=imagecolorallocate($img, 255, 0, 0);
$white=imagecolorallocate($img, 255, 255, 255);
$gray=imagecolorallocate($img, 20, 20, 20);

$sum = 0;
$window = array(0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0);

try {
    // Create horizontal grid lines
    for ($y=50; $y<=$yspan; $y+=(5*10)) {
        imagestring($img,5,5,($yspan-$y-20),($y/10),$white);
        imageline($img, ($x+20), $y, ($xspan-20), $y, $gray);
    }
	// Attempt to open SQL temperature database
	$log = new PDO('sqlite:/var/log/temperature.db')
          or die ("Couldn't access data");

	$query = $log->query('SELECT * FROM (SELECT * FROM temp ORDER BY time DESC          limit '.$points.') as r ORDER BY r.time ASC');

	foreach ($query as $result){
		// Shifts contents of averaging window
		// and inserts latest temperature
		for ($i = 29; $i > 0; $i--){
            $window[$i] = $window[$i-1];
        }
        $window[0] = $result[0];
        for ($i = 0; $i < 30; $i++){
            $sum += $window[$i];
        }
        $t = ($sum / 30);
		
		$x = $xlast + $span;
        $y = ($yspan - (10*$t));

        // Set up time
        $time = new DateTime($result[1]);
        $time->setTimezone(new DateTimeZone('America/New_York'));
        $min = $time->format('i:s');
		
		// print vertical line on every hour
		if ($min == 0){
        	imageline($img, $x, $yspan-10, $x, 10, $gray);
        	$hour = $time->format('H:i');
			imagestring($img, 5, $x-21, $yspan-20, $hour, $white);
		}
	
		// generate line between points
		imageline($img, $x, $y, $xlast, $ylast, $red);
		
		// Update variables
		$sum = 0;
        $xlast = $x;
        $ylast = $y;
	}
}
catch(Exception $e) {
	print $e->getMessage();
}

// Label axes
imagestring($img, 5, 5, 5, "Temp, F", $white);
imagestring($img, 5, $xspan-50, $yspan-35, "Time", $white);
imagestring($img, 5, 550, 5, "ECE 331 Project 2", $white);

imagepng($img);
imagedestroy($img);
?>
