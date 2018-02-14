<?php
  session_start();
  require_once('database.php');
  $config = array("user"=>$_SESSION['username'],
                  "pw"=>$_SESSION['pword'],
                  "db"=>$_SESSION['database']);
  $db = new Database($config);

// Check for duplicate recipe name in Recipe table.
// If duplicate, rename random value and check again.
// Add to table once it is unique.
$recipe = $_SESSION['recipename'];
$directions = $_SESSION['directions'];
$site = $_SESSION['site'];

$sql = "SELECT COUNT(*) AS count FROM Recipe WHERE name LIKE '${recipe}%'";

$x = $db->SelectDatabase_($sql);
$count = $x[0]['count'];
if ($count > 0) {
  $recipe .= " ".($count + 1);
}
$sql = "INSERT INTO Recipe (name, instructions, website) VALUES ('$recipe', '$directions', '$site')";
$db->QueryDatabase_($sql);


// Store ingredients in Ingredient table.
// Store associated amounts in RecipeIngredient table with keys
// that point to Recipe and Ingredient tables.
// Do not add to Ingredient table if duplicates.
// Ingredients in the form of <number> <measurement> <ingredient>

// Grab measurements from database
// Add measurements from previous page
// Insert into regular expression to divide values

echo "Grabbing measurements<br>";
$measRegex = "/".$_POST['newMeas']."/";
echo "${measRegex}<br>";
echo "<br><br><br>";
print_r($_POST['myIngs']);
echo "<br><br><br>";
foreach ($_POST['myIngs'] as $x) {
  echo "$x<br>";
  preg_match($measRegex, $x, $matches);
  print_r($matches);
  storeToDB($matches[1], $matches[2], $matches[3]);
}

echo "<h1>$site</h1>";
echo "<h2><a href='$site'>$recipe</a></h2>";
$sql = "SELECT FORMAT(ri.amount,3) AS 'A', m.name AS 'M', i.name AS 'I'";
$sql .= " FROM Recipe r JOIN RecipeIngredient ri on r.id = ri.recipe_id ";
$sql .= " JOIN Ingredient i on i.id = ri.ingredient_id LEFT OUTER JOIN Measure m on m.id = ri.measure_id";
$sql .= " WHERE r.name = '$recipe'";
$lines = $db->SelectDatabase_($sql);
foreach ($lines as $l) {
  echo "<p>".$l['A']." ".$l['M']." ".$l['I']."</p>";
}
echo "<p>$directions<p>";
echo "<a href='http://www.unawarewolf.com/test/'>Enter new recipe!</a>";

function storeToDB($amount, $meas, $ing) {

  global $recipe, $db;

  // Convert amount to decimal
  // if: Contains whole number and fraction (1 1/4)
  // else if: Contains just fraction (1/4)
  if (preg_match('/[ ]/', $amount)) {
    $a = explode(" ", $amount);    // Grab whole numbers
    $b = explode("/", $a[1]);      // Grab fractions
    $amount = $a[0] + $b[0]/$b[1]; // Calculate total
  } else if (preg_match('/[\/]/', $amount)) {
    $b = explode("/", $amount); // Grab fractions
    $amount = $b[0]/$b[1];      // Calculate
  }

  $sql = "SELECT name FROM Ingredient WHERE name = '$ing'";
  if (mysqli_num_rows($db->QueryDatabase_($sql)) > 0) {
    echo "Skipping adding ingredient <strong>$ing</strong> because it exists<br>";
  } else {
    $sql = "INSERT INTO Ingredient (name) VALUES ('$ing')";
    echo "Storing ingredient <strong>$ing</strong><br>";
    echo $sql;
    $db->QueryDatabase_($sql);
  }

  if ($meas == '') {
    echo "$ing has no meas<br>";
    $sql = "INSERT INTO RecipeIngredient (recipe_id, ingredient_id, measure_id, amount) ";
    $sql .= "SELECT r.id, i.id, NULL, '$amount' FROM Recipe r";
    $sql .= " JOIN Ingredient i";
    $sql .= " WHERE r.name = '$recipe' AND i.name = '$ing'";
    echo $sql;
    $db->QueryDatabase_($sql);
  } else {
    $sql = "SELECT name FROM Measure WHERE name = '$meas'";
    if (mysqli_num_rows($db->QueryDatabase_($sql)) > 0) {
      echo "Skipping adding measurement <strong>$meas</strong> because it exists<br>";
    } else {
      $sql = "INSERT INTO Measure (name) VALUES ('$meas')";
      echo $sql."<br>";
      $db->QueryDatabase_($sql);
    }
    $sql = "INSERT INTO RecipeIngredient (recipe_id, ingredient_id, measure_id, amount) ";
    $sql .= "SELECT r.id, i.id, m.id, '$amount' FROM Recipe r";
    $sql .= " JOIN Ingredient i JOIN Measure m";
    $sql .= " WHERE r.name = '$recipe' AND i.name = '$ing' AND m.name='$meas'";
    echo $sql;
    $db->QueryDatabase_($sql);
  }
}

?>
