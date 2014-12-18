<html>
<head>
<title>"Hello"</title>
</head>
<body>
<div>
This is a test
</div>
<?php

//echo "\nCurrent script owner: ".get_current_user();
//echo "\nCurrent directory: ".getcwd();
//echo "\nProgram result: ".exec("calc");
//$command='C:\Windows\System32\cmd.exe /c PS -i files/input/2014-12-08/alpha_helix.pdb -c A -f 1 -l 20 -a C -r CA -o files/output/2014-12-08/alpha_helix_example_.out';
$command="./PS -i files/input/2014-12-08/alpha_helix.pdb -c A -f 1 -l 20 -a C -r CA -o files/output/2014-12-08/alpha_helix_example_2.out";
//$command="./hello.sh";
echo exec($command,$output);
//echo $command;
echo $output[0];
?>
</body>
