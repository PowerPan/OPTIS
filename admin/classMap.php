<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 15.08.13
 * Time: 15:38
 * To change this template use File | Settings | File Templates.
 */

class Map extends obj{

    public function __construct(){
        ?>
            <div id="map"></div>
            <script lang="javascript">
                var map = new L.Map('map');
                var layersControl = L.Control();
                var baseLayers = {};
                var overlays = {};
                var cloudmadeUrl = 'http://{s}.tile.cloudmade.com/51eac48df2524da9b09235aff37f22ef/997/256/{z}/{x}/{y}.png',
                    cloudmadeAttribution = 'Map data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2011 CloudMade - Software by Johannes Rudolph IT-Dienstleistungen - wwww.rudolphmedia.de - 0171 5646758',
                    cloudmade = new L.TileLayer(cloudmadeUrl, {maxZoom: 18, attribution: cloudmadeAttribution});

                var osmURL = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    osmAttribution = 'Map data &copy; 2011 OpenStreetMap contributors, Imagery &copy; 2011 CloudMade - Software by Johannes Rudolph IT-Dienstleistungen - wwww.rudolphmedia.de - 0171 5646758',
                    osm = new L.TileLayer(osmURL, {maxZoom: 18,attribution: osmAttribution});

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
                    "ESRI": esri,
                    "mapBOX": mapBOX,
                    "Mapquest": mapquest
                };

                layersControl = new L.Control.Layers(baseMaps,overlays);
                map.addControl(layersControl);
                map.setView(new L.LatLng(53.55418, 8.10173), 12).addLayer(osm); // Wilhelmshaven
            </script>
        <?php
    }

    public function add_draw(){
        ?>
            <script lang="javascript">
                var drawnItems = new L.FeatureGroup();
                map.addLayer(drawnItems);
                var drawControl = new L.Control.Draw({
                    draw: {
                        position: 'topleft',
                        polygon: null,
                        polyline: {
                            metric: true
                        },
                        circle:null,
                        marker: null,
                        rectangle: null
                    },
                    edit: {
                        featureGroup: drawnItems
                    }
                });
                map.addControl(drawControl);

                map.on('draw:created', function (e) {
                    var type = e.layerType,
                        layer = e.layer;

                    if (type === 'marker') {
                        layer.bindPopup('A popup!');
                    }

                    drawnItems.addLayer(layer);
                });
            </script>
        <?php
    }

    public function add_draw_edit_polyline(){
        ?>
        <script lang="javascript">
            var drawnItems = new L.FeatureGroup();
            map.addLayer(drawnItems);
            var drawControl = new L.Control.Draw({
                draw: {
                    position: 'topleft',
                    polygon: null,
                    polyline: null,
                    circle:null,
                    marker: null,
                    rectangle: null
                },
                edit: {
                    featureGroup: drawnItems,
                    remove: null
                }
            });
            map.addControl(drawControl);

            map.on('draw:edited', function (e) {
                var layers = e.layers;
                var geometries = {};
                layers.eachLayer(function (layer) {
                    geometries[layer.id+layer.type] = {
                        "latlng" : layer.toGeoJSON().geometry.coordinates,
                        "id": layer.id,
                        "type": layer.type
                    }

                });

                $.post('ajax.php?func=save_polyline',{geometries: geometries}, function(data) {

                    location.href = "?seite=linien";

                });
            });
        </script>
    <?php
    }

    public function add_polyline($json_points,$color,$id){
        ?>
            <script lang="javascript">
                var points = <?php echo $json_points;?>;
                var latlng = new Array();

                for (var i = 0;i < points.length;i++){
                    latlng.push(new L.LatLng(points[i].lat,points[i].lng));
                }
                var polyline = new L.Polyline(latlng,{color: "<?php echo $color;?>",opacity:1}).addTo(map);
                polyline.id = <?php echo $id; ?>;
                polyline.type = "polyline";
                drawnItems.addLayer(polyline);
            </script>
        <?php
    }
    public function add_marker($lat,$lng,$id,$icon,$icon_x,$icon_y){
        ?>
        <script lang="javascript">
            var myicon = new L.icon({
                iconUrl: '<?php echo $icon;?>',
                iconSize: [<?php echo $icon_x;?>, <?php echo $icon_y;?>]
            });
            var marker = new L.Marker([<?php echo $lat;?>,<?php echo $lng;?>],{icon: myicon});

            marker.id = <?php echo $id; ?>;
            marker.type = "marker";
            drawnItems.addLayer(marker);
        </script>
    <?php
    }

}