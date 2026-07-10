<section class="flex min-h-[calc(100vh-4rem)] items-center justify-center p-8">
    <div class="w-full max-w-md rounded-md border border-text-muted/20 bg-surface p-8 shadow-sm dark:bg-surface-dark">
        <h1 class="text-2xl font-semibold text-text dark:text-white">Create Account</h1>
        <p class="mt-2 text-sm text-text-muted dark:text-white/70">Enter your details to create a new account</p>

        <form action="#" class="mt-6 space-y-4">
            <div>
                <label for="register-username" class="mb-1.5 block text-sm font-medium text-text dark:text-white">Username</label>
                <input
                    id="register-username"
                    type="text"
                    name="username"
                    placeholder="jsmith"
                    class="w-full rounded-md border border-text-muted/25 bg-surface px-3 py-2 text-sm text-text placeholder:text-text-muted focus:border-primary focus:outline-none dark:bg-surface-dark dark:text-white"
                />
            </div>

            <div>
                <label for="register-email" class="mb-1.5 block text-sm font-medium text-text dark:text-white">Email</label>
                <input
                    id="register-email"
                    type="email"
                    name="email"
                    placeholder="name@mail.com"
                    class="w-full rounded-md border border-text-muted/25 bg-surface px-3 py-2 text-sm text-text placeholder:text-text-muted focus:border-primary focus:outline-none dark:bg-surface-dark dark:text-white"
                />
            </div>

            <div>
                <label for="register-password" class="mb-1.5 block text-sm font-medium text-text dark:text-white">Password</label>
                <input
                    id="register-password"
                    type="password"
                    name="password"
                    placeholder="********"
                    class="w-full rounded-md border border-text-muted/25 bg-surface px-3 py-2 text-sm text-text placeholder:text-text-muted focus:border-primary focus:outline-none dark:bg-surface-dark dark:text-white"
                />
            </div>

            <div>
                <label for="register-password-confirmation" class="mb-1.5 block text-sm font-medium text-text dark:text-white">Confirm Password</label>
                <input
                    id="register-password-confirmation"
                    type="password"
                    name="password_confirmation"
                    placeholder="********"
                    class="w-full rounded-md border border-text-muted/25 bg-surface px-3 py-2 text-sm text-text placeholder:text-text-muted focus:border-primary focus:outline-none dark:bg-surface-dark dark:text-white"
                />
            </div>

            <button
                type="button"
                class="w-full rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary-hover"
            >
                Create Account
            </button>

            <p class="text-center text-sm text-text-muted dark:text-white/70">
                Already have an account?
                <a href="/login" class="font-medium text-primary hover:text-primary-hover dark:text-primary-light">Sign in</a>
            </p>
        </form>
    </div>
</section>