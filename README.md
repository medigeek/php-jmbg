[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

# Introduction

A PHP Class for the Serbian JMBG Number (Unique Master Citizen Number, Jedinstveni matični broj građana).

Simple example:

```
<?php

require "JMBG.php";

use MediGeek\JMBG;

$jmbg = new JMBG("0101001735005");
$jmbg->validate();
//var_dump($jmbg->getParsed("json"));
var_dump($jmbg->getParsed());
```


