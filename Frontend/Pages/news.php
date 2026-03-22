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
                <section id="news-list" class="w-full p-2 flex flex-wrap items-stretch justify-between"></section>
                <section id="news-viewer" class="hidden absolute top-0 bottom-0 left-0 right-0 bg-gray-950/50 flex flex-col justify-center items-center z-10">
                    <div class="w-sm md:w-md h-auto rounded-lg bg-white p-2 flex flex-col items-center justify-start">
                        <button type="button" id="news-viewer-close" class="self-end bg-red-500 p-1 w-8 rounded-sm font-bold text-white active:scale-95">X</button>
                        <img id="news-viewer-image" src="" alt="news image" class="w-full min-h-[200px] object-cover rounded-sm bg-gray-200"/>
                        <div class="w-full pt-4">
                            <p id="news-viewer-title" class="text-lg font-bold">TOPIC</p>
                            <p id="news-viewer-meta" class="text-sm text-gray-600 pb-2"></p>
                            <p id="news-viewer-description" class="w-full overflow-y-scroll max-h-[400px]" style="scrollbar-width: none;"></p>
                        </div>
                    </div>
                </section>
            </main>
            <?php include __DIR__ . '/../Components/FooterBar.php';?>
        </div>        
    </body>
    <script type="module" src="../JS/main.js"></script>
</html>
