<html>  
<head>
    <?php header("Content-Type: text/html;charset=UTF-8");?>
    
    <title>mission_1-7.php</title>
    </head>
    <body>
        <?php
        $filename = 'kadai1-5.txt';
        $lines = file($filename);
        
        print_r($lines);
               ?>
        
             

</html>