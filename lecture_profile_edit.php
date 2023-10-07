<?php include("./components/navigation_bar.php");?>
<main>
    <div class="profile-update-box">
        <form method="post" action="./backend/script.php" class="profile-update-form" id="profile-update-form">
            <div class="profile-details-box">
                <div class="profile-details-item-left">
                    <label for="">Profile Picture</label>
                    <input type="file" name="" id="" >
                </div>
                <div class="profile-details-item">
                    <label for="">Position</label>
                    <input type="text" name="" id="" class="profile-edit-text-box" placeholder="Type hear...">
                </div>
            </div>

            <div class="profile-details-box">
                <div class="profile-details-item-left">
                    <label for="">Phone number</label>
                    <input type="number" name="" id="" class="profile-edit-text-box" placeholder="Type hear...">
                </div>
                <div class="profile-details-item">
                    <label for="">E-mail</label>
                    <input type="text" name="" id="" style="width:300px" class="profile-edit-text-box" placeholder="Type hear...">
                </div>
            </div>

            <div class="profile-details-box">
                <div class="profile-details-item-left">
                    <label for="">Age</label>
                    <input type="number" name="" id="" class="profile-edit-text-box" placeholder="Type hear...">
                </div>
                <div class="profile-details-item">
                    <label for="">Objective</label>
                    <textarea rows="4" cols="50" id="profile-update-form" class="profile-edit-text-box" placeholder="Type hear..."></textarea>
                </div>
            </div>
            
            <label for="">Codification</label>
            <textarea rows="4"  id="profile-update-form" class="profile-edit-text-box" placeholder="Type hear..."></textarea>

            <input type="submit" name="update" value="Login" class="update-button">
        </form>
    </div>
    
</main>
<?php include("./components/footer_bar.php");?>