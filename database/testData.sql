-- Test data for PhoneInventory database, AI generated
use PhoneInventory;

-- ============================================================================
-- DeviceModel: Apple device catalog
-- ============================================================================
insert into DeviceModel (Mdl_productType, Mdl_friendlyName, Mdl_hasHomeButton, Mdl_hasFaceId, Mdl_hasActionButton, Mdl_hasCameraButton, Mdl_hasTelephoto, Mdl_hasLidar) values
('iPhone15,2', 'iPhone 15', false, true, true, true, false, false),
('iPhone15,3', 'iPhone 15 Plus', false, true, true, true, false, false),
('iPhone15,4', 'iPhone 15 Pro', false, true, true, true, true, true),
('iPhone15,5', 'iPhone 15 Pro Max', false, true, true, true, true, true),
('iPhone14,7', 'iPhone 14', false, true, true, true, false, false),
('iPhone14,8', 'iPhone 14 Plus', false, true, true, true, false, false),
('iPhone14,2', 'iPhone 14 Pro', false, true, true, true, true, true),
('iPhone14,3', 'iPhone 14 Pro Max', false, true, true, true, true, true),
('iPhone13,2', 'iPhone 13', false, true, true, true, false, false),
('iPhone13,3', 'iPhone 13 mini', false, true, true, true, false, false),
('iPhone13,4', 'iPhone 13 Pro', false, true, true, true, true, true),
('iPhone13,1', 'iPhone 13 Pro Max', false, true, true, true, true, true),
('iPhone12,1', 'iPhone 12', false, true, true, true, false, false),
('iPhone12,3', 'iPhone 12 Pro', false, true, true, true, true, false);

-- ============================================================================
-- User: Staff members
-- ============================================================================
insert into User (Usr_username, Usr_email, Usr_passwordHash, Usr_role) values
('admin_user', 'admin@inventory.local', '$2y$10$PgZe1VU.GhWsDQ7G3uXH5OkT5nYn2Vb0X9K5Q8Z7C9W0E7Z4X1Y2Z', 'admin'),
('tech_john', 'john.tech@inventory.local', '$2y$10$PgZe1VU.GhWsDQ7G3uXH5OkT5nYn2Vb0X9K5Q8Z7C9W0E7Z4X1Y2Z', 'technician'),
('tech_sarah', 'sarah.tech@inventory.local', '$2y$10$PgZe1VU.GhWsDQ7G3uXH5OkT5nYn2Vb0X9K5Q8Z7C9W0E7Z4X1Y2Z', 'technician'),
('tech_mike', 'mike.tech@inventory.local', '$2y$10$PgZe1VU.GhWsDQ7G3uXH5OkT5nYn2Vb0X9K5Q8Z7C9W0E7Z4X1Y2Z', 'technician');

-- ============================================================================
-- Batch: Intake batches from suppliers
-- ============================================================================
insert into Batch (Bat_batchId, Bat_supplierBatchId, Bat_userId, Bat_receivedAt, Bat_notes) values
(1, 2001, 1, '2024-01-15 09:00:00', 'First batch from primary supplier'),
(2, 2002, 2, '2024-01-20 14:30:00', 'Mixed condition lot'),
(3, 2003, 3, '2024-02-01 10:15:00', 'Grade A devices only'),
(4, 2004, 2, '2024-02-10 11:45:00', 'Bulk intake - 50 units'),
(5, 2005, 4, '2024-02-25 13:20:00', 'Damaged units - will require repair');

-- ============================================================================
-- SupplierManifest: Items in intake batches
-- ============================================================================
insert into SupplierManifest (Man_batchId, Man_serialNumber, Man_model, Man_color, Man_supplierGrade, Man_hasIssues, Man_issueDescription, Man_supplierValue, Man_revisionPrice, Man_batteryHealth) values
(1, 'SN001001', 'iPhone 15 Pro', 'Space Black', 'A', false, null, 999.99, 949.99, 100.0),
(1, 'SN001002', 'iPhone 15 Pro', 'Titanium Blue', 'A', false, null, 999.99, 949.99, 98.5),
(1, 'SN001003', 'iPhone 15', 'Black', 'B', true, 'Small crack on back glass', 599.99, 499.99, 92.0),
(1, 'SN001004', 'iPhone 14 Pro Max', 'Gold', 'A', false, null, 899.99, 849.99, 97.0),
(2, 'SN002001', 'iPhone 15 Plus', 'White', 'B', false, null, 699.99, 649.99, 95.0),
(2, 'SN002002', 'iPhone 14', 'Red', 'C', true, 'Moderate battery degradation', 399.99, 349.99, 78.0),
(2, 'SN002003', 'iPhone 13', 'Blue', 'C', true, 'Camera has dust inside', 349.99, 299.99, 85.0),
(2, 'SN002004', 'iPhone 13 mini', 'Green', 'D', true, 'Screen burn-in visible', 249.99, 149.99, 65.0),
(3, 'SN003001', 'iPhone 15 Pro Max', 'Space Black', 'A', false, null, 1099.99, 1049.99, 99.0),
(3, 'SN003002', 'iPhone 15 Pro', 'Natural Titanium', 'A', false, null, 999.99, 949.99, 100.0),
(3, 'SN003003', 'iPhone 15 Pro', 'Space Black', 'A', false, null, 999.99, 949.99, 99.5),
(3, 'SN003004', 'iPhone 14 Pro', 'Silver', 'A', false, null, 799.99, 749.99, 98.0),
(4, 'SN004001', 'iPhone 15', 'Black', 'B', false, null, 599.99, 549.99, 94.0),
(4, 'SN004002', 'iPhone 15', 'White', 'B', false, null, 599.99, 549.99, 93.5),
(4, 'SN004003', 'iPhone 14', 'Midnight', 'C', true, 'Minor scratch on frame', 399.99, 349.99, 88.0),
(5, 'SN005001', 'iPhone 13', 'Sierra Blue', 'D', true, 'Cracked screen, battery dead', 299.99, 99.99, 40.0),
(5, 'SN005002', 'iPhone 12', 'Purple', 'D', true, 'Liquid damage indicators', 299.99, 99.99, 35.0);

-- ============================================================================
-- Device: Physical devices (with all intake details)
-- ============================================================================
insert into Device (Dev_serialNumber, Dev_udid, Dev_productType, Dev_modelNumber, Dev_color, Dev_regionCode, Dev_imei, Dev_wifiMac, Dev_bluetoothMac, Dev_storageGb, Dev_batteryOriginal, Dev_screenOriginal, Dev_activationState, Dev_icloudLockStatus, Dev_findMyEnabled, Dev_passcodeProtected, Dev_mdmEnrolled, Dev_canTest, Dev_devicePassword, Dev_knownIssues, Dev_previouslyRepaired, Dev_batchId, Dev_manifestId) values

('SN001001', 'UDID001001', 'iPhone15,4', 'A3093', 'Space Black', 'US', '358674010234567', '00:1A:2B:3C:4D:5E', '00:1E:2A:3B:4C:5D', 256, true, true, 'Activation Locked', 'Locked', 'Enabled', 'Yes', false, true, null, 'Excellent condition', false, 1, 1),
('SN001002', 'UDID001002', 'iPhone15,4', 'A3093', 'Titanium Blue', 'US', '358674010234568', '00:1A:2B:3C:4D:5F', '00:1E:2A:3B:4C:5E', 512, true, true, 'Activation Locked', 'Locked', 'Enabled', 'Yes', false, true, null, 'Like new', false, 1, 2),
('SN001003', 'UDID001003', 'iPhone15,2', 'A3091', 'Black', 'US', '358674010234569', '00:1A:2B:3C:4D:60', '00:1E:2A:3B:4C:5F', 128, true, false, 'Activation Locked', 'Locked', 'Disabled', 'Yes', false, false, 'admin123', 'Back glass crack', true, 1, 3),
('SN001004', 'UDID001004', 'iPhone14,3', 'A2846', 'Gold', 'US', '358674010234570', '00:1A:2B:3C:4D:61', '00:1E:2A:3B:4C:60', 256, true, true, 'Activation Locked', 'Locked', 'Enabled', 'Yes', false, true, null, 'Excellent condition', false, 1, 4),

('SN002001', 'UDID002001', 'iPhone15,3', 'A3092', 'White', 'US', '358674010234571', '00:1A:2B:3C:4D:62', '00:1E:2A:3B:4C:61', 256, true, true, 'Activation Locked', 'Locked', 'Enabled', 'Yes', false, true, null, 'Clean', false, 2, 5),
('SN002002', 'UDID002002', 'iPhone14,7', 'A2846', 'Red', 'US', '358674010234572', '00:1A:2B:3C:4D:63', '00:1E:2A:3B:4C:62', 128, false, true, 'Activation Locked', 'Locked', 'Enabled', 'Yes', false, true, null, 'Battery needs replacement', true, 2, 6),
('SN002003', 'UDID002003', 'iPhone13,2', 'A2605', 'Blue', 'US', '358674010234573', '00:1A:2B:3C:4D:64', '00:1E:2A:3B:4C:63', 64, true, true, 'Activation Locked', 'Locked', 'Disabled', 'Yes', false, false, 'test123', 'Dust under camera', true, 2, 7),
('SN002004', 'UDID002004', 'iPhone13,3', 'A2597', 'Green', 'US', '358674010234574', '00:1A:2B:3C:4D:65', '00:1E:2A:3B:4C:64', 128, false, false, 'Activation Locked', 'Locked', 'Disabled', 'No', false, false, null, 'Screen burn-in, battery poor', true, 2, 8),

('SN003001', 'UDID003001', 'iPhone15,5', 'A3094', 'Space Black', 'US', '358674010234575', '00:1A:2B:3C:4D:66', '00:1E:2A:3B:4C:65', 512, true, true, 'Activation Locked', 'Locked', 'Enabled', 'Yes', false, true, null, 'Perfect condition', false, 3, 9),
('SN003002', 'UDID003002', 'iPhone15,4', 'A3093', 'Natural Titanium', 'US', '358674010234576', '00:1A:2B:3C:4D:67', '00:1E:2A:3B:4C:66', 256, true, true, 'Activation Locked', 'Locked', 'Enabled', 'Yes', false, true, null, 'Like new', false, 3, 10),
('SN003003', 'UDID003003', 'iPhone15,4', 'A3093', 'Space Black', 'US', '358674010234577', '00:1A:2B:3C:4D:68', '00:1E:2A:3B:4C:67', 128, true, true, 'Activation Locked', 'Locked', 'Enabled', 'Yes', false, true, null, 'Excellent condition', false, 3, 11),
('SN003004', 'UDID003004', 'iPhone14,2', 'A2758', 'Silver', 'US', '358674010234578', '00:1A:2B:3C:4D:69', '00:1E:2A:3B:4C:68', 256, true, true, 'Activation Locked', 'Locked', 'Enabled', 'Yes', false, true, null, 'Good condition', false, 3, 12),

('SN004001', 'UDID004001', 'iPhone15,2', 'A3091', 'Black', 'US', '358674010234579', '00:1A:2B:3C:4D:6A', '00:1E:2A:3B:4C:69', 256, true, true, 'Activation Locked', 'Locked', 'Enabled', 'Yes', false, true, null, 'Clean', false, 4, 13),
('SN004002', 'UDID004002', 'iPhone15,2', 'A3091', 'White', 'US', '358674010234580', '00:1A:2B:3C:4D:6B', '00:1E:2A:3B:4C:6A', 128, true, true, 'Activation Locked', 'Locked', 'Enabled', 'Yes', false, true, null, 'Clean', false, 4, 14),
('SN004003', 'UDID004003', 'iPhone14,7', 'A2846', 'Midnight', 'US', '358674010234581', '00:1A:2B:3C:4D:6C', '00:1E:2A:3B:4C:6B', 256, true, false, 'Activation Locked', 'Locked', 'Enabled', 'Yes', false, true, null, 'Minor scratch on frame', false, 4, 15),

('SN005001', 'UDID005001', 'iPhone13,2', 'A2605', 'Sierra Blue', 'US', '358674010234582', '00:1A:2B:3C:4D:6D', '00:1E:2A:3B:4C:6C', 128, false, false, 'Activation Locked', 'Unknown', 'Unknown', 'No', false, false, null, 'Broken screen, needs repair', true, 5, 16),
('SN005002', 'UDID005002', 'iPhone12,1', 'A2404', 'Purple', 'US', '358674010234583', '00:1A:2B:3C:4D:6E', '00:1E:2A:3B:4C:6D', 64, false, false, 'Unknown', 'Unknown', 'Unknown', 'Unknown', false, false, null, 'Liquid damage, needs assessment', false, 5, 17);

-- ============================================================================
-- DiagnosticSession: Test sessions for devices
-- ============================================================================
insert into DiagnosticSession (Ses_serialNumber, Ses_userId, Ses_iosVersion, Ses_buildVersion, Ses_basebandVersion, Ses_batteryPct, Ses_batteryHealthPct, Ses_batteryCycles, Ses_batteryTempC, Ses_batteryImpedanceMohm, Ses_batterySerial, Ses_isCharging, Ses_dataCapacityGb, Ses_dataAvailableGb, Ses_countPass, Ses_countFail, Ses_countNa, Ses_countPending, Ses_startedAt, Ses_endedAt, Ses_elapsedSeconds) values

('SN001001', 2, '18.0', '18.0', '18.0', 85, 100.0, 45, null, null, null, false, 256.00, 200.00, 10, 0, 0, 0, '2024-02-01 09:00:00', '2024-02-01 09:15:00', 900),
('SN001002', 2, '18.0', '18.0', '18.0', 76, 98.5, 92, null, null, null, false, 512.00, 450.00, 10, 0, 0, 0, '2024-02-01 10:00:00', '2024-02-01 10:18:00', 1080),
('SN001003', 3, '18.0', '18.0', '18.0', 45, 92.0, 145, null, null, null, true, 128.00, 90.00, 8, 2, 0, 0, '2024-02-01 11:00:00', '2024-02-01 11:25:00', 1500),
('SN001004', 2, '17.4.1', '17.4.1', '17.4.1', 88, 97.0, 67, null, null, null, false, 256.00, 220.00, 10, 0, 0, 0, '2024-02-02 09:00:00', '2024-02-02 09:20:00', 1200),

('SN002001', 3, '18.0', '18.0', '18.0', 92, 95.0, 78, null, null, null, false, 256.00, 210.00, 10, 0, 0, 0, '2024-02-02 10:00:00', '2024-02-02 10:16:00', 960),
('SN002002', 4, '17.4.1', '17.4.1', '17.4.1', 58, 78.0, 156, null, null, null, true, 128.00, 80.00, 6, 3, 1, 0, '2024-02-03 09:00:00', '2024-02-03 09:22:00', 1320),
('SN002003', 2, '17.3', '17.3', '17.3', 42, 85.0, 198, null, null, null, true, 64.00, 40.00, 7, 1, 1, 1, '2024-02-03 10:30:00', '2024-02-03 10:52:00', 1320),
('SN003001', 4, '18.0', '18.0', '18.0', 100, 99.0, 45, null, null, null, false, 512.00, 480.00, 10, 0, 0, 0, '2024-02-04 09:00:00', '2024-02-04 09:18:00', 1080),
('SN003002', 1, '18.0', '18.0', '18.0', 97, 100.0, 28, null, null, null, false, 256.00, 240.00, 10, 0, 0, 0, '2024-02-04 10:00:00', '2024-02-04 10:15:00', 900),
('SN003003', 2, '18.0', '18.0', '18.0', 95, 99.5, 35, null, null, null, false, 128.00, 110.00, 10, 0, 0, 0, '2024-02-04 11:00:00', '2024-02-04 11:14:00', 840);

-- ============================================================================
-- TestResult: Individual test results from diagnostic sessions
-- ============================================================================
insert into TestResult (Tst_sessionId, Tst_testId, Tst_testLabel, Tst_testGroup, Tst_status, Tst_source) values
(1, 'battery', 'Battery Health', 'Battery', 'pass', 'syslog'),
(1, 'display', 'Display Test', 'Display', 'pass', 'syslog'),
(1, 'camera_front', 'Front Camera', 'Camera', 'pass', 'syslog'),
(1, 'camera_back', 'Back Camera', 'Camera', 'pass', 'syslog'),
(1, 'microphone', 'Microphone', 'Audio', 'pass', 'syslog'),
(1, 'speaker', 'Speaker', 'Audio', 'pass', 'syslog'),
(1, 'wifi', 'WiFi', 'Connectivity', 'pass', 'syslog'),
(1, 'bluetooth', 'Bluetooth', 'Connectivity', 'pass', 'syslog'),
(1, 'nfc', 'NFC', 'Connectivity', 'pass', 'syslog'),
(1, 'gps', 'GPS', 'Sensors', 'pass', 'syslog'),

(2, 'battery', 'Battery Health', 'Battery', 'pass', 'syslog'),
(2, 'display', 'Display Test', 'Display', 'pass', 'syslog'),
(2, 'camera_front', 'Front Camera', 'Camera', 'pass', 'syslog'),
(2, 'camera_back', 'Back Camera', 'Camera', 'pass', 'syslog'),
(2, 'microphone', 'Microphone', 'Audio', 'pass', 'syslog'),
(2, 'speaker', 'Speaker', 'Audio', 'pass', 'syslog'),
(2, 'wifi', 'WiFi', 'Connectivity', 'pass', 'syslog'),
(2, 'bluetooth', 'Bluetooth', 'Connectivity', 'pass', 'syslog'),
(2, 'nfc', 'NFC', 'Connectivity', 'pass', 'syslog'),
(2, 'gps', 'GPS', 'Sensors', 'pass', 'syslog'),

(3, 'battery', 'Battery Health', 'Battery', 'fail', 'syslog'),
(3, 'display', 'Display Test', 'Display', 'pass', 'syslog'),
(3, 'camera_front', 'Front Camera', 'Camera', 'na', 'syslog'),
(3, 'camera_back', 'Back Camera', 'Camera', 'fail', 'syslog'),
(3, 'microphone', 'Microphone', 'Audio', 'pass', 'syslog'),
(3, 'speaker', 'Speaker', 'Audio', 'pass', 'syslog'),
(3, 'wifi', 'WiFi', 'Connectivity', 'pass', 'syslog'),
(3, 'bluetooth', 'Bluetooth', 'Connectivity', 'pass', 'syslog'),
(3, 'nfc', 'NFC', 'Connectivity', 'pending', 'syslog'),
(3, 'gps', 'GPS', 'Sensors', 'pass', 'syslog'),

(4, 'battery', 'Battery Health', 'Battery', 'pass', 'syslog'),
(4, 'display', 'Display Test', 'Display', 'pass', 'syslog'),
(4, 'camera_front', 'Front Camera', 'Camera', 'pass', 'syslog'),
(4, 'camera_back', 'Back Camera', 'Camera', 'pass', 'syslog'),
(4, 'microphone', 'Microphone', 'Audio', 'pass', 'syslog'),
(4, 'speaker', 'Speaker', 'Audio', 'pass', 'syslog'),
(4, 'wifi', 'WiFi', 'Connectivity', 'pass', 'syslog'),
(4, 'bluetooth', 'Bluetooth', 'Connectivity', 'pass', 'syslog'),
(4, 'nfc', 'NFC', 'Connectivity', 'pass', 'syslog'),
(4, 'gps', 'GPS', 'Sensors', 'pass', 'syslog'),

(5, 'battery', 'Battery Health', 'Battery', 'pass', 'syslog'),
(5, 'display', 'Display Test', 'Display', 'pass', 'syslog'),
(5, 'camera_front', 'Front Camera', 'Camera', 'pass', 'syslog'),
(5, 'camera_back', 'Back Camera', 'Camera', 'pass', 'syslog'),
(5, 'microphone', 'Microphone', 'Audio', 'pass', 'syslog'),
(5, 'speaker', 'Speaker', 'Audio', 'pass', 'syslog'),
(5, 'wifi', 'WiFi', 'Connectivity', 'pass', 'syslog'),
(5, 'bluetooth', 'Bluetooth', 'Connectivity', 'pass', 'syslog'),
(5, 'nfc', 'NFC', 'Connectivity', 'pass', 'syslog'),
(5, 'gps', 'GPS', 'Sensors', 'pass', 'syslog'),

(6, 'battery', 'Battery Health', 'Battery', 'fail', 'syslog'),
(6, 'display', 'Display Test', 'Display', 'pass', 'syslog'),
(6, 'camera_front', 'Front Camera', 'Camera', 'pass', 'syslog'),
(6, 'camera_back', 'Back Camera', 'Camera', 'fail', 'syslog'),
(6, 'microphone', 'Microphone', 'Audio', 'fail', 'syslog'),
(6, 'speaker', 'Speaker', 'Audio', 'na', 'syslog'),
(6, 'wifi', 'WiFi', 'Connectivity', 'pass', 'syslog'),
(6, 'bluetooth', 'Bluetooth', 'Connectivity', 'fail', 'syslog'),
(6, 'nfc', 'NFC', 'Connectivity', 'pending', 'syslog'),
(6, 'gps', 'GPS', 'Sensors', 'pass', 'syslog'),

(8, 'battery', 'Battery Health', 'Battery', 'pass', 'syslog'),
(8, 'display', 'Display Test', 'Display', 'pass', 'syslog'),
(8, 'camera_front', 'Front Camera', 'Camera', 'pass', 'syslog'),
(8, 'camera_back', 'Back Camera', 'Camera', 'pass', 'syslog'),
(8, 'microphone', 'Microphone', 'Audio', 'pass', 'syslog'),
(8, 'speaker', 'Speaker', 'Audio', 'pass', 'syslog'),
(8, 'wifi', 'WiFi', 'Connectivity', 'pass', 'syslog'),
(8, 'bluetooth', 'Bluetooth', 'Connectivity', 'pass', 'syslog'),
(8, 'nfc', 'NFC', 'Connectivity', 'pass', 'syslog'),
(8, 'gps', 'GPS', 'Sensors', 'pass', 'syslog'),

(9, 'battery', 'Battery Health', 'Battery', 'pass', 'syslog'),
(9, 'display', 'Display Test', 'Display', 'pass', 'syslog'),
(9, 'camera_front', 'Front Camera', 'Camera', 'pass', 'syslog'),
(9, 'camera_back', 'Back Camera', 'Camera', 'pass', 'syslog'),
(9, 'microphone', 'Microphone', 'Audio', 'pass', 'syslog'),
(9, 'speaker', 'Speaker', 'Audio', 'pass', 'syslog'),
(9, 'wifi', 'WiFi', 'Connectivity', 'pass', 'syslog'),
(9, 'bluetooth', 'Bluetooth', 'Connectivity', 'pass', 'syslog'),
(9, 'nfc', 'NFC', 'Connectivity', 'pass', 'syslog'),
(9, 'gps', 'GPS', 'Sensors', 'pass', 'syslog'),

(10, 'battery', 'Battery Health', 'Battery', 'pass', 'syslog'),
(10, 'display', 'Display Test', 'Display', 'pass', 'syslog'),
(10, 'camera_front', 'Front Camera', 'Camera', 'pass', 'syslog'),
(10, 'camera_back', 'Back Camera', 'Camera', 'pass', 'syslog'),
(10, 'microphone', 'Microphone', 'Audio', 'pass', 'syslog'),
(10, 'speaker', 'Speaker', 'Audio', 'pass', 'syslog'),
(10, 'wifi', 'WiFi', 'Connectivity', 'pass', 'syslog'),
(10, 'bluetooth', 'Bluetooth', 'Connectivity', 'pass', 'syslog'),
(10, 'nfc', 'NFC', 'Connectivity', 'pass', 'syslog'),
(10, 'gps', 'GPS', 'Sensors', 'pass', 'syslog');

-- ============================================================================
-- InventoryItem: Inventory tracking for devices
-- ============================================================================
insert into InventoryItem (Inv_serialNumber, Inv_grade, Inv_conditionNotes, Inv_repairsNeededDone, Inv_costPaid, Inv_repairCost, Inv_b2bFloorPrice, Inv_b2cFloorPrice, Inv_status, Inv_listedAt, Inv_reservedAt, Inv_soldAt, Inv_salePrice, Inv_saleChannel, Inv_buyerInfo, Inv_canonicalSessionId) values

('SN001001', 'A', 'Pristine condition. All components working perfectly.', '', 949.99, 0.00, 899.99, 999.99, 'listed', '2024-02-05 10:00:00', null, null, 999.99, 'eBay', null, 1),
('SN001002', 'A', 'Like new, no visible wear.', '', 949.99, 0.00, 899.99, 999.99, 'sold', '2024-02-05 11:00:00', '2024-02-06 09:00:00', '2024-02-08 14:30:00', 950.00, 'Local Buyer', 'John Smith', 2),
('SN001003', 'B', 'Back glass cracked but display intact.', 'Back glass replacement needed', 499.99, 75.00, 399.99, 499.99, 'in_stock', null, null, null, null, null, null, 3),
('SN001004', 'A', 'Excellent condition, original battery.', '', 849.99, 0.00, 799.99, 899.99, 'listed', '2024-02-06 10:00:00', null, null, 899.99, 'Amazon', null, 4),

('SN002001', 'B', 'Good condition, minor cosmetic wear.', '', 649.99, 0.00, 549.99, 649.99, 'reserved', '2024-02-06 12:00:00', '2024-02-07 10:00:00', null, null, null, 'Reserved for buyer', null),
('SN002002', 'C', 'Battery degraded, needs replacement.', 'Battery replacement - $45', 349.99, 45.00, 249.99, 349.99, 'in_stock', null, null, null, null, null, null, 6),
('SN002003', 'C', 'Dust in camera, otherwise functional.', 'Camera cleaning needed', 299.99, 30.00, 199.99, 299.99, 'in_stock', null, null, null, null, null, null, 7),
('SN002004', 'D', 'Screen burn-in present, poor battery health.', 'Screen replacement - $120, Battery replacement - $45', 149.99, 165.00, null, 149.99, 'in_stock', null, null, null, null, null, null, null),

('SN003001', 'A', 'Perfect condition, maxed storage.', '', 1049.99, 0.00, 999.99, 1149.99, 'listed', '2024-02-07 09:00:00', null, null, 1149.99, 'eBay', null, 8),
('SN003002', 'A', 'Like new, minimal use.', '', 949.99, 0.00, 899.99, 999.99, 'in_stock', null, null, null, null, null, null, 9),
('SN003003', 'A', 'Excellent condition throughout.', '', 949.99, 0.00, 899.99, 999.99, 'listed', '2024-02-07 10:30:00', '2024-02-08 08:00:00', null, null, null, 'Awaiting payment', 10),
('SN003004', 'A', 'Good condition, all tests pass.', '', 749.99, 0.00, 699.99, 799.99, 'sold', '2024-02-07 12:00:00', '2024-02-07 14:00:00', '2024-02-09 10:00:00', 750.00, 'Facebook Marketplace', 'Jane Doe', null),

('SN004001', 'B', 'Clean condition, minor scratches on back.', '', 549.99, 0.00, 449.99, 549.99, 'in_stock', null, null, null, null, null, null, null),
('SN004002', 'B', 'Very good condition, minimal wear.', '', 549.99, 0.00, 449.99, 549.99, 'listed', '2024-02-08 09:00:00', null, null, 549.99, 'eBay', null, null),
('SN004003', 'C', 'Frame scratch visible, screen OK.', 'Frame repair or replacement', 349.99, 60.00, 249.99, 349.99, 'in_stock', null, null, null, null, null, null, null),

('SN005001', 'D', 'Cracked screen, dead battery, needs repair.', 'Screen + Battery replacement - $165', 99.99, 165.00, null, 199.99, 'in_stock', null, null, null, null, null, null, null),
('SN005002', 'D', 'Liquid damage indicators present.', 'Full diagnostic and repair needed', 99.99, 0.00, null, 149.99, 'in_stock', null, null, null, null, null, null, null);