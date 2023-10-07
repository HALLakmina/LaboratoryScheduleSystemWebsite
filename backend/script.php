<script src="./timetable.php"></script>
<script src="./JQuery/jQuery-3.6.4.compressed.production .js"></script>
<script src="./JQuery/jQuery-3.6.4.slim.build.compressed.production .js"></script>
<script src="./JQuery/jQuery-3.6.4.slim.build.uncompressed.development .js"></script>
<script src="./JQuery/jQuery-3.6.4.uncompressed.development .js"></script>

<?php
include_once("./dbConnection.php");
session_start();

if(isset($_POST["login"]))
{
    $username =mysqli_real_escape_string($DB_CON, $_POST["user"]); 
    $password = mysqli_real_escape_string($DB_CON, $_POST["password"]) ;

    $query = "SELECT * FROM lecture_details WHERE user_name = '$username' limit 1";
    $result = mysqli_query($DB_CON,  $query);
    $row = mysqli_fetch_array($result);
    if(mysqli_num_rows($result)>0)
    {
        if($row["user_name"]==$username AND $row["password"]==$password)
        {
            $_SESSION["user_name"]=$row["user_name"];
            $_SESSION["user_id"]=$row["lecture_id"];
            header("Location: ../index.php");
            die;
        }
        else
        {
            echo"user name or password is wrong";
        }
    }
    else
    {
        echo"enter valid information";
    }
}

if (isset($_GET["logout"]))
{
    unset($_SESSION["login"]);
    unset($_GET["logout"]);
    session_destroy();
    header("Location: ../index.php");
}

if(isset($_POST['selectedYear']))
{
    $takeYears = $_POST['selectedYear']; 
    $cell_query = "SELECT * FROM practical_subject WHERE year = '$takeYears'";  
    $cell_results = mysqli_query($DB_CON, $cell_query); 
    if(mysqli_num_rows($cell_results)>0)
    {
    ?>
        <option onclick="">--subject cord--</option>
        <?php
        while($cell_row = mysqli_fetch_array($cell_results))
        {
            $coed= $cell_row["Subject_cord"];'+console.log("run");+'
            ?>
            <option value="<?php echo $coed;?>" onclick="selectSubject(<?php echo $coed;?>)">
                <?php echo $coed;?>
            </option>
        <?php  
        }
    }
    else
    {
    ?>
        <option onclick="">--subject cord--</option>
    <?php
    }
}

if(isset($_POST['selectedCode']))
{
    $takeCode = $_POST['selectedCode']; 
    $cell_query = "SELECT * FROM practical_subject WHERE Subject_cord = '$takeCode'";  
    $cell_results = mysqli_query($DB_CON, $cell_query); 
    if(mysqli_num_rows($cell_results)>0)
    {
        while($cell_row = mysqli_fetch_array($cell_results))
        {
            $coed= $cell_row["Subject"];
            ?>
            <option value="<?php echo $coed;?>" onclick="selectSubject(<?php echo $coed;?>)">
                <?php echo $coed;?>
            </option>
        <?php  
        }
    }
    else
    {
    ?>
        <option onclick="">--subject name--</option>
    <?php
    }
}

if(isset($_POST['selectedCell']))
{
    $takeCell = $_POST['selectedCell'];
    echo $takeCell;
    $cell_query =   "SELECT timetable.id,timetable.practical_group,practical_subject.Subject_cord,practical_subject.Subject,practical_subject.year,lecture_details.full_name FROM timetable INNER JOIN practical_subject ON timetable.Subject_cord = practical_subject.Subject_cord INNER JOIN lecture_details ON practical_subject.lecture_id = lecture_details.lecture_id WHERE timetable.id = $takeCell";
    $cell_results = mysqli_query($DB_CON, $cell_query); 
    //echo $cell_results;
    if(mysqli_num_rows($cell_results)>0)
    {
        while($cell_row = mysqli_fetch_array($cell_results))
        {
            $coed= $cell_row["Subject_cord"];
            ?>
                <div class="request-from-item">
                    <label for="" class="request-from-text">Year</label>
                    <select class="request-input-box" id="yearBox"  name="year">
                        <?php
                            if($cell_row["year"]=="1st Year")
                            {
                            ?>
                                <option value="1st Year"> 1st Year </option>
                                <option value="2nd Year"> 2nd Year </option>
                                <option value="3rd Year"> 3rd Year </option>
                                <option value="4st Year"> 4th Year </option>
                            <?php
                            }
                            else if($cell_row["year"]=="2nd Year")
                            {
                            ?>
                                <option value="2st Year"> 2nd Year </option>
                                <option value="1nd Year"> 1st Year </option>
                                <option value="3rd Year"> 3rd Year </option>
                                <option value="4st Year"> 4th Year </option>
                            <?php
                            }
                            else if($cell_row["year"]=="3rd Year")
                            {?>
                                <option value="3rd Year"> 3rd Year </option>
                                <option value="1st Year"> 1st Year </option>
                                <option value="2nd Year"> 2nd Year </option>
                                <option value="4st Year"> 4th Year </option>
                            <?php
                            }
                            else if($cell_row["year"]=="4th Year")
                            {
                                ?>
                                <option value="4st Year"> 4th Year </option>
                                <option value="1st Year"> 1st Year </option>
                                <option value="2nd Year"> 2nd Year </option>
                                <option value="3rd Year"> 3rd Year </option>
                            <?php
                            }
                            else
                            {
                            ?>
                                <option value="All">--select year--</option>
                                <option value="1st Year"> 1st Year </option>
                                <option value="2nd Year"> 2nd Year </option>
                                <option value="3rd Year"> 3rd Year </option>
                                <option value="4st Year"> 4th Year </option>
                            <?php
                            }
                        ?> 
                    </select>
                </div>

                <div class="request-from-item">
                    <label for="" class="request-from-text">Subject Cord</label>
                    <select class="request-input-box" id="subjectCord" name="subject_cord">
                        <option value="<?php echo $cell_row["Subject_cord"];?>"><?php echo $cell_row["Subject_cord"];?></option>
                    </select>
                </div>

                <div class="request-from-item">
                    <label for="" class="request-from-text">Subject Name</label>
                    <select class="request-input-box" id="subjectName" name="subject_name">
                        <option value="<?php echo $cell_row["Subject"];?>"><?php echo $cell_row["Subject"];?></option>
                    </select>
                </div>

                <div class="request-from-item">
                    <label for="" class="request-from-text">Group</label>
                    <select class="request-input-box" id="Group" name="group">
                        <option value="Group 01">Group 01</option>
                        <option value="Group 02">Group 02</option>
                    </select>
                </div>
        
                <div class="request-from-item">
                    <label for="" class="request-from-text">Date</label>
                    <input type="date" name="date" class="request-input-box">
                </div>

                <div class="request-from-item">
                    <label for="" class="request-from-text">Software/Requirement</label>
                    <input type="text" name="requirement" placeholder="type hear....." class="request-input-box">
                </div>
                <input type="hidden" name="lecture_name" value="<?php echo $_SESSION['user_name']; ?>">
                <input type="hidden" name="cell_id" value="<?php echo $takeCell;?>">
                <div>
                    <input type="submit" name="request_submit" value="Submit">
                </div>
        <?php  
        }
    }
    else
    {
        ?>
        <div class="request-from-item">
            <label for="" class="request-from-text">Year</label>
            <select class="request-input-box" id="yearBox" name="year">
                        <option value="All">--select year--</option>
                        <option value="1st Year"> 1st Year </option>
                        <option value="2nd Year"> 2nd Year </option>
                        <option value="3rd Year"> 3rd Year </option>
                        <option value="4st Year"> 4th Year </option>
            </select>
        </div>

        <div class="request-from-item">
            <label for="" class="request-from-text" >Subject Cord</label>
            <select class="request-input-box" id="subjectCord" name="subject_cord">
                <option value="">--select subject cord--</option>
            </select>
        </div>

        <div class="request-from-item">
            <label for="" class="request-from-text">Subject Name</label>
            <select class="request-input-box" id="subjectName" name="subject_name">
            <option value="">--select subject name--</option>
            </select>
        </div>

        <div class="request-from-item">
            <label for="" class="request-from-text">Group</label>
            <select class="request-input-box" id="Group" name="group">
                <option value="Group 01">Group 01</option>
                <option value="Group 02">Group 02</option>
            </select>
        </div>

        <div class="request-from-item">
            <label for="" class="request-from-text">Date</label>
            <input type="date" name="date" class="request-input-box">
        </div>

        <div class="request-from-item">
            <label for="" class="request-from-text">Software/Requirement</label>
            <input type="text" name="requirement" placeholder="type hear....." class="request-input-box">
        </div>
        <input type="hidden" name="lecture_name" value="<?php echo $_SESSION['user_name']; ?>">
        <input type="hidden" name="cell_id" value="<?php echo $takeCell;?>">
        <div>
            <input type="submit" name="request_submit" value="Submit">
        </div>
    <?php  
    }
    ?>
    <script>
        $(document).ready(function()
        {
            $("#yearBox").on("change", function(){
                var selected = $("#yearBox").val();
                $.ajax({
                    url:'./backend/script.php',
                    type:"POST",
                    data:{selectedYear: selected},
                    success: function(result){
                        $('#subjectCord').html(result);
                        console.log(result);
                    }
                })
            })

            $("#subjectCord").on("change", function(){
                var selectedSubject = $("#subjectCord").val();
                $.ajax({
                    url:'./backend/script.php',
                    type:"POST",
                    data:{selectedCode: selectedSubject},
                    success: function(result){
                        $('#subjectName').html(result);
                        console.log(result);
                    }
                })
            })
        });
    </script>
    <?php
}


if(isset($_POST['request_submit']))
{
    
    $request_id = $_POST['cell_id'];
    echo $request_id;
    $request_year = $_POST['year'];
    $request_subject_cord = $_POST['subject_cord'];
    echo $request_subject_cord;
    $request_subject_name = $_POST['subject_name'];
    $request_student_group = $_POST['group'];
    $request_lecture_name = $_POST['lecture_name']; 
    $request_Requirement = $_POST['requirement'];
    $request_date = $_POST['date'];

    $cellNum = array(
        array(1, 7, 13, 19, 25 ),
        array(2, 8, 14, 20, 26 ),
        array(3, 9, 15, 21, 27 ),
        array(4, 10, 16, 22, 28 ),
        array(5, 11, 17, 23, 29 ),
        array(6, 12, 18, 24, 30 ),
    );
    if($request_id <= 6 ){ $request_day = "Monday";}
    elseif($request_id <= 12){$request_day = "Tuesday";}
    elseif($request_id <= 18){$request_day = "Wednesday";}
    elseif($request_id <= 24){$request_day = "Thursday";}
    elseif($request_id <= 30){$request_day = "Friday";}

    if($request_id == 1 or $request_id == 7 or $request_id == 13 or $request_id == 19 or $request_id == 25){ $request_time = "08.30 AM <br> 09.30 AM";}
    elseif($request_id == 2 or $request_id == 8 or $request_id == 14 or $request_id == 20 or $request_id == 26){$request_time = "09.30 AM <br> 10.30 AM";}
    elseif($request_id == 3 or $request_id == 9 or $request_id == 15 or $request_id == 21 or $request_id == 27){$request_time = "10.30 AM <br> 11.30 AM";}
    elseif($request_id == 4 or $request_id == 10 or $request_id == 16 or $request_id == 22 or $request_id == 28){$request_time = "11.30 AM <br> 12.30 AM";}
    elseif($request_id == 5 or $request_id == 11 or $request_id == 17 or $request_id == 23 or $request_id == 29){$request_time = "13.00 AM <br> 15.00 AM";}
    elseif($request_id == 6 or $request_id == 12 or $request_id == 18 or $request_id == 24 or $request_id == 30){$request_time = "15.00 AM <br> 17.00 A";}

    $cell_query =   "INSERT INTO lecture_request (`year`, `subject_cord`, `subject_name`, `lecture_name`, `student_group`, `Requirement`, `request_date`, `day`, `time`, `cell_id` ) VALUES ('$request_year','$request_subject_cord','$request_subject_name', '$request_lecture_name','$request_student_group','$request_Requirement','$request_date','$request_day','$request_time', '$request_id')";
    if($DB_CON->query($cell_query))
    {
        header("Location: ../timetable.php");
    }
    else
    {
        echo "Error: ". $cell_query . "<br>". $DB_CON->error;
    }
}

if(isset($_POST['selectedFilter']))
{
    $FilterYear = $_POST['selectedFilter'];
  ?>
    <table  cellspacing="null" width="120%" class="timetable">
        <tr> 
            <th class="table-heading corner-top-left-radius">TIME</th>
            <th class="table-heading">Monday</th>
            <th class="table-heading">Tuesday</th>
            <th class="table-heading">Wednesday</th>
            <th class="table-heading">Thursday</th>
            <th class="table-heading corner-top-right-radius">Friday</th>
        </tr>
        <?php
            $cellNum = array(
                array(1, 7, 13, 19, 25 ),
                array(2, 8, 14, 20, 26 ),
                array(3, 9, 15, 21, 27 ),
                array(4, 10, 16, 22, 28 ),
                array(5, 11, 17, 23, 29 ),
                array(6, 12, 18, 24, 30 ),
            );

            $date = array(
                "08.30 AM <br> 09.30 AM",
                "09.30 AM <br> 10.30 AM",
                "10.30 AM <br> 11.30 AM",
                "11.30 AM <br> 12.30 AM",
                "13.00 AM <br> 15.00 AM",
                "15.00 AM <br> 17.00 AM"
            );

            $i = 0;
            while( $i < 6)
            {
            ?>
                <tr>
                    <th class="table-heading" width="100px"><div class="time"><?php echo $date[$i];?></th>
                    <?php
                        $n = 0;
                        while( $n < 5)
                        {
                        ?>
                            <td class="table-item">
                                <?php
                                $call=$cellNum[$i][$n];
                                $today = date("Y-m-d");
                                $request_query ="SELECT * FROM conform_request";
                                $request_result = mysqli_query($DB_CON,$request_query);

                                $query= "SELECT timetable.id,timetable.practical_group,timetable.Action,practical_subject.Subject_cord,practical_subject.Subject,practical_subject.year,lecture_details.full_name FROM timetable INNER JOIN practical_subject ON timetable.Subject_cord = practical_subject.Subject_cord INNER JOIN lecture_details ON practical_subject.lecture_id = lecture_details.lecture_id WHERE timetable.id = $call";
                                $result = @mysqli_query($DB_CON,$query);
                                $row = mysqli_fetch_array($result);
                                $num = mysqli_num_rows($result);
                                if($num>0)
                                {
                                    $double_check ="ok";
                                    if($FilterYear == $row['year'] or $FilterYear == "All Years")
                                    { 
                                        $double_check = "no";
                                        if($call == $row['id']) 
                                        {
                                            $request_true = true;
                                            while( $request_row = mysqli_fetch_array($request_result))
                                            {

                                                if($today == $request_row['request_date'] && $request_row['cell_id'] == $call)
                                                {

                                                    $request_true = false;
                                                    if($FilterYear==$request_row['year'] or $FilterYear == "All Years")
                                                    {
                                                    ?>
                                                        <P style="color:red;font-weight:bold;font-size:18px;">Extra lecture</P>
                                                        <div class="table-data-box">
                                                            <div class="">

                                                                <p class="data-text"><?php echo $request_row['year']; echo "one";?></p>
                                                                <p class="data-text"><?php echo $request_row['subject_cord']; ?></p>
                                                                <p class="data-text"><?php echo $request_row['subject_name'];?></p>
                                                                <p class="data-text"><?php echo $request_row['student_group']; ?></p>
                                                                <p class="data-text"><?php echo $request_row['lecture_name']; ?></p>
                                                            </div>
                                                            <div class="action-box">
                                                                <div class="<?php if($request_row['action'] == "cancel"){echo "cancel";}elseif($request_row['action'] == "free" ){echo "free";}elseif($request_row['action'] == "active" ){echo "active";}?> ">
                                                                    <p style="font-weight:bold;font-size:10px;"><?php if($request_row['action'] == "cancel"){echo "cancel";}elseif($request_row['action'] == "free" ){echo "free";}elseif($request_row['action'] == "active" ){echo "active";}?></p>
                                                                </div>
                                                                <?php
                                                                if(isset($_SESSION['user_name']))
                                                                {
                                                                ?>
                                                                    <div class="">
                                                                    <button class="bnt-add" onclick="requestForm(<?php echo $row['id'];?>)">ADD</button>
                                                                    </div>
                                                                <?php
                                                                }
                                                                ?> 
                                                            </div> 
                                                        </div>
                                                    <?php
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <div class="table-data-box">
                                                            <div class="">
                                                            </div>
                                                            <div class="action-box">
                                                                <div class="<?php if($request_row['action'] == "cancel"){echo "cancel";}elseif($request_row['action'] == "free" ){echo "free";}elseif($request_row['action'] == "active" ){echo "active";}?> ">
                                                                    <p style="font-weight:bold;font-size:10px;"><?php if($request_row['action'] == "cancel"){echo "cancel";}elseif($request_row['action'] == "free" ){echo "free";}elseif($request_row['action'] == "active" ){echo "active";}?></p>
                                                                </div>
                                                                <?php
                                                                if(isset($_SESSION['user_name']))
                                                                {
                                                                ?>
                                                                    <div class="">
                                                                    <button class="bnt-add" onclick="requestForm(<?php echo $row['id'];?>)">ADD</button>
                                                                    </div>
                                                                <?php
                                                                }
                                                                ?> 
                                                            </div> 
                                                        </div>
                                                        <?php
                                                    }
                                                }
                                            }
                                            if($request_true)
                                            {
                                                ?>
                                                <div class="table-data-box">
                                                    <div class="">

                                                        <p class="data-text"><?php echo $row['year']; ?></p>
                                                        <p class="data-text"><?php echo $row['Subject_cord']; ?></p>
                                                        <p class="data-text"><?php echo $row['Subject'];?></p>
                                                        <p class="data-text"><?php echo $row['practical_group']; ?></p>
                                                        <p class="data-text"><?php echo $row['full_name']; ?></p>
                                                    </div>
                                                    <div class="action-box">
                                                        <div class="<?php if($row['Action'] == "cancel"){echo "cancel";}elseif($row['Action'] == "free" ){echo "free";}elseif($row['Action'] == "active" ){echo "active";}?> ">
                                                            <p style="font-weight:bold;font-size:10px;"><?php if($row['Action'] == "cancel"){echo "cancel";}elseif($row['Action'] == "free" ){echo "free";}elseif($row['Action'] == "active" ){echo "active";}?></p>
                                                        </div>
                                                        <?php
                                                        if(isset($_SESSION['user_name']))
                                                        {
                                                        ?>
                                                            <div class="">
                                                            <button class="bnt-add" onclick="requestForm(<?php echo $row['id'];?>)">ADD</button>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?> 
                                                    </div> 
                                                </div>
                                            <?php
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if($call == $row['id']) 
                                        {
                                            $request_true = true;
                                            while( $request_row = mysqli_fetch_array($request_result))
                                            {
                                                $request_true = false;
                                                if($today == $request_row['request_date'] && $request_row['cell_id'] == $call)
                                                {
                                                    if($FilterYear==$request_row['year'] or $FilterYear == "All Years")
                                                    {
                                                    ?>
                                                        <P style="color:red;font-weight:bold;font-size:18px;">Extra lecture</P>
                                                        <div class="table-data-box">
                                                            <div class="">

                                                                <p class="data-text"><?php echo $request_row['year']; ?></p>
                                                                <p class="data-text"><?php echo $request_row['subject_cord']; ?></p>
                                                                <p class="data-text"><?php echo $request_row['subject_name'];?></p>
                                                                <p class="data-text"><?php echo $request_row['student_group']; ?></p>
                                                                <p class="data-text"><?php echo $request_row['lecture_name']; ?></p>
                                                            </div>
                                                            <div class="action-box">
                                                                <div class="<?php if($request_row['action'] == "cancel"){echo "cancel";}elseif($request_row['action'] == "free" ){echo "free";}elseif($request_row['action'] == "active" ){echo "active";}?> ">
                                                                    <p style="font-weight:bold;font-size:10px;"><?php if($request_row['action'] == "cancel"){echo "cancel";}elseif($request_row['action'] == "free" ){echo "free";}elseif($request_row['action'] == "active" ){echo "active";}?></p>
                                                                </div>
                                                                <?php
                                                                if(isset($_SESSION['user_name']))
                                                                {
                                                                ?>
                                                                    <div class="">
                                                                    <button class="bnt-add" onclick="requestForm(<?php echo $row['id'];?>)">ADD</button>
                                                                    </div>
                                                                <?php
                                                                }
                                                                ?> 
                                                            </div> 
                                                        </div>
                                                    <?php
                                                    }
                                                }
                                                else
                                                {
                                                    ?>
                                                    <div class="table-data-box">
                                                        <div class="">
                                                        </div>
                                                        <?php 
                                                            $query_action= "SELECT Action,id FROM timetable WHERE id = $call";
                                                            $result_action = @mysqli_query($DB_CON,$query_action);
                                                            $row_action = mysqli_fetch_array($result_action);
                                                        ?>
                                                        <div class="action-box">
                                                            <div class="<?php if($row_action['Action'] == "cancel"){echo "cancel";}elseif($row_action['Action'] == "free" ){echo "free";}elseif($row_action['Action'] == "active" ){echo "active";}?> " style="margin-bottom:20px;">
                                                                <p style="font-weight:bold;font-size:10px;"><?php if($row_action['Action'] == "cancel"){echo "cancel";}elseif($row_action['Action'] == "free" ){echo "free";}elseif($row_action['Action'] == "active" ){echo "active";}?></p>
                                                            </div>
                                                            <?php
                                                            if(isset($_SESSION['user_name']))
                                                            {
                                                            ?>
                                                                <div class="">
                                                                <button class="bnt-add" onclick="requestForm(<?php echo $row_action['id'];?>)">ADD</button>
                                                                </div>
                                                            <?php
                                                            }
                                                            ?> 
                                                        </div> 
                                                    </div>
                                                <?php
                                                }
                                            }
                                        }
                                    }
                                } 
                                else
                                {
                                    $request_true = true;
                                    while( $request_row = mysqli_fetch_array($request_result))
                                    {
                                        
                                        if($today == $request_row['request_date'] && $request_row['cell_id'] == $call)
                                        {
                                            if($FilterYear == $request_row['year'] or $FilterYear == "All Years" )
                                            {
                                            ?>
                                            <P style="color:red;font-weight:bold;font-size:18px;">Extra lecture</P>
                                                <div class="table-data-box">
                                                    <div class="">
                                                        <p class="data-text"><?php echo $request_row['year']; ?></p>
                                                        <p class="data-text"><?php echo $request_row['subject_cord']; ?></p>
                                                        <p class="data-text"><?php echo $request_row['subject_name'];?></p>
                                                        <p class="data-text"><?php echo $request_row['student_group']; ?></p>
                                                        <p class="data-text"><?php echo $request_row['lecture_name']; ?></p>
                                                    </div>
                                                    <div class="action-box">
                                                        <div class="<?php if($request_row['action'] == "cancel"){echo "cancel";}elseif($request_row['action'] == "free" ){echo "free";}elseif($request_row['action'] == "active" ){echo "active";}?> ">
                                                            <p style="font-weight:bold;font-size:10px;"><?php if($request_row['action'] == "cancel"){echo "cancel";}elseif($request_row['action'] == "free" ){echo "free";}elseif($request_row['action'] == "active" ){echo "active";}?></p>
                                                        </div>
                                                        <?php
                                                        if(isset($_SESSION['user_name']))
                                                        {
                                                        ?>
                                                            <div class="">
                                                            <button class="bnt-add" onclick="requestForm(<?php echo $row['id'];?>)">ADD</button>
                                                            </div>
                                                        <?php
                                                        }
                                                        ?> 
                                                    </div> 
                                                </div>
                                            <?php
                                            }
                                        }
                                        
                                    }
                                    if($request_true)
                                    {
                                        ?>
                                        <div class="table-data-box">
                                            <div class="">
                                            </div>
                                            <?php 
                                                $query_action= "SELECT Action,id FROM timetable WHERE id = $call";
                                                $result_action = @mysqli_query($DB_CON,$query_action);
                                                $row_action = mysqli_fetch_array($result_action);
                                            ?>
                                            <div class="action-box">
                                                <div class="<?php if($row_action['Action'] == "cancel"){echo "cancel";}elseif($row_action['Action'] == "free" ){echo "free";}elseif($row_action['Action'] == "active" ){echo "active";}?> " style="margin-bottom:20px;">
                                                    <p style="font-weight:bold;font-size:10px;"><?php if($row_action['Action'] == "cancel"){echo "cancel";}elseif($row_action['Action'] == "free" ){echo "free";}elseif($row_action['Action'] == "active" ){echo "active";}?></p>
                                                </div>
                                                <?php
                                                if(isset($_SESSION['user_name']))
                                                {
                                                ?>
                                                    <div class="">
                                                    <button class="bnt-add" onclick="requestForm(<?php echo $row_action['id'];?>)">ADD</button>
                                                    </div>
                                                <?php
                                                }
                                                ?> 
                                            </div> 
                                        </div>
                                    <?php
                                    }
                                }
                                $n ++;?>
                            </td>
                            <?php
                        } 
                    ?>
                </tr>
            <?php
                $i ++;
            } 
        ?>
    </table>
  <?php
}
?>