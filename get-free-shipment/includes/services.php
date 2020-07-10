<?php 

function saveData(){
    global $wpdb;
    if(isset($_POST['b1'])){
        $tableName= $wpdb->prefix . 'gfs_data';
        $num1=$_POST['texto'];
        $update = "UPDATE " . $tableName . " SET texto = '" . $num1 . "' WHERE id = 1";
        $wpdb->query($update);
    }
}

function gfs_Get_Text(){
    global $wpdb;
        $tableName= $wpdb->prefix . 'gfs_data';
        $getText = "SELECT * from $tableName WHERE id=1";
        $text = $wpdb->get_results($getText);
        return $text[0]->texto;
    }


