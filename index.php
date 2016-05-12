<?php

//for debugging
$debug = 0;

    function DBCON($query) {

        global $debug;
        // Connect and execute query to DB
        ## enter db info here or create vars.php
        $database_hostname = "hostname";
        $database_username = "username";
        $database_password = "password";
        $database_default = "database";

        if(is_file(dirname(__FILE__) . "/db_info.php"))
            include dirname(__FILE__) . "/db_info.php";
        // Connect and execute query to DB
        $connection = new mysqli($database_hostname, $database_username, $database_password, $database_default);
        if (!$debug == 1) {
            $result = $connection->query($query);
            if (!$result) {
                echo "Error executing query: (".$mysqli->errno.") ".$mysqli->error."\n";
            } else {
                return $result;
            }
        } else {
            return $connection->query($query);
        }
    }

    function GETGRAPH($username) {

        $graphid = mysqli_fetch_assoc(DBCON("SELECT DISTINCT(GTG.local_graph_id) AS Gid, GTG.title, GTG.title_cache, DTD.local_data_id AS Did, DTD.name, DTD.name_cache, DTD.data_source_path, DID.value AS username
                                FROM (data_template_data DTD, data_template_rrd DTR, graph_templates_item GTI, graph_templates_graph GTG, data_input_data DID)
                                WHERE GTI.task_item_id=DTR.id
                                AND DTR.local_data_id=DTD.local_data_id
                                AND GTG.local_graph_id=GTI.local_graph_id
                                AND DTD.id=DID.data_template_data_id
                                AND DID.data_input_field_id=(SELECT DISTINCT(id) FROM cacti.data_input_fields WHERE data_name = 'PPPoE_UserName' order by id desc limit 1)
                                AND DTD.data_template_id=(SELECT DISTINCT(id) FROM cacti.data_template WHERE name = 'PPPoE Interface - Traffic')
                                AND GTI.local_graph_id>0
                                AND DID.value = '$username'"));

        if ($debug == 1) {
            echo "Graph ID: ".$graphid['Gid']."\n";
            echo "<br />";
        }

        $file = "../export/graphs/graph_".$graphid['Gid']."_5.png";
        echo "<div align=center><img src=\"".$file."\" alt=\"".$username."\" ></div>";
        $file = "../export/graphs/graph_".$graphid['Gid']."_1.png";
        echo "<div align=center><img src=\"".$file."\" alt=\"".$username."\" ></div>";
        $file = "../export/graphs/graph_".$graphid['Gid']."_2.png";
        echo "<div align=center><img src=\"".$file."\" alt=\"".$username."\" ></div>";
        $file = "../export/graphs/graph_".$graphid['Gid']."_3.png";
        echo "<div align=center><img src=\"".$file."\" alt=\"".$username."\" ></div>";
        $file = "../export/graphs/graph_".$graphid['Gid']."_4.png";
        echo "<div align=center><img src=\"".$file."\" alt=\"".$username."\" ></div>";

    }

    //echo "<body background=\"http://kurumsal.turk.net/images/logo@2x.png\">";

    if ($debug == 1) {
        var_dump($_REQUEST);
        echo "<br />";
        echo "Commands: ".$_REQUEST['cmd']." ".$_REQUEST['username']."\n";
        echo "<br />";
    }

    if(isset($_REQUEST['cmd'])) {
        if ( $_REQUEST['cmd'] = 'getgraph' ) {
            if(strlen($_REQUEST['username']) > '0' ) {
                GETGRAPH($_REQUEST['username']);
            } else {
                echo "enter username";
            }
        }
    }

?>
