#!/usr/bin/php
<?php


require_once  '../vendor/autoload.php';


$db = new data(array(
    'server' =>'10.102.1.3'
 ,'user' =>'sa'
 ,'pass' =>'i3kygbb2'
 ,'database' =>'sifinca' 
 ,'engine'=>'mssql'
   
));


$p= new propietario($db,10705 );
print $p->getSaldo("201408", false);
print "\n";

print_r($p->getProperData());
print "\n";

$c = new contrato($db,10705);
print "\n";

echo $c->getSaldoConta('13809510705',array('year'=>2014,'month'=>8));
print "\n";

print_r($p->getConc_Comi('201408'));

print_r($p->getConc_NoComi('201408'));

?>
