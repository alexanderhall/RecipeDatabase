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
  var_dump($config);
  $db = new Database($config);
?>

<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="style.css">
  </head>
<body>
<h1>TEST</h1>

  <div class="center">

    <form action='recipes.php' method='post'>
      <input type="text" id="nm1" name="myRegex" placeholder="Add new measurements here (tsp, tbsp, ...)" onkeyup="addtoregex(this.id)">
      <input type='text' id='myM' name='newMeas'>
      <p>Current regex value:</p>
      <p id="myRegex"/>
      <div id="dynamicInput">
      </div>
      <input type='submit'>
    </form>

<?php
// Create array of ingredients
$ingArr = array();
foreach (explode("\n", $ingredients) as $i => $v) {
  echo trim($v, " \r,");
//  $v = preg_replace('/[\']/', '', $v); // Single quotes trips it up
  $ingArr[$i] = trim($v, " \r,");
//  echo trim($v, " \r,");
}
$measRegex = array();
$sql = "SELECT name FROM Measure";
$measRegexArr = $db->SelectDatabase_($sql);
foreach ($measRegexArr as $x) {
  $measRegex[] = $x['name'];
}
echo "<p>Reading measurements:</p>";
print_r($measRegex);
echo "<p>Reading user ingredients:</p>";
print_r($ingArr);
?>
<script type='text/javascript'>
  var ings = JSON.parse('<?php echo json_encode($ingArr); ?>');

var counter = 0;
var limit = 1000; // Just to keep it from running away.
function addInput(divName, ingVal){
     if (counter == limit)  {
          alert("You have reached the limit of adding " + counter + " inputs");
     }
     else {
          var newdiv = document.createElement('div');
          var newp = document.createElement('p');
          newdiv.innerHTML = "<input type='text' name='myIngs[]' value='"+ ingVal +"' id='ingid"+counter+"' onkeyup=\"evalRegex(ingRegex, this.id, 'p"+counter+"')\">";
          newdiv.id = "ing"+counter;
          newp.innerHTML = ingVal;
          newp.id = "p"+counter;
          document.getElementById(divName).appendChild(newdiv);
          document.getElementById("ing"+counter).appendChild(newp);
          counter++;
     }
}

var mbeg = "([0-9]*[ ]*[0-9\\/.]+)[ ]*(";
//var m = "cup[s]*|tablespoon[s]*|teaspoon[s]*|pound[s]*|slice[s]*|ounce[s]*|strip[s]*)*";
var mmid = "";
//var mend = ")*[ ]+([a-z-, ]+)";
var mend = ")*[ ]+([\x20-\x7E ']+)";

var meas = JSON.parse('<?php echo json_encode($measRegex); ?>');
for (var j = 0; j < meas.length - 1; j++) {
  mmid += meas[j] + "|";
}
mmid += meas[meas.length-1];
var ingRegex = mbeg + mmid + mend;

function evalRegex(re, id, p) {
  var r = new RegExp(re, 'i');
  var y = document.getElementById(id);
  var z = document.getElementById(p);
  if (y.value.match(r) === null) {
    return;
  }
  z.innerHTML = "Amount: [" + y.value.match(r)[1] + "]";
  if (y.value.match(r)[2]) {
    z.innerHTML += " [" + y.value.match(r)[2] + "]";
  }
  z.innerHTML += "<br>Ingredient: [" + y.value.match(r)[3] + "]";
}

function addtoregex(id) {
  var a = document.getElementById(id);
  var ingArr = a.value.split(/[,]/);
  var newMeas = "";  
  for (var i = 0; i < ingArr.length; i++) {
    newMeas += ingArr[i].trim() + "|";
    ingRegex = mbeg + newMeas + mmid + mend;
  }
  // Re-evaluate the regex fields 
  for (var i = 0; i < ings.length; i++) {
    evalRegex(ingRegex, "ingid"+i, "p"+i);
  }
  document.getElementById('myRegex').innerHTML = ingRegex;
  document.getElementById('myM').value = ingRegex;
}

// Evaluate functions to populate screen
for (var i = 0; i < ings.length; i++) {
  addInput('dynamicInput', ings[i]);
  evalRegex(ingRegex, "ingid"+i, "p"+i);
}
document.getElementById('myRegex').innerHTML = ingRegex;
document.getElementById('myM').value = ingRegex;

</script>
</div>
</body>
</html>
