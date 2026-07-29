<?php
namespace Core;

use Models\User;

class Common {
    // If this gets too large, it can be split up into more specific groups
    public static function get_uri()
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    }

    public static function current_user(Database $db): ?User
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }
        return $db->get_user_by_id($_SESSION['user_id']);
    }

    public static function require_login(Database $db): User
    {
        $user = self::current_user($db);
        if (!$user) {
            header('Location: /login');
            exit;
        }
        return $user;
    }

    public static function require_admin(Database $db): User
    {
        $user = self::require_login($db);
        if ($user->role !== \Models\UserRole::Admin) {
            header('Location: /inventory');
            exit;
        }
        return $user;
    }

    public static function uri_is($value)
    {
        return get_uri() == $value;
    }

    public static function println($in)
    {
        echo "$in <br>";
    }

    public static function console_log($value)
    {
        echo '<script>console.log(' . json_encode($value) . ');</script>';
    }

    public static function log_info($value)
    {
        file_put_contents('php://stdout', "[INFO] $value" . PHP_EOL);
    }

    public static function log_error($value)
    {
        error_log("[ERROR] $value");
    }

    public static function draw_svg($svg, $width, $height = 0)
    {
        $height = $height ?: $width;
        echo str_replace('<svg', '<svg class="h-' . $height . ' w-' . $width . '"', $svg);
    }

    public static function admin_dashboard_data(Database $db): array
    {
        $users = $db->get_all_users() ?? [];
        $adminCount = count(array_filter($users, fn($u) => $u->role === \Models\UserRole::Admin));

        return [
            'users' => $users,
            'adminCount' => $adminCount,
        ];
    }
}
