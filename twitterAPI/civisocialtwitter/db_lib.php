<?php
// 


class db
{
  // Store the database handle for use by all functions
  public $dbh;
 
   // Create a database connection for use by all functions in this class
  function __construct() {
    require('config.php');
    $this->dbh = mysqli_connect('localhost', $db_user, $db_password, $db_name);
	
    if ($this->dbh == '') {      
      print 'Error: Unable to connect to database';
    }  
	
		// Set the timezone for tweets collected from the Twitter API
		date_default_timezone_set('America/New_York');      
  }
  
	// Create a standard date format for insertion of PHP dates into MySQL
  public function date($php_date) {
    return date('Y-m-d H:i:s', strtotime($php_date));	
  }
    
  // All text added to the DB should be cleaned with mysqli_real_escape_string
  // to block attempted SQL insertion exploits
  public function escape($str) {
    return mysqli_real_escape_string($this->dbh,$str);
  }
    
  // Test to see if a specific field value is already in a table
  // Return false if no, true if yes
  public function in_table($table,$where) {
    $query = 'SELECT * FROM ' . $table . 
      ' WHERE ' . $where;
    $results = mysqli_query($this->dbh,$query);
    return mysqli_num_rows($results) > 0;
  }
  
  // Perform a generic select and return a pointer to the result
  public function select($query) {
    $results = mysqli_query( $this->dbh, $query );
    return $results;
  }
  
  // Perform a select and return an array containing all the results
  // This should only be done for queries that return a limited set of data
  public function select_array($query) {
  	$results = $this->select($query);
		$rows = array();
		while($row = mysqli_fetch_assoc($results)) {
			array_push($rows,$row);
		}
		return $rows;
  }
    
  // Add a row to any table
  public function insert($table,$field_values) {
    $query = 'INSERT  INTO ' . $table . ' SET ' . $field_values;
    mysqli_query($this->dbh,$query);
  }
    
  // Update any row that matches a WHERE clause
  public function update($table,$field_values,$where) {
    $query = 'UPDATE ' . $table . ' SET ' . $field_values . 
      ' WHERE ' . $where;
    mysqli_query($this->dbh,$query);
  }  
   
}  
?>