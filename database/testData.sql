-- Test/seed data for the phone_inventory database.
-- Matches database/schema.sql. Safe to re-run: truncates all tables first.

USE phone_inventory;

SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE sale;
TRUNCATE TABLE test_result;
TRUNCATE TABLE inventory_item;
TRUNCATE TABLE diagnostic_session;
TRUNCATE TABLE device;
TRUNCATE TABLE supplier_manifest_item;
TRUNCATE TABLE batch;
TRUNCATE TABLE user;
TRUNCATE TABLE device_model;
SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================================
-- device_model: device catalog, lazily populated in real usage
-- ============================================================================
INSERT INTO device_model
    (product_type, brand, friendly_name, has_home_button, has_face_id, has_action_button, has_camera_button, has_telephoto, has_lidar)
VALUES
    ('iPhone13,1', 'Apple', 'iPhone 12 mini',     FALSE, TRUE, FALSE, FALSE, FALSE, FALSE),
    ('iPhone13,2', 'Apple', 'iPhone 12',          FALSE, TRUE, FALSE, FALSE, FALSE, FALSE),
    ('iPhone13,3', 'Apple', 'iPhone 12 Pro',      FALSE, TRUE, FALSE, FALSE, TRUE,  TRUE),
    ('iPhone13,4', 'Apple', 'iPhone 12 Pro Max',  FALSE, TRUE, FALSE, FALSE, TRUE,  TRUE),
    ('iPhone14,2', 'Apple', 'iPhone 13 Pro',      FALSE, TRUE, FALSE, FALSE, TRUE,  TRUE),
    ('iPhone14,3', 'Apple', 'iPhone 13 Pro Max',  FALSE, TRUE, FALSE, FALSE, TRUE,  TRUE),
    ('iPhone14,7', 'Apple', 'iPhone 14',          FALSE, TRUE, FALSE, FALSE, FALSE, FALSE),
    ('iPhone14,8', 'Apple', 'iPhone 14 Plus',     FALSE, TRUE, FALSE, FALSE, FALSE, FALSE),
    ('iPhone15,2', 'Apple', 'iPhone 14 Pro',      FALSE, TRUE, FALSE, FALSE, TRUE,  TRUE),
    ('iPhone15,3', 'Apple', 'iPhone 14 Pro Max',  FALSE, TRUE, FALSE, FALSE, TRUE,  TRUE),
    ('iPhone15,4', 'Apple', 'iPhone 15',          FALSE, TRUE, FALSE, FALSE, FALSE, FALSE),
    ('iPhone15,5', 'Apple', 'iPhone 15 Plus',     FALSE, TRUE, FALSE, FALSE, FALSE, FALSE),
    ('iPhone16,1', 'Apple', 'iPhone 15 Pro',      FALSE, TRUE, TRUE,  FALSE, TRUE,  TRUE),
    ('iPhone17,1', 'Apple', 'iPhone 16 Pro',      FALSE, TRUE, TRUE,  TRUE,  TRUE,  TRUE);

-- ============================================================================
-- user: staff accounts
-- password_hash values are dummy bcrypt-shaped strings, NOT real credentials
-- ============================================================================
INSERT INTO user (id, username, email, password_hash, role) VALUES
    (1, 'admin',       'admin@phoneinventory.local',   '$2y$10$Q7z1J5m1n9r0KpV8v6iVoOeWc1s2H3lM4rY5tZ6uA7bC8dE9fG0hK', 'admin'),
    (2, 'john.ramirez','john.ramirez@phoneinventory.local', '$2y$10$Q7z1J5m1n9r0KpV8v6iVoOeWc1s2H3lM4rY5tZ6uA7bC8dE9fG0hK', 'technician'),
    (3, 'sarah.lee',   'sarah.lee@phoneinventory.local',    '$2y$10$Q7z1J5m1n9r0KpV8v6iVoOeWc1s2H3lM4rY5tZ6uA7bC8dE9fG0hK', 'technician'),
    (4, 'mike.chen',   'mike.chen@phoneinventory.local',    '$2y$10$Q7z1J5m1n9r0KpV8v6iVoOeWc1s2H3lM4rY5tZ6uA7bC8dE9fG0hK', 'technician');

-- ============================================================================
-- batch: supplier intake batches
-- ============================================================================
INSERT INTO batch (id, batch_number, supplier_batch_id, technician, location, received_at, notes) VALUES
    (1, 'B-2024-0001', 'SB-88213', 'John Ramirez', 'Warehouse A', '2024-01-15 09:00:00', 'First batch from primary supplier, mostly Grade A'),
    (2, 'B-2024-0002', 'SB-88340', 'Sarah Lee',    'Warehouse A', '2024-02-03 11:30:00', 'Mixed condition lot, several units flagged for repair'),
    (3, 'B-2024-0003', 'SB-88512', 'John Ramirez', 'Warehouse B', '2024-03-10 14:00:00', 'Grade A/B devices only'),
    (4, 'B-2024-0004', 'SB-88701', 'Mike Chen',    'Warehouse B', '2024-05-22 10:15:00', 'Includes one liquid-damaged unit, one unable to power on'),
    (5, 'B-2024-0005', 'SB-88950', 'Sarah Lee',    'Warehouse A', '2024-07-14 08:45:00', 'Recent intake, not yet processed');

-- ============================================================================
-- supplier_manifest_item: as-received line items from the supplier manifest
-- ============================================================================
INSERT INTO supplier_manifest_item
    (id, batch_id, imei, serial_number, supplier_item_id, model, color, supplier_grade, has_issues, issue_description, supplier_value, revision_price, battery_health)
VALUES
    (1,  1, '358674010001001', 'SNA1001', 'ITEM-0001', 'iPhone 14 Pro',     'Space Black', 'A', FALSE, NULL,                                 799.99, 759.99, 97.0),
    (2,  1, '358674010001002', 'SNA1002', 'ITEM-0002', 'iPhone 15',         'Blue',        'A', FALSE, NULL,                                 649.99, 619.99, 100.0),
    (3,  1, '358674010001003', 'SNA1003', 'ITEM-0003', 'iPhone 12',         'White',       'B', TRUE,  'Light scuffing on frame',            349.99, 299.99, 89.0),
    (4,  1, '358674010001004', 'SNA1004', 'ITEM-0004', 'iPhone 12 Pro',     'Graphite',    'B', FALSE, NULL,                                 449.99, 419.99, 91.5),
    (5,  2, '358674010001005', 'SNA1005', 'ITEM-0005', 'iPhone 14',         'Midnight',    'B', TRUE,  'Small crack on rear glass',           499.99, 429.99, 93.0),
    (6,  2, '358674010001006', 'SNA1006', 'ITEM-0006', 'iPhone 13 Pro Max', 'Silver',      'A', FALSE, NULL,                                 649.99, 609.99, 96.0),
    (7,  2, '358674010001007', 'SNA1007', 'ITEM-0007', 'iPhone 12 mini',    'Green',       'C', TRUE,  'Battery health below 85%',            249.99, 189.99, 82.0),
    (8,  2, '358674010001008', 'SNA1008', 'ITEM-0008', 'iPhone 14 Plus',    'Purple',      'B', FALSE, NULL,                                 549.99, 519.99, 94.0),
    (9,  3, '358674010001009', 'SNA1009', 'ITEM-0009', 'iPhone 15 Pro',     'Natural Titanium', 'A', FALSE, NULL,                             899.99, 859.99, 99.0),
    (10, 3, '358674010001010', 'SNA1010', 'ITEM-0010', 'iPhone 15 Plus',    'Black',       'A', FALSE, NULL,                                 699.99, 669.99, 98.0),
    (11, 3, '358674010001011', 'SNA1011', 'ITEM-0011', 'iPhone 12 Pro Max', 'Pacific Blue','B', TRUE,  'Minor scratches on display',          499.99, 449.99, 88.0),
    (12, 4, '358674010001012', 'SNA1012', 'ITEM-0012', 'iPhone 16 Pro',     'Desert Titanium', 'A', FALSE, NULL,                              999.99, 969.99, 100.0),
    (13, 4, '358674010001013', 'SNA1013', 'ITEM-0013', 'iPhone 13 Pro Max', 'Gold',        'D', TRUE,  'Liquid damage indicators triggered',  299.99, 79.99,  NULL),
    (14, 4, NULL,               'SNA1014', 'ITEM-0014', 'iPhone 12',        'Red',         'D', TRUE,  'Unit does not power on',              199.99, 49.99,  NULL),
    (15, 5, '358674010001015', 'SNA1015', 'ITEM-0015', 'iPhone 15',         'Pink',        'A', FALSE, NULL,                                 649.99, 619.99, 100.0);

-- ============================================================================
-- device: physical devices identified/tested at intake
-- ============================================================================
INSERT INTO device
    (serial_number, udid, product_type, model_number, color, region_code, imei, wifi_mac, bluetooth_mac,
     imei2, meid, iccid, baseband_serial, hardware_model, storage_gb, battery_original, screen_original,
     activation_state, icloud_lock_status, find_my_enabled, passcode_protected, mdm_enrolled,
     can_test, device_password, known_issues, previously_repaired,
     batch_id, supplier_manifest_item_id, intake_at, deleted_at)
VALUES
    ('SNA1001', '00008110-0010A1B2C3D4001E', 'iPhone15,2', 'A2890', 'Space Black', 'US', '358674010001001', '00:1A:2B:10:00:01', '00:1E:2A:10:00:01', NULL, NULL, '8901410123456780001', 'BB-1001', 'D73AP', 256.00, TRUE,  TRUE,  'Not Activated', 'Unlocked', 'Disabled', 'No',  FALSE, TRUE,  NULL,       'None reported',                    FALSE, 1, 1,  '2024-01-15 09:10:00', NULL),
    ('SNA1002', '00008110-0010A1B2C3D4002E', 'iPhone15,4', 'A2846', 'Blue',        'US', '358674010001002', '00:1A:2B:10:00:02', '00:1E:2A:10:00:02', NULL, NULL, '8901410123456780002', 'BB-1002', 'D74AP', 128.00, TRUE,  TRUE,  'Not Activated', 'Unlocked', 'Disabled', 'No',  FALSE, TRUE,  NULL,       'None reported',                    FALSE, 1, 2,  '2024-01-15 09:20:00', NULL),
    ('SNA1003', '00008110-0010A1B2C3D4003E', 'iPhone13,2', 'A2172', 'White',       'US', '358674010001003', '00:1A:2B:10:00:03', '00:1E:2A:10:00:03', NULL, NULL, '8901410123456780003', 'BB-1003', 'D53gAP', 64.00, TRUE, FALSE, 'Not Activated', 'Unlocked', 'Disabled', 'No',  FALSE, TRUE,  NULL,       'Frame scuffing',                   TRUE,  1, 3,  '2024-01-15 09:35:00', NULL),
    ('SNA1004', '00008110-0010A1B2C3D4004E', 'iPhone13,3', 'A2341', 'Graphite',    'US', '358674010001004', '00:1A:2B:10:00:04', '00:1E:2A:10:00:04', NULL, NULL, '8901410123456780004', 'BB-1004', 'D53pAP',256.00, TRUE,  TRUE,  'Not Activated', 'Unlocked', 'Disabled', 'No',  FALSE, TRUE,  NULL,       'None reported',                    FALSE, 1, 4,  '2024-01-15 09:50:00', NULL),

    ('SNA1005', '00008110-0010A1B2C3D4005E', 'iPhone14,7', 'A2882', 'Midnight',    'US', '358674010001005', '00:1A:2B:10:00:05', '00:1E:2A:10:00:05', NULL, NULL, '8901410123456780005', 'BB-1005', 'D27AP', 128.00, TRUE,  FALSE, 'Not Activated', 'Unlocked', 'Disabled', 'No',  FALSE, TRUE,  NULL,       'Small crack on rear glass',        FALSE, 2, 5,  '2024-02-03 11:40:00', NULL),
    ('SNA1006', '00008110-0010A1B2C3D4006E', 'iPhone14,3', 'A2484', 'Silver',      'US', '358674010001006', '00:1A:2B:10:00:06', '00:1E:2A:10:00:06', NULL, NULL, '8901410123456780006', 'BB-1006', 'D64AP', 256.00, TRUE,  TRUE,  'Not Activated', 'Unlocked', 'Disabled', 'No',  FALSE, TRUE,  NULL,       'None reported',                    FALSE, 2, 6,  '2024-02-03 11:55:00', NULL),
    ('SNA1007', '00008110-0010A1B2C3D4007E', 'iPhone13,1', 'A2176', 'Green',       'US', '358674010001007', '00:1A:2B:10:00:07', '00:1E:2A:10:00:07', NULL, NULL, '8901410123456780007', 'BB-1007', 'D57AP', 64.00,  FALSE, TRUE,  'Not Activated', 'Unlocked', 'Disabled', 'No',  FALSE, TRUE,  NULL,       'Battery health below 85%',         TRUE,  2, 7,  '2024-02-03 12:05:00', NULL),
    ('SNA1008', '00008110-0010A1B2C3D4008E', 'iPhone14,8', 'A2886', 'Purple',      'US', '358674010001008', '00:1A:2B:10:00:08', '00:1E:2A:10:00:08', NULL, NULL, '8901410123456780008', 'BB-1008', 'D28AP', 256.00, TRUE,  TRUE,  'Not Activated', 'Unlocked', 'Disabled', 'No',  FALSE, TRUE,  NULL,       'None reported',                    FALSE, 2, 8,  '2024-02-03 12:20:00', NULL),

    ('SNA1009', '00008110-0010A1B2C3D4009E', 'iPhone16,1', 'A3101', 'Natural Titanium', 'US', '358674010001009', '00:1A:2B:10:00:09', '00:1E:2A:10:00:09', NULL, NULL, '8901410123456780009', 'BB-1009', 'D83AP', 512.00, TRUE,  TRUE,  'Not Activated', 'Unlocked', 'Disabled', 'No', FALSE, TRUE,  NULL,       'None reported',                    FALSE, 3, 9,  '2024-03-10 14:10:00', NULL),
    ('SNA1010', '00008110-0010A1B2C3D400AE', 'iPhone15,5', 'A2847', 'Black',       'US', '358674010001010', '00:1A:2B:10:00:0A', '00:1E:2A:10:00:0A', NULL, NULL, '8901410123456780010', 'BB-1010', 'D75AP', 256.00, TRUE,  TRUE,  'Not Activated', 'Unlocked', 'Disabled', 'No',  FALSE, TRUE,  NULL,       'None reported',                    FALSE, 3, 10, '2024-03-10 14:25:00', NULL),
    ('SNA1011', '00008110-0010A1B2C3D400BE', 'iPhone13,4', 'A2342', 'Pacific Blue','US', '358674010001011', '00:1A:2B:10:00:0B', '00:1E:2A:10:00:0B', NULL, NULL, '8901410123456780011', 'BB-1011', 'D54pAP',128.00, TRUE,  FALSE, 'Not Activated', 'Unlocked', 'Disabled', 'No',  FALSE, TRUE,  NULL,       'Minor scratches on display',       TRUE,  3, 11, '2024-03-10 14:40:00', NULL),

    ('SNA1012', '00008110-0010A1B2C3D400CE', 'iPhone17,1', 'A3293', 'Desert Titanium',  'US', '358674010001012', '00:1A:2B:10:00:0C', '00:1E:2A:10:00:0C', '358674010001112', NULL, '8901410123456780012', 'BB-1012', 'D93AP', 512.00, TRUE, TRUE, 'Not Activated', 'Unlocked', 'Disabled', 'No', FALSE, TRUE, NULL, 'None reported', FALSE, 4, 12, '2024-05-22 10:25:00', NULL),
    ('SNA1013', '00008110-0010A1B2C3D400DE', 'iPhone14,3', 'A2484', 'Gold',        'US', '358674010001013', '00:1A:2B:10:00:0D', '00:1E:2A:10:00:0D', NULL, NULL, '8901410123456780013', 'BB-1013', 'D64AP', 256.00, TRUE,  FALSE, 'Unknown', 'Unknown', 'Unknown', 'Unknown', FALSE, FALSE, NULL, 'Liquid damage indicators triggered, does not reliably power on', FALSE, 4, 13, '2024-05-22 10:40:00', '2024-06-01 09:00:00'),
    ('SNA1014', '00008110-0010A1B2C3D400EE', 'iPhone13,2', 'A2172', 'Red',         'US', '358674010001014', '00:1A:2B:10:00:0E', '00:1E:2A:10:00:0E', NULL, NULL, '8901410123456780014', 'BB-1014', 'D53gAP', 64.00, TRUE, TRUE, 'Unknown', 'Unknown', 'Unknown', 'Unknown', FALSE, FALSE, NULL, 'Unit does not power on', FALSE, 4, 14, '2024-05-22 10:55:00', NULL),

    ('SNA1015', '00008110-0010A1B2C3D400FE', 'iPhone15,4', 'A2846', 'Pink',        'US', '358674010001015', '00:1A:2B:10:00:0F', '00:1E:2A:10:00:0F', NULL, NULL, '8901410123456780015', 'BB-1015', 'D74AP', 128.00, TRUE,  TRUE,  'Not Activated', 'Unlocked', 'Disabled', 'No',  FALSE, TRUE,  NULL,       'None reported',                    FALSE, 5, 15, '2024-07-14 08:50:00', NULL);

-- ============================================================================
-- diagnostic_session: one completed test session per testable device
-- (SNA1013, SNA1014, SNA1015 have not been tested yet)
-- ============================================================================
INSERT INTO diagnostic_session
    (id, serial_number, technician, ios_version, build_version, baseband_version,
     battery_pct, battery_health_pct, battery_cycles, battery_temp_c, battery_impedance_mohm, battery_serial, is_charging,
     data_capacity_gb, data_available_gb, count_pass, count_fail, count_na, count_pending,
     started_at, ended_at, elapsed_seconds)
VALUES
    (1,  'SNA1001', 'John Ramirez', '18.1', '22B83', '4.10.01', 82, 97.0, 58,  27.4, 34, 'BATT-SNA1001', TRUE, 256.00, 198.30, 8, 0, 0, 0, '2024-01-16 10:00:00', '2024-01-16 10:14:00', 840),
    (2,  'SNA1002', 'John Ramirez', '18.1', '22B83', '4.10.01', 91, 100.0, 3,   26.8, 28, 'BATT-SNA1002', TRUE, 128.00, 121.10, 8, 0, 0, 0, '2024-01-16 10:20:00', '2024-01-16 10:33:00', 780),
    (3,  'SNA1003', 'John Ramirez', '17.6', '21G93', '3.60.02', 64, 89.0, 412, 29.1, 51, 'BATT-SNA1003', TRUE, 64.00,  40.20,  6, 2, 0, 0, '2024-01-16 10:45:00', '2024-01-16 11:02:00', 1020),
    (4,  'SNA1004', 'John Ramirez', '17.6', '21G93', '3.60.02', 88, 91.5, 231, 27.9, 40, 'BATT-SNA1004', TRUE, 256.00, 210.75, 8, 0, 0, 0, '2024-01-16 11:15:00', '2024-01-16 11:29:00', 840),

    (5,  'SNA1005', 'Sarah Lee',    '18.0', '22A3354', '4.05.10', 77, 93.0, 189, 28.2, 37, 'BATT-SNA1005', TRUE, 128.00, 95.60,  7, 1, 0, 0, '2024-02-04 09:00:00', '2024-02-04 09:16:00', 960),
    (6,  'SNA1006', 'Sarah Lee',    '17.5', '21F90', '3.50.05', 85, 96.0, 96,  26.5, 31, 'BATT-SNA1006', TRUE, 256.00, 188.40, 8, 0, 0, 0, '2024-02-04 09:25:00', '2024-02-04 09:39:00', 840),
    (7,  'SNA1007', 'Sarah Lee',    '16.7', '20H19', '2.80.01', 55, 82.0, 601, 30.0, 63, 'BATT-SNA1007', TRUE, 64.00,  22.10,  5, 3, 0, 0, '2024-02-04 09:50:00', '2024-02-04 10:09:00', 1140),
    (8,  'SNA1008', 'Sarah Lee',    '18.0', '22A3354', '4.05.10', 90, 94.0, 142, 27.0, 33, 'BATT-SNA1008', TRUE, 256.00, 201.90, 8, 0, 0, 0, '2024-02-04 10:20:00', '2024-02-04 10:34:00', 840),

    (9,  'SNA1009', 'John Ramirez', '18.2', '22C150', '4.20.00', 96, 99.0, 5,   26.2, 27, 'BATT-SNA1009', TRUE, 512.00, 470.00, 8, 0, 0, 0, '2024-03-11 13:00:00', '2024-03-11 13:13:00', 780),
    (10, 'SNA1010', 'John Ramirez', '18.2', '22C150', '4.20.00', 89, 98.0, 12,  26.6, 29, 'BATT-SNA1010', TRUE, 256.00, 240.10, 8, 0, 0, 0, '2024-03-11 13:25:00', '2024-03-11 13:38:00', 780),
    (11, 'SNA1011', 'John Ramirez', '17.6', '21G93', '3.60.02', 71, 88.0, 268, 28.7, 42, 'BATT-SNA1011', TRUE, 128.00, 88.50,  7, 1, 0, 0, '2024-03-11 13:50:00', '2024-03-11 14:07:00', 1020),

    (12, 'SNA1012', 'Mike Chen',    '18.3', '22D8073', '4.30.00', 100, 100.0, 0, 25.9, 25, 'BATT-SNA1012', TRUE, 512.00, 505.00, 8, 0, 0, 0, '2024-05-23 08:30:00', '2024-05-23 08:42:00', 720);

-- ============================================================================
-- test_result: individual diagnostic checks per session
-- ============================================================================
INSERT INTO test_result (session_id, test_id, test_label, test_group, status, source) VALUES
    -- SNA1001 - all pass
    (1, 'BATTERY_HEALTH', 'Battery Health',   'power',        'pass', 'syslog'),
    (1, 'DISPLAY',        'Display & Touch',  'screen',       'pass', 'syslog'),
    (1, 'FRONT_CAMERA',   'Front Camera',     'camera',       'pass', 'syslog'),
    (1, 'REAR_CAMERA',    'Rear Camera',      'camera',       'pass', 'syslog'),
    (1, 'WIFI',           'Wi-Fi',            'connectivity', 'pass', 'syslog'),
    (1, 'BLUETOOTH',      'Bluetooth',        'connectivity', 'pass', 'syslog'),
    (1, 'SPEAKER',        'Speaker',          'audio',        'pass', 'manual'),
    (1, 'FACE_ID',        'Face ID',          'biometric',    'pass', 'manual'),

    -- SNA1002 - all pass
    (2, 'BATTERY_HEALTH', 'Battery Health',   'power',        'pass', 'syslog'),
    (2, 'DISPLAY',        'Display & Touch',  'screen',       'pass', 'syslog'),
    (2, 'FRONT_CAMERA',   'Front Camera',     'camera',       'pass', 'syslog'),
    (2, 'REAR_CAMERA',    'Rear Camera',      'camera',       'pass', 'syslog'),
    (2, 'WIFI',           'Wi-Fi',            'connectivity', 'pass', 'syslog'),
    (2, 'BLUETOOTH',      'Bluetooth',        'connectivity', 'pass', 'syslog'),
    (2, 'SPEAKER',        'Speaker',          'audio',        'pass', 'manual'),
    (2, 'FACE_ID',        'Face ID',          'biometric',    'pass', 'manual'),

    -- SNA1003 - display touch and speaker failing (matches frame scuffing / age)
    (3, 'BATTERY_HEALTH', 'Battery Health',   'power',        'pass', 'syslog'),
    (3, 'DISPLAY',        'Display & Touch',  'screen',       'fail', 'manual'),
    (3, 'FRONT_CAMERA',   'Front Camera',     'camera',       'pass', 'syslog'),
    (3, 'REAR_CAMERA',    'Rear Camera',      'camera',       'pass', 'syslog'),
    (3, 'WIFI',           'Wi-Fi',            'connectivity', 'pass', 'syslog'),
    (3, 'BLUETOOTH',      'Bluetooth',        'connectivity', 'pass', 'syslog'),
    (3, 'SPEAKER',        'Speaker',          'audio',        'fail', 'manual'),
    (3, 'FACE_ID',        'Face ID',          'biometric',    'pass', 'manual'),

    -- SNA1004 - all pass
    (4, 'BATTERY_HEALTH', 'Battery Health',   'power',        'pass', 'syslog'),
    (4, 'DISPLAY',        'Display & Touch',  'screen',       'pass', 'syslog'),
    (4, 'FRONT_CAMERA',   'Front Camera',     'camera',       'pass', 'syslog'),
    (4, 'REAR_CAMERA',    'Rear Camera',      'camera',       'pass', 'syslog'),
    (4, 'WIFI',           'Wi-Fi',            'connectivity', 'pass', 'syslog'),
    (4, 'BLUETOOTH',      'Bluetooth',        'connectivity', 'pass', 'syslog'),
    (4, 'SPEAKER',        'Speaker',          'audio',        'pass', 'manual'),
    (4, 'FACE_ID',        'Face ID',          'biometric',    'pass', 'manual'),

    -- SNA1005 - rear camera failing (cracked rear glass)
    (5, 'BATTERY_HEALTH', 'Battery Health',   'power',        'pass', 'syslog'),
    (5, 'DISPLAY',        'Display & Touch',  'screen',       'pass', 'syslog'),
    (5, 'FRONT_CAMERA',   'Front Camera',     'camera',       'pass', 'syslog'),
    (5, 'REAR_CAMERA',    'Rear Camera',      'camera',       'fail', 'manual'),
    (5, 'WIFI',           'Wi-Fi',            'connectivity', 'pass', 'syslog'),
    (5, 'BLUETOOTH',      'Bluetooth',        'connectivity', 'pass', 'syslog'),
    (5, 'SPEAKER',        'Speaker',          'audio',        'pass', 'manual'),
    (5, 'FACE_ID',        'Face ID',          'biometric',    'pass', 'manual'),

    -- SNA1006 - all pass
    (6, 'BATTERY_HEALTH', 'Battery Health',   'power',        'pass', 'syslog'),
    (6, 'DISPLAY',        'Display & Touch',  'screen',       'pass', 'syslog'),
    (6, 'FRONT_CAMERA',   'Front Camera',     'camera',       'pass', 'syslog'),
    (6, 'REAR_CAMERA',    'Rear Camera',      'camera',       'pass', 'syslog'),
    (6, 'WIFI',           'Wi-Fi',            'connectivity', 'pass', 'syslog'),
    (6, 'BLUETOOTH',      'Bluetooth',        'connectivity', 'pass', 'syslog'),
    (6, 'SPEAKER',        'Speaker',          'audio',        'pass', 'manual'),
    (6, 'FACE_ID',        'Face ID',          'biometric',    'pass', 'manual'),

    -- SNA1007 - old worn unit, several failures
    (7, 'BATTERY_HEALTH', 'Battery Health',   'power',        'fail', 'syslog'),
    (7, 'DISPLAY',        'Display & Touch',  'screen',       'pass', 'syslog'),
    (7, 'FRONT_CAMERA',   'Front Camera',     'camera',       'pass', 'syslog'),
    (7, 'REAR_CAMERA',    'Rear Camera',      'camera',       'fail', 'manual'),
    (7, 'WIFI',           'Wi-Fi',            'connectivity', 'pass', 'syslog'),
    (7, 'BLUETOOTH',      'Bluetooth',        'connectivity', 'pass', 'syslog'),
    (7, 'SPEAKER',        'Speaker',          'audio',        'fail', 'manual'),
    (7, 'FACE_ID',        'Face ID',          'biometric',    'pass', 'manual'),

    -- SNA1008 - all pass
    (8, 'BATTERY_HEALTH', 'Battery Health',   'power',        'pass', 'syslog'),
    (8, 'DISPLAY',        'Display & Touch',  'screen',       'pass', 'syslog'),
    (8, 'FRONT_CAMERA',   'Front Camera',     'camera',       'pass', 'syslog'),
    (8, 'REAR_CAMERA',    'Rear Camera',      'camera',       'pass', 'syslog'),
    (8, 'WIFI',           'Wi-Fi',            'connectivity', 'pass', 'syslog'),
    (8, 'BLUETOOTH',      'Bluetooth',        'connectivity', 'pass', 'syslog'),
    (8, 'SPEAKER',        'Speaker',          'audio',        'pass', 'manual'),
    (8, 'FACE_ID',        'Face ID',          'biometric',    'pass', 'manual'),

    -- SNA1009 - all pass
    (9, 'BATTERY_HEALTH', 'Battery Health',   'power',        'pass', 'syslog'),
    (9, 'DISPLAY',        'Display & Touch',  'screen',       'pass', 'syslog'),
    (9, 'FRONT_CAMERA',   'Front Camera',     'camera',       'pass', 'syslog'),
    (9, 'REAR_CAMERA',    'Rear Camera',      'camera',       'pass', 'syslog'),
    (9, 'WIFI',           'Wi-Fi',            'connectivity', 'pass', 'syslog'),
    (9, 'BLUETOOTH',      'Bluetooth',        'connectivity', 'pass', 'syslog'),
    (9, 'SPEAKER',        'Speaker',          'audio',        'pass', 'manual'),
    (9, 'FACE_ID',        'Face ID',          'biometric',    'pass', 'manual'),

    -- SNA1010 - all pass
    (10, 'BATTERY_HEALTH', 'Battery Health',  'power',        'pass', 'syslog'),
    (10, 'DISPLAY',        'Display & Touch', 'screen',       'pass', 'syslog'),
    (10, 'FRONT_CAMERA',   'Front Camera',    'camera',       'pass', 'syslog'),
    (10, 'REAR_CAMERA',    'Rear Camera',     'camera',       'pass', 'syslog'),
    (10, 'WIFI',           'Wi-Fi',           'connectivity', 'pass', 'syslog'),
    (10, 'BLUETOOTH',      'Bluetooth',       'connectivity', 'pass', 'syslog'),
    (10, 'SPEAKER',        'Speaker',         'audio',        'pass', 'manual'),
    (10, 'FACE_ID',        'Face ID',         'biometric',    'pass', 'manual'),

    -- SNA1011 - display has minor scratches, marked fail
    (11, 'BATTERY_HEALTH', 'Battery Health',  'power',        'pass', 'syslog'),
    (11, 'DISPLAY',        'Display & Touch', 'screen',       'fail', 'manual'),
    (11, 'FRONT_CAMERA',   'Front Camera',    'camera',       'pass', 'syslog'),
    (11, 'REAR_CAMERA',    'Rear Camera',     'camera',       'pass', 'syslog'),
    (11, 'WIFI',           'Wi-Fi',           'connectivity', 'pass', 'syslog'),
    (11, 'BLUETOOTH',      'Bluetooth',       'connectivity', 'pass', 'syslog'),
    (11, 'SPEAKER',        'Speaker',         'audio',        'pass', 'manual'),
    (11, 'FACE_ID',        'Face ID',         'biometric',    'pass', 'manual'),

    -- SNA1012 - all pass, flagship unit
    (12, 'BATTERY_HEALTH', 'Battery Health',  'power',        'pass', 'syslog'),
    (12, 'DISPLAY',        'Display & Touch', 'screen',       'pass', 'syslog'),
    (12, 'FRONT_CAMERA',   'Front Camera',    'camera',       'pass', 'syslog'),
    (12, 'REAR_CAMERA',    'Rear Camera',     'camera',       'pass', 'syslog'),
    (12, 'WIFI',           'Wi-Fi',           'connectivity', 'pass', 'syslog'),
    (12, 'BLUETOOTH',      'Bluetooth',       'connectivity', 'pass', 'syslog'),
    (12, 'SPEAKER',        'Speaker',         'audio',        'pass', 'manual'),
    (12, 'FACE_ID',        'Face ID',         'biometric',    'pass', 'manual');

-- ============================================================================
-- inventory_item: sellable stock derived from graded/tested devices
-- SNA1013 goes straight to scrap without a diagnostic session (liquid damage)
-- SNA1014 was soft-deleted and never entered inventory
-- SNA1015 has not been graded yet
-- ============================================================================
INSERT INTO inventory_item
    (serial_number, grade, condition_notes, repairs_needed_done, cost_paid, repair_cost,
     b2b_floor_price, b2c_floor_price, status, reserved_at, listing_channel, reservation_notes,
     canonical_session_id)
VALUES
    ('SNA1001', 'A',     'Excellent condition, no visible wear',        NULL,                              759.99, 0.00,   820.00, 899.99, 'in_stock', NULL,                  NULL,            NULL, 1),
    ('SNA1002', 'A',     'Like new',                                    NULL,                              619.99, 0.00,   680.00, 749.99, 'listed',   NULL,                  'B2C Store',     NULL, 2),
    ('SNA1003', 'C',     'Display digitizer replaced, frame scuffing',  'Replaced digitizer, new speaker',  299.99, 65.00,  340.00, 389.99, 'in_stock', NULL,                  NULL,            NULL, 3),
    ('SNA1004', 'A',     'Excellent condition',                         NULL,                              419.99, 0.00,   470.00, 519.99, 'sold',     NULL,                  NULL,            NULL, 4),
    ('SNA1005', 'B',     'Rear camera glass replaced',                  'Replaced rear camera assembly',    429.99, 45.00,  470.00, 519.99, 'reserved', '2024-07-20 09:15:00', NULL,            'Reserved for wholesale order #WS-771', 5),
    ('SNA1006', 'A',     'Excellent condition',                         NULL,                              609.99, 0.00,   660.00, 719.99, 'in_stock', NULL,                  NULL,            NULL, 6),
    ('SNA1007', 'Parts', 'Failed battery and speaker, sold for parts',  NULL,                              189.99, 0.00,   60.00,  NULL,   'listed',   NULL,                  'B2B Wholesale', NULL, 7),
    ('SNA1008', 'A',     'Excellent condition',                         NULL,                              519.99, 0.00,   570.00, 619.99, 'sold',     NULL,                  NULL,            NULL, 8),
    ('SNA1009', 'A',     'Excellent condition, flagship unit',          NULL,                              859.99, 0.00,   930.00, 999.99, 'listed',   NULL,                  'B2C Store',     NULL, 9),
    ('SNA1010', 'A',     'Excellent condition',                         NULL,                              669.99, 0.00,   730.00, 799.99, 'in_stock', NULL,                  NULL,            NULL, 10),
    ('SNA1011', 'B',     'Display has minor scratches',                 NULL,                              449.99, 0.00,   490.00, 539.99, 'returned', NULL,                  NULL,            NULL, 11),
    ('SNA1012', 'A',     'Excellent condition, flagship unit',          NULL,                              969.99, 0.00,   1050.00,1149.99,'sold',     NULL,                  NULL,            NULL, 12),
    ('SNA1013', 'Scrap', 'Liquid damage, does not power on, unrepairable', NULL,                            79.99,  0.00,   0.00,   NULL,   'scrapped', NULL,                  NULL,            NULL, NULL);

-- ============================================================================
-- sale: completed (and one reversed) transactions
-- user ids: 1 = admin, 2 = john.ramirez, 3 = sarah.lee, 4 = mike.chen
-- ============================================================================
INSERT INTO sale
    (serial_number, technician_id, sale_price, sale_channel, buyer_info, sold_at, reversed_at, reversed_by_id, notes)
VALUES
    ('SNA1004', 2, 549.99,  'B2C Store', 'Order #10432, e.reyes@example.com',  '2024-02-20 15:32:00', NULL,                  NULL, NULL),
    ('SNA1008', 3, 649.99,  'B2C Store', 'Order #10501, m.patel@example.com',  '2024-03-02 12:05:00', NULL,                  NULL, NULL),
    ('SNA1012', 4, 1199.99, 'B2C Store', 'Order #10688, t.nguyen@example.com', '2024-06-10 16:45:00', NULL,                  NULL, NULL),
    ('SNA1011', 3, 529.99,  'B2C Store', 'Order #10555',                       '2024-04-01 09:00:00', '2024-04-05 10:20:00', 1,    'Buyer changed mind');