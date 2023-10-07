<?php

include('src/BarcodeGenerator.php');
include('src/BarcodeGeneratorPNG.php');
include('src/BarcodeGeneratorSVG.php');
include('src/BarcodeGeneratorHTML.php');

$generatorPNG = new Picqer\Barcode\BarcodeGeneratorPNG();
$generatorSVG = new Picqer\Barcode\BarcodeGeneratorSVG();
$generatorHTML = new Picqer\Barcode\BarcodeGeneratorHTML();

// echo $generatorHTML->getBarcode('35210924973647000168550010000332281375149156', $generatorPNG::TYPE_CODE_128_C);

// echo "<br>";
// echo "<br>";
// echo $generatorSVG->getBarcode('35210924973647000168550010000332281375149156', $generatorPNG::TYPE_EAN_13);
echo "<br>";
echo "<br>";
echo '<img src="data:image/png;base64,' . base64_encode($generatorPNG->getBarcode('35210924973647000168550010000332281375149156', $generatorPNG::TYPE_CODE_128_C, 1, 40)) . '">';
