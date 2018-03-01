<?php 
  require_once('database.php');

  $db = new Database(parse_ini_file('../../config.ini'));

  
	$to      = '';
	//$subject = 'Weekly Menu';
	//$headers = "From: A.J. <aj@unawarewolf.com>\r\n";
	//$message = 'hello from email';

	//if(mail($to, $subject, $message, $headers)) {
	  //echo 'email coming from email';
	//}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $to = $_POST["email"];
}

echo "<h2><a href='$site'>$recipe</a></h2>";

  $b = '';
  $ids = '';
  // Change this to a name query.
  $sql = "SELECT id FROM Recipe ORDER BY RAND() LIMIT 3";
  $result = $db->SelectDatabase_($sql);
  foreach ($result as $i) {
    $sql = "SELECT name FROM Recipe WHERE id = ".$i['id'];
    $a = $db->SelectDatabase_($sql)[0]['name'];
    $site = str_replace(" ", "+", $a);
    $b .= "<p><a href=http://www.unawarewolf.com/RecipeDatabase/showrecipe.php?recipe=$site>$a</a></p>";
  }
  $ids .= $result[0]['id'].", ";
  $ids .= $result[1]['id'].", ";
  $ids .= $result[2]['id'];
  // Return shopping list of all recipes with ingredients combined.
  $sql = "SELECT SUM(FORMAT(ri.amount,3)) AS A, m.name AS M, i.name AS I ";
  $sql .= "FROM Recipe r JOIN RecipeIngredient ri on r.id = ri.recipe_id ";
  $sql .= "JOIN Ingredient i on i.id = ri.ingredient_id ";
  $sql .= "LEFT OUTER JOIN Measure m on m.id = ri.measure_id ";
  $sql .= "WHERE r.id IN (".$ids.") GROUP BY i.name, m.name";
  $d = $db->SelectDatabase_($sql);
  $x = '';
  foreach ($d as $j) {
    $x .= "<tr><td>".$j['A']."</td><td>".$j['M']."</td><td>".$j['I']."</td>";
  }

  echo $b;
  echo "<table>".$x."</table>";

$subject = "Weekly Menu";

$message = "
<html>
<head>
<title>Weekly Menu</title>
</head>
<body>".
"$b".
"<h3>Shopping List</h3>
<table>".$x."</table>";
"</body>
</html>
";


$headers = 'From: aj@unawarewolf.com' . "\r\n";
$headers .= 'CC: hall.alexander.j@gmail.com' . "\r\n";
$headers .= "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

mail($to,$subject,$message,$headers);
?>
