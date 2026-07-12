<section class="flex min-h-[calc(100vh-4rem)] items-center justify-center p-8">
    <div class="w-full max-w-md rounded-md border border-text-muted/20 bg-surface p-8 shadow-sm dark:bg-surface-dark">
        <h1 class="text-2xl font-semibold text-text dark:text-white">Sign In</h1>
        <p class="mt-2 text-sm text-text-muted dark:text-white/70">Enter your email and password to sign in</p>

        <?php if (!empty($errors)): ?>
            <div class="mt-4 rounded-md border border-red-500/30 bg-red-500/10 p-3">
                <ul class="list-inside list-disc text-sm text-red-600 dark:text-red-400">
                    <?php foreach ($errors as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/login" class="mt-6 space-y-4">
            <div>
                <label for="login-identifier" class="mb-1.5 block text-sm font-medium text-text dark:text-white">Username or Email</label>
                <input
                    id="login-identifier"
                    type="text"
                    name="identifier"
                    placeholder="jsmith / name@mail.com"
                    value="<?= htmlspecialchars($identifier ?? '') ?>"
                    class="w-full rounded-md border border-text-muted/25 bg-surface px-3 py-2 text-sm text-text placeholder:text-text-muted focus:border-primary focus:outline-none dark:bg-surface-dark dark:text-white"
                />
            </div>

            <div>
                <label for="login-password" class="mb-1.5 block text-sm font-medium text-text dark:text-white">Password</label>
                <input
                    id="login-password"
                    type="password"
                    name="password"
                    placeholder="********"
                    class="w-full rounded-md border border-text-muted/25 bg-surface px-3 py-2 text-sm text-text placeholder:text-text-muted focus:border-primary focus:outline-none dark:bg-surface-dark dark:text-white"
                />
            </div>

            <button
                type="submit"
                class="w-full rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary-hover"
            >
                Sign In
            </button>

            <div class="flex justify-end">
                <a href="#" class="text-sm font-medium text-primary hover:text-primary-hover dark:text-primary-light">Forgot password?</a>
            </div>

            <p class="text-center text-sm text-text-muted dark:text-white/70">
                Not registered?
                <a href="/register" class="font-medium text-primary hover:text-primary-hover dark:text-primary-light">Create account</a>
            </p>
        </form>
    </div>
</section>