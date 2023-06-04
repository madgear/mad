<?php
include "db.php";

$requestType = $_SERVER['REQUEST_METHOD'];

switch ($requestType) {

    case 'GET':

        $sql = "SELECT * FROM tbl_schedule;";

        $rows = ExecuteRows($sql);

        if (is_array($rows)) {

            $events = array();
            foreach ($rows as $row) {
                $event = array();
                $event['id'] = $row['id'];
                $event['title'] = $row['sched_title'];
                $event['desc'] = $row['sched_desc'];
                $event['start'] = $row['start_time'];
                $event['end'] = $row['end_time'];
                $event['room_id'] = $row['room_id'];
                $event['intpr_id'] = $row['interpreter_id'];
                $event['status'] = $row['sched_status'];
                if ($row["all_day"] == 0) {
                    $event['allDay'] = false;
                } else {
                    $event['allDay'] = true;
                }

                $event['borderColor'] = "yellow";
                $event['classNames'] = ['bg-success', 'text-white', 'border', 'border-dark'];

                $events[] = $event;
            }

            echo json_encode($events);
        }

        break;

    case 'POST':

        $option = $_POST['option'];

        if ($option == "new_schedule_dd") {

            $title = $_POST['title'];
            $dstr = $_POST['cdate'];

            //Fri Jun 09 2023 00:00:00 GMT+0800 (Taipei Standard Time)

            $date = date_create(substr($dstr, 4, 11));
            $date_start =  date_format($date, "Y-m-d");

            $d = [
                'title' => $title,
                'datestart' => $date_start
            ];

            $insert = "INSERT INTO `sie_schedule_project`.`tbl_schedule` (`sched_title`, `sched_date`, `start_time`, `end_time`, `all_day`) VALUES 
            (:title, :datestart, :datestart, :datestart, '1');";

            $last_id = Execute($insert, $d);
            _msg(200, $last_id);

            //SELECT LAST_INSERT_ID();"

        } else if ($option == "new_schedule") {
        } else if ($option == "get_rooms") {

            $query = "select * from tbl_rooms;";
            ExecuteJSON($query);
        } else if ($option == "get_interpreters") {

            $query = "select * from tbl_users where user_type=2;";
            ExecuteJSON($query);
        } else if ($option == "get_schedule") {

            $s_id = $_POST['sid'];
            $query = "SELECT * from tbl_schedule where id=" . $s_id;
            $row = ExecuteRow($query);
            if ($row) {
                echo json_encode($row);
            } else {
                _msg(500, "RECORD NOT FOUND!");
            }
        } else if ($option == "update_schedule_dd") {

            $sid = $_POST['sid'];
            $start = $_POST['start'];
            $end = $_POST['end'];
            $allday = $_POST['allday'];

            $date1 = date_create(substr($start, 4, 20));
            $date_start =  date_format($date1, "Y-m-d H:i:s");

            $date2 = date_create(substr($end, 4, 20));
            $date_end =  date_format($date2, "Y-m-d H:i:s");           

            if($allday == "true"){
                $a_day = 1;
            }else{
                $a_day = 0;
            }

            $d = [
                'starttime'=> $date_start,
                'endtime'=> $date_end,
                'allday'=> $a_day,
                'sid'=> $sid
            ];

            $update = "UPDATE `sie_schedule_project`.`tbl_schedule` SET 
            `start_time`= :starttime, 
            `end_time`= :endtime, 
            `all_day`= :allday
             WHERE `id`=:sid";            

            Execute($update, $d);

        } else {
            _msg(500, "INVALID OPTION!");
        }

        break;
}





?>