<?php include("./components/navigation_bar.php");?>
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