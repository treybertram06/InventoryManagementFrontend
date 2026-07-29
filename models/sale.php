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

        return $sale;
    }

    public function is_reversed(): bool {
        return $this->reversedAt !== null;
    }
}