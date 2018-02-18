<?php
try{
$pdo = new PDO('mysql: host=159.89.183.50;port=3306;dbname=misc',
   'umsi', 'php123');
//$pdo = new PDO('mysql:host=localhost;port=3306;dbname=misc',
//See the "errors" folder for details...
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); }

catch (PDOException $e){
     echo 'ERROR: '. $e->getMessage();

};
