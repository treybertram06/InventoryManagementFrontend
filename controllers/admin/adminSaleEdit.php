<?php

function blank($value): bool {
    return trim((string)($value ?? '')) === '';
}

function handle_sale_edit(Core\Database $db, Models\Sale $sale): void {

    $values = $_POST;
    $errors = [];

    if (blank($values['sale_price'] ?? null)) {
        $errors[] = "Sale price is required.";
    } elseif ((float)$values['sale_price'] <= 0) {
        $errors[] = "Sale price must be greater than zero.";
    }

    if (blank($values['sale_channel'] ?? null)) {
        $errors[] = "Please select a sale channel.";
    }

    $soldAt = $sale->soldAt->format('Y-m-d H:i:s');
    if (blank($values['sold_at'] ?? null)) {
        $errors[] = "Sale date is required.";
    } else {
        $parsed = DateTime::createFromFormat('Y-m-d\TH:i', $values['sold_at']);
        if (!$parsed) {
            $errors[] = "Please enter a valid sale date.";
        } elseif ($parsed > new DateTime()) {
            $errors[] = "Sale date cannot be in the future.";
        } else {
            $soldAt = $parsed->format('Y-m-d H:i:s');
        }
    }

    if (!empty($errors)) {
        view('adminSaleEdit.view.php', [
            'sale' => $sale,
            'errors' => $errors,
            'values' => $values
        ]);
        return;
    }

    $db->update_sale([
        'id' => $sale->id,
        'sale_price' => (float)$values['sale_price'],
        'sale_channel' => $values['sale_channel'],
        'buyer_info' => trim($values['buyer_info'] ?? ''),
        'sold_at' => $soldAt,
        'notes' => nullable_str($values['notes'] ?? null),
    ]);

    header('Location: /sales-history');
    exit;
}

function nullable_str($value): ?string {
    return blank($value) ? null : $value;
}

Core\Common::require_admin($db);

$saleId = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
if ($saleId === 0) {
    header('Location: /sales-history');
    exit;
}

$sale = $db->get_sale_by_id($saleId);
if (!$sale) {
    header('Location: /sales-history');
    exit;
}

if ($sale->is_reversed()) {
    header('Location: /sales-history');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    view('adminSaleEdit.view.php', ['sale' => $sale]);
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    handle_sale_edit($db, $sale);
}
