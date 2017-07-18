<html>
<body>
<?php
$name = $pw = $db = "";
$recipe = $site = "";
$ings = $dirs = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = test_input($_POST["name"]);
  $pw = test_input($_POST["pw"]);
  $db = test_input($_POST["db"]);
  $recipe = test_input($_POST["recipe"]);
  $site = test_input($_POST["site"]);
  $dirs = test_input($_POST["dirs"]);
  $ings = test_input($_POST["ings"]);
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

// Check for duplicate recipe name in Recipe table.
// If duplicate, rename random value and check again.
// Add to table once it is unique.
$sql = "SELECT name FROM Recipe WHERE name = '$recipe'";
while (readFromSQL($sql, 1) === true) {
  echo "$recipe already exists ";
  $recipe = "$recipe ".mt_rand();
  echo "renaming to $recipe<br>";
  $sql = "SELECT name FROM Recipe WHERE name = '$recipe'";
}
$sql = "INSERT INTO Recipe (name, instructions, website) VALUES ('$recipe', '$dirs', '$site')";
writeToSql($sql);

// Store ingredients in Ingredient table.
// Store associated amounts in RecipeIngredient table with keys
// that point to Recipe and Ingredient tables.
// Do not add to Ingredient table if duplicates.
// Ingredients in the form of <number> <measurement> <ingredient>
$ingRegex = "~([0-9/.]+ [a-z-]*) ([ a-z-]+)~i";
//$ingUnused = storeIngredients(explode(",", $ings), $ingRegex);
$ingUnused = storeIngredients(preg_split("~[\n,]~", $ings), $ingRegex);
// Ingredients in the form of <number> <ingredient>
$ingRegex = "~([0-9/.]+) ([ a-z-]+)~i";
$ingUnused = storeIngredients($ingUnused, $ingRegex);

if (!empty($ingUnused)) {
  echo "Could not store:<br>";
  foreach($ingUnused as $a) {
    if (preg_match("~[a-z0-9]~i", $a)) echo "$a<br>";
  }
}

//$sql = "SELECT r.name AS 'Recipe', i.name AS 'Ingredient', ri.measurement AS 'Measure' ";
//$sql .= "FROM Recipe r JOIN RecipeIngredient ri on r.id = ri.recipe_id JOIN Ingredient i on i.id = ri.ingredient_id ";
//$sql .= "WHERE r.name = '$recipe' ";
//$sql .= "ORDER BY r.name";
$sql = "SELECT ri.measurement AS 'Measure', i.name AS 'Ingredient' ";
$sql .= "FROM Recipe r JOIN RecipeIngredient ri on r.id = ri.recipe_id JOIN Ingredient i on i.id = ri.ingredient_id ";
$sql .= "WHERE r.name = '$recipe' ";
//echo "$sql<br>";
echo "<h2>$recipe</h2>";
readFromSQL($sql);

function storeIngredients($ingExplode, $regex) {
  global $name, $pw, $db, $sql, $recipe;
  $unused = [];
  foreach($ingExplode as $x) {
    preg_match($regex, $x, $m);
    if (empty($m)) {
      array_push($unused, $x);
    } else {
      $sql = "SELECT name FROM Ingredient WHERE name = '$m[2]'";
      if (readFromSQL($sql, 1) === true) {
        //echo "Skipping adding ingredient $m[2] because it exists<br>";
      } else {
        $sql = "INSERT INTO Ingredient (name) VALUES ('$m[2]')";
        writeToSql($sql);
      }
      $sql = "INSERT INTO RecipeIngredient (recipe_id, ingredient_id, measurement) ";
      $sql .= "SELECT r.id, i.id, '$m[1]' FROM Recipe r JOIN Ingredient i WHERE r.name = '$recipe' AND i.name = '$m[2]'";
      writeToSql($sql);
    }
  }
  return $unused;
}

function writeToSql($sql) {
  global $name, $pw, $db;
  $conn = new mysqli("localhost", $name, $pw, $db);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  if ($conn->query($sql) === TRUE) {
    //echo "Record updated successfully<br>";
  } else {
    echo "Error updating record: " . $conn->error;
  }
  $conn->close();
}

function readFromSQL ($sql, $checkDup = 0) {
  global $name, $pw, $db;
  $conn = new mysqli("localhost", $name, $pw, $db);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
  $result = $conn->query($sql);
  if ($result->num_rows > 0) {
    if ($checkDup) {
      $conn->close();
      //echo "Value already exists within database.<br>$sql<br>";
      return true;
    }
    // output data of each row
    while($row = $result->fetch_assoc()) {
      //echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
      //echo "Reading from db<br>";
      //var_dump($row);
      //echo "<br>";
      echo "<h5>".$row['Measure']." ".$row['Ingredient']."</h5>";
    }
  }
  $conn->close();
}
?>
<br><br><br>

</body>
</html>
