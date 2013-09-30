<?php header('Content-Type: text/html; charset=iso-8859-1');
/* Diese Datei lädt eine zip Datei auf den Server, entpackt diese und führt den GTFS Import durch */
?>
<html>
<head>
    <title>GTFS-Import</title>
    <script src="js/jquery-1.9.1.min.js"></script>
    <link rel="shortcut icon" type="image/x-icon" href="../faviconswwv.ico" />
    <script language="javascript">

        /* Upload: http://www.smart-webentwicklung.de/2013/04/ajax-datei-upload-mit-xhr2-und-formdata/ */
        var fileInput;
        var uploadButton;
        var file;

        $(document).ready(function()
        {
            init();
        });

        function init()
        {
            fileInput = $('#file');
            uploadButton = $('#upload');
            file = null;


            fileInput.bind('change', function()
            {
                file = this.files[0];
            });

            uploadButton.bind('click', function()
            {
                if(file !== null)
                {
                        document.getElementById('complete_loader').style.visibility = "visible";
                        upload();

                }
                else{
                    alert(unescape("Keine Datei ausgew%E4hlt!"));
                }
            });
        }

        function upload()
        {
            var xhr = new XMLHttpRequest();
            xhr.open('post', 'upload.php', true);


            //Wenn der Request abgeschlossen ist wird der eigentliche Import gestartet
            xhr.onreadystatechange=function()
            {
                if (xhr.readyState==4 && xhr.status==200)
                {
                    start_import();
                }
            }

            var formData = new FormData($('form').get(0));

            xhr.send(formData);
        }

        var delete_old_data;
        function start_import(){

            if(document.getElementById('delete_old_data').checked == true)
                delete_old_data = true;
            else
                delete_old_data = false;
            import_agency();
        }

        function import_agency(delte){
            document.getElementById('data').innerHTML = "Import Agency:" + "<br>";
            $.post('gtfs_import_agency.php?delete=' + delete_old_data,function(data) {
                document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + data + "<br><br>";
                import_calendar();
            });
        }

        function import_calendar(){
            document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + "Import Calendar:" + "<br>";
            $.post('gtfs_import_calendar.php?delete=' + delete_old_data,function(data) {
                document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + data + "<br><br>";
                import_calendar_dates();
            });
        }

        function import_calendar_dates(){
            document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + "Import Calendar Dates:" + "<br>";
            $.post('gtfs_import_calendar_dates.php?delete=' + delete_old_data,function(data) {
                document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + data + "<br><br>";
                import_routes();
            });
        }

        function import_routes(){
            document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + "Import Routes:" + "<br>";
            $.post('gtfs_import_routes.php?delete=' + delete_old_data,function(data) {
                document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + data + "<br><br>";
                import_stop_times();
            });
        }

        function import_stop_times(){
            document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + "Import Stop Times:" + "<br>";
            $.post('gtfs_import_stop_times.php?delete=' + delete_old_data,function(data) {
                document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + data + "<br><br>";
                import_stops();
            });
        }

        function import_stops(){
            document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + "Import Stops:" + "<br>";
            $.post('gtfs_import_stops.php?delete=' + delete_old_data,function(data) {
                document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + data + "<br><br>";
                import_trips();
            });
        }

        function import_trips(){
            document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + "Import Trips:" + "<br>";
            $.post('gtfs_import_trips.php?delete=' + delete_old_data,function(data) {
                document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + data + "<br><br>";
                linienverlauefe();
            });
        }

        function linienverlauefe(){
            document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + "Linien Verläufe:" + "<br>";
            $.post('linien_verlauefe.php',function(data) {
                document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + data + "<br><br>";
                read_haltestellen();
            });
        }

        function read_haltestellen(){
            document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + "Read Haltestellen:" + "<br>";
            $.post('read_haltestellen.php',function(data) {
                document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + data + "<br><br>";
                read_linien()
            });
        }

        function read_linien(){
            document.getElementById('data').inynerHTML = document.getElementById('data').innerHTML + "Read Linien:" + "<br>";
            $.post('read_linien.php',function(data) {
                document.getElementById('data').innerHTML = document.getElementById('data').innerHTML + data + "<br><br>";
                document.getElementById('complete_loader').style.visibility = "hidden";
            });
        }
    </script>
</head>
<body>

<h1>GTFS Import</h1>
<ol>
    <li>Import Datei ausw&auml;hlen (GTFS .txt Dateien im ZIP-Archiv)</li>
    <li>Import Starten dr&uuml;cken</li>
</ol>
<form action="upload.php" method="post">
    <input id="file" type="file" name="file" size="50"/>
    <input id="upload" type="button" name="upload" value="Import Starten" />
</form>
<input type="checkbox" checked="checked" id="delete_old_data"> Alte Daten aus der Datenbank l&ouml;schen?
<br />
<br />
<div id="data"></div>
<img id="complete_loader" src="images/ajax-loader.gif" style="visibility: hidden" />
</body>
</html>