<?php
/**
 * Created by JetBrains PhpStorm.
 * User: student
 * Date: 14.10.13
 * Time: 09:51
 * To change this template use File | Settings | File Templates.
 */

if($_GET['action'] == "writerows"){
    echo shell_exec('/home/pi/display.py -r '.$_GET['rows']);
}