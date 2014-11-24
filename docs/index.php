<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

define("MAXCELLS", 8);

$boardparams = Array(
  2 => 3,
  3 => 4,
  4 => 5,
  5 => 6,
  6 => 7,
  7 => 8,
  8 => 7,
  9 => 6,
  10 => 5,
  11 => 4,
  12 => 3 
);
session_start();
$mysqli = new mysqli("localhost", "horserace", "skyd1ve", "horserace");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
// find or set gid
$gid = (isset($_SESSION['gid']))?$_SESSION['gid']:$_REQUEST['gid'];
if(!$gid) {
  // create new game
  print "creating new game\n";
  $cmd = "SELECT keyvalue FROM control WHERE keyname = 'next_gid'";
  $res = $mysqli->query($cmd);
  while ($row = $res->fetch_assoc()) {
    $gid = $row['keyvalue'];
  }
  $gid++;
  $cmd = "UPDATE control SET keyvalue = $gid WHERE keyname = 'next_gid'";
  $res = $mysqli->query($cmd);
  $_SESSION['gid'] = $gid;
}

$init = Array(
  'horses' => Array(
    2 => 0,
    3 => 0,
    4 => 0,
    5 => 0,
    6 => 0,
    7 => 0,
    8 => 0,
    9 => 0,
    10 => 0,
    11 => 0,
    12 => 0,
  ),
  'win' => Array(
    2 => 3,
    3 => 4,
    4 => 5,
    5 => 6,
    6 => 7,
    7 => 8,
    8 => 7,
    9 => 6,
    10 => 5,
    11 => 4,
    12 => 3,
  ),
  'scratched' => Array(
  ),
);
$cmd = "SELECT * FROM rolls WHERE gid = $gid ORDER BY id";
$res = $mysqli->query($cmd);
while ($row = $res->fetch_assoc()) {
  $roll = $row['roll'];
  $code = $row['code'];
  switch($code) {
    case 'scratch1':
      $init['scratched'][1] = $roll;
      break;
    case 'scratch2':
      $init['scratched'][2] = $roll;
      break;
    case 'scratch3':
      $init['scratched'][3] = $roll;
      break;
    case 'scratch4':
      $init['scratched'][4] = $roll;
      break;
    default:
      $pos = $init['horses'][$roll];
      $pos++;
      $init['horses'][$roll] = $pos;
  }
}
?>
<html>
<head>
  <link rel="stylesheet" media="screen, projection" href="/styles/reset.css">
  <link rel="stylesheet" media="screen, projection" href="/styles/screen.css">
  <link rel="stylesheet" media="screen, projection" href="/styles/colorbox.css">
  <script src="/scripts/jquery.min.js"></script>
  <script src="/scripts/jquery.colorbox-min.js"></script>
  <script src="/scripts/global.js"></script>
</head>

<body>
<div class="screen">
  <!--
  <h1>Horse Racing Game #<?php printf("%05d",$gid); ?></h1>
  -->
  <h1>Horse Racing Game (black = $1, blue = $.50, everything else = $.10)</h1>
  <div class="board">
  <table>
<?php
    foreach($boardparams as $rownum => $numcells) {
?>
    <tr class="row row-<?php echo $rownum; ?>">
<?php
      for($x = 1; $x <= MAXCELLS; $x++) {
        if($x <= (MAXCELLS - $numcells)) {
?>
      <td class="cell"> </td>
<?php
        } else {
?>
      <td class="cell cellelement cell-<?php echo $rownum; ?>-<?php echo ($x - MAXCELLS + $numcells); ?>"> </td>
<?php
        }
      }
?>
      <td class="cell horsenum"><?php echo $rownum; ?></td>
    </tr>
<?php
    }
?>
  </table>
  </div>
  <div class="status">
    <div class="scratchtable">
    <table class="scratch">
      <tr>
        <td class="heading">Scratch 1:</td>
        <td class="scratchvalue scratch-1"> </td>
      </tr>
      <tr>
        <td class="heading">Scratch 2:</td>
        <td class="scratchvalue scratch-2"> </td>
      </tr>
      <tr>
        <td class="heading">Scratch 3:</td>
        <td class="scratchvalue scratch-3"> </td>
      </tr>
      <tr>
        <td class="heading">Scratch 4:</td>
        <td class="scratchvalue scratch-4"> </td>
      </tr>
    </table>
    </div>
    <div class="dice">
    <table class="diceroll">
      <tr>
        <td class="rollheader" colspan="11">Roll (click anywhere in gray box)</td>
      </tr>
      <tr>
        <td> </td>
        <td class="roll roll-3"> 3 </td>
        <td class="roll roll-4"> 4 </td>
        <td class="roll roll-5"> 5 </td>
        <td class="roll roll-6"> 6 </td>
        <td class="roll roll-7"> 7 </td>
        <td class="roll roll-8"> 8 </td>
        <td class="roll roll-9"> 9 </td>
        <td class="roll roll-10"> 10 </td>
        <td class="roll roll-11"> 11 </td>
        <td> </td>
      </tr>
      <tr>
        <td class="roll roll-2-d"> 2d </td>
        <td> </td>
        <td class="roll roll-4-d"> 4d </td>
        <td> </td>
        <td class="roll roll-6-d"> 6d </td>
        <td> </td>
        <td class="roll roll-8-d"> 8d </td>
        <td> </td>
        <td class="roll roll-10-d"> 10d </td>
        <td> </td>
        <td class="roll roll-12-d"> 12d </td>
      </tr>
    </table>
    </div>
  </div>
  <script>
    var init = <?php echo json_encode($init); ?>;
  </script>
</body>
</html>
