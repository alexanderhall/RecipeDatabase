<html>
<body>
<?php 
  require_once('database.php');

  $db = new Database(parse_ini_file('../../config.ini'));
  $recipe = '';
  $ings = '';

  // Grab value of variables from URL
  if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $recipe = test_input($_GET["recipe"]);
  }
  echo $recipe;

  $sql = "SELECT FORMAT(ri.amount,3) AS 'A', m.name AS 'M', i.name AS 'I' ";
  $sql .= "FROM Recipe r JOIN RecipeIngredient ri on r.id = ri.recipe_id ";
  $sql .= "JOIN Ingredient i on i.id = ri.ingredient_id LEFT OUTER JOIN Measure m on m.id = ri.measure_id ";
  $sql .= "WHERE r.name = '$recipe'";
  echo $sql;
  $lines = $db->SelectDatabase_($sql);
  echo "<h3>$recipe</h3>";
  echo "<h4>Ingredients:</h4>";
  echo "<table>";
  foreach ($lines as $l) {
    $ings .= "<tr><td>".$l['A']."</td><td>".$l['M']."</td><td>".$l['I']."</td></tr>";
  }
  echo $ings;
  echo "</table>";
  echo "<h4>Directions:</h4>";
  $sql = "SELECT instructions FROM Recipe WHERE name = '$recipe'";
  echo "<p>".$db->SelectDatabase_($sql)[0]['instructions']."</p>";
//instructions
?>
</body>
</html>
