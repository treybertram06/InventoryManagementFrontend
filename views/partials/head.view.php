<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php

        $theme = CONFIG['theme'] ?? 'teal';
        switch ($theme) {
            case 'indigo':
                $colors = '--color-primary: #4f46e5; --color-primary-hover: #4338ca; --color-primary-light: #a5b4fc; --color-surface-dark: #1e1b4b; --color-surface-muted-dark: #2e2a6e; --color-background: #eef2ff; --color-background-dark: #14123a;';
                break;
            case 'slate':
                $colors = '--color-primary: #2563eb; --color-primary-hover: #1d4ed8; --color-primary-light: #93c5fd; --color-surface-dark: #0f172a; --color-surface-muted-dark: #1e2d47; --color-background: #f8fafc; --color-background-dark: #0b1220;';
                break;
            case 'rose':
                $colors = '--color-primary: #e11d48; --color-primary-hover: #be123c; --color-primary-light: #fda4af; --color-surface-dark: #1f0a10; --color-surface-muted-dark: #3b1220; --color-background: #fff1f2; --color-background-dark: #180608;';
                break;
            case 'mono':
                $colors = '--color-primary: #171717; --color-primary-hover: #404040; --color-primary-light: #a3a3a3; --color-surface-dark: #0a0a0a; --color-surface-muted-dark: #262626; --color-background: #fafafa; --color-background-dark: #171717;';
                break;

            default: // teal
                $colors = '--color-primary: #0d9488; --color-primary-hover: #0f766e; --color-primary-light: #5eead4; --color-surface-dark: #0f2b29; --color-surface-muted-dark: #1a3f3c; --color-background: #f0fdfa; --color-background-dark: #071916;';
        }


        ?>
        @theme {
            <?= $colors ?>
            --color-surface: #ffffff;
            --color-surface-muted: #f3f4f6;
            --color-text: #111827;
            --color-text-muted: #6b7280;
        }
    </style>
</head>