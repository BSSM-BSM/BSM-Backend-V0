<?php
if ($_POST['grade']==NULL||$_POST['classNo']==NULL) {
    exit();
}else{
    $grade = Mysqli_real_escape_string(conn(), $_POST['grade']);
    $classNo = Mysqli_real_escape_string(conn(), $_POST['classNo']);
    $timetable_query = "SELECT * from `timetable` where `grade`='$grade' AND `classNo`='$classNo'";
    $result = db($timetable_query);
    $timetable=$result->fetch_array(MYSQLI_ASSOC);
    $arrTimetable=[
        explode(',', $timetable['monday']),
        explode(',', $timetable['tuesday']),
        explode(',', $timetable['wednesday']),
        explode(',', $timetable['thursday']),
        explode(',', $timetable['friday'])
    ];
    if($timetable['monday'].$timetable['tuesday'].$timetable['wednesday'].$timetable['thursday'].$timetable['friday']==NULL){
        $arrTimetable=NULL;
    }
    //$json = json_encode(array('status' => 1, 'arrTimetable' => $arrTimetable));
    echo json_encode(array('status' => 1, 'arrTimetable' => $arrTimetable));
}
?>