-- Migration: bring production phone_inventory in line with database/schema.sql
-- Generated 2026-08-06. Backed up production to database/backups/ before applying.

-- 1. device_model: add brand column (backfilled below, then locked to NOT NULL with no default)
ALTER TABLE device_model
    ADD COLUMN brand VARCHAR(32) NOT NULL DEFAULT '' AFTER product_type;

UPDATE device_model SET brand = 'Apple'   WHERE product_type LIKE 'iPhone%';
UPDATE device_model SET brand = 'Samsung' WHERE product_type LIKE 'SM-%';
UPDATE device_model SET brand = 'Unknown' WHERE product_type = 'Phone';

ALTER TABLE device_model
    ALTER COLUMN brand DROP DEFAULT;

-- 2. device: drop columns not present in schema.sql (chip_id/cpu_architecture data confirmed disposable)
ALTER TABLE device
    DROP COLUMN chip_id,
    DROP COLUMN cpu_architecture;

-- 3. inventory_item: move sale fields out to the new `sale` table
ALTER TABLE inventory_item
    ADD COLUMN listing_channel VARCHAR(64) NULL AFTER listed_at,
    ADD COLUMN reservation_notes VARCHAR(256) NULL AFTER reserved_at,
    DROP COLUMN sold_at,
    DROP COLUMN sale_price,
    DROP COLUMN sale_channel,
    DROP COLUMN buyer_info;

-- 4. sale: new table
CREATE TABLE IF NOT EXISTS sale (
    id                  INT             NOT NULL AUTO_INCREMENT,
    serial_number       VARCHAR(32)     NOT NULL,
    technician_id       INT             NOT NULL,
    sale_price          DECIMAL(8,2)    NOT NULL,
    sale_channel        VARCHAR(64),
    buyer_info          VARCHAR(256),
    sold_at             DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    reversed_at         DATETIME,
    reversed_by_id      INT,
    notes               TEXT,
    created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    FOREIGN KEY (serial_number) REFERENCES device (serial_number),
    FOREIGN KEY (technician_id)  REFERENCES user (id),
    FOREIGN KEY (reversed_by_id) REFERENCES user (id)
);

CREATE INDEX idx_sale_serial     ON sale (serial_number);
CREATE INDEX idx_sale_sold_at    ON sale (sold_at);
CREATE INDEX idx_sale_technician ON sale (technician_id);