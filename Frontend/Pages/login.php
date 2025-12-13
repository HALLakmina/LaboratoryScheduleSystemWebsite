<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <!-- <script type="module" src="./Js/main.js"></script>
        <script type="module" src="./Js/pageRouting.js"></script> -->
        <script type="module" src="./Js/customElements.js"></script>
        <title>Laboratory Scheduling System User Login</title>
    </head>
    <body class="w-full bg-[url('../resources/img/Wallpaper.jpg')] bg-cover bg-center w-full bg-no-repeat backdrop-blur-xs h-svh overflow-auto"  id="index-content">
        <main  class="w-full h-screen overflow-y-scroll">
            <section class="w-full h-full">
                <div  class="w-full h-full flex flex-col justify-center items-center">
                    <form class="bg-white rounded-lg w-sm md:w-md w-full p-2 flex flex-col justify-start">
                        <p class="text-3xl font-bold py-4 text-center">Hi, User Welcome Back!</p>
                        <div class="flex flex-col justify-start items-left pb-4">
                            <label for="access" class="font-bold text-lg">Access</label>
                            <select id="access" class="w-full p-2 bg-gray-200 rounded-sm" required>
                                <option>-</option>
                                <option>LECHER</option>
                                <option>ADMIN</option>
                            </select>
                        </div>
                        <div class="flex flex-col justify-start items-left pb-4">
                            <label for="email" class="font-bold text-lg">Email</label>
                            <input type="email" id="email" name="email"  class="w-full p-2 bg-gray-200 rounded-sm" placeholder="jone@gmail.com" required/>
                        </div>
                        <div class="flex flex-col justify-start items-left pb-4">
                            <label for="email" class="font-bold text-lg">Password</label>
                            <input type="password" id="password" name="password"  class="w-full p-2 bg-gray-200 rounded-sm" placeholder="#######" required/>
                        </div>
                        <input type="submit" name="login" class="w-[100px] bg-blue-500 p-2 rounded-sm font-bold text-white active:scale-95 self-center text-sm" value="LOGIN"/>
                    </form>
                </div>
            </section>
        </main>
        <?php include __DIR__ . '/../Components/FooterBar.php';?>
    </body>
</html>