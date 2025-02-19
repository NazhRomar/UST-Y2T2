<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>
    <div class="container bg-dark text-white p-5 w-75">
        <form action="index.php" method="post">
            <div class="row">
                <div class="col">
                    <input type="date" name="date" id="" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col mt-3 text-end">
                    <input type="submit" value="Format Date" name="submit" class="btn btn-primary">
                </div>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>

<?php
$var1 = "Hello World!";
$var2 = strlen("Hello World!");

echo $var1 . " has ", strlen($var1), " characters";
//echo $var2;
echo "<br>No. of words: ", str_word_count($var1);
echo "<br>Reverse: ", strrev($var1);
echo "<br>Word Position: ", strpos($var1, "World");
echo "<br>Word Position: ", strpos($var1, "d!");
echo "<br>Strin Replace: ", str_replace("World!", "Ben", $var1);

echo "<br> Convers string to lowercase: ", strtolower($var1);
echo "<br> Convers string to uppercase: ", strtoupper($var1);
echo "<br> Caps first letter for each word: ", ucwords("ronan pogi");
//lcfirst
//ucfirst

//Numeric Functions
$num1 = 1000.5678;
$arr1 = [2, 56, 42, 23, 65];
$arr2 = [2, 56, 42, 23, 65];
echo "<br> Price Display: ", number_format($num1, 2);
echo "<br> Round number: ", round($num1, 2);
//abs
//rand
//max
//min

echo "<hr>";
echo "<br> Date today: ", date("M d Y, l");
echo "<br>", date("m/d/y");
echo "<br>", date("jS"), " of the month";
echo "<br>", date("h:i:s A");

//date_create
//date_format

if (isset($_POST["submit"])) {
    // $date = $_POST['date'];
    // echo "<br>",var_dump($date);
    // echo "<br> Date Set: ".$date;

    $date = date_create($_POST["date"]);
    echo "<br> Date Set: " . date_format($date, "F d, Y");
}
echo "<hr>";

/////////////////////////////////////
///////////

function display()
{
    echo "gumana si func";
}
display();
echo "<br>";

function displayName($name)
{
    echo $name;
}

displayName("Nazh Arcedo");
echo "<br>";



function add($add1, $add2)
{
    $sum = $add1 + $add2;

    return $sum;
}

echo add(10, 30);
echo "<br>";
?>

