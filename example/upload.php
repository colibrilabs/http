<?php

use Dez\Http\Request\File;

error_reporting(1);
ini_set('display_errors', 1);

include_once '../vendor/autoload.php';

$request = new \Dez\Http\Request();

foreach ($request->getUploadedFiles('user.extra') as $file) {
    echo $file->getName() . ' -> ' . $file->getKey() . ' -> size ' . $file->getSize(File::SIZE_MEGABYTES) . 'Mb '. $file->getMimeType() . '/' . $file->getRealMimeType() .' -> ' . $file->getExtension() .' <br>';
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>

<form method="post" enctype="multipart/form-data">
    <input type="file" name="test_file" >
    <hr>
    <input type="file" name="few_files[]" >
    <input type="file" name="few_files[]" >
    <hr>
    <input type="file" name="user[avatar]">
    <input type="file" name="user[info]">
    <hr>
    <input type="file" name="user[extra][avatar]">
    <hr>
    <input type="submit" value="send">
</form>

</body>
</html>
