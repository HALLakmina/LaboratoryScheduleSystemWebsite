<?php include("./components/navigation_bar.php");?>
<style>
    .news-content
    {
        margin:10px;
        margin-top:10%;
        margin-bottom:5%;
        display:flex;
        flex-wrap: wrap;
    }
    .news-card
    {
        background-color: #fff;
        width:20%;
        height: 400px;
        margin:20px;
        padding:10px;
        border-radius: 10px;
    }
    .news-image-box
    {
        width:100%;
        height:250px;
        box-shadow: 0px 0px 5px rgba(0,0,0,0.8);
        border-radius:10px;
    }
    .news-details
    {
        margin-top: 20px;
        overflow: hidden;
        overflow-y: scroll;
        max-height: 32%;
    }
    .news-image
    {
        border-radius:10px;
        display:flex;
        justify-content: center;
        align-items: center;
        max-width: 100%;
    }
</style>
    <main class="news-content">
        <?php
            $news_query = "SELECT * FROM news";
            $news_result = mysqli_query($DB_CON, $news_query);
             
            if($num = mysqli_num_rows($news_result)> 0)
            {
                while($news_row = mysqli_fetch_array($news_result))
                {
                    ?>
                    <div class="news-card">
                        <div class="news-image-box">
                            <img src="./resources/news_image/<?php echo $news_row["image"]; ?>" class="news-image">
                        </div>
                        <div class="news-details">
                            <p style="font-size:25px;font-weight:bold; margin:5px;"><?php echo $news_row["title"];?></p>
                            <p style="font-size:15px; margin:5px;"><?php echo $news_row["time"]; echo "  "; echo $news_row["date"];?></p>
                            <p style="font-size:15px; margin:5px;"><?php echo $news_row["place"];?></p>
                            <p style="font-size:10px; margin:5px;"><?php echo $news_row["description"];?></p>
                        </div>
                    </div>
                <?php
                }
            }
        ?>
    </main>
<?php include("./components/footer_bar.php");?>