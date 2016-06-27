<?php

//for debugging
$debug = 0;

    function DBCON($query) {

        global $debug;
        // Connect and execute query to DB
        ## enter db info here or create db_info.php
        $database_hostname = "hostname";
        $database_username = "username";
        $database_password = "password";
        $database_default = "database";
        // include db information
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

        global $debug;

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

        if (sizeof($graphid) > 0) {
            echo "<h3><center>$username</center></3>";
            $file = "../export/graphs/graph_".$graphid['Gid']."_5.png";
            echo "<div align=center><img src=\"".$file."\" alt=\"".$username."\" ></div>";
            echo "<center>Hourly</center>";
            $file = "../export/graphs/graph_".$graphid['Gid']."_1.png";
            echo "<div align=center><img src=\"".$file."\" alt=\"".$username."\" ></div>";
            echo "<center>Daily</center>";
            $file = "../export/graphs/graph_".$graphid['Gid']."_2.png";
            echo "<div align=center><img src=\"".$file."\" alt=\"".$username."\" ></div>";
            echo "<center>Weekly</center>";
            $file = "../export/graphs/graph_".$graphid['Gid']."_3.png";
            echo "<div align=center><img src=\"".$file."\" alt=\"".$username."\" ></div>";
            echo "<center>Monthly</center>";
            $file = "../export/graphs/graph_".$graphid['Gid']."_4.png";
            echo "<div align=center><img src=\"".$file."\" alt=\"".$username."\" ></div>";
            echo "<center>Yearly</center>";
        } else {
            if ($debug == 1) {
                echo "<h3><center>$username hizmeti için Cacti'de tanımlı bir grafik yok.</center></3>";
            }
            TOOLDGRAPH($username);
        }
    }

    function TOOLDGRAPH($username) {

        global $oldgraphid;

        if(strlen($oldgraphid) > '0' ) {
            header("Location: http://graph/trafikV2.asp?GRAPHID=$oldgraphid");
        } else {
            echo "<h3><center>$username hizmeti için graph'ta tanımlı bir grafik yok.</center></3>";
        }

    }

    //echo "<body background=\"http://kurumsal.turk.net/images/logo@2x.png\">";

    if ($debug == 1) {
        var_dump($_REQUEST);
        echo "<br />";
        echo "Commands: ";
        foreach($_REQUEST as $cmd) {
            echo "$cmd ";
        }
        echo "<br />";
    }

    if(count($_REQUEST) < 1) {
        echo "<h3><center>No direct access!</center></3>";
    } else {
        if(isset($_REQUEST['cmd']))
            $command = $_REQUEST['cmd'];
        if(isset($_REQUEST['username']))
            $username = $_REQUEST['username'];
        if(isset($_REQUEST['oldgraphid']))
            $oldgraphid = $_REQUEST['oldgraphid'];

        if ($command = 'getgraph') {
            if(strlen($username) > '0' ) {
                GETGRAPH($username);
            } else {
                echo "Please specify username";
            }
        }
    }

?>
