<?php 

	header('Content-Type: text/html; charset=iso-8859-1'); 
	include('classMySQL.php');
	
	$mysql = new MySQL();
	
	$stop_id = '1218408';
	$route_id = '1256';

    if(isset($_GET['stop']))
        $stop_id = $_GET['stop'];

    if(isset($_GET['route']))
        $route_id = $_GET['route'];

    $abfahrtszeiten =  Array();
	
	//Haltestellenname auslesen
	$query = "select stop_name from gtfs_stops where stop_id = '".$stop_id."'";
	$mysql->query($query);
	$row = $mysql->fetchRow();
	$haltestellenname = $row['stop_name'];
	
	//Linie auslesen
	$query = "select route_short_name from gtfs_routes where route_id = '".$route_id."'";
	$mysql->query($query);
	$row = $mysql->fetchRow();
	$linie = $row['route_short_name'];
	
	//Richtung auslesen
	$query = "select t.trip_headsign from gtfs_trips t left join gtfs_stop_times as st on(t.trip_id = st.trip_id) where route_id = '".$route_id."' and st.stop_id = '".$stop_id."' group by trip_headsign";
	$mysql->query($query);
	while($row = $mysql->fetchRow())
		$richtung[] = $row['trip_headsign'];
		
	//Abfahrtszeiten auslesen
	$query = "  select
                    st.departure_time time
                    ,t.trip_id
                    ,t.service_id
                    ,c.monday
                    ,c.tuesday
                    ,c.wednesday
                    ,c.thursday
                    ,c.friday
                    ,c.saturday
                    ,c.sunday
                from
                    gtfs_stop_times st
                    left join gtfs_trips as t on (t.trip_id = st.trip_id)
                    left join gtfs_calendar as c on (t.`service_id` = c.`service_id`)
                where
                        st.stop_id = '".$stop_id."'
                    and t.route_id = '".$route_id."'

                order by
                    departure_time";
	echo $query;
echo "<br>";
echo "<br>";
	$mysql->query($query);
    while($row = $mysql->fetchRow()){
        //Abfrage ob der Trip unter der Woche fährt
        if(($row['monday'] or $row['tuesday'] or $row['wednesday'] or $row['thursday'] or $row['friday']) == 1 ){
            if(@!in_array(substr($row['time'],3,2),$abfahrtszeiten['mo-fr'][substr($row['time'],0,2)]['minuten']))
                $abfahrtszeiten['mo-fr'][substr($row['time'],0,2)]['minuten'][] = substr($row['time'],3,2);
            $abfahrtszeiten['mo-fr'][substr($row['time'],0,2)][substr($row['time'],3,2)]['service_id'][] = $row['service_id'];
        }
        //Abfrage ob der Trip am Samstag fährt
        if($row['saturday'] == 1){
            $abfahrtszeiten['sa'][substr($row['time'],0,2)]['minuten'][] = substr($row['time'],3,2);
            $abfahrtszeiten['sa'][substr($row['time'],0,2)][substr($row['time'],3,2)]['service_id'][] = $row['service_id'];
        }
        //Abfrage ob der Trip am Sonntag fährt
        if($row['sunday'] == 1){
            $abfahrtszeiten['so'][substr($row['time'],0,2)]['minuten'][] = substr($row['time'],3,2);
            $abfahrtszeiten['so'][substr($row['time'],0,2)][substr($row['time'],3,2)]['service_id'][] = $row['service_id'];
        }
        $service[] = $row['service_id'];
    }


    echo "Haltestelle: ";
    echo $haltestellenname;
    echo "<br /></br />";

    echo "Linie: ";
    echo $linie;
    echo "<br /></br />";

    echo "Ziel: ";
    print_r($richtung);
    echo "<br /></br />";






    echo "<table border='1'><tr><td></td><td>Mo - Fr</td><td>Sa</td><td>So</td></tr>";
    for($k = 0;$k <= 23;$k++){
        $i = $k;
        if($i < 10)
            $i = "0".$i;
        echo "<tr>";
        echo "<td>";
        echo $i;
        echo "</td>";
        echo "<td>";
        //print_r ($abfahrtszeiten['mo-fr'][$i]['minuten']);
        if(count($abfahrtszeiten['mo-fr'][$i]['minuten']) > 0){
            foreach($abfahrtszeiten['mo-fr'][$i]['minuten'] as $minute){
                echo $minute."&nbsp;";
            }
        }
        else
            echo "&nbsp;";
        echo "</td>";
        echo "<td>";
        //print_r ($abfahrtszeiten['sa'][$i]['minuten']);
        if(count($abfahrtszeiten['sa'][$i]['minuten']) > 0){
            foreach($abfahrtszeiten['sa'][$i]['minuten'] as $minute){
                echo $minute."&nbsp;";
            }
        }
        else
            echo "&nbsp;";
        echo "</td>";
        echo "<td>";
        //print_r ($abfahrtszeiten['so'][$i]['minuten']);
        if(count($abfahrtszeiten['so'][$i]['minuten']) > 0){
            foreach($abfahrtszeiten['so'][$i]['minuten'] as $minute){
                echo $minute."&nbsp;";
            }
        }
        else
            echo "&nbsp;";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";


    print_r($abfahrtszeiten);

//print_r($abfahrtszeiten);
    $query = 'select min(date)min,max(date)max from gtfs_calendar_dates where service_id in ('.implode(",",$service).')';


    //Calendar Auslesen
    $query = 'select service_id,monday,tuesday,wednesday,thursday,friday,saturday,sunday,start_date,end_date from gtfs_calendar where service_id in ('.implode(",",$service).')';
    while($row = $mysql->fetchRow()){
        $calendar[] = $row;
    }

    //Calendar Dates Auslesen
    $query = 'select service_id,monday,tuesday,wednesday,thursday,friday,saturday,sunday,start_date,end_date from gtfs_calendar where service_id in ('.implode(",",$service).')';
    while($row = $mysql->fetchRow()){
        $calendar_dates[] = $row;
    }
    //echo $query;


	$startdatum = NULL;
	$enddatum = NULL;
	while($row = $mysql->fetchRow()){
		$datum = $row['date'];
		$datum = strtotime(substr($datum,0,4)."-".substr($datum,4,2)."-".substr($datum,6,2));
		//echo date('d.m.Y',$datum);
		if($startdatum == NULL){
			$startdatum = $datum;
		}
		if($enddatum < $datum)
			$enddatum = $datum;
	}
    echo "<br /></br />";
	echo "Anzahl Abfahrten: ";
	echo $mysql->count();
	echo "<br /></br />";
	echo date('d.m.Y',$startdatum);	
	echo "<br /></br />";
	echo date('d.m.Y',$enddatum);	
	
	echo "<br /></br />";
	

	
	$richtung;
	
	$datumvon;
	$datumbis;
	//Array Erste Spalte Tag 0 für Monatg bis 6 für Sonntag. Feiertag ist auch Sonntag zweite Spalte für Stunde an dem Tag. Die Dritte Spalte für die jeweiligen Abfahrtszeiten
	$abfahrtszeiten;
	






/**
 * Ermittle Feiertage, Arbeitstage und Wochenenden von einem Datum
 *
 * @param string $datum im Format YYYY-MM-DD
 * @param string $bundesland
 * @return string
 */
function checkfeiertag ($datum,$bundesland='') {

    $datum = explode("-", $datum);

    $datum[1] = str_pad($datum[1], 2, "0", STR_PAD_LEFT);
    $datum[2] = str_pad($datum[2], 2, "0", STR_PAD_LEFT);

    if (!checkdate($datum[1], $datum[2], $datum[0])) return false;

    $datum_arr = getdate(mktime(0,0,0,$datum[1],$datum[2],$datum[0]));

    $easter_d = date("d", easter_date($datum[0]));
    $easter_m = date("m", easter_date($datum[0]));

    /*
     *
        BW = Baden-Württemberg
        BY = Bayern
        BE = Berlin
        BB = Brandenburg
        HB = Bremen
        HH = Hamburg
        HE = Hessen
        MV = Mecklenburg-Vorpommern	NI = Niedersachsen
        NW = Nordrhein-Westfalen
        RP = Rheinland-Pfalz
        SL = Saarland	SN = Sachsen
        ST = Sachsen-Anhalt
        SH = Schleswig-Holstein
        TH = Thüringen
     */

    if ($datum[1].$datum[2] == '0101') {
        return true;
        //return 'Neujahr';
    } elseif ($datum[1].$datum[2] == '0106' && ($bundesland == "BW" || $bundesland = "BY" || $bundesland = "ST")){
        return true;
        //return 'Heilige Drei Könige';
    } elseif ($datum[1].$datum[2] == $easter_m.$easter_d) {
        return true;
        //return 'Ostersonntag';
    } elseif ($datum[1].$datum[2] == date("md",mktime(0,0,0,$easter_m,$easter_d+1,$datum[0]))) {
        return true;
        //return 'Ostermontag';
    } elseif ($datum[1].$datum[2] == date("md",mktime(0,0,0,$easter_m,$easter_d+39,$datum[0]))) {
        return true;
        //return 'Christi Himmelfahrt';
    } elseif ($datum[1].$datum[2] == date("md",mktime(0,0,0,$easter_m,$easter_d+49,$datum[0]))) {
        return true;
        //return 'Pfingstsonntag';
    } elseif ($datum[1].$datum[2] == date("md",mktime(0,0,0,$easter_m,$easter_d+50,$datum[0]))) {
        return true;
        //return 'Pfingstmontag';
    } elseif ($datum[1].$datum[2] == date("md",mktime(0,0,0,$easter_m,$easter_d+60,$datum[0])) && ($bundesland == "BW" || $bundesland = "BY" || $bundesland = "HE" || $bundesland = "NW" || $bundesland = "RP" || $bundesland = "SL")) {
        return true;
        //return 'Fronleichnam';
    } elseif ($datum[1].$datum[2] == '0501') {
        return true;
        //return 'Erster Mai';
    } elseif ($datum[1].$datum[2] == '0815' && ($bundesland == "BY" || $bundesland = "SL")) {
        return true;
        //return 'Mariä Himmelfahrt';
    } elseif ($datum[1].$datum[2] == '1101' && ($bundesland == "BW" || $bundesland = "BY" || $bundesland = "NW"  || $bundesland = "RP"  || $bundesland = "SL")) {
        return true;
        // return 'Allerheiligen';
    } elseif ($datum[1].$datum[2] == '1120' && $bundesland == "SN") {
        return true;
        //return 'Bus und Bettag';
    } elseif ($datum[1].$datum[2] == '1225') {
        return true;
        //return '1. Weihnachtsfeiertag';
    } elseif ($datum[1].$datum[2] == '1226') {
        return true;
        //return '2. Weihnachtsfeiertag';

    } else {
        return false;
    }

}
?>
