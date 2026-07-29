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

    /**
     * The sales that count as revenue: everything except reversed ones.
     *
     * @param  \Models\Sale[] $sales
     * @return \Models\Sale[] integer-keyed
     */
    public static function reportable_sales(array $sales): array
    {
        // array_filter preserves the original keys, so array_values reindexes from 0
        return array_values(array_filter($sales, fn($s) => !$s->is_reversed()));
    }

    /**
     * Bucket a list of completed sales into the aggregates the report renders.
     *
     * @param \Models\Sale[] $sales output of reportable_sales()
     */
    public static function sales_statistics(array $sales): array
    {
        $totalRevenue = 0.0;
        $totalCost    = 0.0;
        $unknownCost  = 0;
        $byMonth      = [];
        $byModel      = [];
        $byChannel    = [];

        foreach ($sales as $sale) {
            $revenue = $sale->salePrice;

            $totalRevenue += $revenue;
            $totalCost    += $sale->cost();
            if (!$sale->has_known_cost()) {
                $unknownCost++;
            }

            $month = $sale->soldAt->format('Y-m');
            $byMonth[$month]['units']   = ($byMonth[$month]['units'] ?? 0) + 1;
            $byMonth[$month]['revenue'] = ($byMonth[$month]['revenue'] ?? 0) + $revenue;

            $model = $sale->friendlyName;
            $byModel[$model]['units']   = ($byModel[$model]['units'] ?? 0) + 1;
            $byModel[$model]['revenue'] = ($byModel[$model]['revenue'] ?? 0) + $revenue;

            // trim() as well as ??: the column is a plain VARCHAR, so it can hold '' as well as NULL.
            $channel = trim((string)($sale->saleChannel ?? '')) ?: 'Unknown';
            $byChannel[$channel]['units']   = ($byChannel[$channel]['units'] ?? 0) + 1;
            $byChannel[$channel]['revenue'] = ($byChannel[$channel]['revenue'] ?? 0) + $revenue;
        }

        // 'Y-m' keys sort correctly as plain strings, so ksort gives chronological order
        ksort($byMonth);

        // uasort, not usort: keeps the model/channel name as the key while ordering by revenue
        uasort($byModel,   fn($a, $b) => $b['revenue'] <=> $a['revenue']);
        uasort($byChannel, fn($a, $b) => $b['revenue'] <=> $a['revenue']);

        $unitsSold = count($sales);

        return [
            'unitsSold'    => $unitsSold,
            'totalRevenue' => $totalRevenue,
            'totalCost'    => $totalCost,
            'totalMargin'  => $totalRevenue - $totalCost,
            'avgSalePrice' => $unitsSold > 0 ? $totalRevenue / $unitsSold : 0.0,
            // Sales missing part of their cost of goods; non-zero means the margin is optimistic.
            'unknownCost'  => $unknownCost,
            'byMonth'      => $byMonth,
            'byModel'      => $byModel,
            'byChannel'    => $byChannel,
        ];
    }

    public static function admin_dashboard_data(Database $db): array
    {
        $users = $db->get_all_users() ?? [];
        $adminCount = count(array_filter($users, fn($u) => $u->role === \Models\UserRole::Admin));

        return [
            'users' => $users,
            'adminCount' => $adminCount,
            'stats' => self::sales_statistics(self::reportable_sales($db->get_all_sales())),
        ];
    }
}
