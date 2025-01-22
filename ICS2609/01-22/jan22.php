<!-- Nazh Romar Arcedo -->
<?php
$nraFirstname = "Nazh Romar"; //
$nraMiddleInitial = "J."; //
$nraLastname = "Arcedo"; //
$nraAge = "19"; //
$nraGender = "Male"; //
$nraBirthday = "January 9, 2006"; //
$nraNationality = "Filipino";//
$nraOccupation = "Student";
$nraUniversity = "UST"; //
$nraCollege = "College of Information and Computing Sciences"; //
$nraCollegePrefix = "CICS";//
$nraSection = "2ITA";//
$nraLikes = "Driving, Gaming, Sleeping";
$nraProgram = "Information Technology";//
$nraCivilStatus = "Single, But Interested";//
$nraLanguages = "Java, HTML, CSS, Javascript, Python, PHP";
$nraStudNum = "2023188661";
$nraDislikes = "Vehicle Accidents, Losing Games, Staying Awake";
$nraQuote = "Just Because You're in the Storm, Doesn't Mean The Game Is Over - Jonesy Fortnite";
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Arcedo Nazh Romar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- <style>
        .container-custom{
            width: 1000px;
        }

        .container-center{
            align-items: center;
        }

    </style> -->
    </head>
    <body class="bg-dark">
    <div class="container mt-5">
        <div class="container bg-info rounded p-3">
            <div class="container row mt-3">
                <div class="container col bg-white rounded col-4 m-3 p-5">
                    <img src="jonesy.webp" alt="" class="rounded-circle d-block mx-auto bg-dark" style="width: auto; height: 256px">
                    <p class="text-center p-3 mt-3 h4 fw-bold"><?php echo $nraFirstname. " " .$nraMiddleInitial. " " .$nraLastname;?></p>
                    <p class="fs-5 m-0 px-4 mb-1"><b>Gender:</b> <?php echo $nraGender?></p>
                    <p class="fs-5 m-0 px-4 mb-1"><b>Age:</b> <?php echo $nraAge?></p>
                    <p class="fs-5 m-0 px-4 mb-1"><b>Birth Date:</b> <?php echo $nraBirthday?></p>
                    <p class="fs-5 m-0 px-4 mb-1"><span class="fw-bold">Nationality:</span> <?php echo $nraNationality?></p>
                    <p class="fs-5 m-0 px-4 mb-1"><b>Civil Status:</b> <?php echo $nraCivilStatus?></p>
                    
                </div>
                
                <div class="container col bg-white rounded col m-3 p-5">
                    <h1 class="mb-3">About Me:</h1>
                    <p class="fs-5 m-0 px-4 mb-1"><span class="fw-bold">University: </span> <?php echo $nraUniversity. "   | " .$nraCollege. " (" .$nraCollegePrefix. ")"?> </p>
                    <p class="fs-5 m-0 px-4 mb-1"><span class="fw-bold">Program:</span> <?php echo $nraProgram ?></p>
                    <p class="fs-5 m-0 px-4 mb-1"><span class="fw-bold">Section:</span> <?php echo $nraSection?></p>
                    <p class="fs-5 m-0 px-4 mb-1"><span class="fw-bold">Student Number:</span> <?php echo $nraStudNum?></p>
                    <p class="fs-5 m-0 px-4 mb-1"><span class="fw-bold">Programming Languages That I Know:</span> <?php echo $nraLanguages?></p>
                    <br>
                    <p class="fs-5 m-0 px-4 mb-1"><span class="fw-bold">Likes:</span> <?php echo $nraLikes?></p>
                    <p class="fs-5 m-0 px-4 mb-1"><span class="fw-bold">Dislikes:</span> <?php echo $nraDislikes?></p>

                    <h1 class="my-3">Quote:</h1>
                    <p class="fs-5 m-0 px-4 mb-1"><?php echo $nraQuote?></p>
                </div>
            </div>
        </div>
    </div>
    
  </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>