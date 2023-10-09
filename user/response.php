<?php
session_start();
$start_time = $_SESSION["start_time"];
//echo $start_time."-<br>";

$ts1 = strtotime(str_replace('/', '-',date('Y-m-d H:i:s')));
$ts2 = strtotime(str_replace('/', '-', $_SESSION["start_time"]));
$diff = abs($ts1 - $ts2);

if(isset($_SESSION["duration"])){
    $duration=$_SESSION["duration"];
    $diff=$duration-$diff;
    if($diff==0 || $diff<0){
        echo "Timeout";
    }else{
        $time=convertTime($diff);
        echo $time;
    }
}
function convertTime($dec){
    $seconds = $dec ;
    $hours = floor($seconds / 3600);
    $seconds -= $hours * 3600;
    $minutes = floor($seconds / 60);
    $seconds -= $minutes * 60;
    // return the time formatted HH:MM:SS
    return $hours.":".$minutes.":".$seconds;
}

    /*session_start();

    $duration=$_SESSION["duration"];
    $start_time=$_SESSION["start_time"];
    $ongoing_time=date('Y-m-d H:i:s');
    if(!isset($_SESSION['time'])){
        $_SESSION['time']=time();
    }else{
        $diff=$ongoing_time-$start_time;
        $diff=$duration-$diff;

        $hours=floor($diff/60);
        $minute=(int)($diff/60);
        $second=$diff%60;
        $show=$hours.":".$minute.":".$second;

        if($diff==0 || $diff<0){
            //echo "Timeout";
        }else{
            //echo $show;
        }
    }*/
?>