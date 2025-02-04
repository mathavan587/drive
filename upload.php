<?php
session_start();
require_once __DIR__ . '/lib/Util.php';
$util = new Util();

if (! empty($_POST['submit'])) {
    require_once __DIR__ . '/lib/Config.php';

    require_once __DIR__ . '/lib/FileModel.php';
    $fileModel = new FileModel();

    if (! empty($_FILES["file"]["name"])) {
        $fileName = basename($_FILES["file"]["name"]);
        $targetFilePath = "data/" . $fileName;
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
            $fileInsertId = $fileModel->insertFile($fileName);
            if ($fileInsertId) {
                $_SESSION['fileInsertId'] = $fileInsertId;

                $googleOAuthURI = 'https://accounts.google.com/o/oauth2/auth?scope=' .
                urlencode(Config::GOOGLE_ACCESS_SCOPE) . '&redirect_uri=' .
                Config::AUTHORIZED_REDIRECT_URI . '&response_type=code&client_id=' .
                Config::GOOGLE_WEB_CLIENT_ID . '&access_type=online';

                header("Location: $googleOAuthURI");
                exit();
            } else {
                $util->redirect("error", 'Failed to insert into the database.');
            }
        } else {
            $util->redirect("error", 'Failed to upload file.');
        }
    } else {
        $util->redirect("error", 'Choose file to upload.');
    }
} else {
    $util->redirect("error", 'Failed to find the form data.');
}
?>