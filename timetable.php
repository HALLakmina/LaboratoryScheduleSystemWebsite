<?php include("./components/navigation_bar.php");
$_COOKIE["get_cell_id"]=1;
?>
<main>

<select class="filter">
    <option value="All Years">--select year--</option>
    <option value="1st Year"> 1st Year </option>
    <option value="2nd Year"> 2nd Year </option>
    <option value="3rd Year"> 3rd Year </option>
    <option value="4th Year"> 4th Year </option>
</select>

    <div class="table-box">
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
                                        if($call == $row['id']) 
                                        {
                                            $request_true = true;
                                            while( $request_row = mysqli_fetch_array($request_result))
                                            {
                                               
                                                if($today == $request_row['request_date'] && $request_row['cell_id'] == $call)
                                                {
                                                    $request_true = false;
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
                                            if($request_true)
                                            {
                                                echo"run two <br>";
                                                
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
                                        $request_true = true;
                                        while( $request_row = mysqli_fetch_array($request_result))
                                        {
                                            
                                            if($today == $request_row['request_date'] && $request_row['cell_id'] == $call)
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
                                        if($request_true){
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
    </div>
</main>

<div class="request-from-box">
    <div class="close-request-from-box" onclick="closeRequestForm()"></div>
    <?php //echo ;?>
    <form method="post" action="./backend/script.php" class="request-from-content" id="requestFrom">
       
    <span id="queryCell"> </span>
        
    </form>
</div>

<script>
    function requestForm(id)
    {
        document.cookie="get_cell_id="+id;
        document.querySelector(".request-from-box").classList.add("open-request-from-box");
        var selected = id;
            $.ajax({
                url:'./backend/script.php',
                type:"POST",
                data:{selectedCell: selected},
                success: function(result){
                    $('#requestFrom').html(result);
                }
            })
    }
    function closeRequestForm()
    {
        document.querySelector(".request-from-box").classList.remove("open-request-from-box");
    }
 
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


        $(".filter").on("change", function(){
            var selectedFilterYear = $(".filter").val();
            $.ajax({
                url:'./backend/script.php',
                type:"POST",
                data: {selectedFilter: selectedFilterYear},
                success: function(result){
                    $('.table-box').html(result);
                }
            })
        })
    });
</script>
<?php include("./components/footer_bar.php");?>