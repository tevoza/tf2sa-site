<?php
$target_dir = "files/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imgFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

//Check if file is real image
if (isset($_POST["submit"]))
{
  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]); //not always reliable
  if ($check !== false)
  {
    $uploadOk = 1;
  }
  else
  {
    $uploadOk = 0;
  }
}

// check if file already exists
$imgHash = hash_file('sha256', $_FILES["fileToUpload"]["tmp_name"]);
$target_file = $target_dir . $imgHash;
if (file_exists($target_file))
{
  echo $imgHash;
}
else
{
  if ($_FILES["fileToUpload"]["size"] > 5000000)
  {
    $uploadOk = 0;
  }

  //Allow certain file formats
  if ($imgFileType != "jpg" && $imgFileType != "png" && $imgFileType != "jpeg" && $imgFileType != "gif")
  {
    $uploadOk = 0;
  }

  if ($uploadOk == 0)
  {
    echo "err";
  }
  else
  { 
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file))
    {
      echo $imgHash;
    }
    else
    {
      echo "err";
    }
  }
}
?>
