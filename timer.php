<?php 

class Timer { 

   var $classname = "Timer"; 
   var $start     = 0; 
   var $stop      = 0; 
   var $elapsed   = 0; 

   # Constructor 
   function Timer( $start = true ) { 
      if ( $start ) 
         $this->start(); 
   } 

   # Start counting time 
   function start() { 
      $this->start = $this->_gettime(); 
   } 

   # Stop counting time 
   function stop() { 
      $this->stop    = $this->_gettime(); 
      $this->elapsed = $this->_compute(); 
   } 
    
   # Get Elapsed Time 
   function elapsed() { 
      if ( !$elapsed ) 
         $this->stop(); 

      return $this->elapsed; 
   } 
    
   # Get Elapsed Time 
   function reset() { 
      $this->start   = 0; 
      $this->stop    = 0; 
      $this->elapsed = 0; 
   } 

   #### PRIVATE METHODS #### 
    
   # Get Current Time 
   function _gettime() { 
      $mtime = microtime(); 
      $mtime = explode( " ", $mtime ); 
      return $mtime[1] + $mtime[0]; 
   } 
    
   # Compute elapsed time 
   function _compute() { 
      return $this->stop - $this->start; 
   } 
} 

?>
