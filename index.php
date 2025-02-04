<?php
session_start();

?>
<html>
<head>
<title>How to upload file to Google drive</title>
<link rel="stylesheet" type="text/css" href="css/style.css" />
<link rel="stylesheet" type="text/css" href="css/form.css" />
<style>
input.btn-submit {
    background: #ffc72c url("google-drive-icon.png") no-repeat center left
        45px;
    text-align: right;
    padding-right: 45px;
}
</style>
</head>
<body>
    <div class="phppot-container tile-container">
        <form method="post" action="upload.php" class="form"
            enctype="multipart/form-data">
<?php if(!empty($_SESSION['responseMessage'])){ ?>
    <div id="phppot-message"
                class="<?php echo $_SESSION['responseMessage']['messageType']; ?>">
                <?php echo $_SESSION['responseMessage']['message']; ?>
    </div>
<?php
    $_SESSION['responseMessage'] = "";
}
?>
<h2 class="text-center">Upload file to drive</h2>
            <div>
                <div class="row">
                    <label class="inline-block">Select file to upload</label>
                    <input type="file" name="file" class="full-width"
                        required>

                </div>
                <div class="row">
                    <input type="submit" name="submit"
                        value="Upload to Drive"
                        class="btn-submit full-width">
                </div>
            </div>
        </form>

    </div>
</body>
</html>