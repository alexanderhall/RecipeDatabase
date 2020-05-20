
function add_ingredient_field(divName, ingVal){
  var counter = add_ingredient_field.count;
  var limit = 1000; // Just to keep it from running away.
  if (counter == limit)  {
    alert("You have reached the limit of adding " + counter + " inputs");
  } else {
    var newdiv = document.createElement('div');
    var newp = document.createElement('p');
    newdiv.innerHTML = "<input type='text' name='myIngs[]' value='"+ ingVal +"' id='ingid"+counter+"' onkeyup=\"evalRegex(ingRegex, this.id, 'p"+counter+"')\">";
    newdiv.id = "ing"+counter;
    newp.innerHTML = ingVal;
    newp.id = "p"+counter;
    document.getElementById(divName).appendChild(newdiv);
    document.getElementById("ing"+counter).appendChild(newp);
    add_ingredient_field.count++;
  }
}

var mbeg = "([0-9]*[ ]*[0-9\\/.]+)[ ]*(";
var mmid = "";
var mend = ")*[ ]+([\x20-\x7E ']+)";
var ingRegex = mbeg + mmid + mend;

function populate_meas_regex(meas) {
  for (var j = 0; j < meas.length - 1; j++) {
    mmid += meas[j] + "|";
  }
  mmid += meas[meas.length-1];
  ingRegex = mbeg + mmid + mend;
}

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
  for (var i = 0; i < ingredients.length; i++) {
    evalRegex(ingRegex, "ingid"+i, "p"+i);
  }
  document.getElementById('myRegex').innerHTML = ingRegex;
  document.getElementById('myM').value = ingRegex;
}

// Evaluate functions to populate screen
function populate_ingredients(ings) {
  for (var i = 0; i < ings.length; i++) {
    add_ingredient_field('dynamicInput', ings[i]);
    evalRegex(ingRegex, "ingid"+i, "p"+i);
  }
}

function populate_regex() {
  document.getElementById('myRegex').innerHTML = ingRegex;
  document.getElementById('myM').value = ingRegex;
}

add_ingredient_field.count = 0;
populate_meas_regex(measurements);
populate_ingredients(ingredients);
populate_regex();

