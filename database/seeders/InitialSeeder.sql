SET FOREIGN_KEY_CHECKS = 0;

-- =========================
-- 1. HEALTH CENTERS
-- =========================
INSERT INTO HealthCenters (Name, Address) VALUES
('Central Health Unit', 'Central City'),
('North District Clinic', 'Northtown'),
('Southside Medical Center', 'Southville');

-- =========================
-- 2. USERS
-- =========================
INSERT INTO Users (Username, FName, LName, Role, Password, HealthCenterID) VALUES
('admin', 'Admin', 'User', 'Administrator', SHA2('password', 256), NULL),
('hstaff1', 'Health', 'Staff', 'Health Center Staff', SHA2('password', 256), 1),
('hstaff2', 'Center', 'User', 'Health Center Staff', SHA2('password', 256), 2),
('hpharm', 'Head', 'Pharmacist', 'Head Pharmacist', SHA2('password', 256), NULL),
('wstaff', 'Warehouse', 'Staff', 'Warehouse Staff', SHA2('password', 256), NULL),
('frank', 'Frank', 'Gold', 'Accounting Office User', SHA2('password', 256), NULL),
('grace', 'Grace', 'Silver', 'CMO/GSO/COA User', SHA2('password', 256), NULL);

-- =========================
-- 3. ITEMS
-- =========================
INSERT INTO Item (ItemName, ItemType, UnitOfMeasure) VALUES
('Paracetamol 500mg', 'Analgesic', 'Tablet'),
('Amoxicillin 250mg', 'Antibiotic', 'Capsule'),
('Gauze Pads 4x4', 'Medical Supply', 'Pack'),
('Salbutamol Nebule', 'Respiratory', 'Nebule'),
('Losartan 50mg', 'Cardiovascular', 'Tablet'),
('Antiseptic Solution 500ml', 'Antiseptic', 'Bottle');

-- =========================
-- 4. WAREHOUSE
-- =========================
INSERT INTO Warehouse (WarehouseName, Location, WarehouseType) VALUES
('Main Warehouse', 'Central City', 'Central');

-- =========================
-- 5. SUPPLIERS
-- =========================
INSERT INTO Supplier (Name, Address, ContactInfo) VALUES
('MedSupply Inc.', 'Pharma Lane', '555-1234'),
('Global Health Distributors', 'Wellness Ave', '555-5678');

-- =========================
-- 6. INVENTORY BATCHES
-- =========================
INSERT INTO CentralInventoryBatch 
(ItemID, ExpiryDate, QuantityOnHand, UnitCost, WarehouseID, QuantityReleased, DateReceived)
VALUES
(1, '2025-12-31', 4500, 0.10, 1, 0, '2023-01-01'),
(1, '2025-06-30', 3000, 0.11, 1, 0, '2023-01-01'),
(2, '2026-02-28', 1200, 0.25, 1, 0, '2023-01-01'),
(3, '2027-01-31', 8000, 1.50, 1, 0, '2023-01-01'),
(4, '2025-05-31', 150, 2.10, 1, 0, '2023-01-01'),
(5, '2026-08-31', 2500, 0.50, 1, 0, '2023-01-01'),
(6, '2028-01-01', 500, 3.00, 1, 0, '2023-01-01');

-- =========================
-- 7. PROCUREMENT ORDER
-- =========================
INSERT INTO ProcurementOrder 
(UserID, SupplierID, HealthCenterID, PONumber, PODate, StatusType)
VALUES
(2, 1, 1, 'PO-240001', '2023-10-10 09:00:00', 'Approved'),
(3, 2, 2, 'PO-240002', '2023-10-12 14:30:00', 'Pending'),
(5, 1, NULL, 'PO-230003', '2023-09-15 11:00:00', 'Completed');

-- =========================
-- 8. PROCUREMENT ORDER ITEMS
-- =========================
INSERT INTO ProcurementOrderItem (POID, ItemID, QuantityOrdered) VALUES
(1, 1, 5000),
(1, 3, 2000),
(2, 5, 3000),
(3, 2, 1500);

-- =========================
-- 9. REQUISITIONS
-- =========================
INSERT INTO Requisition (HealthCenterID, UserID, RequestDate, StatusType) VALUES
(1, 2, '2023-10-01 10:00:00', 'Approved'),
(2, 2, '2023-10-05 09:00:00', 'Pending'),
(3, 3, '2023-10-06 11:30:00', 'Rejected');

-- =========================
-- 10. REQUISITION ITEMS
-- =========================
INSERT INTO RequisitionItem (RequisitionID, ItemID, QuantityRequested) VALUES
(1, 1, 1000),
(1, 3, 500),
(2, 2, 500),
(2, 4, 200),
(2, 6, 100),
(3, 5, 2000);

-- =========================
-- 11. APPROVAL LOG
-- =========================
INSERT INTO ApprovalLog (RequisitionID, UserID, Decision, DecisionDate) VALUES
(1, 4, 'Approved', '2023-10-02 14:00:00'),
(3, 1, 'Rejected', '2023-10-07 16:00:00');

-- =========================
-- 12. RECEIVING
-- =========================
INSERT INTO Receiving (UserID, POID, ReceivedDate) VALUES
(5, 3, '2023-09-20 10:00:00');

-- =========================
-- 13. RECEIVING ITEMS
-- =========================
INSERT INTO ReceivingItem (ReceivingID, BatchID, QuantityReceived) VALUES
(1, 2, 1500);

-- =========================
-- 14. NOTIFICATIONS
-- =========================
INSERT INTO Notifications (id, title, message, timestamp, isRead, type, targetRoles) VALUES
(1, 'System Update', 'Scheduled maintenance tonight', NOW(), 1, 'system', NULL),
(2, 'Low Stock Alert', 'Paracetamol running low', NOW(), 1, 'alert', NULL),
(3, 'PO Approved', 'PO approved', NOW(), 0, 'po', NULL);

SET FOREIGN_KEY_CHECKS = 1;