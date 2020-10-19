<?php
class mysql_obj{
   var $debug    = false;
   var $persistent   = false;
   var $local    = true;
   
   var $host     = 'localhost';
   var $db       = 'valutisite';
   var $user     = 'vratza';
   var $pass     = 'mar711';
   
   var $next     = false;
   var $prev     = false;
   
   var $step     = 10;
   
   var $conn     = 0;
   var $stmt     = 0;
   var $curs     = 0;
   var $row_num  = 0;
    

// Connection Function.  It check for existing connection in current instance
// If the connection is establish the next statement will use it. 

   function connect(){
      if($this->local){
         $this->host = "localhost";
      }
      if( true ){
	      if( $this->debug ){ echo "<b>Debug: </b>Connecting to $this->db...<br>\n"; };
         if( $this->persistent ){
            $this->conn=mysql_pconnect( $this->host, $this->user, $this->pass );
         }else{
            $this->conn=mysql_connect( $this->host, $this->user, $this->pass );
         };
         if( !$this->conn ){
            if( $this->debug )
               echo "<b>Debug:ERR:</b> $this->conn=mysql_connect():".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
            exit();
	      }else{
            if( $this->debug ){
               echo "<b>Debug:OK:</b> $this->conn=mysql_connect():".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
            };
            if( mysql_select_db( $this->db, $this->conn ) ){
               if( $this->debug )
                  echo "<b>Debug:OK:</b>mysql_select_db($this->db, $this->conn):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
            }else{
               if( $this->debug )
                  echo "<b>Debug:ERR:</b>mysql_select_db($this->db, $this->conn):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
               exit();
            }
	      }
      }mysql_query("SET NAMES utf8");
   }
   
   function query( $query, $aid = "" ){ // result <none>
      $this->connect();
      $this->stmt = mysql_query($query);
      if( $this->stmt ){
         if( $this->debug )
            echo "<b>Debug:OK:</b> $this->stmt=mysql_query($query):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
         $aid = mysql_insert_id(); //$this->conn
      }else{
         if( $this->debug )
            echo "<b>Debug:ERR:</b> $this->stmt=mysql_query($query):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
         $this->stmt = false;
      }
      //$this->logoff();
      return $this->stmt;
   }

   function fetch_scalar( $query ){ // result <none>
      $retr=array();
      $this->connect();
      if( $this->query($query) ){
         $retr = mysql_fetch_row($this->stmt );
         if( $retr ){
            if( $this->debug )
               echo "<b>Debug:OK:</b>fetch_scalar($this->stmt, $this->conn):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
         }else{
            if( $this->debug )
               echo "<b>Debug:ERR:</b>fetch_scalar($this->stmt, $this->conn):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
            $retr = array();
         }
      }
      $this->logoff();
      return $retr[0];
   }

   function fetch_row( $query, $ar_type = MYSQL_BOTH ){ // result <none>
      $retr=array();
      $this->connect();
      if( $this->query($query) ){
         $retr=mysql_fetch_row($this->stmt, $ar_type);
         if( $retr ){
            if( $this->debug )
               echo "<b>Debug:OK:</b>fetch_row($this->stmt, $this->conn):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
         }else{
            if( $this->debug )
               echo "<b>Debug:ERR:</b>fetch_row($this->stmt, $this->conn):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
            $retr = false;
         }
      }
      $this->logoff();
      return $retr;
   }

   function fetch_row_sp( $query, $start, $rows, &$total, $ar_type = MYSQL_BOTH ){ // result <none>
      $retr=array();
      $this->connect();
      if( $this->query($query) ){
         $retr=mysql_fetch_row($this->stmt, $ar_type);
         if( $retr ){
            if( $this->debug )
               echo "<b>Debug:OK:</b>fetch_row($this->stmt, $this->conn):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
         }else{
            if( $this->debug )
               echo "<b>Debug:ERR:</b>fetch_row($this->stmt, $this->conn):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
            $retr = false;
         }
      }
      $this->logoff();
      return $retr;
   }
   
   function fetch_array($query, $ar_type = MYSQL_ASSOC){ // result <none>
      $this->retr=array();
      $this->row_num = 0;
      $this->connect();
      if( $this->query($query) ){
         while( $this->tmp=mysql_fetch_array($this->stmt, $ar_type) ) {
            $this->retr[]=$this->tmp; // creating indexed array containing hashes
			   if( $this->debug ){
               var_dump($this->tmp); 
            }
	      }
         if( $this->retr ){
            $this->row_num = mysql_num_rows($this->stmt);
            if( $this->debug )
               echo "<b>Debug:OK:</b>mysql_fetch_array($this->stmt, $this->conn):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
         }else{
			   $this->retr = array();
            if( $this->debug ){
               echo "<b>Debug:ERR:</b>mysql_fetch_array($this->stmt, $this->conn):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
               var_dump($this->retr);
			   }
         }
      }
      $this->logoff();
      //echo "<hr>\n";
      //var_dump($this->retr);
      if(!is_array($this->retr)) $this->retr = array();
      return $this->retr;
   }

   function fetch_array_sp( $query, $start,$ar_type = MYSQL_BOTH )
   { // result <none>
//   	  $this->debug=true;
      $this->retr=array();
      $this->row_num = 0;
      $this->connect();
      if( $this->query($query))
	  {
	  	 @mysql_data_seek($this->stmt,$start);
		 for ($i=0;(($i<$this->step) and ($i<mysql_num_rows($this->stmt)-$start));$i++)
		 {
		 	$this->tmp=mysql_fetch_array($this->stmt, $ar_type);
            $this->retr[]=$this->tmp; // creating indexed array containing hashes
			   if( $this->debug )
			   {
               	var_dump($this->tmp); 
               }
		 }
         if( $this->retr )
		 {
            $this->row_num = mysql_num_rows($this->stmt);
			if ($this->row_num>($start+$this->step)) {$this->next=true;}
			if (($start-$this->step)>=0) {$this->prev=true;}
            if( $this->debug )
               echo "<b>Debug:OK:</b>mysql_fetch_array($this->stmt, $this->conn):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
         }
	else
		{
			   $this->retr = array();
            if( $this->debug )
			{
               echo "<b>Debug:ERR:</b>mysql_fetch_array($this->stmt, $this->conn):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
               var_dump($this->retr);
 		    }
         }
      }
      $this->logoff();
      //echo "<hr>\n";
      //var_dump($this->retr);
      if(!is_array($this->retr)) $this->retr = array();
      return $this->retr;
   }

   function fetch_array_assoc( $query ){ // result <none>
      $this->retr=array();
      $this->row_num = 0;
      $this->connect();
      if( $this->query($query) ){
         while( $this->tmp=mysql_fetch_array($this->stmt, MYSQL_NUM) ){
            $this->retr[$this->tmp[0]]=$this->tmp[1]; // creating indexed array containing hashes
			   if( $this->debug ){
               var_dump($this->tmp); 
            }
	      }
         if( $this->retr ){
            $this->row_num = mysql_num_rows($this->stmt);
            if( $this->debug )
               echo "<b>Debug:OK:</b>mysql_fetch_array($this->stmt, $this->conn):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
         }
      }
      if($this->auto_off)
            $this->logoff();
      //echo "<hr>\n";
      //var_dump($this->retr);
      if(!is_array($this->retr)) $this->retr = array();
      return $this->retr;
   }

   
   function fetch_column( $query, $column = 0, $ar_type = MYSQL_BOTH ){ // result <none>
      $this->retr=array();
      $this->row_num = 0;
      $this->connect();
      if( $this->query($query) ){
         while( $this->tmp=mysql_fetch_array($this->stmt, $ar_type) ) {
            $this->retr[]=$this->tmp["$column"]; // creating indexed array containing hashes
			   if( $this->debug ){
               var_dump($this->tmp); 
            }
	      }
         if( $this->retr ){
            $this->row_num = mysql_num_rows($this->stmt);
            if( $this->debug )
               echo "<b>Debug:OK:</b>mysql_fetch_array($this->stmt, $this->conn):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
         }else{
            if( $this->debug )
               echo "<b>Debug:ERR:</b>mysql_fetch_array($this->stmt, $this->conn):".mysql_errno($this->conn).": ".mysql_error($this->conn)."<br>\n";
            $this->retr = false;
         }
      }
      $this->logoff();
      return $this->retr;
   }

      function next_id($sequence) {
      $this->next_id = "0";
      $esequence=ereg_replace("'","''",$sequence)."_seq";
      $result=$this->fetch_scalar("Select nextval from $esequence limit 1");
      $this->query("UPDATE $esequence SET nextval=(nextval+1)");
      if ($this->stmt) {
         $this->next_id["$sequence"] = $result;
      }else{
         $this->query("CREATE TABLE $esequence ( seq char(1) DEFAULT '' NOT NULL, nextval bigint(20) unsigned DEFAULT '0' NOT NULL auto_increment, PRIMARY KEY (seq), KEY (nextval) )");
//         $this->query("REPLACE INTO $esequence values ('', nextval+1)", &$result);  
         $this->next_id["$sequence"] = $result;
      }
      return $result;
   }           


   function logoff(){
      @mysql_close($this->conn);
      $this->conn = 0;
      $this->stmt = 0;
      $this->curs = 0;   
      if($this->debug){
         echo "<b>Debug:</b> Log out ...<br>\n";
      };
   }

};
?>
