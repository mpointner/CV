<?php
$c = 1;
$idCounter = 1;

$addressName = "ad";
$addressHiddenName = "adh";
$qrHiddenName = "qrh";
$addressQrHiddenName = "adqrh";
$languageName = "l";

$address = isset($_GET[$addressName]) ? $_GET[$addressName] : null;
$addressHidden = isset($_GET[$addressHiddenName]) ? $_GET[$addressHiddenName] : null;
$qrHidden = isset($_GET[$qrHiddenName]) ? $_GET[$qrHiddenName] : null;
$addressQrHidden = isset($_GET[$addressQrHiddenName]) ? $_GET[$addressQrHiddenName] : null;
$language = isset($_GET[$languageName]) ? $_GET[$languageName] : null;

$dataFile = "data/data-en.xml";
if(isset($language)) {
    $dataFile = "data/data-".$language.".xml";
}
libxml_use_internal_errors(true);
$xml=simplexml_load_file($dataFile);
if ($xml === false) {
    echo 'Loading XML failed<br>';
    foreach(libxml_get_errors() as $error) {
        echo $error->message . '<br>';
    }
	return;
}

require_once("includes/functions.php");

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en-us">
<head>
    <title><?php echo $xml->title[0]; ?></title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo $xml->favicon; ?>">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
    <link rel="stylesheet" href="css/screen.css" media="all">
    <link rel="stylesheet" href="css/mobile.css" media="screen">
    <link rel="stylesheet" href="css/print.css" media="print">

    <script type="text/javascript">
        var closedCategories = 0;
        var printClosedCategoriesWarningEnabled = true;
        var printChromeWarningEnabled = true;
        function openCloseCategory(id) {
            var item = document.getElementById('items'+id);
            var bereich = document.getElementById('bereich'+id);
            var caret = bereich.getElementsByTagName("h2").item(0).getElementsByTagName("i").item(1);
            if(item.style.display == "none") {
                item.style.display = "block";
                bereich.classList.remove("printHidden");
                caret.classList.remove("fa-caret-up");
                caret.classList.add("fa-caret-down");
                closedCategories--;
            } else {
                item.style.display = "none";
                bereich.classList.add("printHidden");
                caret.classList.remove("fa-caret-down");
                caret.classList.add("fa-caret-up");
                closedCategories++;
            }
        }
        window.onbeforeprint = function () {
            if(printChromeWarningEnabled && !window.chrome) {
                alert('Please note that this site is optimized for printing in Google Chrome and might be printed incorrectly in other browsers!');
                printChromeWarningEnabled = false;
            }
            if(printClosedCategoriesWarningEnabled && closedCategories > 0) {
                alert('Collapsed categories will not be printed, you have to expand them in case you wish to print them!');
                printClosedCategoriesWarningEnabled = false;
            }
        }
    </script>
</head>
<body>
	<?php container($xml); ?>
</body>
</html>