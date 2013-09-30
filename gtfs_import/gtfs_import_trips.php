<?php
/* Import Skript für die die Datei trips.txt */
header('Content-Type: text/html; charset=iso-8859-1');
include_once("../classMySQL.php");
$mysql = new MySQL();
if($_GET['delete']  == "true")
    $mysql->query("Truncate gtfs_trips");
$datei = fopen("data/trips.txt","r");
$firstrow = 1;
$rowcount = 0;
$insertcloums;
while(!feof($datei)){
	set_time_limit(5000);
	$row = fgets($datei);
    if($firstrow == 0){
        $row = explode(",",$row);
        $query = "insert into gtfs_trips (".$insertcloums.") values (";
        foreach($row as $data){

            $data = str_replace("Kicrhdorf(Aurich)","Kirchdorf",$data);
            $data = str_replace("Platzdorf(Aurich)","Platzdorf",$data);
            $data = str_replace("Sandhorst(Aurich)","Sandhorst",$data);
            $data = str_replace("Walle(Aurich)","Walle",$data);

            $data = str_replace("Holzdorf(Berumbur)","Holzdorf",$data);

            $data = str_replace("Nesse(Dornum)","Nesse",$data);
            $data = str_replace("Ostdorf(Dornum)","Ostdorf (Dornum)",$data);

            $data = str_replace("Lehe(Ems)","Lehe",$data);

            $data = str_replace("Fresenburg(Emsld)","Fresenburg",$data);

            $data = str_replace("Etzel(Friedeburg)","Etzel",$data);
            $data = str_replace("Horsten(Friedeburg)","Horsten",$data);
            $data = str_replace("Marx(Friedeburg)","Marx",$data);
            $data = str_replace("Moorstrich(Friedeburg)","Moorstrich",$data);
            $data = str_replace("Streek(Friedeburg)","Streek",$data);

            $data = str_replace("Grafschaft(Friesl)","Grafschaft",$data);

            $data = str_replace("Felde(Großefehn)","Felde",$data);
            $data = str_replace("Moorlage(Großefehn)","Moorlage",$data);

            $data = str_replace("Westerende(Großheide)","Westerende (Großheide)",$data);

            $data = str_replace("Junkersrott(Hagermarsch)","Junkersrott",$data);

            $data = str_replace("Osterhausen(Hinte)","Osterhausen",$data);
            $data = str_replace("Westerhusen(Hinte)","Westerhusen (Hinte)",$data);

            $data = str_replace("Ludwigsdorf(Ihlow)","Ludwigsdorf",$data);
            $data = str_replace("Ostende(Ihlow)","Ostende",$data);
            $data = str_replace("Riepe(Ihlow)","Riepe",$data);
            $data = str_replace("Westerende(Ihlow)","Westerende (Ihlow)",$data);

            $data = str_replace("Sande(Kr Friesland)","Sande",$data);

            $data = str_replace("Campen(Krummhörn)","Campen",$data);

            $data = str_replace("Coldam(Leer)","Coldam",$data);

            $data = str_replace("Norddeich(Norden)","Norddeich",$data);
            $data = str_replace("Süderneuland(Norden)","Süderneuland",$data);
            $data = str_replace("Süderneuland(Norden)","Süderneuland",$data);
            $data = str_replace("Westerloog(Norden)","Westerloog",$data);

            $data = str_replace("Aurich(Ostfriesl)","Aurich",$data);
            $data = str_replace("Blomberg(Ostfriesl)","Blomberg",$data);
            $data = str_replace("Boen(Ostfriesl)","Boen",$data);
            $data = str_replace("Brinkum(Ostfriesl)","Brinkum",$data);
            $data = str_replace("Burhafe(Ostfriesl)","Burhafe",$data);
            $data = str_replace("Emden(Ostfriesl)","Emden",$data);
            $data = str_replace("Friedeburg(Ostfriesl)","Friedeburg",$data);
            $data = str_replace("Hasselt(Ostfriesl)","Hasselt",$data);
            $data = str_replace("Hesel(Ostfriesl)","Hesel (Ostfriesl)",$data);
            $data = str_replace("Hesel(Friedeburg)","Hesel (Friedeburg)",$data);
            $data = str_replace("Ihlow(Ostfriesl)","Ihlow",$data);
            $data = str_replace("Leer(Ostfriesl)","Leer",$data);
            $data = str_replace("Nenndorf(Ostfriesl)","Nenndorf (Ostfriesl)",$data);
            $data = str_replace("Norden(Ostfriesl)","Norden",$data);
            $data = str_replace("Westerholt(Ostfriesl)","Westerholt",$data);

            $data = str_replace("Aschendorf(Papenburg)","Aschendorf",$data);

            $data = str_replace("Buralge(Rhauderfehn)","Buralge",$data);
            $data = str_replace("Holte(Rhauderfehn)","Holte",$data);
            $data = str_replace("Klostermoor(Rhauderfehn)","Klostermoor",$data);

            $data = str_replace("Heidmühle(Schortens)","Heidmühle",$data);
            $data = str_replace("Moorhausen(Schortens)","Moorhausen",$data);

            $data = str_replace("Rodenkirchen(Stadtland)","Rodenkirchen",$data);

            $data = str_replace("Finkenburg(Südbrookmerland)","Finkenburg",$data);
            $data = str_replace("Moordorf(Südbrookmerland)","Moordorf",$data);

            $data = str_replace("Ahlen(Uplengen)","Ahlen",$data);
            $data = str_replace("Bühren(Uplengen)","Bühren",$data);
            $data = str_replace("Hollen(Uplengen)","Hollen",$data);
            $data = str_replace("Neudorf(Uplengen)","Neudorf",$data);
            $data = str_replace("Stapel(Uplengen)","Stapel",$data);


            $data = str_replace("Hohenkirchen(Wangerland)","Hohenkirchen",$data);
            $data = str_replace("Helle(Wangerland)","Helle",$data);
            $data = str_replace("Nenndorf(Wangerland)","Nenndorf (Wangerland)",$data);
            $data = str_replace("Oldorf(Wangerland)","Oldorf",$data);
            $data = str_replace("Tettens(Wangerland)","Tettens",$data);
            $data = str_replace("Westurm(Wangerland)","Westurm (Wangerland)",$data);

            $data = str_replace("Holthusen(Weener)","Holthusen",$data);

            $data = str_replace("Ihren(Westoverledigen)","Ihren",$data);

            $data = str_replace("Moorburg(Westerstede)","Moorburg",$data);

            $data = str_replace("Voßbarg(Wiesmoor)","Voßbarg",$data);

            $data = str_replace("Inhausen(Wilhelmshaven)","Inhausen",$data);

            $data = str_replace("Asel(Wittmund)","Asel",$data);
            $data = str_replace("Kloster(Wittmund)","Kloster (Wittmund)",$data);
            $data = str_replace("Westerhusen(Wittmund)","Westerhusen (Wittmund)",$data);

            $data = str_replace("Neuenhaus(Wittmund)","Neuenhaus",$data);


            $querydata[] = "'".str_replace('"','',utf8_decode($data))."'";
        }
        $querydata = implode(",",$querydata);
        $query .= $querydata.");";
        if($querydata != ""){
            $mysql->query($query);
            $querydata = "";
            $rowcount++;
        }
    }
    else{
        $insertcloums = $row;
    }
	$firstrow = 0;		
}
fclose($datei);

echo $rowcount." Zeilen in Datenbank geschrieben";
?>