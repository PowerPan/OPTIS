<?php
/* Upload Skript Entpackt die GTFS .txt Dateien in den Ordner data/ */

//Vor dem entpacken alle alten Dateien löschen
function deleteFilesFromDirectory($ordnername){
    //überprüfen ob das Verzeichnis überhaupt existiert
    if (is_dir($ordnername)) {
        //Ordner öffnen zur weiteren Bearbeitung
        if ($dh = opendir($ordnername)) {
            //Schleife, bis alle Files im Verzeichnis ausgelesen wurden
            while (($file = readdir($dh)) !== false) {
                //Oft werden auch die Standardordner . und .. ausgelesen, diese sollen ignoriert werden
                if ($file!="." AND $file !="..") {
                    //Files vom Server entfernen
                    unlink("".$ordnername."".$file."");
                }
            }
            //geöffnetes Verzeichnis wieder schließen
            closedir($dh);
        }
    }
}

//Funktionsaufruf - Directory immer mit endendem / angeben
deleteFilesFromDirectory("data/");

$zip = new ZipArchive;
if ($zip->open($_FILES['file']['tmp_name']) === TRUE) {
    $zip->extractTo('data');
    $zip->close();
    echo 'ok';
} else {
    echo 'Fehler';
}
?>