<?php
  function controllers_autoload($clase) 
  {
    require_once 'controllers/' . $clase . '.php';
  }

  spl_autoload_register('controllers_autoload');
?>