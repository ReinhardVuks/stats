<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

$gameId = $_GET['gameid'];
$conn = new mysqli("mysql.hostinger.ee", "u720719028_admin", "123456789", "u720719028_stat");
$result = $conn->query("SELECT * FROM stats WHERE game_id = '$gameId'");

$outp = "";
while($rs = $result->fetch_array(MYSQLI_ASSOC)) {
    if ($outp != "") {$outp .= ",";}
    $outp .= '{"Name":"'  . $rs["name"] . '",';
    $outp .= '"Nr":"'   . $rs["number"]        . '",';
    $outp .= '"Team":"'. $rs["team"]     . '",'; 
    $outp .= '"Time":"'. $rs["time"]     . '",'; 
    $outp .= '"OnePtMade":"'. $rs["one_pt_made"]     . '",'; 
    $outp .= '"OnePtMiss":"'. $rs["one_pt_miss"]     . '",'; 
    $outp .= '"TwoPtMade":"'. $rs["two_pt_made"]     . '",'; 
    $outp .= '"TwoPtMiss":"'. $rs["two_pt_miss"]     . '",'; 
    $outp .= '"ThreePtMade":"'. $rs["three_pt_made"]     . '",'; 
    $outp .= '"ThreePtMiss":"'. $rs["three_pt_miss"]     . '",'; 
    $outp .= '"OffRebs":"'. $rs["off_reb"]     . '",'; 
    $outp .= '"DefRebs":"'. $rs["def_reb"]     . '",'; 
    $outp .= '"Assists":"'. $rs["assists"]     . '",'; 
    $outp .= '"Steals":"'. $rs["steals"]     . '",'; 
    $outp .= '"Blocks":"'. $rs["blocks"]     . '",'; 
    $outp .= '"Turnovers":"'. $rs["turnovers"]     . '",'; 
    $outp .= '"Fouls":"'. $rs["fouls"]     . '",'; 
    $outp .= '"PlusMinus":"'. $rs["plus_minus"]     . '"}'; 
}
$outp ='{"stats":['.$outp.'],';

$result1 = $conn->query("SELECT * FROM game_log where game_id = '$gameId'");
$outp1 = "";
while ($rs = $result1->fetch_array(MYSQLI_ASSOC)) {
    if($outp1 != "") {$outp1 .= ",";}
    $outp1 .= '["'. $rs["data"]. '"]';
}

$outp1= '"game_log":['.$outp1.']}';

$conn->close();

echo $outp;
echo $outp1;
?>