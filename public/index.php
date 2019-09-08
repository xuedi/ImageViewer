<?php
const thumbSize = 100;
const imageSize = 200;
$list = json_decode(file_get_contents('../data/tree.json'), true);
?>

<html>
<head>
    <title>Gallery2</title>
    <script type="text/javascript" src="static/js/jquery-1.8.1.min.js"></script>
    <link rel="stylesheet" href="static/css/style.css" type="text/css"/>
</head>
<body>
<div id="base">
    <div class="menu_container">
        <h1>galleryV3</h1>
        <div id="menue_elemnts">
            <?php foreach ($list as $location => $data): ?>
                <li class='menue_location'><?php echo $location ?></li>
                <?php foreach ($data as $events): ?>
                    <li class='menue_item'>
                        <a class='menue_link'
                           id='<?php echo $events['hash'] ?>'><?php echo $events['date'] ?>
                            &nbsp;<?php echo $events['name'] ?></a>
                        <div class='menue_item_cnt'>(<?php echo count($events['files']) ?>)</div>
                    </li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </div>
    </div>
    <div id="gallery">
        <h2 id="gallery_name">Name</h2>
        <div id="thumbnails">ready to load</div>
        <div id="details" style="display: none">ready to load</div>
    </div>
</div>

<script type="text/javascript">

    function showImage(eventHash, imageHash) {
        $('#thumbnails').hide();
        $(window).scrollTop(0);

        $img = "<img width='700' class='image' onclick='showThumbs()' src='view.php?show=" + imageHash + "' />";

        let details = $('#details');
        details.html("");
        details.show();
        details.append("<div class='image_box'>" + $img + "</div>");
        details.append("<div class='image_details'>");

        $.getJSON("/details.php?show=" + imageHash, null, function (data) {
            details.append("<table>");
            details.append("<tr><th><b>Details</b></th></tr>");
            details.append("<tr><td>FileName" + data.meta.fileName + "</td></tr>");
            details.append("<tr><td>DateTime" + data.meta.dateTime + "</td></tr>");
            details.append("<tr><th>&nbsp;</th></tr>");
            details.append("<tr><th><b>Tags</b></th></tr>");
            data.meta.tags.forEach(function(element) {
                details.append("<tr><td>" + element + "</td></tr>");
            });
            details.append("</table>");
        });
        details.append("</div>");
    }

    function showThumbs() {
        $('#details').hide();
        $('#thumbnails').show();
    }

    function buildThumbs(eventHash) {
        showThumbs();
        let target = $('#thumbnails');
        target.html("");
        $.getJSON("/cache/events/" + eventHash + ".json", null, function (data) {
            $('#gallery_name').html(data.name);
            $.each(data.images, function () {
                $src = "/cache/<?php echo imageSize ?>/" + this.thumb;
                $img = "<img class='thumb' width='<?php echo thumbSize ?>' height='<?php echo thumbSize ?>' src='" + $src + "' />";
                $div = "<div class='thumb_box' onclick=\"showImage('" + eventHash + "', '" + this.src + "')\">" + $img + "</div>";
                target.append($div);
            });
        });
    }

    $(document).ready(function () {
        $('.menue_link').click(function () {
            buildThumbs($(this).attr('id'));
        });
    });
</script>

</body>
</html>