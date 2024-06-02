<?php

namespace php_sys\system;

header('Access-Control-Allow-Origin: *');
header('Content-Type: text/html;charset=utf-8');

// define('EXT', '.' . pathinfo(__FILE__, PATHINFO_EXTENSION));
// define('FCPATH', __FILE__);
// define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
// define('PUBPATH', str_replace(SELF, '', FCPATH));

if (!isset($_POST['data'])) {
    echo _msg(500, "Server Error 500!");
    exit;
}


date_default_timezone_set('Asia/Manila');
$date_time = date("Y-m-d H:i:s");
$data = $_POST['data'];

if ($data == "update_profile_picture") {
    update_user_avatar();
    exit;
} else {
    $option = $data[0];
}

$s_data = $data[1];
$csrf = $s_data[0];

if ($option == "login") {
    user_login($s_data[1], $s_data[2]);
} elseif ($option == "get-todays-overview") {
    get_todays_overview($s_data[1]);
} elseif ($option == "add-new-task") {
    add_new_task($data[1]);
} elseif ($option == "get-task-type") {
    echo _msg(200, load_tasktype(0));
} elseif ($option == "get-work-type") {
    echo _msg(200, load_worktype(0));
} elseif ($option == "logout") {
    $get_id = get_userid($s_data[1]);;
    $insert_logs = "INSERT INTO `sie_management`.`tbl_logs` (`user_id`, `process` ,`notes`) VALUES ($get_id, 'LOGOUT', 'LOGGED OUT @" . get_client_ip() . "');";
    Execute($insert_logs);
} elseif ($option == "verify_user") {
    verify_logged_user($s_data[1], $s_data[2]);
} elseif ($option == "add_card") {
    add_card($s_data[1], $s_data[2]);
} elseif ($option == "get_card_type") {
    $query = "SELECT * FROM card_type order by sort_num asc";
    echo ExecuteJSON($query);
} elseif ($option == "delete_card") {
    delete_card($s_data[1], $s_data[2]);
} elseif ($option == "get_image") {
    $query = "SELECT img FROM tbl_users WHERE MD5(id) = '" . $s_data[1] . "';";
    echo ExecuteJSON($query);
} elseif ($option == "get_all_cards") {

    //     $query = "
    // SELECT task_timer.*, 
    // tbl_users.img AS avatar,
    // ((UNIX_TIMESTAMP(NOW())) - (UNIX_TIMESTAMP(`pause_time`))) + initial_time + revision_time AS ttime,
    // IF(task_timer.user_id=0,0,MD5(task_timer.user_id)) AS md_id,
    // MD5(task_timer.created_by) AS cr_id,
    // card_type.card_name AS cname,
    // card_type.bgcolor AS cbgcolor,
    // card_type.allowed_time AS atime,    	     
    // task_type.task_type AS ttype,
    // task_type.abbr AS tabbr,
    // work_type.work_type AS wtype,
    // work_type.bgcolor AS wbgcolor    
    // FROM task_timer 
    // LEFT JOIN tbl_users ON task_timer.user_id = tbl_users.id
    // LEFT JOIN task_type ON task_timer.task_type = task_type.id
    // LEFT JOIN work_type ON task_timer.work_type = work_type.id
    // LEFT JOIN card_type ON task_timer.card_type = card_type.id    
    // WHERE card_status <> 0 ORDER BY date_updated DESC;
    // ";

    $query = "
    SELECT task_timer.*, 
    tbl_users.img AS avatar,
    IFNULL(CONCAT(tbl_users.fld_firstname,' ',tbl_users.fld_lastname),'NEW CARD') AS full_name,
    ((UNIX_TIMESTAMP(NOW())) - (UNIX_TIMESTAMP(`pause_time`))) + initial_time + revision_time AS ttime,
    IF(task_timer.user_id=0,0,MD5(task_timer.user_id)) AS md_id,
    MD5(task_timer.created_by) AS cr_id,
    card_type.card_name AS cname,
    card_type.bgcolor AS cbgcolor,
    card_type.allowed_time AS atime,    	     
    task_type.task_type AS ttype,
    task_type.abbr AS tabbr,
    work_type.work_type AS wtype,
    IFNULL(work_type.bgcolor,'#000000') AS wbgcolor    
    FROM task_timer 
    LEFT JOIN tbl_users ON task_timer.user_id = tbl_users.id
    LEFT JOIN task_type ON task_timer.task_type = task_type.id
    LEFT JOIN work_type ON task_timer.work_type = work_type.id
    LEFT JOIN card_type ON task_timer.card_type = card_type.id    
    WHERE card_status <> 0 ORDER BY date_updated DESC;";
    echo ExecuteJSON($query);
} elseif ($option == "add_working_cards") {
    add_working_cards($s_data[1], $s_data[2]);
} elseif ($option == "working_todo") {
    working_to_pause($s_data[1], $s_data[2]);
} elseif ($option == "working_todone") {
    working_to_pause($s_data[1], $s_data[2], 3);
} elseif ($option == "done_to_working") {
    done_to_working($s_data[1], $s_data[2]);
} elseif ($option == "done_to_todo") {
    done_to_todo($s_data[1], $s_data[2]);
} elseif ($option == "get_card_details") {
    get_card_details($s_data[1], $s_data[2]);
} elseif ($option == "update_card_details") {
    update_card_details($s_data);
} elseif ($option == "load_task_type") {
    $query = "SELECT * from task_type";
    echo ExecuteJSON($query);
} elseif ($option == "load_work_type") {
    $query = "SELECT * from work_type";
    echo ExecuteJSON($query);
} elseif ($option == "get_app_source") {
    $query = "SELECT `app_codes`.* from app_codes WHERE `app_codes`.page = '" . $s_data[1] . "' LIMIT 1;";
    $row = ExecuteRow($query);
    if ($row) {
        echo _msg(200, html_entity_decode($row['code']));
    } else {
        echo _msg(500, "INVALID PAGE OPTION!");
    }
} elseif ($option == "get_boards_last_update") {
    //$query = "SELECT MAX(date_updated) AS lastUpdate, id FROM task_timer limit 10";
    $query = "SELECT * FROM task_timer ORDER BY date_updated DESC limit 1;";
    echo ExecuteJSON($query);
} elseif ($option == "join_multi_card") {
    join_cards_multi($s_data);
} elseif ($option == "get_joiners_img") {

    // $query = "SELECT tbl_users.img FROM group_card
    // LEFT JOIN tbl_users ON group_card.user_id = tbl_users.id
    // WHERE card_id = " . $s_data[1];

    // echo ExecuteJSON($query);
} elseif ($option == "sample_display") {

    $query = "SELECT * FROM task_timer";
    $code = "";

    $rows = ExecuteRows($query);
    if (is_array($rows)) {

        $code = "<table border='1'>";
        foreach ($rows as $row) {

            $code .= "<tr><td>" . $row['id'] . "</td><tr>";
        }

        $code .= "</table>";

        echo _msg(200, $code);
    }
} elseif ($option == "user_profile_update") {
    update_user_profile($s_data);
} elseif ($option == "get_my_cardlist_today") {

    $query = "
    SELECT 
    card_type.card_name AS cname, 
    IFNULL(task_type.task_type,'') AS ttype,
    IFNULL(task_timer.article_number,'') AS article_num,
    DATE_FORMAT(task_timer.start_time, '%H:%i') AS stime,
    task_timer.total_time AS ttime,
    task_timer.card_status AS cstatus
    FROM task_timer 
    LEFT JOIN tbl_users ON task_timer.user_id = tbl_users.id
    LEFT JOIN task_type ON task_timer.task_type = task_type.id
    LEFT JOIN work_type ON task_timer.work_type = work_type.id
    LEFT JOIN card_type ON task_timer.card_type = card_type.id 
    WHERE md5(user_id) = '" . $s_data[1] . "' AND date_created BETWEEN CONCAT(CURDATE(),' 00:00:00') AND NOW()";

    //echo _msg(500, $query);

    echo ExecuteJSON($query);
} elseif ($option == "task_timer_report") {
    report_task_timer($s_data);
} elseif ($option == "individual_report") {
    individual_report($s_data);
} elseif ($option == "get_all_users") {
    echo load_allusers();
} elseif ($option == "load_theme") {

    $query = "SELECT id, theme_name FROM app_theme;";
    $id = $s_data[1];
    $rows = ExecuteRows($query);
    if (is_array($rows)) {

        $code = "";

        foreach ($rows as $row) {
            if ($row['id'] == $id) {
                $code .= "<option value='" . $row['id'] . "' selected>" . $row['theme_name'] . "</option>";
            } else {
                $code .= "<option value='" . $row['id'] . "'>" . $row['theme_name'] . "</option>";
            }
        }
        echo $code;
    }
} elseif ($option == "change_theme") {
    $query = "SELECT * FROM app_theme WHERE id=" . $s_data[1];
    echo ExecuteJSON($query);
} elseif ($option == "get_card_option") {
    //echo $s_data[1];
    load_card_option($s_data[1]);
} elseif ($option == "select-new-task") {
    generate_new_task_details($s_data[1]);
} elseif ($option == "add-new-task-admin") {
    add_task_admin();
} else {
    echo _msg(500, "INVALID ARGUMENT!");
}

//SELECT MAX(date_updated) AS lastUpdate FROM task_timer limit 10

function add_task_admin()
{
    $insert = "INSERT INTO `sie_management`.`task_tracker` (`task_type`) VALUES (1);";
}



function get_task_code_details($tid)
{
    $query = "select code_options from task_type where id = " . $tid;
    $row = ExecuteRow($query);
    if ($row) {
        return $row['code_options'];
    } else {
        return "";
    }
}

function generate_new_task_details($tid)
{
    $code = get_task_code_details($tid);
    echo _msg(200, $code . "<span id='next_day_date' class='d-none'>" . date('Y-m-d', strtotime("+1 days")) . "</span>");
}


function get_member_status()
{
}


function get_todays_overview($mid = 0)
{

    $task_today = "SELECT 
    COUNT(IF(card_status = 1, 1, NULL)) AS task_avail, 
    COUNT(IF(card_status = 2, 1, NULL)) AS task_progress,
    COUNT(IF(card_status = 3, 1, NULL)) AS task_done
    FROM task_timer WHERE card_type = 1 AND DATE_FORMAT(task_timer.date_created,'%Y-%m-%d') = CURDATE() 
    ORDER BY id DESC;";

    $row = ExecuteRow($task_today);

    if ($row) {
        $task_avail = $row['task_avail'];
        $task_progress = $row['task_progress'];
        $task_done = $row['task_done'];
    }

    $member_search = "";
    if ($mid <> 0) {
        $member_search = " user_id = " . $mid . " AND ";
    }

    $check_others = "SELECT 
    SUM(IF(card_type = 1, total_time, 0)) AS total_task,
    SUM(IF(card_type = 0, total_time, 0)) AS total_break,
    SUM(IF(card_type = 2, total_time, 0)) AS total_aux,
    SUM(IF(card_type = 3, total_time, 0)) AS total_meeting,
    SUM(IF(card_type = 4, total_time, 0)) AS total_training,
    SUM(IF(card_type = 9, total_time, 0)) AS total_others,
    SUM(IF(card_type = 7, total_time, 0)) AS total_dev
    FROM task_timer WHERE " . $member_search . " DATE_FORMAT(task_timer.date_created,'%Y-%m-%d') = CURDATE();";
    $row = ExecuteRow($check_others);

    if ($row) {
        $total_task = $row['total_task'];
        $total_break = $row['total_break'];
        $total_aux = $row['total_aux'];
        $total_meeting = $row['total_meeting'];
        $total_dev = $row['total_dev'];
        $total_training = $row['total_training'];
        $total_others = $row['total_others'];
    }

    $data = [
        "avail" => $task_avail,
        "progress" => $task_progress,
        "done" => $task_done,
        "task" => $total_task,
        "break" => $total_break,
        "aux" => $total_aux,
        "dev" => $total_dev,
        "meeting" => $total_meeting,
        "training" => $total_training,
        "others" => $total_others,
    ];

    echo _msg(200, $data);
}


function add_new_task($data)
{

    $sze = count($data);
    $cid = get_userid($data[$sze - 1]);
    $deadline = $data[$sze - 2];
    $request = $data[$sze - 3] . " " . date("h:i:s");
    $task_type = $data[1];

    $check_count = "SELECT COUNT(request_date) as cnt FROM task_tracker WHERE DATE(request_date) = DATE(NOW()) AND task_type = " . $task_type;
    $row = ExecuteRow($check_count);
    if ($row) {
        $task_count = $row["cnt"];
    }

    $get_abbr = "SELECT abbr from task_type where id =" . $task_type;
    $row = ExecuteRow($get_abbr);

    if ($row) {
        $task_abbr = $row["abbr"];
    }

    $task_id = $task_abbr . " - " . date("Ymd") . "-" . ($task_count + 1);
    $db_fields = "";
    $db_values = "";

    if ($task_type == 7) {
        $db_fields = ", `aem_page_type`, `page_title`, `edit_page`";
        $db_values = ", '" . $data[2] . "' , '" . $data[3] . "', '" . $data[4] . "'";
    }

    if ($task_type == 5) {
        $task_id = $task_abbr . "-" . strtoupper($data[2]) .  " - " . date("Ymd") . "-" . ($task_count + 1);
        $db_fields = ", `ref_number`, `page_title`, `calypso_record_type`";
        $db_values = ", '" . $data[4] . "' , '" . $data[5] . "', '" . $data[3] . "'";
    }

    if ($task_type == 1 || $task_type == 2 || $task_type == 6) {
        $db_fields = ", `ref_number`, `page_title`, `calypso_record_type`";
        $db_values = ", '" . $data[2] . "' , '" . $data[3] . "', '" . $data[4] . "'";
    }

    if ($task_type == 10) {
        $db_fields = ", `ref_number`, `page_title`";
        $db_values = ", '" . $data[2] . "' , '" . $data[3] . "'";
    }

    if ($task_type == 3) {
        $db_fields = ", `ref_number`, `page_title`, `calypso_record_type`, `published_status`, `fld_language`";
        $db_values = ", '" . $data[5] . "' , '" . $data[6] . "', '" . $data[2] . "', '" . $data[4] . "', '" . $data[3] . "'";
    }


    $query = "INSERT INTO `sie_management`.`task_tracker` (`task_id`, `task_type`,`task_status`, `request_date`, `deadline`, `created_by`" . $db_fields . ") 
    VALUES ('" . $task_id . "', " . $task_type . ", 1, '" . $request . "', '" . $deadline . "' , " . $cid . $db_values . ");";

    $ret = Execute($query);
    echo $ret;
    //madgear

}

function load_card_option($c)
{

    $get_list = "SELECT card_id FROM card_option WHERE id = " . $c;
    $res = ExecuteRow($get_list);

    if ($res) {
        $query = "SELECT * FROM card_option WHERE card_id = " . $res["card_id"] . " ORDER BY id ASC;";
        $rows = ExecuteRows($query);
        if (is_array($rows)) {
            $opt = "";
            foreach ($rows as $row) {
                if ($row['id'] == $c) {
                    $opt .= "<option selected value='" . $row['id'] . "'>" . $row['c_option'] . "</option>";
                } else {
                    $opt .= "<option value='" . $row['id'] . "'>" . $row['c_option'] . "</option>";
                }
            }
            echo _msg(200, $opt);
        }
    }
}


function update_user_avatar()
{

    $csrf = $_POST['csrf'];
    $id = $_POST['enckey'];

    $query = "SELECT img FROM tbl_users WHERE MD5(id) = '" . $id . "';";
    $row = ExecuteRow($query);
    if ($row) {

        $imgname = $row['img'];
        // $img_path = PUBPATH . "img/" . get_filename($imgname);
        $img_path = "//10.10.90.100/xampp/htdocs/sie-system/assets/systems/saikuru/images/profile/" . get_filename($imgname);
        $real_path = str_replace('\\', '/', $img_path);
        $check_file = file_exists($real_path);
        if ($check_file == 1) {
            unlink($real_path);
        }

        $name = $_FILES['image']['name'];
        $image_ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $fname = random_string(10);
        $savename = $fname . "." . $image_ext;
        $dir_path = "http://10.10.90.100/assets/systems/saikuru/images/profile/";
        $db_name = $dir_path . $savename;
        move_uploaded_file($_FILES['image']['tmp_name'], 'assets/systems/saikuru/images/profile/' . $savename);
        $update = "UPDATE `sie_management`.`tbl_users` SET `img`='" . $db_name . "' WHERE  MD5(`id`)='" . $id . "';";

        $ret = json_decode(Execute($update));

        if ($ret->message == "update") {
            echo _msg(200, $db_name);
        } else {
            echo _msg(500, "Error Occured on updating image!");
        }
        exit;
    } else {
        echo _msg(500, "Invalid Account Credentials, Try to refresh or re-login to the app!");
        exit;
    }
}

function password_validation($pass)
{

    $res = 0;

    if (strlen($pass) < 8) {
        return 1;
        exit;
    }

    if (!preg_match('/[A-Z]/', $pass)) {
        return 2;
        exit;
    }

    if (!preg_match('/[a-z]/', $pass)) {
        return 3;
        exit;
    }

    if (!preg_match('/[0-9]/', $pass)) {
        return 4;
        exit;
    }

    return $res;
}


function update_user_profile($data)
{

    $update_password = "";


    if ($data[7] !== "") {

        $old_pass = md5(decrypt($data[7], $data[1]));

        $tmppass = decrypt($data[8], $data[1]);

        $res = password_validation($tmppass);

        $new_pass = md5(decrypt($data[8], $data[1]));

        $check = "SELECT * FROM tbl_users WHERE MD5(id) = '" . $data[1] . "';";

        $row = ExecuteRow($check);
        if ($row) {

            if ($row['fld_password']  <> $old_pass) {
                echo _msg(500, "OLD Password is incorect!");
                exit;
            }

            if ($res <> 0) {
                echo _msg(500, "New Password should be 8 Characters long and Alpha Numeric!");
                exit;
            }

            $update_password = ", `fld_password`='" . $new_pass . "' ";
        } else {

            echo _msg(500, "Error Occured!, Try to Refresh or Re-Login!");
            exit;
        }
    }


    $update = "UPDATE `sie_management`.`tbl_users` SET 
    `theme`='" . $data[9] . "',
    `fld_firstname`='" . $data[2] . "', 
    `fld_lastname`='" . $data[3] . "', 
    `fld_email`='" . $data[4] . "', 
    `lob`='" . $data[5] . "',
    `position`='" . $data[6] . "'
    " . $update_password . " 
    WHERE  MD5(`id`)='" . $data[1] . "';";

    //$res = Execute($update);

    $ret = json_decode(Execute($update));

    if ($ret->message == "update") {
        echo _msg(200, "Profile Updated!");
    } else {
        echo _msg(500, "Error Occured!");
    }

    //echo _msg(200, $res);
}


function join_cards_multi($data)
{

    $id = $data[2];
    $query = "SELECT id FROM task_timer WHERE MD5(user_id) = '" . $id . "' AND card_status= 2";
    $row = ExecuteRow($query);
    if ($row) {
        echo _msg(500, "Unable to join, card still active!");
        exit;
    }

    $uid = get_userid($id);
    $check = "SELECT id FROM group_card WHERE user_id = " . $uid . " AND card_id = " . $data[1];
    $row = ExecuteRow($check);
    if ($row) {
        echo _msg(500, "Unable to join, you are already a participant!");
        exit;
    }

    $insert = "INSERT INTO `sie_management`.`group_card` (`card_id`, `user_id`) VALUES (" . $data[1] . ", " . $uid . ");";
    $res = Execute($insert);
    echo _msg(200, $res);
}

function update_card_details($data)
{

    // echo _msg(500, "INVALID CARD!");
    // exit;

    $uid = $data[1];
    $cid = $data[2];



    //VALIDATION HERE

    $query = "SELECT * FROM task_timer WHERE MD5(id)='" . $cid . "'";
    $row = ExecuteRow($query);

    if ($row) {

        if ($row['user_id'] <> 0) {
            if ($uid <> MD5($row['user_id'])) {
                echo _msg(500, "You're not allowed to update other users card!");
                exit;
            }
        }


        $card_type = $row['card_type'];
    } else {
        echo _msg(500, "INVALID CARD!");
        exit;
    }


    if ($card_type == 1) {
        $task_type = $data[3];
        $article_number = $data[4];
        $work_type = $data[5];
        $update = "UPDATE `sie_management`.`task_timer` SET 
        `task_type`= " . $task_type . ", 
        `article_number`='" . $article_number . "', 
        `work_type`= " . $work_type . " 
        WHERE
        MD5(`id`)= '" . $cid . "';";
    } else {
        $details = $data[3];
        $update = "UPDATE `sie_management`.`task_timer` SET 
        `details`='" . $details . "' ,
        `card_option`='" . $data[4] . "'
        WHERE
        MD5(`id`)= '" . $cid . "';";
    }

    echo Execute($update);
}


function load_allusers()
{
    $query = "SELECT id, concat(fld_firstname,' ', fld_lastname) as full_name FROM tbl_users";
    $rows = ExecuteRows($query);
    if (is_array($rows)) {
        $code = "<option value='0'>ALL</option>";
        foreach ($rows as $row) {
            $code .= "<option value='" . $row['id'] . "'>" . $row['full_name'] . "</option>";
        }
        return $code;
    }
}


function load_worktype($id)
{
    $query = "SELECT * FROM work_type";
    $rows = ExecuteRows($query);
    if (is_array($rows)) {
        $code = "";
        if ($id == 0) {
            $code .= "<option selected disabled>Select Work Type</option>";
        }
        foreach ($rows as $row) {
            if ($row['id'] == $id) {
                $code .= "<option value='" . $row['id'] . "' selected>" . $row['work_type'] . "</option>";
            } else {
                $code .= "<option value='" . $row['id'] . "'>" . $row['work_type'] . "</option>";
            }
        }
        return $code;
    }
}

function load_tasktype($id)
{

    $gquery = "SELECT task_group FROM task_type group by task_group";

    $rows = ExecuteRows($gquery);

    $code = "";

    if (is_array($rows)) {

        $code = "";
        if ($id == 0) {
            $code .= "<option selected disabled>Select Task</option>";
        }

        foreach ($rows as $row) {

            $code .= "<optgroup label='" . $row["task_group"] . "'>";

            $query = "SELECT * FROM task_type where task_group = '" . $row["task_group"] . "'";
            $rows2 = ExecuteRows($query);

            if (is_array($rows2)) {

                foreach ($rows2 as $row2) {

                    if ($row2['id'] == $id) {
                        $code .= "<option value='" . $row2['id'] . "' selected>" . $row['task_group'] . " - " . $row2['task_type'] . " (" . $row2['abbr'] .  ")</option>";
                    } else {
                        $code .= "<option value='" . $row2['id'] . "'>" . $row['task_group'] . " - " . $row2['task_type'] . " (" . $row2['abbr'] . ")</option>";
                    }
                }
            }
        }
    }

    return $code;


    // $query = "SELECT * FROM task_type order by task_group";
    // $rows = ExecuteRows($query);
    // if (is_array($rows)) {
    //     $code = "";
    //     if ($id == 0) {
    //         $code .= "<option selected disabled>Select Task</option>";
    //     }
    //     foreach ($rows as $row) {

    //         if ($row['id'] == $id) {
    //             $code .= "<option value='" . $row['id'] . "' selected>" . $row['task_type'] . " (" . $row['abbr'] .  ")</option>";
    //         } else {
    //             $code .= "<option value='" . $row['id'] . "'>" . $row['task_type'] . " (" . $row['abbr'] . ")</option>";
    //         }


    //     }
    //     return $code;
    // }


}


function get_card_details($d, $k)
{
    //SELECT * FROM task_timer WHERE id = 8 AND MD5(user_id) = '' ;
    $query = "SELECT * from task_timer WHERE id = " . $d;
    $row = ExecuteRow($query);

    if ($row) {
        $ctype = $row['card_type'];
        if ($ctype == 1) {
            $data = array(
                "task_type" => load_tasktype($row['task_type']),
                "work_type" => load_worktype($row['work_type']),
                "article_number" => $row['article_number'],
                "card_id" => md5($row["id"])
            );
            echo _msg(200, json_encode($data));
        } else {

            $data = array(
                "details" => $row['details'],
                "card_id" => md5($row["id"]),
                "card_opt" => $row["card_option"]
            );
            echo _msg(200, json_encode($data));
        }
    } else {
    }
}


function check_total_time($eid)
{
    $query = "SELECT initial_time FROM task_timer WHERE id = " . $eid;
    $row = ExecuteRow($query);
    if ($row) {
        return $row['initial_time'];
    }
}

function done_to_todo($eid, $uid)
{
    $update = "UPDATE `sie_management`.`task_timer` SET `pause_time`= NOW(), `card_status`= 1 WHERE  `id`=" . $eid . ";";
    echo Execute($update);
}

function done_to_working($eid, $uid)
{
    $update = "UPDATE `sie_management`.`task_timer` SET `pause_time`= NOW(), `card_status`= 2 WHERE  `id`=" . $eid . ";";
    echo Execute($update);
}

function working_to_pause($eid, $uid, $card = 1)
{
    global $date_time;
    $get_oldtime = "SELECT pause_time, initial_time, revision_time, done_status FROM task_timer WHERE id = " . $eid;
    $row = ExecuteRow($get_oldtime);

    if ($row) {

        $old_time = strtotime($row['pause_time']);


        if ($row['done_status'] == 0) {
            $total_time = $row['revision_time'];
            $new_time = (strtotime($date_time) - $old_time) + $total_time;
            $update = "UPDATE `sie_management`.`task_timer` SET 
            `pause_time`='" . $date_time . "', 
            `revision_time`= " . $new_time . ", 
            `total_time`= `initial_time` + " . $new_time . ", 
            `card_status`= " . $card . " 
             WHERE  `id`=" . $eid . ";";
        } else {
            $total_time = $row['initial_time'];
            $new_time = (strtotime($date_time) - $old_time) + $total_time;
            $update = "UPDATE `sie_management`.`task_timer` SET 
            `pause_time`='" . $date_time . "', 
            `initial_time`= " . $new_time . ", 
            `total_time`= `revision_time` + " . $new_time . ", 
            `card_status`= " . $card . " 
             WHERE  `id`=" . $eid . ";";
        }

        $ret = json_decode(Execute($update));

        if ($ret->message == "update") {
            echo _msg(200, $date_time);
        } else {
            echo _msg(500, $ret->message);
        }
    } else {
        echo _msg(500, "INVALID!");
    }
}

function add_working_cards($eid, $u)
{

    //ADD VALIDATION HERE
    $cquery = "SELECT * FROM task_timer WHERE id = " . $eid;
    $row = ExecuteRow($cquery);

    if (!$row) {
        echo _msg(500, "Invalid!");
        exit;
    }

    $rec_uid = $row['user_id'];
    $rec_ct = $row['card_type'];
    $id = get_userid($u);

    if ($rec_uid <> 0) {
        if ($rec_uid <> $id) {
            echo _msg(500, "This card is owned by another user!");
            exit;
        }
    }

    if (check_total_time($eid) == 0) {
        $update = "UPDATE `sie_management`.`task_timer` SET 
        `user_id`= " . $id . ", 
        `card_status`= 2 ,
        `start_time` = NOW(),
        `pause_time` = NOW()
        WHERE  `id`=" . $eid;
    } else {
        $update = "UPDATE `sie_management`.`task_timer` SET 
        `user_id`= " . $id . ", 
        `card_status`= 2 ,
        `pause_time` = NOW()    
        WHERE  `id`=" . $eid;
    }

    //echo Execute($update);

    $ret = json_decode(Execute($update));
    if ($ret->message == "update") {
        $query = "select * from task_timer where id = " . $eid;
        echo ExecuteJSON($query);
    } else {
        echo _msg(500, $ret->message);
    }
}

function check_updated_card()
{
    $lastUpdate = $_GET['lastUpdate'];
    $sql = "SELECT MAX(updated_at) AS lastUpdate FROM events";
    $row = ExecuteRows($sql);
    if ($row) {
        $serverUpdate = $row['lastUpdate'];
        if ($serverUpdate > $lastUpdate) {
            $response = array('updated' => true);
        } else {
            $response = array('updated' => false);
        }
    }
}


function delete_card($c, $u)
{
    $id = get_userid($u);
    //add code here for supervisor delete
    $query = "SELECT id, initial_time FROM task_timer WHERE id = " . $c . " AND created_by = " . $id;
    $row = ExecuteRow($query);

    if ($row) {

        if ($row['initial_time'] > 0) {
            echo _msg(500, "Unable to Delete, Card already started");
            exit;
        }

        $delete = "DELETE FROM `sie_management`.`task_timer` WHERE  `id`=" . $c . ";";
        echo Execute($delete);
    } else {
        // echo _msg(500, "Unable to Delete, This card is owned by another user!");

        echo ' $("#success_copy").toast("show");';
    }
}

function add_card($c, $u)
{
    $id = get_userid($u);
    $card_type_with_option = [7];

    if (array_search($c, $card_type_with_option) <> "") {
        $getfirst = "SELECT * FROM card_option WHERE card_id= " . $c . " ORDER BY id ASC LIMIT 1;";
        $row = ExecuteRow($getfirst);

        if ($row) {
            $card_opt = $row["id"];
        } else {
            $card_opt = 1;
        }
    } else {
        $card_opt = 0;
    }

    if ($id) {
        $insert = "INSERT INTO `sie_management`.`task_timer` (`created_by`, `card_type`, `card_option`) VALUES (" . $id . ", " . $c . ", " . $card_opt . ");";
        echo Execute($insert);
    } else {
        echo "ERROR";
    }
}


function get_userid($u)
{
    $query = "SELECT id FROM tbl_users WHERE MD5(id) = '" . $u . "';";
    $row = ExecuteRow($query);

    if ($row) {
        return $row['id'];
    } else {
        return "";
    }
}


function user_login($u, $p)
{

    $pass = decrypt($p, $u);

    $d = ["uname" => $u];

    $query = "SELECT * FROM tbl_users WHERE fld_username = :uname";
    $row = ExecuteRow($query, $d);

    if ($row) {

        if (md5($pass) == $row['fld_password']) {

            $data = [
                "id" => md5($row['id']),
                "fname" => $row['fld_firstname'],
                "lname" => $row['fld_lastname'],
                "email" => $row['fld_email'],
                "image" => $row['img'],
                "position" => $row['position'],
                "lob" => $row['lob'],
                "theme" => $row['theme']
            ];

            $insert_logs = "INSERT INTO `sie_management`.`tbl_logs` (`user_id`, `process` ,`notes`) VALUES ($row[id], 'LOGIN', 'LOGGED IN @" . get_client_ip() . "');";
            Execute($insert_logs);
            echo _msg(200, $data);
        } else {
            echo _msg(501, "INVALID PASSWORD.");
        }
    } else {

        echo _msg(500, "INVALID USERNAME.");
    }
}

function verify_logged_user($id, $uname)
{
    $query = "SELECT * FROM tbl_users WHERE MD5(id) = '" . $id . "';";
    $row = ExecuteRow($query);

    if ($row) {

        $u = decrypt($uname, $id);
        if ($row['fld_username'] == $u) {
            echo _msg(200, "verified");
        } else {
            echo _msg(500, "invalid");
        }
    } else {

        echo _msg(500, "invalid");
    }
}


function report_task_timer($data)
{

    $datefrom = $data[1];
    $dateto = $data[2];
    $userid = $data[3];


    if ($dateto == "") {
        $dateto =  $datefrom;
    }


    if ($datefrom == "") {
        $datefrom = date("Y-m-d");
        $dateto = date("Y-m-d");
    } else {
        $datefrom = date("Y-m-d", strtotime($datefrom));
        $dateto = date("Y-m-d", strtotime($dateto));
    }


    if ($userid <> '0') {
        $user_query = "AND tt2.user_id = " . $userid;
    } else {
        $user_query = "AND tt2.user_id <> 0";
    }

    $query = "
    SELECT 
    tt2.user_id, 
    CONCAT(tbl_users.fld_firstname, ' ', tbl_users.fld_lastname) AS full_name, 
    COALESCE(SUM(CASE WHEN tt2.card_type = 0 THEN tt2.total_time ELSE 0 END), 0) AS total_break, 
    COALESCE(SUM(CASE WHEN tt2.card_type = 1 THEN tt2.total_time ELSE 0 END), 0) AS total_task, 
    COALESCE(SUM(CASE WHEN tt2.card_type = 2 THEN tt2.total_time ELSE 0 END), 0) AS total_aux, 
    COALESCE(SUM(CASE WHEN tt2.card_type = 3 THEN tt2.total_time ELSE 0 END), 0) AS total_meeting, 
    COALESCE(SUM(CASE WHEN tt2.card_type = 4 THEN tt2.total_time ELSE 0 END), 0) AS total_training, 
    COALESCE(SUM(CASE WHEN tt2.card_type = 5 THEN tt2.total_time ELSE 0 END), 0) AS total_short_discussion, 
    COALESCE(SUM(CASE WHEN tt2.card_type = 6 THEN tt2.total_time ELSE 0 END), 0) AS total_engagement, 
    COALESCE(SUM(CASE WHEN tt2.card_type = 7 THEN tt2.total_time ELSE 0 END), 0) AS total_development, 
    COALESCE(SUM(CASE WHEN tt2.card_type = 8 THEN tt2.total_time ELSE 0 END), 0) AS total_manual_operation, 
    COALESCE(SUM(CASE WHEN tt2.card_type = 9 THEN tt2.total_time ELSE 0 END), 0) AS total_others, 
    COALESCE(SUM(CASE WHEN tt2.card_type = 10 THEN tt2.total_time ELSE 0 END), 0) AS total_translation, 
    COALESCE(TIME(MIN(tt2.date_created)), 0) AS login_time, 
    DATE(tt2.date_created) AS ddate 
FROM 
    task_timer tt2 
LEFT JOIN 
    tbl_users ON tt2.user_id = tbl_users.id 
WHERE 
    tt2.date_created BETWEEN '" . $datefrom . " 00:00:00' AND '" . $dateto . " 23:59:59'
    " . $user_query . " 
GROUP BY 
    DATE(tt2.date_created), 
    tt2.user_id
ORDER BY ddate DESC;";
    //echo $query;
    echo ExecuteJSON($query);
}

function individual_report($data)
{

    $datefrom = $data[1];
    $dateto = $data[2];
    $userid = $data[3];
    $cardtype = $data[4];

    if ($datefrom == "") {
        $datefrom = date("Y-m-d");
        $dateto = date("Y-m-d");
    }


    if ($datefrom == "") {
        $datefrom = date("Y-m-d");
        $dateto = date("Y-m-d");
    } else {
        $datefrom = date("Y-m-d", strtotime($datefrom));
        $dateto = date("Y-m-d", strtotime($dateto));
    }

    if ($userid <> '0') {
        $user_query = "AND task_timer.user_id = " . $userid;
    } else {
        $user_query = "AND task_timer.user_id <> 0";
    }

    if ($cardtype == "") {
        $card_query = "";
    } else {
        $card_query = "AND task_timer.card_type = " . $cardtype;
    }



    $query = "
SELECT 
task_timer.user_id, 
CONCAT(tbl_users.fld_firstname, ' ', tbl_users.fld_lastname) AS full_name, 
card_type.card_name,
IFNULL(card_option.c_option,'') AS card_opt,
IFNULL(task_timer.details,'') AS cdetails,
IFNULL(CONCAT(task_type.task_group, ' ', task_type.task_type),'') AS ttype,
IFNULL(work_type.work_type,'') AS wtype,
IFNULL(task_timer.article_number,'') AS anum,
task_timer.start_time,
task_timer.pause_time,
task_timer.total_time
FROM task_timer
LEFT JOIN tbl_users ON task_timer.user_id = tbl_users.id 
LEFT JOIN card_type ON task_timer.card_type = card_type.id
LEFT JOIN task_type ON task_timer.task_type = task_type.id
LEFT JOIN work_type ON task_timer.work_type = work_type.id
LEFT JOIN card_option ON task_timer.card_option = card_option.id
WHERE date_created BETWEEN '" . $datefrom . " 00:00:00' AND '" . $dateto . " 23:59:59'
" . $user_query . "
" . $card_query . "
ORDER BY task_timer.id DESC;
";

    echo ExecuteJSON($query);

    //echo $query;

}
