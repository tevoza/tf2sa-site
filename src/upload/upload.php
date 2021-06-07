<?php
$target_dir="files/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imgFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

//Check if file is real image
if (isset($_POST["submit"]))
{
  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]); //not always reliable
  if ($check !== false)
  {
    echo "File is an image - ". $check["mime"]. ".";
    $uploadOk = 1;
  }
  else
  {
    echo "File not image";
    $uploadOk = 0;

  }
}

// check if file already exists
if (file_exists($target_file))
{
  echo "sorry, file already exists";
  $uploadOk = 0;
}

if ($_FILES["fileToUpload"]["size"] > 5000000)
{
  echo "file too massive";
  $uploadOk = 0;
}

//Allow certain file formats
if ($imgFileType != "jpg" && $imgFileType != "png" && $imgFileType != "jpeg" && $imgFileType != "gif")
{
  echo "not acceptable image format!";
  $uploadOk = 0;
}

if ($uploadOk == 0)
{
  echo "file not acceptable";
}
else
{
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file))
  {
    echo "file ". htmlspecialchars(basename($_FILES["fileToUpload"]["name"])). "has been uploaded";
  }
  else
  {
    echo "some error has happened upon you!";
  }
}

?>
