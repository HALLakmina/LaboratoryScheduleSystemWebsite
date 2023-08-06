<?php include("./components/navigation_bar.php");?>

<?php
    $user_id = $_SESSION["user_id"];
    $query = "SELECT * FROM lecture_details WHERE id = $user_id";
    $result = mysqli_query($DB_CON, $query);
    $row = mysqli_fetch_array($result);
?>

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
            </div>
        </div>
    </div>
    <div class="profile-row2">
        <div class="left-content">
            <p class="profile-heading">contract</p>
            <p class="profile-details">
                <table>
                    <tr >
                        <th>Phone</th>
                        <td>0714986705</td>
                    </tr>
                    <tr>
                        <th>E-mail</th>
                        <td>lahirulakmina1999@gmail.com</td>
                    </tr>
                    <tr>
                        <th>Faculty</th>
                        <td>Technological Studies</td>
                    </tr>
                    <tr>
                        <th>Birth Day</th>
                        <td>04/08/1999</td>
                    </tr>
                </table>
            </p>
        </div>
        <div class="right-content">
            <p class="profile-heading">About</p>
            <p class="profile-details">
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Quam architecto<br>
                laborum non quaerat, nostrum doloribus repudiandae atque iure deserunt inventore ratione voluptatem<br>
                voluptas nihil, obcaecati omnis, explicabo officiis cum sapiente?
            </p>

        </div>
    </div>
</main>
<?php include("./components/footer_bar.php");?>