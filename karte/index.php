<?php header('Content-Type: text/html; charset=iso-8859-1'); ?>
<html>
<head>
<title>Busplan Stadtwerke Wilhelmshaven</title>

<script src="../js/leaflet/leaflet.js"></script>
<script src="../js/leaflet/locatecontrol/L.Control.Locate.js"></script>
<script src="../js/leaflet/label/leaflet.label.js"></script>
<script src="js/jquery-1.9.1.min.js"></script>
<script src="haltestellen.js"></script>
<script src="linien.js"></script>
<script src="js/jquery.marquee.js"></script>
<link rel="shortcut icon" type="image/x-icon" href="../faviconswwv.ico" />
<link rel="stylesheet" href="../js/leaflet/leaflet.css" />
<link rel="stylesheet" href="../js/leaflet/label/leaflet.label.css" />
<link rel="stylesheet" href="../js/leaflet/locatecontrol/L.Control.Locate.css" />
 <!--[if IE ]>
     <link rel="stylesheet" href="../js/leaflet/leaflet.ie.css" />
     <link rel="stylesheet" href="../js/leaflet/locatecontrol/L.Control.Locate.ie.css" />
 <![endif]-->
<style type="text/css">
    #notification_marquee{
        color: gold;
        font-weight: bolder;
        width: 100%;
    }

    .header_zeile_linie{
        min-width: 50px;
        font-weight: bold;
    }

    .header_zeile_ziel{
        width: 95%;
        font-weight: bold;
    }
    .header_zeile_abfahrt{
        font-weight: bold;
    }

    h4{
        margin: 0;
        font-weight: bold;

    }

    .popup_abfahrtsmonitor h4{
        color: white;
    }

    .popup_abfahrtsmonitor td{
        color: white;
    }

    .popup_abfahrtsmonitor a{
        color: gold;
    }

    .leaflet-popup{
        opacity: 0.95 !important;
    }

    .leaflet-popup-content-wrapper{
        background-image: -webkit-linear-gradient(#12436C 0%, #82b2cc 100%);
        background-image: -moz-linear-gradient(#12436C 0%, #82b2cc 100%);
        background-image: -o-linear-gradient(#12436C 0%, #82b2cc 100%);
        background-image: linear-gradient(#12436C 0%, #82b2cc 100%);
    }

    .leaflet-popup-tip {
        background-color: #82b2cc;
    }
</style>
</head>
<body style="margin: 0;">

<div id="map" style="height: 100%; width:100%;"></div>
<script lang="javascript">

function makeNumericCmp(property) {
    return function (a, b) {
        return parseInt(a[property]) - parseInt(b[property]);
    };
}



//Karte initialisieren
    var map = new L.Map('map');

	//Alle Variabeln für die Haltestellen
	var haltestellenLayerGroup = new L.LayerGroup();
	var haltestellen = new Array();
	var haltestellenGroupLayerGroup = new L.LayerGroup();
	var haltestellenGroup = new Array();
	var haltestelleicon = new L.icon({iconUrl: 'images/haltestelle.png',iconSize: [36, 36]});

	//Alle Variabeln für die Linien
	var linienGroup = new Array();

    var visibleshapes = new L.FeatureGroup();

    var stopsvisible = new Array();
    var stops = new Array();

    var layer = new Array();
    layer['stops'] = new L.FeatureGroup();


	var layersControl = L.Control();
	var baseLayers = {};
	var overlays = {};

	var zoomstart;
	var zoomend;

    var fahrplanrows = 6;





    //Kartenlayer erstellen
    var cloudmadeUrl = 'http://{s}.tile.cloudmade.com/51eac48df2524da9b09235aff37f22ef/997/256/{z}/{x}/{y}.png',
			cloudmadeAttribution = 'Map data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2011 CloudMade - Software by Johannes Rudolph IT-Dienstleistungen - wwww.rudolphmedia.de - 0171 5646758',
			cloudmade = new L.TileLayer(cloudmadeUrl, {maxZoom: 18, attribution: cloudmadeAttribution});

        var osmURL = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            osmAttribution = 'Map data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2011 CloudMade - Software by Johannes Rudolph IT-Dienstleistungen - wwww.rudolphmedia.de - 0171 5646758',
            osm = new L.TileLayer(osmURL, {maxZoom: 18,attribution: osmAttribution});

        var osmURLde = 'http://{s}.tile.openstreetmap.de/tiles/osmde/{z}/{x}/{y}.png',
            osmAttributionde = 'Map data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2011 CloudMade - Software by Johannes Rudolph IT-Dienstleistungen - wwww.rudolphmedia.de - 0171 5646758',
            osmde = new L.TileLayer(osmURLde, {maxZoom: 18,attribution: osmAttributionde});

        var esriURL = 'http://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}',
            esriAttribution  = 'Map data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2011 CloudMade - Software by Johannes Rudolph IT-Dienstleistungen - wwww.rudolphmedia.de - 0171 5646758',
            esri = new L.TileLayer(esriURL, {maxZoom: 18,attribution: esriAttribution});

        var mapBOXURL = 'http://{s}.tiles.mapbox.com/v3/examples.map-4l7djmvo,tmcw.map-5hafkxww/{z}/{x}/{y}.png'
            mapBOXAttribution = 'Map data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2011 CloudMade - Software by Johannes Rudolph IT-Dienstleistungen - wwww.rudolphmedia.de - 0171 5646758',
            mapBOX = new L.TileLayer(mapBOXURL, {maxZoom: 18,attribution: mapBOXAttribution});

            var mapquest = new L.TileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png', {
                attribution: 'Tiles Courtesy of <a href="http://www.mapquest.com/">MapQuest</a> &mdash; Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
                maxZoom: 18,
                subdomains: '1234'
            })



		 var baseMaps = {
            "CloudMade": cloudmade,
            "OpenStreetMap": osm,
            "OpenStreetMap DE": osmde,
            "ESRI": esri,
            "mapBOX": mapBOX,
			"Mapquest": mapquest
        };

	layersControl = new L.Control.Layers(baseMaps,overlays);
	map.addControl(layersControl);
    map.setView(new L.LatLng(53.55418, 8.10173), 14).addLayer(osmde); // Wilhelmshaven

	L.control.scale().addTo(map);
	L.control.locate().addTo(map);


    map.on('zoomstart', function(e) {
			zoomstart = map.getZoom();
    });


    layersControl.addOverlay(layer['stops'],"Haltestellen");
    map.addLayer(layer['stops']);

	//Linien hinzufügen
    for(var i = 0; i < linienObj.linien.length;i++){
		var linenpunkte = new Array();
        linienGroup[i] = new L.FeatureGroup();
		for(var j = 0; j < linienObj.linien[i].latlng.length;j++){
			linenpunkte[j] = new L.LatLng(linienObj.linien[i].latlng[j].lat,linienObj.linien[i].latlng[j].lng);
		}
		var linienicon = new L.icon({iconUrl: 'images/'+ linienObj.linien[i].icon  ,iconSize: [32, 32]});
        linienGroup[i].addLayer(new L.Marker([linienObj.linien[i].lat,linienObj.linien[i].lng], {icon: linienicon}));
		linienGroup[i].addLayer(new L.Polyline(linenpunkte, {color: linienObj.linien[i].color,opacity: 1,weight: 5}).bindLabel(linienObj.linien[i].name));
        linienGroup[i].addTo(map);
        layersControl.addOverlay(linienGroup[i], linienObj.linien[i].name);

	}


    function haltestellenplan_laden(haltestellen_id,reload){
        //Abfragen ob das div noch vorhanden, es ist nur vorhanden wenn das Popup geöffnet ist
        //Die Abfrage wird benötigt, um den reload der jeweiligen Haltestelleninfos abzubrechen
        //alert('fahrplan_'+haltestellen_id);
        if(document.getElementById("fahrplan_"+haltestellen_id) || reload == 0){
            var notification_queue;
            var notification_row = false;
            $.post('../abfahrtsmonitor/read_haltestellenplan.php?stop_id=' + haltestellen_id+'&rows='+fahrplanrows,function(data) {
                //Erstellen HTML Code für das Popup

                var fahrplanObj = JSON.parse(data);
                var rows = fahrplanrows;
                if(fahrplanObj.notifications)
                    rows--;

                for(i = 0; i < rows ; i++){
                    if(fahrplanObj.fahrplan != null){


                    if(fahrplanObj.fahrplan[i]){
                        $("#fahrplan_zeile_"+haltestellen_id+"_"+i).html(
                            "<td class='zeile_linie' nowrap>"+ fahrplanObj.fahrplan[i].route_short_name +"</td>" +
                                "<td class='zeile_ziel'>"+ fahrplanObj.fahrplan[i].trip_headsign + "</td>" +
                                "<td class='zeile_abfahrt' align='right' valign='top' nowrap>"+ fahrplanObj.fahrplan[i].departure_time + "</td>");

                    }

                    }
                }

                if(!notification_row && fahrplanObj.notifications){
                    $("#fahrplan_zeile_"+haltestellen_id+"_"+rows).html("<td colspan='3' style='max-width: "+((screen.width)-60)+"px'><marquee scrollamount='3'><div id='notification_marquee'>&nbsp;</div></marquee></td>");
                    notification_row = true;
                }

                if(fahrplanObj.notifications){
                    var notification = new Array();
                    for(i = 0;i < fahrplanObj.notifications.length;i++)
                        notification.push(fahrplanObj.notifications[i]);
                    notification_queue = notification.join('&nbsp;+++&nbsp;');
                    $("#notification_marquee").html(notification_queue);
                }

                if(notification_row && !fahrplanObj.notifications)
                    notification_row = false;

                   // document.getElementById('haltestelle_'+haltestellen_id).innerHTML = html;
                    if(document.getElementById("fahrplan_"+haltestellen_id)){
                        setTimeout("haltestellenplan_laden("+haltestellen_id+",1)",30000);
                    }
            });
        }
    }

    function uhrzeit(haltestelle){
        //console.log('uhrzeit_'+haltestelle);
        time = new Date();
        var stunde = time.getHours();
        var minute = time.getMinutes();
        var sekunde = time.getSeconds();
        if(stunde < 10)
            stunde = "0" + stunde;
        if(minute < 10)
            minute = "0" + minute;
        if(sekunde < 10)
            sekunde = "0" + sekunde;
        if(document.getElementById('uhrzeit_'+haltestelle)){
            document.getElementById('uhrzeit_'+haltestelle).innerHTML =  stunde + ":" + minute + ":" + sekunde;
            setTimeout("uhrzeit("+haltestelle+")",100);
        }
        else
            return false;

    }

	map.on('click', function(e) {
            var tempmarker = new L.Marker(e.latlng);
            /*map.addLayer(tempmarker);
            alert(e.latlng);*/

        });



function get_print_stops(){
    $.post('get_stops.php?nElat=' + map.getBounds()._northEast.lat + '&nElng=' + map.getBounds()._northEast.lng +'&sWlat=' + map.getBounds()._southWest.lat +'&sWlng=' + map.getBounds()._southWest.lng,function(data) {
        var stopsObj = JSON.parse(data);
        if(stopsObj){
            for(var i = 0;i < stopsObj.length;i++){
                if(jQuery.inArray(stopsObj[i].id,stopsvisible) == "-1"){
                    var stop = {};
                    stop = new L.Marker([stopsObj[i].lat,stopsObj[i].lng], {icon: haltestelleicon}).bindLabel(stopsObj[i].name).bindPopup("",{minWidth: 350, maxWidth: 500}).addTo(layer['stops']);
                    stop.id = stopsObj[i].id;
                    stop.name = stopsObj[i].name;
                    stops[stopsObj[i].id] = stop;
                    stop.on("click", function(e) {
                        //this.setPopupContent("<div id='haltestelle_"+this.id+"'>"+this.id+"</div>");
                        var html = "<div class='popup_abfahrtsmonitor' id='fahrplan_"+this.id+"'>" +
                            "<table style='width: 100%'><tr><td style='width: 95%'><h4>"+this.name+"</h4></td><td valign='top'><h4 id='uhrzeit_"+this.id+"'></h4></td></tr></table>" +
                            "<hr>"+
                            "<table border='0' style='width: 100%'>" +
                            "<tr class='headerzeile'><td class='header_zeile_linie'>Linie</td><td class='header_zeile_ziel'>Ziel</td><td class='header_zeile_abfahrt'>Abfahrt</td></tr>";
                            for(var i = 0;i < fahrplanrows;i++){
                                html += "<tr id='fahrplan_zeile_"+this.id+"_"+i +"' class='fahrplanzeile'><td class='zeile_linie'>&nbsp;</td><td class='zeile_ziel'>&nbsp;</td><td class='zeile_abfahrt' valign='top'>&nbsp;</td></tr>";
                            }
                            html += "</table><hr>" +
                                "<a href='../abfahrtsmonitor/?stop_id=" +this.id + "'>Abfahrtsmonitor</a>"
                                "</div>"

                        this.setPopupContent(html);
                        haltestellenplan_laden(this.id,false);
                        var haltestelle = this.id;
                        map.on("popupopen",function(e){
                            uhrzeit(haltestelle)
                            //console.log(haltestelle);
                        })
                    });
                    stopsvisible.push(stopsObj[i].id);
                }
            }
        }
        if(stopsvisible){
            var swlat = map.getBounds()._southWest.lat;
            var swlng =  map.getBounds()._southWest.lng;
            var nelat =  map.getBounds()._northEast.lat;
            var nelng = map.getBounds()._northEast.lng;
            var stopsloeschen = new Array();
            for(var q in stopsvisible){
                if(stops[stopsvisible[q]].getLatLng().lat > swlat
                    && stops[stopsvisible[q]].getLatLng().lat < nelat
                    && stops[stopsvisible[q]].getLatLng().lng > swlng
                    && stops[stopsvisible[q]].getLatLng().lng < nelng){
                }
                else{
                    stopsloeschen.push(stops[stopsvisible[q]].id);
                }
            }
            for(var r in stopsloeschen){
                stopsvisible.splice(stopsvisible.indexOf(stopsloeschen[r]),1);
                layer['stops'].removeLayer(stops[stopsloeschen[r]]);
            }
        }

    });
}



map.on('moveend', function(e){
    get_print_stops();
});

get_print_stops();
</script> 

</body>
</html>