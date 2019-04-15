<?php
function getValue($xml, $name, $default = "")
{
    if (isset($xml[$name])) {
        return $xml[$name];
    }
    if (isset($xml->$name)) {
        if (count($xml->$name->children()) > 0) {
            return $xml->$name->asXml();
        }
        return $xml->$name;
    }
    return $default;
}

function r($value)
{
    //$value = str_replace("&", "&amp;", $value);
    $value = str_replace("ä", "&auml;", $value);
    $value = str_replace("Ä", "&Auml;", $value);
    $value = str_replace("ó", "&oacute;", $value);
    $value = str_replace("ö", "&ouml;", $value);
    $value = str_replace("Ö", "&Ouml;", $value);
    $value = str_replace("ü", "&uuml;", $value);
    $value = str_replace("Ü", "&Uuml;", $value);
    $value = str_replace("ß", "&szlig;", $value);
	//$value = str_replace("-", "&minus;", $value);
    return $value;
}

function head($xml)
{
    languages($xml->languages[0]);
    image($xml->image[0]);
    letterhead($xml->letterhead[0]);
    echo '<h3 align="center">' . ($xml->title[0] != "" ? $xml->title[0] : "&nbsp;") . '</h3>';
    echo '<h1 align="center" style="margin-top:0; margin-bottom:0px;">' . $xml->name[0] . '</h1>';
}

function image($xml)
{
    echo '<img src="' . $xml . '" class="bild">';
}

function letterhead($xml)
{
    global $address, $addressHidden, $addressQrHidden, $language;

    $nameWithTitle = getValue($xml, "nameWithTitle");
    $adress = r(getValue($xml, "adress"));
    $phone = getValue($xml, "phone");
    $phoneHref = str_replace('<wbr>', '', str_replace(' ', '', $phone));
    $email = getValue($xml, "email");
    $emailHref = str_replace('<wbr>', '', $email);

    echo '<div class="anschrift">';
    if (isset($address) || isset($addressHidden) || isset($addressQrHidden)) {
        echo '<i class="fas fa-user" alt=""></i>' . $nameWithTitle . '<br>';
        echo '<i class="fas fa-envelope" alt=""></i>' . $adress . '<br>';
        echo '<i class="fas fa-phone" alt=""></i><a href="tel:' . $phoneHref . '">' . $phone . '</a><br>';
        echo '<i class="fas fa-at" alt=""></i><a href="mailto:' . $emailHref . '">' . $email . '</a>';
    } else {
        echo '<div class="addressHidden printHidden gray" style="margin-left: -2em;">';
        if (isset($language) && $language == "de") {
            echo r('Aus Datenschutzgründen <br>ist der Briefkopf in dieser <br>Online-Version ausgeblendet.');
        } else {
            echo 'For privacy reasons the <br>letter head is hidden <br>in this online version.';
        }
        echo '</div>';
    }
    echo '</div>';
}

function languages($xml)
{
    global $address, $addressHidden, $qrHidden, $addressQrHidden, $addressName, $addressHiddenName, $qrHiddenName, $addressQrHiddenName;

    echo '<div class="language printHidden">';
    $url = 'http://' . $_SERVER['HTTP_HOST'] . strtok($_SERVER['REQUEST_URI'], '?') . "?";

    $parameters = array();
    if (isset($address)) $parameters[] = $addressName;
    if (isset($addressHidden)) $parameters[] = $addressHiddenName;
    if (isset($qrHidden)) $parameters[] = $qrHiddenName;
    if (isset($addressQrHidden)) $parameters[] = $addressQrHiddenName;
    if (!empty($parameters)) {
        $url .= join("&", $parameters) . "&";
    }

    foreach ($xml->children() as $name => $value) {
        $urlL = $url . 'l=' . $name;
        echo '<a href="' . $urlL . '"><img src="' . $value["image"] . '" /></a>';
    }
    echo '</div>';
}

function qr($xml)
{
    global $addressHidden, $qrHidden, $addressQrHidden;
    if (isset($qrHidden) || isset($addressQrHidden)) return;

    $float = getValue($xml, "float");
    $dateText = getValue($xml, "dateText");
    $linkText = getValue($xml, "linkText");
    $size = getValue($xml, "size");
    if ($size == "") $size = 60;

    $url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    if (isset($addressHidden)) {
        $url = str_replace("?adh", "", $url);
    }

    echo '<div class="bereich qr">';
    echo '<div style="display: table; width:100%;">';

    if ($float == "left") {
        echo '<div style="display: table-cell; width: ' . $size . 'px; float:' . $float . ';">
                    <a href="' . $url . '" style="text-decoration:none;"><img src="https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . urlencode($url) . '" width="' . $size . '" height="' . $size . '"></a>
                </div>';
    }

    echo '<div style="display: table-cell; text-align:' . $float . '; vertical-align:bottom; height: ' . $size . 'px; box-sizing: border-box; width: 100%;">
                    <div style="margin:' . ($size / 5) . 'px;">
                        ' . ($dateText != "" ? $dateText . ' ' : '') . date("j.n.Y") . '
                        <br />' . ($linkText != "" ? $linkText . ' ' : '') . '<a href="' . $url . '">' . $url . '</a>
                    </div>
                </div>';

    if ($float == "right") {
        echo '<div style="display: table-cell; width: ' . $size . 'px; float:' . $float . ';">
                    <a href="' . $url . '" style="text-decoration:none;"><img src="https://api.qrserver.com/v1/create-qr-code/?size=' . $size . 'x' . $size . '&data=' . urlencode($url) . '" width="' . $size . '" height="' . $size . '"></a>
                </div>';
    }

    echo '</div>';
    echo '</div>';
}

function item($xml)
{
    if (isset($xml["hidden"]) && $xml["hidden"] == "true") return;

    $nameDefault = "";
    $name = r(getValue($xml, "name", $nameDefault));
    if ($name == "") $name = r(getValue($xml, "value", $nameDefault));
    if ($name == "") $name = r(getValue($xml, "title", $nameDefault));
    $date = getValue($xml, "date");
    $location = getValue($xml, "location");
    $description = r(getValue($xml, "description"));
    $link = r(getValue($xml, "link"));

    if ($link != "") {
        echo '<a href="' . $link . '" style="text-decoration:none; color:inherent;">';
    }
    echo '<div class="item">';

    if ($location != "" || $date != "") {
        echo '<div class="top">';
        if ($date != "" && $location == "") {
            echo '<div class="date"><i class="far fa-calendar-alt" alt="Date:"></i> ' . $date . '</div>';
        } else if ($date == "" && $location != "") {
            echo '<div class="location"><i class="fas fa-map-marker-alt" alt="Location: "></i>' . $location . '</div>';
        } else if ($date != "") {
            echo '<div class="date"><i class="far fa-calendar-alt" alt="Date:"></i> ' . $date . '</div><div class="location"><i class="fas fa-map-marker-alt" alt="Location: "></i>' . $location . '</div>';
        }
        echo '</div>';
    }

	if ($name != "") {
    	echo '<div class="name">' . $name . '</div>';
	}

    if ($description != "") {
        echo '<div class="description">' . $description . '</div>';
    }

    echo '</div>';
    if ($link != "") {
        echo '</a>';
    }
}

function legend($xml)
{
    $div = $xml->asXml();
    $div = str_replace('<legend>', '<div class="legend">', $div);
    $div = str_replace('</legend>', '</div>', $div);
    echo $div;
}

function category($xml)
{
    global $c, $idCounter;

    if (isset($xml["hidden"]) && $xml["hidden"] == "true") return;

    $title = r(getValue($xml, "title", "No title set"));
    $icon = getValue($xml, "icon", "fa-question");

    echo '<div class="bereich color' . $c . '" id="bereich' . $idCounter . '">';
    echo '<h2 onClick="openCloseCategory(\'' . $idCounter . '\')" style="cursor: pointer;">';
    echo '<i class="fas ' . $icon . ' categoryIcon" alt="" aria-hidden="true"></i>';
    echo $title;
    echo '<i class="fas fa-caret-down" alt=""></i>';
    echo '</h2>';
    echo '<div class="items color' . $c . '" id="items' . $idCounter . '">';

    foreach ($xml->children() as $tag => $value) {
        if ($tag == "item") {
            item($value);
        }
        if ($tag == "legend") {
            legend($value);
        }
    }

    echo '</div>';
    echo '</div>';

    $c++;
    $idCounter++;
}

function container($xml)
{
    echo '<div class="container">';
    echo '<div class="paper">';
    echo '<div class="content">';
    head($xml->head);
    $body = $xml->body;
    echo '<div class="columns">';
    foreach ($body->column as $column) {
        echo '<div class="column">';
        foreach ($column->children() as $tag => $value) {
            if ($tag == "category") {
                category($value);
            }
            if ($tag == "qr") {
                qr($value);
            }
        }
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}

?>