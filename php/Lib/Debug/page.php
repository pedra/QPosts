<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="description" content="Start PHP Framework - ERROR PAGE">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>QPosts</title>
        <link rel="stylesheet" href="assets/style/start-debug.css">
        <link rel="author" href="humans.txt">
        <style type="text/css" media="screen">
            *{ font-family: Helvetica, Tahoma, "Lucida Sans Typewriter", monospace, "sans serif"; 
                transition-duration: .6s; color:#678; margin:0; padding:0;}
            body {background:#639EA9; color:#182343;}
            h1, h2, h3 {font-weight: normal; color:#A00; text-shadow: 1px 1px 1px #AAA;}
            h1 {display:none;}
            h2 {font-size:1.8em; padding-top:10px;}
            h3 {font-size:1.5em; padding:20px 0 0 0; margin:20px 0 6px 0; border-top:1px solid #DDD;}
            a {text-decoration: none;}
            a:hover {color:#A50;}            

            tbody tr:nth-child(even) td, tbody tr.even td {background:#CCC;}
            table { width: 100%; border-collapse: separate; border-spacing: 0;}
            table td {border: none; padding: 4px; vertical-align: middle;}
            table tr:last-child td { color:#A00; font-weight: bold;}
            
            .container {font-size: .75em; background: #FFF; border-radius: 5px;
                        max-width: 570px; padding: 30px 20px; margin: 10px auto; 
                        box-shadow: 0 0 10px #345; background-image: linear-gradient(to bottom, #FFF, #CCC);} 

            .msg {min-height: 50px; padding: 0 0 0 60px; background: url(<?php echo $img?>) left top no-repeat;}
            .msg p {color:#800;}
            .footer { max-width: 610px; padding: 10px; font-size: .7em; margin: 0 auto; }
            .footer * {display: inline; color:#182343}
            .footer h2 {font-size:1em; font-weight: bold; padding: 0; background: none;} 
            
            @media only screen and (max-width: 615px) {
                .container {margin: 0; width: 94%; padding: 3%; border-radius: 0;}
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Start PHP Framework</h1>

            <div class="msg">
                <h2><?php echo $msg?></h2>
            <?php if(!isset($eType)) {?> </div>
            <?php } else { ?>
                <p>file: <?php echo str_replace(trim(ROOT, ' /'), '', $file)?>
                <br/>line: <?php echo $line?></p>
            </div>            

            <h3><?php echo $eType?></h3>
            <p><?php echo $eText?></p>

            <h3>Backtrace</h3>
            <?php echo $trace?>

            <?php echo $debug;
            }?>

        </div>
        <div class="footer">
            <h2><a href="http://github.com/pedra/QPosts">&copy;QPosts</a></h2> | 
            <div>Software criado e desenvolvido sob licença CREATIVE COMMUNS.</div>
        </div>
    </body>
</html>