<?php

class Device {
    // Identity & specs
    public string $serialNumber;
    public ?string $imei;
    public string $productType;
    public string $friendlyName;
    public ?string $modelNumber;
    public ?string $color;
    public ?string $regionCode;
    public ?float $storageGb;

    // Condition
    public string $grade;
    public ?string $conditionNotes;
    public ?string $repairsNeededDone;
    public ?bool $batteryOriginal;
    public ?bool $screenOriginal;
    public ?bool $previouslyRepaired;
    public ?string $knownIssues;
    public ?float $batteryHealthPct;

    // Commercial
    public string $status;
    public ?float $costPaid;
    public float $repairCost;
    public ?float $b2bFloorPrice;
    public ?float $b2cFloorPrice;
    public ?float $salePrice;
    public ?string $saleChannel;

    // Provenance
    public ?string $batchNumber;
    public ?string $technician;
    public DateTime $intakeAt;
    public DateTime $receivedAt;

    // Last diagnostic summary
    public ?int $countPass;
    public ?int $countFail;
    public ?int $countNa;
    public ?int $countPending;

    public static function from_row(array $row): Device {
        $device = new Device();

        $device->serialNumber = $row['serial_number'];
        $device->imei = $row['imei'];
        $device->productType = $row['product_type'];
        $device->friendlyName = $row['friendly_name'];
        $device->modelNumber = $row['model_number'];
        $device->color = $row['color'];
        $device->regionCode = $row['region_code'];
        $device->storageGb = isset($row['storage_gb']) ? (float)$row['storage_gb'] : null;

        $device->grade = $row['grade'] ?? 'C';
        $device->conditionNotes = $row['condition_notes'];
        $device->repairsNeededDone = $row['repairs_needed_done'];
        $device->batteryOriginal = isset($row['battery_original']) ? (bool)$row['battery_original'] : null;
        $device->screenOriginal = isset($row['screen_original']) ? (bool)$row['screen_original'] : null;
        $device->previouslyRepaired = isset($row['previously_repaired']) ? (bool)$row['previously_repaired'] : null;
        $device->knownIssues = $row['known_issues'];
        $device->batteryHealthPct = isset($row['battery_health_pct']) ? (float)$row['battery_health_pct'] : null;

        $device->status = $row['status'] ?? 'in_stock';
        $device->costPaid = isset($row['cost_paid']) ? (float)$row['cost_paid'] : null;
        $device->repairCost = (float)($row['repair_cost'] ?? 0);
        $device->b2bFloorPrice = isset($row['b2b_floor_price']) ? (float)$row['b2b_floor_price'] : null;
        $device->b2cFloorPrice = isset($row['b2c_floor_price']) ? (float)$row['b2c_floor_price'] : null;
        $device->salePrice = isset($row['sale_price']) ? (float)$row['sale_price'] : null;
        $device->saleChannel = $row['sale_channel'];

        $device->batchNumber = $row['batch_number'];
        $device->technician = $row['technician'];
        $device->intakeAt = new DateTime($row['intake_at']);
        $device->receivedAt = new DateTime($row['received_at']);

        $device->countPass = isset($row['count_pass']) ? (int)$row['count_pass'] : null;
        $device->countFail = isset($row['count_fail']) ? (int)$row['count_fail'] : null;
        $device->countNa = isset($row['count_na']) ? (int)$row['count_na'] : null;
        $device->countPending = isset($row['count_pending']) ? (int)$row['count_pending'] : null;

        return $device;
    }
}