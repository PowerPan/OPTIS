<?php
/**
 * Dieses script findet für alle Linien alle möglichen Linienverläufe und schreibt diese in
 * die MySQL Tabellen.
 */
include('classMySQL.php');
//Erstellen der benötigten MySQL Objekte
$mysql_route = new MySQL();
$mysql_trips = new MySQL();
$mysql_verlauf = new MySQL();
$mysql_insert = new MySQL();

//Tabellen vor dem neubefüllen löschen
$mysql_insert->query("truncate verlauf;");
$mysql_insert->query("truncate routes_verlauf;");
$mysql_insert->query("truncate ver_verlauf_trip;");

//Benötigte Variabeln deklarieren
$verlauf = Array();
$temp_verlauf = Array();
$direction = NULL;
$count_linien = 0;
$count_verlauf = 0;

//Query um alle Linien nacheinander auszulesen
$query_route = "select route_id from gtfs_routes";
$mysql_route->query($query_route);
while($row_route = $mysql_route->fetchRow()){
    //Linienzähler erhöhen
    $count_linien++;
    //Alle Trips für eine bestimmte Linie auslesen
    $query_trips = "select trip_id,direction_id from gtfs_trips where route_id = ".$row_route['route_id'];
    $mysql_trips->query($query_trips);
    while($row_trips = $mysql_trips->fetchRow()){
        //Aus der Stop Times die Reihenfolge der Halte auslesen
        $query_verlauf = "select stop_id from gtfs_stop_times where trip_id = ".$row_trips['trip_id']." order by stop_sequence";
        $mysql_verlauf->query($query_verlauf);
        //Das Array $temp_verlauf hat 2 Dimensionen
        //$temp_verlauf
        //  |-->stops hier drunter ein ein einfaches Array in der Reihenfolge der Haltestellen
        //  |-->trips Array mit der Trip_id (Das Array wäre an diser Stelle noch nicht erforderlich macht es später aber einfacher da das temp_array so einfacher in das verlauf Array kopiert werden kann)
        while($row_verlauf = $mysql_verlauf->fetchRow()){
            //Das Array wir in der Reihenfolge der Abfahrten gefüllt
            $temp_verlauf['stops'][] = $row_verlauf['stop_id'];
        }
        //Hinzufügen der Trip ID des verlaufes
        $temp_verlauf['trips'][] = $row_trips['trip_id'];

        //Schauen ob es schon einen Verlauf gibt für die jeweilige Richtung
        if($verlauf[$row_trips['direction_id']]){
            //Durchlaufen aller Verläufe füreine Richtung um bei gleichjeit einem Verlauf nur einen
            //zusätzlichen trip zuordnen zu können
            $gefunden = 0;
            foreach($verlauf[$row_trips['direction_id']] as $type => $properties){
                //Abfrage ob der Verlauf schon vorhanden ist, wenn ja nur die trip_id hinzufügen und die Variabel
                //$gefunden auf 1 setzen
                if($properties['stops'] == $temp_verlauf['stops']){
                    $verlauf[$row_trips['direction_id']][$type]['trips'][] = $temp_verlauf['trips'][0];
                    $gefunden = 1;
                }
            }
            //Wurde der Verlauf nicht gefunden ist die Variabel $gefunden hier noch 0
            //Das bedeutet wir haben einen bisher noch nicht bekannten Verlauf
            if($gefunden == 0){
                $verlauf[$row_trips['direction_id']][] = $temp_verlauf;
                $count_verlauf++;
            }

        }
        else{
            //Wenn es keinen Verlauf Gibt legen wir einen ann
            $verlauf[$row_trips['direction_id']][] = $temp_verlauf;
            $count_verlauf++;
        }
        //Löschen des Arrays für den nächsten Schleifendurchgang
        $temp_verlauf = NULL;
    }
    //Alle Verläufe durchlaufen
    foreach($verlauf as $direction => $values){
        //Bei Richtiungen durchlaufen
        foreach($values as $ver){
            //Alle Verläufe einer Richtung durchlaufen und in die Tabellen schreiben
            $mysql_insert->query("insert into routes_verlauf (route_id,direction) values (".$row_route['route_id'].",".$direction.")");
            $verlauf_id = $mysql_insert->last_insert_id;
            $sequence = 1;
            //Stops füllen
            foreach($ver['stops'] as $stop){
                $mysql_insert->query("insert into verlauf (verlauf_id,stop_id,sequence) values (".$verlauf_id.",".$stop.",".$sequence.");");
                $sequence++;

            }
            //Trips füllen
            foreach($ver['trips'] as $trip)
                $mysql_insert->query("insert into ver_verlauf_trip (verlauf_id,trip_id) values (".$verlauf_id.",".$trip.");");
        }
    }
    //Array für den nächsten Verlauf löschen
    $verlauf = NULL;
}
echo "Für ".$count_linien." Linien ".$count_verlauf." Verl&auml;ufe gefunden";