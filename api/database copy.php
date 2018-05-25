<?php
function dbConnect($dbId){
  $servername   = "";
  $usernamesql  = "";
  $passwordsql  = "";
  $databasename = "";
  if ($dbId === 0) {
    $servername   = "localhost";
    $usernamesql  = "Username";
    $passwordsql  = "Password";
    $databasename = "database name";
  }
  $GLOBALS['conn'] = new mysqli($servername, $usernamesql, $passwordsql, $databasename);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
}

class Column {

  function __construct($columnName, $type, $input)
  {
    $this->columnName = $columnName;
    $this->type = $type;
    $this->input = $input;
  }
}



function dbInsert($table, $columns) {
  /*
  How to use:
  $table = "login";
  $columns = array(
    new Column("username", "s", "datjap"),
    new Column("email", "s", "johndoe@gmail.com")
  );

  dbInsert($table, $columns);
  INSERT INTO `users` (`username`, `displayname`, `email`, `hash`, `salt`) VALUES ('datjap', 'Justin Fernald', 'justin@gmail.com', 'kasdjflkjdslakjflka', 'ads')
  */

  $columnNames = "";
  $inputSpaces = "";
  $types = "";
  foreach ($columns as $column) {
    $columnNames .= $column->columnName . ", ";
    $inputSpaces .= "?, ";
    $types .= $column->type;
  }
  $columnNames = chop($columnNames, ", ");
  $inputSpaces = chop($inputSpaces, ", ");

  $statement = "INSERT INTO " . $table . " (" . $columnNames .") VALUES (" . $inputSpaces . ")";

  $dbArgs = array($statement, $types);

  foreach ($columns as $columns) {
    array_push($dbArgs, $columns->input);
  }
  call_user_func_array("dbPrepare", $dbArgs);
}

function dbPrepare($statement){
  /*
  How to use:
  $statement = "SELECT * FROM tableName WHERE id=?";
  $types = "i";
  $result = dbPrepare($statement, $types, 0);

  //Result:
  if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
      $username $row["username"]
    }
  }

  --------------------------------------------

  How to use:
  $statement = "INSERT INTO tableName (column1, column2, column3) VALUES (?, ?, ?)"
  $types = "ssi";
  dbPrepare($statement, $types, "Cool Guy", "fake@email.com", 123);
  */

  $args = array();
  $numargs = func_num_args();
  $arg_list = func_get_args();
  for ($i = 1; $i < $numargs; $i++) {
    array_push($args, $arg_list[$i]);
  }
  //$stmtArray = array_merge(array($types), $args);

  //logger(varToString($args));
  $stmt = $GLOBALS['conn']->prepare($statement);

  //logger(varToString($stmt));
  call_user_func_array (array($stmt, "bind_param"), refValues($args));
  $stmt->execute();
  return $result = $stmt->get_result();
}

function refValues($arr){
    if (strnatcmp(phpversion(),'5.3') >= 0) //Reference is required for PHP 5.3+
    {
        $refs = array();
        foreach($arr as $key => $value)
            $refs[$key] = &$arr[$key];
        return $refs;
    }
    return $arr;
}

dbConnect(0);
/*
$statement = "SELECT * FROM login WHERE id=?";
$types = "i";
$result = dbPrepare($statement, $types, 0);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo $row["username"] . "<br />";
  }
}*/

?>
