<?php
/*
  Plugin Name: Student Attendance Plugin
  Plugin URI: http://www.evoxyz.com
  Description: short code  for student attendance in csv [student_attendance]
  Version: 1.0
  Author: Raman Kant Kamboj
  Author URI: http://evoxyz.com
 */
ob_start();
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
if (isset($_POST['refresh'])) {
    $get_site_name = $_POST['get_title_name'];
    ?>
    <script>
        window.location = "<?php echo site_url(); ?>/<?php echo $get_site_name; ?>";
    </script>	
    <?php
}

if (isset($_POST['go']) || isset($_GET['id'])) {
    global $post_at;
    global $post_at_to_date;
    $post_at = "";
    $post_at_to_date = "";

    $queryCondition = "";
    if (!empty($_POST["search"]["post_at"])) {
        $post_at = $_POST["search"]["post_at"];
        list($fid, $fim, $fiy) = explode("-", $post_at);

        $post_at_todate = date('Y-m-d');
        if (!empty($_POST["search"]["post_at_to_date"])) {
            $post_at_to_date = $_POST["search"]["post_at_to_date"];
            list($tid, $tim, $tiy) = explode("-", $_POST["search"]["post_at_to_date"]);
            $post_at_todate = "$tiy-$tim-$tid";
        }
        if (!empty($_POST['stu_name'])) {
            $search_name = $_POST['stu_name'];
        }
    }
    $post_at_fromdate = "$fiy-$fim-$fid";
    global $wpdb;
    global $fetchdata;
    global $post_at_fromdate;
    global $post_at_todate;
    global $search_name;
    $table_users = $wpdb->prefix . "users";
    $table_usermeta = $wpdb->prefix . "usermeta";
    //$table_be_loc = $wpdb->prefix . "41_beacon_location";
    //$table_bus_details = $wpdb->prefix . "41_evo_bus_details";
    $fetchdata = $wpdb->get_results("SELECT $table_users.display_name,$table_users.ID,$table_usermeta.* from $table_users join $table_usermeta on  $table_users.ID =$table_usermeta.user_id WHERE $table_users.ID ='$search_name' AND $table_usermeta.meta_key='bt_deviceid'");
//echo $wpdb->last_query;
}

// allbus show form
function studentattendance_form() {
    global $post_at;
    global $post_at_to_date;
    ?>
    <link rel="stylesheet" href="<?php echo plugins_url('css/jquery-ui.css', __FILE__); ?>">
    <script src="<?php echo plugins_url('js/jquery.min.js', __FILE__); ?>"></script>
    <style>


        .table-content th {background: #F0F0F0;vertical-align:top;} 
        .table-content td { border-bottom: #F0F0F0 1px solid;vertical-align:top;} 
    </style>
    <script>
            $(document).ready(function () {
                $("#datepicker").datepicker();
                $("#datepicker1").datepicker();
            });
    </script>
    <div class="demo-content">


        <form name="frmSearch" method="post" action="">
            <p class="search_input">
                <select name="stu_name" required >
                    <option value=""> select name</option>
                    <?php
                    $args = array(
                        'blog_id' => $GLOBALS['blog_id'],
                        'role' => 'subscriber',
                        'order' => 'ASC',
                        'orderby' => 'login'
                    );
                    $blogusers = get_users('blog_id=$args[blog_id]&order=ASC&orderby=login&role=$args[role]');
// Array of WP_User objects.
                    foreach ($blogusers as $user) {
                        ?>

                        <option value="<?php echo $user->ID; ?>"><?php echo $user->display_name; ?></option>
                    <?php } ?>

                </select>

                <input type="text"  required placeholder="From Date" id="datepicker" name="search[post_at]"  value="<?php echo $post_at; ?>" />
                <input type="text"  required placeholder="To Date" id="datepicker1"  name="search[post_at_to_date]" style="margin-left:10px"  value="<?php echo $post_at_to_date; ?>" />			 
                <input type="hidden" name="get_title_name" value="<?php echo get_the_title(); ?>">
                <input type="submit" name="go" value="Search" >
                <input type="submit" name="refresh" value="refresh" >
            </p>
        </form>
    </div>
    <?php
    global $fetchdata;
    global $post_at_fromdate;
    global $post_at_todate;
    global $search_name;
    if (!empty($fetchdata)) {
        ?>

        <p style="margin-left: 150px;font-weight: bold;">SUNCITY  SCHOOL - Student's Bus ATTENDANCE REPORT<p>

            <link rel="stylesheet" href="<?php echo plugins_url('css/jquery.dataTables.min.css', __FILE__); ?>">
            <link rel="stylesheet" href="<?php echo plugins_url('css/buttons.dataTables.min.css', __FILE__); ?>">

            <script src="//code.jquery.com/jquery-1.12.3.js"></script>
            <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
            <script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
            <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
            <script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/pdfmake.min.js"></script>
            <script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.18/build/vfs_fonts.js"></script>
            <script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
            <script src="//cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>

            <script>
            $(document).ready(function () {
                $('#example').DataTable({
                    dom: 'Bfrtip',
                    buttons: [
                        //'csv', 'excel', 'pdf'
                    ]
                });
            });
            </script>

        <table  id="testTable" class="table1" style="width: 87%;margin-left: 96px;">
            <tr>
            <p style="margin-left: 273px;">Student Profile</p>

        </tr>
        <?php
        global $wpdb;
        $table_users = $wpdb->prefix . "users";
        $table_usermeta = $wpdb->prefix . "usermeta";
        $resultmeta = $wpdb->get_row("SELECT $table_users.display_name,$table_users.ID,$table_usermeta.* from $table_users join $table_usermeta on  $table_users.ID =$table_usermeta.user_id WHERE $table_users.ID ='$search_name' AND $table_usermeta.meta_key='bt_deviceid'");
        $metavalue = $resultmeta->meta_value;
        $result_nicname = $wpdb->get_row("SELECT $table_users.display_name,$table_users.ID,$table_usermeta.* from $table_users join $table_usermeta on  $table_users.ID =$table_usermeta.user_id WHERE $table_users.ID ='$search_name' AND $table_usermeta.meta_key='nickname'");
        $result_routeno = $wpdb->get_row("SELECT $table_users.display_name,$table_users.ID,$table_usermeta.* from $table_users join $table_usermeta on  $table_users.ID =$table_usermeta.user_id WHERE $table_users.ID ='$search_name' AND $table_usermeta.meta_key='bus_route'");
        $result_stops = $wpdb->get_row("SELECT $table_users.display_name,$table_users.ID,$table_usermeta.* from $table_users join $table_usermeta on  $table_users.ID =$table_usermeta.user_id WHERE $table_users.ID ='$search_name' AND $table_usermeta.meta_key='bus_stop_1'");
        $result_father_no = $wpdb->get_row("SELECT $table_users.display_name,$table_users.ID,$table_usermeta.* from $table_users join $table_usermeta on  $table_users.ID =$table_usermeta.user_id WHERE $table_users.ID ='$search_name' AND $table_usermeta.meta_key='father_telephone'");

        $table_be_loc = $wpdb->prefix . "beacon_location";
        $table_bus_details = $wpdb->prefix . "evo_bus_details";
        $fetchalldata = $wpdb->get_row("SELECT $table_be_loc.*,$table_bus_details.* from $table_be_loc join $table_bus_details on  $table_be_loc.reader_id =$table_bus_details.evoReaderId WHERE $table_be_loc.minor ='$metavalue' AND date($table_be_loc.timestamp) BETWEEN '$post_at_fromdate' AND '$post_at_todate'");
//echo $wpdb->last_query;
        ?>
        <tr>
            <th scope="row">Name</th>
            <td><?php echo $resultmeta->display_name; ?></td>


        </tr>
        <tr>
            <th scope="row">Reg ID</th>
            <td><?php echo $result_nicname->meta_value; ?></td>

        </tr>
        <tr>
            <th scope="row">Assigned Route & Bus  No.</th>
            <td><?php echo $result_routeno->meta_value; ?> (<?php echo $fetchalldata->vehicleNo; ?>)</td>

        </tr>
        <tr>
            <th scope="row">Assigned Stop</th>
            <td><?php echo $result_stops->meta_value; ?> </td>

        </tr>
        <tr>
            <th scope="row">Father's Contact  Number</th>
            <td><?php echo $result_father_no->meta_value; ?></td>

        </tr>
        <tr>
            <th scope="row">Evotag ID</th>
            <td><?php echo $fetchalldata->evoReaderId; ?></td>

        </tr>
        <tr>
            <th scope="row">Bus Teacher Incharge</th>
            <td>Ms. Seema Chaudhary</td>

        </tr>



        </table>
        <script src="<?php echo plugins_url('js/tableTOExcel.js', __FILE__); ?>"></script>
        <input type="button" onclick="tableToExcel('example')" value="Export to Excel">		
        <table id="example" class="" cellspacing="0" >
            <thead>
                <tr>

                    <th style="width: 83px;background-color: #8fbee0;">Trip Date</th>
                    <th style="background-color: #8fbee0;">Boarded In Route</th>          
                    <th style="background-color: #8fbee0;">Vehicle Registration No.</th>
                    <th style="background-color: #8fbee0;">Board the bus for School</th>
                    <th style="background-color: #8fbee0;">De-Board the bus in school</th>
                    <th style="background-color: #8fbee0;">Board the bus for Home</th> 
                    <th style="background-color: #8fbee0;">De-Board the bus at Stop</th> 
                </tr>
            </thead>


            <?php
            foreach ($fetchdata as $result) {
                $metavalue = $result->meta_value;
                global $wpdb;
                $table_be_loc = $wpdb->prefix . "beacon_location";
                $table_bus_details = $wpdb->prefix . "evo_bus_details";
                $allstudentlist = $wpdb->get_results("SELECT date($table_be_loc.timestamp) as timestamp,$table_bus_details.* from $table_be_loc join $table_bus_details on  $table_be_loc.reader_id =$table_bus_details.evoReaderId WHERE $table_be_loc.minor ='$metavalue' AND date($table_be_loc.timestamp) BETWEEN '$post_at_fromdate' AND '$post_at_todate'");
//echo $wpdb->last_query;

                foreach ($allstudentlist as $studentlist) {
                    ?>
                    <tr>
                        <td><?php echo date($studentlist->timestamp); ?></td>
                        <td><?php echo 'RT-' . $studentlist->routeId; ?></td>
                        <td><?php echo $studentlist->vehicleNo; ?></td>
                        <td><?php echo '-'; ?></td>
                        <td><?php echo '-'; ?></td>
                        <td><?php echo '-'; ?></td>
                        <td><?php echo '-'; ?></td>

                    </tr>
                    <?php
                }
            }
            ?>
        </table>
        <?php
    }
    ?>
    <script src="<?php echo plugins_url('js/jquery-ui.js', __FILE__); ?>"></script>
    <script>
            $.datepicker.setDefaults({
                showOn: "button",
                buttonImage: "<?php echo plugins_url('image/datepicker.png', __FILE__); ?>",
                buttonText: "Date Picker",
                buttonImageOnly: true,
                dateFormat: 'dd-mm-yy'
            });
            $(function () {
                $("#post_at").datepicker();
                $("#post_at_to_date").datepicker();
            });
    </script>
    <?php
}

// student attendance show for each date a new shortcode: [student_attendance]
add_shortcode('student_attendance', 'studentattendance_form');
?>