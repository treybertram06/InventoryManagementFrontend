<header class="bg-surface dark:bg-surface-dark">
    <div class="mx-auto flex h-16 max-w-7xl items-center gap-8 px-4 sm:px-6 lg:px-8">
        <a class="block text-primary dark:text-primary-light" href="/">
            <span class="sr-only">Home</span>
            <span class="block h-8 w-auto">
                <?php
                    draw_svg(file_get_contents(__DIR__ . '/../../public/icons/warehouse-solid-full.svg'), 8);
                ?>
            </span>
        </a>

        <div class="flex flex-1 items-center justify-end md:justify-between">
            <nav aria-label="Global" class="hidden md:block">
                <ul class="flex items-center gap-6 text-sm">

                    <li>
                        <a class="text-text-muted transition hover:text-text-muted/75 dark:text-white dark:hover:text-white/75" href="/inventory">
                            Inventory
                        </a>
                    </li>

                    <li>
                        <a class="text-text-muted transition hover:text-text-muted/75 dark:text-white dark:hover:text-white/75" href="/intake">
                            Intake
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="flex items-center gap-4">
                <div class="sm:flex sm:gap-4">
                    <a class="block rounded-md bg-primary px-5 py-2.5 text-sm font-medium text-white transition hover:bg-primary-hover" href="#">
                        Login
                    </a>

                    <a class="hidden rounded-md bg-surface-muted px-5 py-2.5 text-sm font-medium text-primary transition hover:text-primary-hover sm:block dark:bg-surface-muted-dark dark:text-white dark:hover:text-white/75" href="#">
                        Register
                    </a>
                </div>

                <button class="block rounded-sm bg-surface-muted p-2.5 text-text-muted transition hover:text-text-muted/75 md:hidden dark:bg-surface-muted-dark dark:text-white dark:hover:text-white/75">
                    <span class="sr-only">Toggle menu</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</header>

