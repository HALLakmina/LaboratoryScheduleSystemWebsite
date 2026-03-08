<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <!-- <script type="module" src="./Js/main.js"></script>
        <script type="module" src="./Js/pageRouting.js"></script> -->
        <!-- <script type="module" src="./Js/customElements.js"></script> -->
        <title>Laboratory Scheduling System News</title>
    </head>
    <body class="w-full bg-[url('../resources/img/Wallpaper.jpg')] bg-cover bg-center w-full bg-no-repeat backdrop-blur-xs h-svh overflow-auto"  id="index-content">
        <div class="w-full h-screen overflow-y-scroll pb-24">
            <?php include __DIR__ . '/../Components/NavigationBar.php';?>
            <main>
                <section class="w-full p-2 flex flex-wrap items-center justify-between">
                    <div class="w-sm md:w-md rounded-lg bg-white p-2 m-2 hover:bg-gray-200 active:bg-blue-200">
                        <img src="#" alt="name" class="w-full min-h-[200px] h-full"/>
                        <p class="text-lg font-bold">TOPIC</p>
                        <p class="w-full min-h-[100px] h-full overflow-y-scroll" style="scrollbar-width: none;">
                            Lorem ipsum dolor sit, amet consectetur adipisicing elit. 
                            Accusantium dolore modi totam exercitationem illo dolor, 
                            quam temporibus reiciendis dicta illum sequi beatae cumque qui? 
                            Perspicia.....
                        </p>
                    </div>
                </section>
                <section class="hidden absolute top-0 bottom-0 left-0 right-0 bg-gray-950/50 flex flex-col justify-center items-center">
                    <div class="w-sm md:w-md h-auto rounded-lg bg-white p-2 flex flex-col items-center justify-star">
                        <button class="self-end bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95">X</button>
                        <img src="#" alt="name" class="w-full min-h-[200px]"/>
                        <div>
                            <p class="text-lg font-bold">TOPIC</p>
                            <p class="w-full overflow-y-scroll max-h-[400px]" style="scrollbar-width: none;">
                                Lorem ipsum, dolor sit amet consectetur adipisicing elit. 
                                Soluta dolorem amet esse tempora, temporibus, 
                                atque modi fuga cumque obcaecati accusamus, vero expedita. 
                                Necessitatibus nesciunt nostrum esse fugit, quas iste commodi.
                            </p>
                        </div>
                    </div>
                </section>
            </main>
            <?php include __DIR__ . '/../Components/FooterBar.php';?>
        </div>        
    </body>
    <script type="module" src="../JS/main.js"></script>
</html>
