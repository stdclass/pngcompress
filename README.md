Compress Text to .PNG Images and vice versa
===========================================


Usage
-----


### Convert Text to Image

    <?php
    
    require "pngcompress.php";
    
    $convert = new PNGCompress;
    
    # $convert->from_file( "sometext.txt" ); 
    $convert->from_string( "Some Text" ); 
    
    
    # $convert->save( "sometext.png" );
    $convert->show();
    
    ?>


### Convert Image to Text

    <?php
    
    require "pngcompress.php";
    
    $convert = new PNGCompress;
    
    $sometext = $convert->restore( "sometext.png" ); 
    
    ?>
    
### Demo

jQuery.js compressed as image

![Demo](http://lab.dornauer.cc/convert-text-to-image/test.php?jquery.js)