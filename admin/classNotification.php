<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 14.08.13
 * Time: 10:42
 * To change this template use File | Settings | File Templates.
 */

class Notification extends Obj{

    private $text;
    private $valid_from;
    private $valid_to;
    private $stops;

    protected function read(){
        $mysql = new MySQL();
        $mysql->query("select text,DATE_FORMAT(valid_from,'%d.%m.%Y %H:%i')valid_from,DATE_FORMAT(valid_to,'%d.%m.%Y %h:%i')valid_to from notification where notification_id = ".$this->id);
        $row = $mysql->fetchRow();
        $this->text = utf8_encode($row['text']);
        $this->valid_from = $row['valid_from'];
        $this->valid_to = $row['valid_to'];
        $mysql->query("select stop_id from ver_notification_gtfs_stops where notification_id =".$this->id);
        while($row = $mysql->fetchRow())
            $this->stops[] = new GTFSStop($row['stop_id']);

    }

    public function set_text($text){
        $this->text = $text;
    }
    public function set_valid_from($valid_from){
        $this->valid_from = $valid_from;
    }
    public function set_valid_to($valid_to){
        $this->valid_to = $valid_to;
    }

    public function save(){
        $mysql = new MySQL();
        $mysql->query("update notification set text = '".$this->text."',valid_from = '".$this->valid_from."', valid_to = '".$this->valid_to."' where notification_id =".$this->id);
    }

    public function uebersicht(){
        ?>
        <a href="?seite=notificationneu">[ Neue Notification hinzuf&uuml;gen ]</a>
        <h2>Aktuelle</h2>
        <table class="notification-overview-table">
            <tr>
                <th class='notification-overview-table-collum-von'>Von</th>
                <th class='notification-overview-table-collum-bis'>Bis</th>
                <th width="95%">Text</th>
            </tr>
            <?php
            $mysql = new MySQL();
            $query = "select notification_id from notification where valid_from < NOW() and valid_to > NOW()";
            $mysql->query($query);
            while($row = $mysql->fetchRow()){
                $notification = new Notification($row['notification_id']);
                echo "<tr>";
                echo "<td>";
                echo str_replace(" ","&nbsp;",$notification->valid_from);
                echo "</td>";
                echo "<td>";
                echo str_replace(" ","&nbsp;",$notification->valid_to);
                echo "</td>";
                echo "<td>";
                echo $notification->text;
                echo "</td>";
                echo "<td>";
                echo "<a href='?seite=notificationdetails&id=".$row['notification_id']."'>[&nbsp;details&nbsp;]</a>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
        <h2>Zuk&uuml;nftige</h2>
        <table class="notification-overview-table">
            <tr>
                <th class='notification-overview-table-collum-von'>Von</th>
                <th class='notification-overview-table-collum-bis'>Bis</th>
                <th width="95%">Text</th>
            </tr>
            <?php
            $mysql = new MySQL();
            $query = "select notification_id from notification where valid_from > NOW()";
            $mysql->query($query);
            while($row = $mysql->fetchRow()){
                $notification = new Notification($row['notification_id']);
                echo "<tr>";
                echo "<td>";
                echo str_replace(" ","&nbsp;",$notification->valid_from);
                echo "</td>";
                echo "<td>";
                echo str_replace(" ","&nbsp;",$notification->valid_to);
                echo "</td>";
                echo "<td>";
                echo $notification->text;
                echo "</td>";
                echo "<td>";
                echo "<a href='?seite=notificationdetails&id=".$row['notification_id']."'>[&nbsp;details&nbsp;]</a>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
        <h2>Alte</h2>
        <table class="notification-overview-table">
            <tr>
                <th class='notification-overview-table-collum-von'>Von</th>
                <th class='notification-overview-table-collum-bis'>Bis</th>
                <th width="95%">Text</th>
            </tr>
            <?php
            $mysql = new MySQL();
            $query = "select notification_id from notification where valid_to < NOW()";
            $mysql->query($query);
            while($row = $mysql->fetchRow()){
                $notification = new Notification($row['notification_id']);
                echo "<tr>";
                echo "<td>";
                echo str_replace(" ","&nbsp;",$notification->valid_from);
                echo "</td>";
                echo "<td>";
                echo str_replace(" ","&nbsp;",$notification->valid_to);
                echo "</td>";
                echo "<td>";
                echo $notification->text;
                echo "</td>";
                echo "<td>";
                echo "<a href='?seite=notificationdetails&id=".$row['notification_id']."'>[&nbsp;details&nbsp;]</a>";
                echo "</td>";
                echo "</tr>";
            }
            ?>
        </table>
    <?php
    }

    public function details($action = null){
        ?>
        <h3>Notification Details</h3>
        <input type="hidden" id="id" value="<?php echo $this->id;?>"/>
        <table>
            <tr>
                <th>Gültig von</th>
                <td>
                    <?php
                        if($action == "edit")
                            echo '<input id="von" size="15" value="'.$this->valid_from.'">';
                        else
                            echo $this->valid_from;
                    ?>
                </td>
                <td rowspan="4" valign="top">
                    <?php
                    if($action == "edit"){
                        ?>
                        <a onclick="speichern()">[ speichern ]</a>
                        <br>
                        <a href="?seite=notificationdetails&id=<?php echo $this->id;?>">[ zur&uuml;ck ]</a>

                    <?php
                    }
                    else{
                        ?>
                        <a href="?seite=notificationdetails&id=<?php echo $this->id;?>&action=edit">[ bearbeiten ]</a>
                        <br>
                        <a href="?seite=notificationdetails&id=<?php echo $this->id;?>&action=edit">[ l&ouml;schen ]</a>
                        <br>
                        <a href="?seite=notifications">[ zur&uuml;ck ]</a>
                    <?php
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Gültig bis</th>
                <td>
                    <?php
                    if($action == "edit")
                        echo '<input id="bis" size="15" value="'.$this->valid_to.'">';
                    else
                        echo $this->valid_to;
                    ?>
                </td>
            </tr>
            <tr>
                <th>Text</th>
                <td>
                    <?php
                    if($action == "edit")
                        echo '<input id="text" size="50" value="'.$this->text.'">';
                    else
                        echo $this->text;
                    ?>
                </td>
            </tr>
            <tr valign="top">
                <th>Haltestellen</th>
                <td>
                    <?php
                    foreach($this->stops as $stop)
                        echo $stop->get_name()."<br>";
                    ?>
                </td>
            </tr>
        </table>
        <br>


        <?php
            if($action == "edit"){
                ?>
                    <script type="text/javascript">
                        $(function() {
                            $( "#von" ).datetimepicker();
                            $( "#bis" ).datetimepicker();
                        });

                        function speichern(){
                            var id = $("#id").val()
                            var von = $("#von").val()
                            var bis = $("#bis").val()
                            var text  = encodeURIComponent($("#text").val());
                            $.post('ajax.php?func=save_notification_edit',{id:id,von: von,bis: bis,text: text}, function(data) {
                                location.href = "?seite=notifications";
                            });
                        }
                    </script>
                <?php
            }
    }

    public function neu(){
        ?>
            <h2>Neue Notification</h2>
            <table>
                <tr valign="top">
                    <td valign="top">
                        <table>
                            <tr>
                                <th valign="top">Von</th>
                                <td valign="top"><input type="text" id="von" value="<?php echo date("d.m.Y H:i");?>"/></td>
                            </tr>
                            <tr>
                                <th valign="top">Bis</th>
                                <td valign="top"><input type="text" id="bis"></td>
                            </tr>
                            <tr>
                                <th valign="top">Text</th>
                                <td valign="top"><input type="text" id="text" size="90"></td>
                            </tr>
                            <tr height="95%">
                                <th valign="top">Haltestellen</th>
                                <td valign="top">
                                    <select id="haltestellenoptionen" onchange="select_haltestellen_optionen()">
                                        <option value="null"></option>
                                        <option value="all">Alle Haltestellen anzeigen</option>
                                        <option value="linie">Alle Haltestellen einer Linie anzeigen</option>
                                        <option value="stadt">Alle Haltestellen einer Stadt anzeigen</option>
                                    </select><img style="margin-left: 10px; visibility: hidden" src="../ajax-loader(4).gif" id="loader" />
                                    <div id="linien"></div>
                                    <div id="stadt"></div>
                                    <div id="haltestellen"></div>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td valign="top" style="min-width: 400px;" ">
                    <a href="?seite=notifications">[ zur&uuml;ck ]</a>
                    <br>
                    <a onclick="speichern()">[ speichern ]</a>
                    <br><br><br>
                    <div id="haltestellenselect"></div>
                    </td>
                </tr>
            </table>
            <script type="text/javascript">
                $(function() {
                    $( "#von" ).datetimepicker();
                    $( "#bis" ).datetimepicker();
                });

                var stops;
                var save_stops = new Array();

                function select_haltestellen_optionen(){
                    var option = $("#haltestellenoptionen").val()
                    $('#linien').html("");
                    $('#haltestellen').html("");
                    $('#stadt').html("");
                    $('#loader').css("visibility","visible");
                    if(option == "null"){
                        $('#linien').html("");
                        $('#haltestellen').html("");

                    }
                    if(option == "all"){
                        $('#linien').html("");
                        $('#haltestellen').html("");
                        $.getJSON('ajax.php?func=get_all_stops', function(data) {
                            $('#loader').css("visibility","hidden");
                            stops = data;
                            print_stops();
                        });
                    }
                    if(option == "linie"){
                        $.getJSON('ajax.php?func=get_all_routes', function(data) {
                            $('#loader').css("visibility","hidden");
                            var html = '<select id="selectlinien" onchange="print_linien_stops()">';
                            html += '<option value="null" ></option>';
                            for(var i = 0; i< data.length;i++){
                                html += '<option value="'+ data[i].id +'">'+ data[i].agency +' - '+ data[i].linie +'</option>'
                            }
                            html += '</select>';
                            $('#linien').html(html);
                        });
                    }
                    if(option == "stadt"){
                        $.getJSON('ajax.php?func=get_all_stadt', function(data) {
                            $('#loader').css("visibility","hidden");
                            var html = '<select id="selectstadt" onchange="print_stadt_stops()">';
                            html += '<option value="null" ></option>';
                            for(var i = 0; i< data.length;i++){
                                html += '<option value="'+ data[i] +'">'+ data[i] +'</option>'
                            }
                            html += '</select>';
                            $('#stadt').html(html);
                        });
                    }
                }

                function print_stadt_stops(){
                    $('#loader').css("visibility","visible");
                    var stadt = $("#selectstadt").val();
                    $.getJSON('ajax.php?func=get_stadt_stops&stadt='+stadt, function(data) {
                        $('#loader').css("visibility","hidden");
                        stops = data;
                        print_stops();
                    });
                }

                function print_linien_stops(){
                    $('#loader').css("visibility","visible");
                    var linien = $("#selectlinien").val();
                    $.getJSON('ajax.php?func=get_linien_stops&linie='+linien, function(data) {
                        $('#loader').css("visibility","hidden");
                        stops = data;
                        print_stops();
                    });
                }

                function print_stops(){
                    var html = "<br/><a onclick='add_all_station_to_queue()'>-->Alle ausw&auml;len --></a><br/><br/>";
                    for(var i in stops){
                        html += "<li id='"+stops[i].id+"'" ;
                        if(stops[i].location_type == 0)
                            html +="style='text-indent:0px;'";
                        html += ">" +
                            "<span class='dropt'>" +
                            "<a onclick='add_station_to_queue("+stops[i].id+")'>"+stops[i].name;
                        if(stops[i].location_type == 1)
                            html += " (G)";
                        html +=        "</a>" +
                            "<span style='width:500px;text-indent:0px;'>"+stops[i].ziele.join("<br>")+"</span>" +
                            "</span>" +
                            "</li>";
                    }
                    //html += "</ul>";
                    $('#haltestellen').html(html);
                }

                function add_all_station_to_queue(){
                    for(var i in stops){
                        add_station_to_queue(i);
                    }
                }

                function add_station_to_queue(id){
                    if(jQuery.inArray(id,save_stops) == -1){
                        save_stops.push(id);
                        $("#"+id).appendTo("#haltestellenselect");
                    }
                    else{
                        save_stops.splice(save_stops.indexOf(id),1);
                        $("#"+id).appendTo("#haltestellen");
                    }
                }

                function speichern(){
                    var von = $("#von").val()
                    var bis = $("#bis").val()
                    var text  = encodeURIComponent($("#text").val());
                    $.post('ajax.php?func=save_notification',{von: von,bis: bis,text: text, stops: save_stops}, function(data) {
                        //location.href = "?seite=notifications";
                        alert(data);
                    });
                }
            </script>
        <?php
    }
}