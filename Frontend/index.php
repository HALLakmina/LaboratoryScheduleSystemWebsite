<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <title>Laboratory Scheduling System</title>
    </head>
    <body class="w-full h-screen bg-[url('./resources/img/Wallpaper.jpg')] bg-cover bg-center bg-no-repeat backdrop-blur-xs "  id="index-content">
        <div class="w-full h-screen overflow-y-scroll pb-24">
            <?php include __DIR__ . '/Components/NavigationBar.php';?>
            <main class=" w-full">
                <section class="flex flex-col w-full justify-between">
                    <article class="w-2/3 p-2 pt-24">
                        <h1 class="text-8xl font-bold text-white text-shadow-[8px_8px_5px_rgb(0_0_0_/_0.5)]">Laboratory Scheduling System</h1>
                    </article>
                    <article class="w-full flex flex-row justify-end p-2 pt-24">
                        <div class="w-1/3">
                            <p class="text-white font-bold text-2xl">About Us</p>
                            <p class="text-lg text-white">
                                Lorem, ipsum dolor sit amet consectetur adipisicing elit. 
                                Illo minima sunt recusandae dolores eaque quos aut ipsum facilis asperiores accusamus illum deserunt cum optio inventore, tenetur soluta. 
                                Modi, necessitatibus rem.
                            </p>
                        </div>
                    </article>
                </section>
            </main>
            <?php include __DIR__ . '/Components/FooterBar.php';?>
        </div>
    </body>
    <script type="module" src="./JS/main.js"></script>
</html>
