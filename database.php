<?php

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  // Remove single quotes.
  $data = str_replace("'", "", $data);
  return $data;
}

class Database {

  protected static $conn_;

  public function __construct($config) {
    self::$conn_ = new mysqli('localhost', 
                       $config['user'], 
                       $config['pw'], 
                       $config['db']);
    if (self::$conn_->connect_error) {
      die("Connection failed: " . 
          self::$conn_->connect_error . "\n");
    } else {
      //echo "Created new mysql connection.\n";
    }
  }

  public function QueryDatabase_($sql) {
    $result = self::$conn_->query($sql);
    if ($result === false) {
      echo "Error querying database: " . 
           self::$conn_->error . "\n";
    } else {
      //echo "Database query successful.\n";
      return $result;
    }
  }

  public function SelectDatabase_($sql) {
    $rows = array();
    $result = $this->QueryDatabase_($sql);  
    while($row = $result->fetch_assoc()) {
      $rows[] = $row;
    }
    return $rows;
  }

  public function __destruct() {
    self::$conn_->close();
    //echo "Closed mysql connection.\n";
  }
}

//$db = new Database(parse_ini_file('../../config.ini'));
//$answer = array();
//$sql = "SELECT name FROM Recipe WHERE id IN (1,2)";
//$answer = $db->SelectDatabase_($sql);
//$db->SelectDatabase_($sql);

//foreach ($answer as $x) {
  //echo $x['name']."\n";
//}

//$sql = "INSERT INTO Recipe (name) VALUES ('Lemon Cake ".rand()."')";
//echo $sql."\n";
//$db->QueryDatabase_($sql);

?>
