<?php
/* Wichtige benötigte Funktionen */
function gtfsdate_2_mysqldate($date){
    return substr($date,0,4)."-".substr($date,4,2)."-".substr($date,6,2);
}
?>