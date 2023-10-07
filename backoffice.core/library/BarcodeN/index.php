<?php 
require_once('barcode.inc.php');

$code_number = '35210924973647000168550010000332281375149156';

#new barCodeGenrator($code_number,0,'hello.gif');

new barCodeGenrator($code_number,0,'hello.gif', 430, 100, true);


?>