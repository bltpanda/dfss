<?php



	$path = 'D:/Program Files/ImageMagick-6.7.3-Q16/convert.exe';
	
#	exec(escapeshellarg($path)." -compress none  -colorspace Gray -monochrome out.jpg out.tif");
	exec(escapeshellarg($path)." -compress none  -colorspace Gray out.jpg out.tif");
	exec(escapeshellarg($path)."  -scale 200% out.tif out1.tif");
	echo "aaa";
	
	$path = 'D:/GreenProgram/Tesseract-ocr/tesseract.exe';
		
	exec(escapeshellarg($path)." out.tif out");
#	exec("{$path} out.jpg out 2>&1 >nul");
?>