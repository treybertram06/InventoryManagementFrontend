<?php
namespace Models;

use DateTime;

class Sale {
    public int $id;
    public string $serialNumber;
    public ?string $imei;
    public string $productType;
    public string $friendlyName;

    public string $technician;
    public float $salePrice;
    public ?string $saleChannel;
    public ?string $buyerInfo;
    public DateTime $soldAt;

    public ?DateTime $reversedAt;
    public ?string $reversedBy;
    public ?string $notes;
    public DateTime $createdAt;

    // Read from the CURRENT manifest/inventory rows, not frozen as at sold_at.
    public ?float $costPaid;
    public float $repairCost;

    // False when there is no inventory_item row, i.e. $repairCost's 0.0 means "unknown".
    public bool $hasInventoryRow;

    public static function from_row(array $row): Sale {
        $sale = new Sale();

        $sale->id = (int)$row['id'];
        $sale->serialNumber = $row['serial_number'];
        $sale->imei = $row['imei'];
        $sale->productType = $row['product_type'];
        $sale->friendlyName = $row['friendly_name'];

        $sale->technician = $row['technician'];
        $sale->salePrice = (float)$row['sale_price'];
        $sale->saleChannel = $row['sale_channel'];
        $sale->buyerInfo = $row['buyer_info'];
        $sale->soldAt = new DateTime($row['sold_at']);

        $sale->reversedAt = isset($row['reversed_at']) ? new DateTime($row['reversed_at']) : null;
        $sale->reversedBy = $row['reversed_by'] ?? null;
        $sale->notes = $row['notes'] ?? null;
        $sale->createdAt = new DateTime($row['created_at']);

        $sale->costPaid = isset($row['cost_paid']) ? (float)$row['cost_paid'] : null;
        $sale->repairCost = (float)($row['repair_cost'] ?? 0);
        $sale->hasInventoryRow = (bool)($row['has_inventory_row'] ?? false);

        return $sale;
    }

    public function is_reversed(): bool {
        return $this->reversedAt !== null;
    }

    /** Supplier price plus repairs; an unknown half counts as 0, so check has_known_cost(). */
    public function cost(): float {
        return ($this->costPaid ?? 0) + $this->repairCost;
    }

    public function margin(): float {
        return $this->salePrice - $this->cost();
    }

    /** True only when both halves of cost() are real figures, not stand-ins for missing data. */
    public function has_known_cost(): bool {
        return $this->costPaid !== null && $this->hasInventoryRow;
    }
}
