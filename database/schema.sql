CREATE DATABASE IF NOT EXISTS phone_inventory
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE phone_inventory;

CREATE TABLE IF NOT EXISTS device_model ( -- Lazily populated when a new device is identified through testing
    product_type        VARCHAR(20)     NOT NULL, -- Real device identifier, e.g. iPhone17,2
    friendly_name       VARCHAR(64)     NOT NULL, -- Human-readable name, e.g. iPhone 16 Pro Max
    has_home_button     BOOLEAN         NOT NULL DEFAULT FALSE,
    has_face_id         BOOLEAN         NOT NULL DEFAULT TRUE, -- Hardware-presence flag, could be determined from
    has_action_button   BOOLEAN         NOT NULL DEFAULT FALSE, -- the device model and a lookup table but this is easy
    has_camera_button   BOOLEAN         NOT NULL DEFAULT FALSE,
    has_telephoto       BOOLEAN         NOT NULL DEFAULT FALSE,
    has_lidar           BOOLEAN         NOT NULL DEFAULT FALSE,

    PRIMARY KEY (product_type)
);

CREATE TABLE IF NOT EXISTS batch (
    id                  INT             NOT NULL AUTO_INCREMENT,
    batch_number        VARCHAR(64),                    -- NULL if not provided at intake
    supplier_batch_id   VARCHAR(64),
    technician          VARCHAR(64), -- Technician name who processed the batch
    location            VARCHAR(128), -- Not necessary yet
    received_at         DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    notes               TEXT, -- Any notes that may be needed

    PRIMARY KEY (id),
    UNIQUE KEY uq_batch_number (batch_number)
);

CREATE TABLE IF NOT EXISTS supplier_manifest_item (
    id                  INT             NOT NULL AUTO_INCREMENT,
    batch_id            INT             NOT NULL,
    imei                VARCHAR(20), -- supplier's primary device identifier
    serial_number       VARCHAR(32), -- populated when device is tested
    supplier_item_id    VARCHAR(64), -- supplier's internal batch item ID
    model               VARCHAR(64), -- supplier's informal model text
    color               VARCHAR(32),
    supplier_grade      VARCHAR(8),
    has_issues          BOOLEAN,
    issue_description   TEXT,
    supplier_value      DECIMAL(8,2),-- their starting price
    revision_price      DECIMAL(8,2),-- adjusted price after testing
    battery_health      DECIMAL(5,1),

    PRIMARY KEY (id),
    UNIQUE KEY uq_manifest_imei (batch_id, imei),
    FOREIGN KEY (batch_id) REFERENCES batch (id)
);

CREATE TABLE IF NOT EXISTS device (
    serial_number               VARCHAR(32)     NOT NULL,
    udid                        VARCHAR(64),
    product_type                VARCHAR(20)     NOT NULL,
    model_number                VARCHAR(16),
    color                       VARCHAR(32),
    region_code                 VARCHAR(8),
    imei                        VARCHAR(20), -- primary IMEI for stolen-device checks and secondary identification
    wifi_mac                    VARCHAR(17),
    bluetooth_mac               VARCHAR(17),
    imei2                       VARCHAR(20),
    meid                        VARCHAR(20),
    iccid                       VARCHAR(22),
    baseband_serial             VARCHAR(32),
    hardware_model              VARCHAR(16),
    chip_id                     VARCHAR(16),
    cpu_architecture            VARCHAR(16),
    storage_gb                  DECIMAL(6,2),
    battery_original            BOOLEAN,
    screen_original             BOOLEAN,

    -- Activation / lock state at intake
    activation_state            VARCHAR(32),
    icloud_lock_status          VARCHAR(32),
    find_my_enabled             VARCHAR(16),
    passcode_protected          VARCHAR(32),
    mdm_enrolled                BOOLEAN,

    -- Check-in fields from intake form
    can_test                    BOOLEAN,
    device_password             VARCHAR(128),
    known_issues                TEXT,
    previously_repaired         BOOLEAN,

    batch_id                    INT             NOT NULL,
    supplier_manifest_item_id   INT,
    intake_at                   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (serial_number),
    UNIQUE KEY uq_udid (udid),
    FOREIGN KEY (product_type)              REFERENCES device_model (product_type),
    FOREIGN KEY (batch_id)                  REFERENCES batch (id),
    FOREIGN KEY (supplier_manifest_item_id) REFERENCES supplier_manifest_item (id)
);

CREATE TABLE IF NOT EXISTS diagnostic_session (
    id                      INT             NOT NULL AUTO_INCREMENT,
    serial_number           VARCHAR(32)     NOT NULL,
    technician              VARCHAR(64),
    ios_version             VARCHAR(16),
    build_version           VARCHAR(16),
    baseband_version        VARCHAR(32),

    -- Battery measurements at time of test
    battery_pct             TINYINT UNSIGNED,
    battery_health_pct      DECIMAL(5,1),
    battery_cycles          SMALLINT UNSIGNED,
    battery_temp_c          DECIMAL(4,1),
    battery_impedance_mohm  INT UNSIGNED,
    battery_serial          VARCHAR(32),
    is_charging             BOOLEAN, -- Should always be true (device must be plugged in to test)

    -- Storage at time of test
    data_capacity_gb        DECIMAL(6,2),
    data_available_gb       DECIMAL(6,2),

    -- Result summary
    count_pass              SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
    count_fail              SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
    count_na                SMALLINT UNSIGNED   NOT NULL DEFAULT 0,
    count_pending           SMALLINT UNSIGNED   NOT NULL DEFAULT 0,

    started_at              DATETIME            NOT NULL,
    ended_at                DATETIME,
    elapsed_seconds         SMALLINT UNSIGNED,

    PRIMARY KEY (id),
    FOREIGN KEY (serial_number) REFERENCES device (serial_number)
);

CREATE TABLE IF NOT EXISTS test_result (
    id          INT             NOT NULL AUTO_INCREMENT,
    session_id  INT             NOT NULL,
    test_id     VARCHAR(32)     NOT NULL,
    test_label  VARCHAR(64),
    test_group  VARCHAR(32),
    status      ENUM('pass','fail','na','pending','skipped')  NOT NULL,
    source      ENUM('syslog','manual')                       NOT NULL DEFAULT 'syslog',

    PRIMARY KEY (id),
    UNIQUE KEY uq_session_test (session_id, test_id),
    FOREIGN KEY (session_id) REFERENCES diagnostic_session (id)
);

CREATE TABLE IF NOT EXISTS inventory_item (
    serial_number           VARCHAR(32)     NOT NULL,

    grade                   ENUM('A','B','C','D','Parts','Scrap') NOT NULL DEFAULT 'C',
    condition_notes         TEXT,
    repairs_needed_done     TEXT,

    cost_paid               DECIMAL(8,2),
    repair_cost             DECIMAL(8,2)    NOT NULL DEFAULT 0.00,
    b2b_floor_price         DECIMAL(8,2), -- Floor price is the minimum price a device may be sold for
    b2c_floor_price         DECIMAL(8,2),

    status                  ENUM('in_stock','listed','reserved','sold','returned','scrapped')
                                            NOT NULL DEFAULT 'in_stock',
    listed_at               DATETIME,
    reserved_at             DATETIME,
    sold_at                 DATETIME,
    sale_price              DECIMAL(8,2),
    sale_channel            VARCHAR(64),
    buyer_info              VARCHAR(256),

    canonical_session_id    INT,

    created_at              DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                                     ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (serial_number),
    FOREIGN KEY (serial_number)         REFERENCES device (serial_number),
    FOREIGN KEY (canonical_session_id)  REFERENCES diagnostic_session (id)
);

CREATE INDEX idx_session_serial   ON diagnostic_session (serial_number);
CREATE INDEX idx_inventory_status  ON inventory_item (status);
CREATE INDEX idx_inventory_grade   ON inventory_item (grade);
CREATE INDEX idx_device_batch      ON device (batch_id);
CREATE INDEX idx_result_test_id    ON test_result (test_id, status);
CREATE INDEX idx_manifest_serial   ON supplier_manifest_item (serial_number);

-- Migrations from schema edits
ALTER TABLE device ADD COLUMN imei2            VARCHAR(20);
ALTER TABLE device ADD COLUMN meid             VARCHAR(20);
ALTER TABLE device ADD COLUMN iccid            VARCHAR(22);
ALTER TABLE device ADD COLUMN baseband_serial  VARCHAR(32);
ALTER TABLE device ADD COLUMN hardware_model   VARCHAR(16);
ALTER TABLE device ADD COLUMN chip_id          VARCHAR(16);
ALTER TABLE device ADD COLUMN cpu_architecture VARCHAR(16);

ALTER TABLE supplier_manifest_item ADD COLUMN imei             VARCHAR(20);
ALTER TABLE supplier_manifest_item ADD COLUMN supplier_item_id VARCHAR(64);
ALTER TABLE supplier_manifest_item MODIFY COLUMN serial_number VARCHAR(32) NULL;
ALTER TABLE supplier_manifest_item ADD UNIQUE KEY uq_manifest_imei (batch_id, imei);
ALTER TABLE supplier_manifest_item DROP INDEX uq_manifest_serial;
