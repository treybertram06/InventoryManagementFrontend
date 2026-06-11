create database if not exists PhoneInventory;
use PhoneInventory;


-- Device model catalog
create table if not exists DeviceModel (
    Mdl_productType         varchar(20) not null,
    Mdl_friendlyName        varchar(64) not null,
    Mdl_hasHomeButton       boolean     not null default false,
    Mdl_hasFaceId           boolean     not null default true,
    Mdl_hasActionButton     boolean     not null default false,
    Mdl_hasCameraButton     boolean     not null default false,
    Mdl_hasTelephoto        boolean     not null default false,
    Mdl_hasLidar            boolean     not null default false,

    primary key (Mdl_productType)
);


-- Users
create table if not exists User (
    Usr_userId          int             not null auto_increment,
    Usr_username        varchar(64)     not null,
    Usr_email           varchar(128)    not null,
    Usr_passwordHash    varchar(255)    not null,
    Usr_role            ENUM('admin','technician') not null default 'technician',
    Usr_createdAt       datetime        not null default current_timestamp,

    primary key (Usr_userId),
    UNIQUE key uq_username (Usr_username),
    UNIQUE key uq_email (Usr_email)
);


-- Intake batches
create table if not exists Batch (
    Bat_batchId         int      not null,
    Bat_supplierBatchId int,
    Bat_userId          int      not null,
    Bat_receivedAt      datetime not null default current_timestamp,
    Bat_notes           text,

    primary key (Bat_batchId),
    FOREIGN key (Bat_userId) REFERENCES User (Usr_userId)
);


-- Supplier manifest items
create table if not exists SupplierManifest (
    Man_manifestId          int             not null auto_increment,
    Man_batchId             int             not null,
    Man_serialNumber        varchar(32)     not null,
    Man_model               varchar(64),
    Man_color               varchar(32),
    Man_supplierGrade       varchar(8),
    Man_hasIssues           boolean,
    Man_issueDescription    text,
    Man_supplierValue       DECIMAL(8,2),
    Man_revisionPrice       DECIMAL(8,2),
    Man_batteryHealth       DECIMAL(5,1),

    primary key (Man_manifestId),
    UNIQUE key uq_manifest_serial (Man_batchId, Man_serialNumber),
    FOREIGN key (Man_batchId) REFERENCES Batch (Bat_batchId)
);


-- Physical devices
create table if not exists Device (
    Dev_serialNumber        varchar(32)     not null,
    Dev_udid                varchar(64),
    Dev_productType         varchar(20)     not null,
    Dev_modelNumber         varchar(16),
    Dev_color               varchar(32),
    Dev_regionCode          varchar(8),
    Dev_imei                varchar(20),
    Dev_wifiMac             varchar(17),
    Dev_bluetoothMac        varchar(17),
    Dev_storageGb           DECIMAL(6,2),
    Dev_batteryOriginal     boolean,
    Dev_screenOriginal      boolean,

    -- Activation / lock state at intake
    Dev_activationState     varchar(32),
    Dev_icloudLockStatus    varchar(32),
    Dev_findMyEnabled       varchar(16),
    Dev_passcodeProtected   varchar(32),
    Dev_mdmEnrolled         boolean,

    -- Check-in fields from intake form
    Dev_canTest             boolean,
    Dev_devicePassword      varchar(128),
    Dev_knownIssues         text,
    Dev_previouslyRepaired  boolean,

    Dev_batchId             int             not null,
    Dev_manifestId          int,
    Dev_intakeAt            datetime        not null default current_timestamp,

    primary key (Dev_serialNumber),
    UNIQUE key uq_udid (Dev_udid),
    FOREIGN key (Dev_productType)   REFERENCES DeviceModel (Mdl_productType),
    FOREIGN key (Dev_batchId)       REFERENCES Batch (Bat_batchId),
    FOREIGN key (Dev_manifestId)    REFERENCES SupplierManifest (Man_manifestId)
);


-- Diagnostic sessions
create table if not exists DiagnosticSession (
    Ses_sessionId               int             not null auto_increment,
    Ses_serialNumber            varchar(32)     not null,
    Ses_userId                  int,
    Ses_iosVersion              varchar(16),
    Ses_buildVersion            varchar(16),
    Ses_basebandVersion         varchar(32),

    -- Battery measurements at time of test
    Ses_batteryPct              TINYINT UNSIGNED,
    Ses_batteryHealthPct        DECIMAL(5,1),
    Ses_batteryCycles           SMALLINT UNSIGNED,
    Ses_batteryTempC            DECIMAL(4,1),
    Ses_batteryImpedanceMohm    int UNSIGNED,
    Ses_batterySerial           varchar(32),
    Ses_isCharging              boolean,

    -- Storage at time of test
    Ses_dataCapacityGb          DECIMAL(6,2),
    Ses_dataAvailableGb         DECIMAL(6,2),

    -- Result summary
    Ses_countPass               SMALLINT UNSIGNED   not null default 0,
    Ses_countFail               SMALLINT UNSIGNED   not null default 0,
    Ses_countNa                 SMALLINT UNSIGNED   not null default 0,
    Ses_countPending            SMALLINT UNSIGNED   not null default 0,

    Ses_startedAt               datetime            not null,
    Ses_endedAt                 datetime,
    Ses_elapsedSeconds          SMALLINT UNSIGNED,

    primary key (Ses_sessionId),
    FOREIGN key (Ses_serialNumber)  REFERENCES Device (Dev_serialNumber),
    FOREIGN key (Ses_userId)        REFERENCES User (Usr_userId)
);


-- Test results
create table if not exists TestResult (
    Tst_resultId    int             not null auto_increment,
    Tst_sessionId   int             not null,
    Tst_testId      varchar(32)     not null,
    Tst_testLabel   varchar(64),
    Tst_testGroup   varchar(32),
    Tst_status      ENUM('pass','fail','na','pending','skipped')  not null,
    Tst_source      ENUM('syslog','manual')                       not null default 'syslog',

    primary key (Tst_resultId),
    UNIQUE key uq_session_test (Tst_sessionId, Tst_testId),
    FOREIGN key (Tst_sessionId) REFERENCES DiagnosticSession (Ses_sessionId)
);


-- Inventory items
-- Do not store computed fields — calculate at query time:
--   total_cost = Inv_costPaid + Inv_repairCost
--   profit     = Inv_salePrice - Inv_costPaid - Inv_repairCost
create table if not exists InventoryItem (
    Inv_serialNumber        varchar(32)     not null,

    Inv_grade               ENUM('A','B','C','D','Parts','Scrap') not null default 'C',
    Inv_conditionNotes      text,
    Inv_repairsNeededDone   text,

    Inv_costPaid            DECIMAL(8,2),
    Inv_repairCost          DECIMAL(8,2)    not null default 0.00,
    Inv_b2bFloorPrice       DECIMAL(8,2),
    Inv_b2cFloorPrice       DECIMAL(8,2),

    Inv_status              ENUM('in_stock','listed','reserved','sold','returned','scrapped') not null default 'in_stock',
    Inv_listedAt            datetime,
    Inv_reservedAt          datetime,
    Inv_soldAt              datetime,
    Inv_salePrice           DECIMAL(8,2),
    Inv_saleChannel         varchar(64),
    Inv_buyerInfo           varchar(256),

    Inv_canonicalSessionId  int,

    Inv_createdAt           datetime        not null default current_timestamp,
    Inv_updatedAt           datetime        not null default current_timestamp on update current_timestamp,

    primary key (Inv_serialNumber),
    FOREIGN key (Inv_serialNumber)          REFERENCES Device (Dev_serialNumber),
    FOREIGN key (Inv_canonicalSessionId)    REFERENCES DiagnosticSession (Ses_sessionId)
);


-- =============================================================================
-- Indexes
-- =============================================================================

create INDEX idx_session_serial    ON DiagnosticSession (Ses_serialNumber);
create INDEX idx_inventory_status  ON InventoryItem (Inv_status);
create INDEX idx_inventory_grade   ON InventoryItem (Inv_grade);
create INDEX idx_device_batch      ON Device (Dev_batchId);
create INDEX idx_result_test_id    ON TestResult (Tst_testId, Tst_status);
create INDEX idx_manifest_serial   ON SupplierManifest (Man_serialNumber);