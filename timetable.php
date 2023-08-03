<?php include("./components/navigation_bar.php");?>
<main>

<select class="filter">
    <option>--select year--</option>
    <option> 1st Year </option>
    <option> 2nd Year </option>
    <option> 3rd Year </option>
    <option> 4th Year </option>
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
                                    $callNum=$cellNum[$i][$n];
                                    $query= "select * from timetable where id = $callNum";
                                    $result = @mysqli_query($DB_CON,$query);
                                    $row = mysqli_fetch_array($result);
                                    if($callNum == $row['id']) 
                                    {
                                        //$cell = $row['Action'];
                                        // echo $cell;
                                    ?>
                                        <div class="table-data-box">
                                            <div class="">
                                                <p class="data-text"><?php echo $row['Batch']; ?></p>
                                                <p class="data-text"><?php echo $row['Subject_cord']; ?></p>
                                                <p class="data-text"><?php echo $row['Subject'];?></p>
                                                <p class="data-text"><?php echo $row['practical_group']; ?></p>
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
                                                    <a href="lectureRequestPage.php" class="bnt-add">ADD</a>
                                                    </div>
                                                <?php
                                                }
                                                ?> 
                                            </div> 
                                        </div>
                                    <?php
                                        $n ++;
                                    } 
                                    ?>
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
<?php include("./components/footer_bar.php");?>