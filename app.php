<?php
include('functions.php');

$fontname = "Arial";
$fontsize = "16";
$topColor = "ffffff";
$botColor = "ffffff";


function printHelp() {
    echo "Usage:\n";
    echo "-r\t\t Directory Mode : Merge all .srt files with the same name\n";
    echo "\t\t country code infront of .srt expected! (e.g. subtitle1.en.srt)\n";
    echo "-n1\t\t Country code of 1. subtitle\n";
    echo "-n2\t\t Country code of 2. subtitle\n";
    echo "Optional:\n";
    echo "-d\t\t Destination directory (Default: same as source)\n";
    echo "-f\t\t Font name (Default: Arial)\n";
    echo "-s\t\t Font size (Default: 16)\n";
    echo "-t\t\t Top color (Default: FFFFFF)\n";
    echo "-b\t\t Bottom color (Default: FFFFFF)\n";
}


function doStuff($file1, $file2, $dest){
    // Process
	$outputName = preg_replace('/(\.[a-zA-Z]{2,3})?\.srt$/', '.ass', $file1);
	$contentTop = file_get_contents($file1);
    $contentBot = file_get_contents($file2);
    
    print($outputData);

    global $fontname, $fontsize, $topColor, $botColor;
    

    $forceBottom = "0";
    $styles = "";
    $stylesKeys = "";


    getStyles($fontname, $fontsize, $topColor, $botColor, $forceBottom, $styles, $stylesKeys);

    cleanSRT($contentTop);
    cleanSRT($contentBot);

    $tree = array(/*array(
        'start' => '00:00:01.00',
        'end'   => '00:00:03.00',
        'type'  => 'Mid',
        'text'  => 'Merged with 2SRT2ASS\N(http://pas-bien.net/2srt2ass/)'
    )*/);

    
    parseAndAddSRT($contentTop, $tree, 'Top');
    parseAndAddSRT($contentBot, $tree, 'Bot');

    usort($tree,compare);

    // Evrything ok, send file !

    $outputData = "";

    $outputData .= "[Script Info]\r\n";
    $outputData .= "ScriptType: v4.00+\r\n";
    $outputData .= "Collisions: Normal\r\n";
    $outputData .= "PlayDepth: 0\r\n";
    $outputData .= "Timer: 100,0000\r\n";
    $outputData .= "Video Aspect Ratio: 0\r\n";
    $outputData .= "WrapStyle: 0\r\n";
    $outputData .= "ScaledBorderAndShadow: no\r\n";
    $outputData .= "\r\n";
    $outputData .= "[V4+ Styles]\r\n";
    $outputData .= "Format: Name," . implode(',', $stylesKeys) . "\r\n";
    foreach($styles as $styleName => $styleValues)
    {
        $outputData .= "Style: " . $styleName . "," . implode(',', $styleValues) . "\r\n";
    }
    $outputData .= "\r\n";
    $outputData .= "[Events]\r\n";
    $outputData .= "Format: Layer, Start, End, Style, Name, MarginL, MarginR, MarginV, Effect, Text\r\n";

    foreach ($tree as $dialogue)
    {
        $outputData .= "Dialogue: 0,".getTimeStamp($dialogue['start']).",".getTimeStamp($dialogue['end']).",".$dialogue['type'].",,0000,0000,0000,,".$dialogue['text']."\r\n";
    }

// Render

    if($dest == "") {
        file_put_contents($outputName, $outputData);
        print("Saved: " . $outputName . "\n");
    }
    else {
        file_put_contents($dest . "/" . basename($outputName), $outputData);
        print("Saved: " . $dest . "/" . basename($outputName) . "\n");
    }

} 

function parseArg($arg, &$dest) {
    global $fontname, $fontsize, $topColor, $botColor, $argv;
    if($argv[$arg] == "-d") {
        $dest = $argv[$arg+1];
    }
    else if($argv[$arg] == "-f") {
        $fontname = $argv[$arg+1];
    }
    else if($argv[$arg] == "-s") {
        $fontsize = $argv[$arg+1];
    }
    else if($argv[$arg] == "-t") {
        $topColor = $argv[$arg+1];
    }
    else if($argv[$arg] == "-b") {
        $botColor = $argv[$arg+1];
    }
    else {print("HALP");}
} 

if($argc < 2 || $argv[1] != "-r" | $argv[3] != "-n1" || $argv[5] != "-n2") {
    printHelp();
}
else {
    $dir = $argv[2];
    $n1  = $argv[4];
    $n2  = $argv[6];
    $dest = "";

    for($i = 7; $i < $argc; $i+=2) {
        parseArg($i, $dest);
    }

    $filesInDir = scandir($dir);
    $subtitlesInDir = array();

    $subtitlesTyp1 = array();
    $subtitlesTyp2 = array();

    if($filesInDir != FALSE) {
        $subtitlesInDir = glob($dir . "/*.srt");

        if(!$subtitlesInDir) {
            print("No files found!\n");
            die();
        }

        $subtitlesTyp1 = glob($dir . "/*" . $n1 . ".srt");
        $subtitlesTyp2 = glob($dir . "/*" . $n2 . ".srt");


        foreach($subtitlesTyp1 as $value) {
            print("Found: " . $value);

            $counterpartName = preg_replace('/(\.[a-zA-Z]{2,3})?\.srt$/',"." . $n2 . ".srt", $value);
            if(file_exists($counterpartName)) {
                print(" - counterpart exists - now merging..\n");
                doStuff($value, $counterpartName, $dest);
            }
            else {
                print(" - counterpart does NOT exist - skipping..\n");
            }
        }
    }
}