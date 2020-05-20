<?php 
  session_start(); 
  require_once('database.php');

  // Grab value of variables from previous screen
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['username'] = test_input($_POST["username"]);
    $_SESSION['pword'] = test_input($_POST["pword"]);
    $_SESSION['database'] = test_input($_POST["database"]);
    $_SESSION['recipename'] = test_input($_POST["recipename"]);
    $_SESSION['site'] = test_input($_POST["site"]);
    $ingredients = test_input($_POST["ingredients"]);
    $_SESSION['directions'] = test_input($_POST["directions"]);
  }

  $config = array("user"=>$_SESSION['username'],
                  "pw"=>$_SESSION['pword'],
                  "db"=>$_SESSION['database']);
  //var_dump($config);
  $db = new Database($config);

  // Create array of ingredients from input.
  $ingArr = array();
  foreach (explode("\n", $ingredients) as $i => $v) {
    //echo trim($v, " \r,");
    $ingArr[$i] = trim($v, " \r,");
  }

  // Create array of measurements from database.
  $meas_regex = array();
  $sql = "SELECT name FROM Measure";
  $meas_regex_sql = $db->SelectDatabase_($sql);
  //var_dump($meas_regex_sql);
  foreach ($meas_regex_sql as $x) {
    $meas_regex[] = $x['name'];
  }
  //echo "<p>Reading measurements:</p>";
  //print_r($measRegex);
  //echo "<p>Reading user ingredients:</p>";
  //print_r($ingArr);
?>

<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="style.css">
  </head>
  <body>
    <div class="center">

      <form action='recipes.php' method='post'>
        <input type="text" id="nm1" name="myRegex" placeholder="Add new measurements here (tsp, tbsp, ...)" onkeyup="addtoregex(this.id)">
        <input type='text' id='myM' name='newMeas'>
        <p><strong>Regular Expression value:</strong></p>
        <p id="myRegex"/>
        <p><strong>Ingredients:<strong></p>
        <div id="dynamicInput"></div>
        <input type='submit'>
      </form>

    </div>
    <script type='text/javascript'>
      var ingredients = JSON.parse('<?php echo json_encode($ingArr); ?>');
      var measurements = JSON.parse('<?php echo json_encode($meas_regex); ?>');
    </script>
    <script type='text/javascript' src='js/ing.js?v=2'></script>

  </body>
</html>
