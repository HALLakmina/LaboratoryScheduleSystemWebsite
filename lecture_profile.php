<?php include("./components/navigation_bar.php");?>

<?php
    $user_id = $_SESSION["user_id"];
    $query = "SELECT * FROM lecture_details WHERE lecture_id = $user_id";
    $result = mysqli_query($DB_CON, $query);
    $row = mysqli_fetch_array($result);
?>
<body class="profile-background">
<main class="profile-content">
    <div class="profile-row1">
        <div class="image-box">
            <img src="./resources/lecture_image/<?php echo $row["profile_image"];?>" class="profile-img">
        </div>
        <div class="name-content">
            <div class="name-box">
                <p class="lecture-name"><?php echo $row["full_name"];?></p>
                <p class="lecture-position">Lecture</p> 
            </div>
            <div class="logout-box">
                <a href="./backend/script.php?logout='True'" class="logout-link">Logout</a>
                <a href="./lecture_profile_edit.php" class="profile-edit">Edit</a>
            </div>
        </div>
    </div>
    <div class="profile-row2">
        <div class="left-content">
           <p class="profile-heading">Qualification</p>
        </div>
        <div class="right-content">
            <p class="profile-heading">About</p>
            <p class="profile-sub-heading">Objective</p>
            <p class="profile-details">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Quam architecto<br>
                laborum non quaerat, nostrum doloribus repudiandae atque iure deserunt inventore ratione voluptatem<br>
                voluptas nihil, obcaecati omnis, explicabo officiis cum sapiente?
            </p>
            <p class="profile-sub-heading">contract</p>
            <p class="profile-details">
                <table class="profile-table">
                    <tr>
                        <th class="profile-table-cell">Phone</th>
                        <td class="profile-table-cell-details">0714986705</td>
                    </tr>
                    <tr>
                        <th class="profile-table-cell">E-mail</th>
                        <td class="profile-table-cell-details">lahirulakmina1999@gmail.com</td>
                    </tr>
                    <tr>
                        <th  class="profile-table-cell">Faculty</th>
                        <td  class="profile-table-cell-details">Technological Studies</td>
                    </tr>
                    <tr>
                        <th  class="profile-table-cell">Age</th>
                        <td  class="profile-table-cell-details">24</td>
                    </tr>
                </table>
            </p>
        </div>
    </div>
</main>
</body>
<?php include("./components/footer_bar.php");?>