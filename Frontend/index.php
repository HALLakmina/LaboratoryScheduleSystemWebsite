<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <title>Laboratory Scheduling System</title>
    </head>
    <body class="min-w-[320px] min-h-svh overflow-y-auto no-scrollbar bg-[url('./resources/img/Wallpaper.jpg')] bg-cover bg-center bg-no-repeat backdrop-blur-xs" id="index-content">
        <?php include __DIR__ . '/Components/NavigationBar.php';?>
        <main class="mx-auto flex w-full max-w-6xl flex-col gap-10 px-4 pb-28 pt-10 sm:gap-16 sm:px-6 sm:pt-16 lg:px-10">
            <section class="w-full">
                <h1 class="max-w-3xl text-4xl font-bold leading-tight text-white text-shadow-[8px_8px_5px_rgb(0_0_0_/_0.5)] sm:text-6xl lg:text-8xl">
                    Laboratory Scheduling System
                </h1>
            </section>
            <section class="flex w-full justify-center sm:justify-end">
                <div class="w-full max-w-md">
                    <p class="text-xl font-bold text-white sm:text-2xl">About Us</p>
                    <p class="mt-2 text-base text-white sm:text-lg">
                        Lorem, ipsum dolor sit amet consectetur adipisicing elit.
                        Illo minima sunt recusandae dolores eaque quos aut ipsum facilis asperiores accusamus illum deserunt cum optio inventore, tenetur soluta.
                        Modi, necessitatibus rem.
                    </p>
                </div>
            </section>
        </main>
        <?php include __DIR__ . '/Components/FooterBar.php';?>
    </body>
    <script type="module" src="./JS/main.js"></script>
</html>
