<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <title>Laboratory Scheduling System User Login</title>
    </head>
    <body class="min-w-[320px] min-h-svh overflow-auto bg-[url('../resources/img/Wallpaper.jpg')] bg-cover bg-center bg-no-repeat backdrop-blur-xs" id="index-content">
        <main class="mx-auto flex min-h-svh w-full max-w-6xl items-center justify-center px-3 py-6 sm:px-4">
            <section class="flex items-center justify-center w-full">

                <div class="w-lg rounded-lg bg-white/95 p-4 shadow-2xl ring-1 ring-white/80 sm:p-5">
                    <form id="login-form" class="flex w-full flex-col gap-4">
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.28em] text-gray-500">Sign In</p>
                            <h2 class="pt-2 text-2xl font-black text-gray-950 sm:text-3xl">Access Your Account</h2>
                            <p class="pt-2 text-sm font-semibold text-gray-600">
                                Select your access level and continue with your registered email and password.
                            </p>
                        </div>

                        <div id="login-error" class="hidden rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-bold text-red-700" role="alert"></div>

                        <div class="flex flex-col gap-2">
                            <label for="access" class="text-sm font-black uppercase tracking-wide text-gray-600">Access</label>
                            <select id="access" class="w-full rounded-lg border border-gray-200 bg-gray-100 px-4 py-3 font-bold text-gray-900 outline-none transition focus:border-sky-400 focus:bg-white" required>
                                <option>-</option>
                                <option>LECTURER</option>
                                <option>ADMIN</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="email" class="text-sm font-black uppercase tracking-wide text-gray-600">Email</label>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                class="w-full rounded-lg border border-gray-200 bg-gray-100 px-4 py-3 font-bold text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-sky-400 focus:bg-white"
                                placeholder="jone@gmail.com"
                                required
                            />
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="password" class="text-sm font-black uppercase tracking-wide text-gray-600">Password</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                class="w-full rounded-lg border border-gray-200 bg-gray-100 px-4 py-3 font-bold text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-sky-400 focus:bg-white"
                                placeholder="#######"
                                required
                            />
                        </div>

                        <input
                            type="submit"
                            name="login"
                            class="mt-2 w-full rounded-lg bg-sky-600 px-4 py-3 text-sm font-black uppercase tracking-wide text-white shadow-md transition hover:bg-sky-700 active:scale-95"
                            value="LOGIN"
                        />
                    </form>
                </div>
            </section>
        </main>
        <?php include __DIR__ . '/../Components/FooterBar.php';?>
    </body>
    <script type="module" src="../JS/main.js"></script>
</html>
