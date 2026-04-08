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
('admin', 'Admin', 'User', 'Administrator', '$2y$12$O7RbF0hFRFrrEBmXVFq6ouNuO.s.ua1pObOPTPoWgBCVqlpuL4y9y', NULL),
('hstaff1', 'Health', 'Staff', 'Health Center Staff', '$2y$12$O7RbF0hFRFrrEBmXVFq6ouNuO.s.ua1pObOPTPoWgBCVqlpuL4y9y', 1),
('hstaff2', 'Center', 'User', 'Health Center Staff', '$2y$12$O7RbF0hFRFrrEBmXVFq6ouNuO.s.ua1pObOPTPoWgBCVqlpuL4y9y', 2),
('hpharm', 'Head', 'Pharmacist', 'Head Pharmacist', '$2y$12$O7RbF0hFRFrrEBmXVFq6ouNuO.s.ua1pObOPTPoWgBCVqlpuL4y9y', NULL),
('wstaff', 'Warehouse', 'Staff', 'Warehouse Staff', '$2y$12$O7RbF0hFRFrrEBmXVFq6ouNuO.s.ua1pObOPTPoWgBCVqlpuL4y9y', NULL),
('frank', 'Frank', 'Gold', 'Accounting Office User', '$2y$12$O7RbF0hFRFrrEBmXVFq6ouNuO.s.ua1pObOPTPoWgBCVqlpuL4y9y', NULL),
('grace', 'Grace', 'Silver', 'CMO/GSO/COA User', '$2y$12$O7RbF0hFRFrrEBmXVFq6ouNuO.s.ua1pObOPTPoWgBCVqlpuL4y9y', NULL);

-- =========================
-- 2. ITEMS
-- =========================
INSERT INTO Item (ItemName, ItemType, UnitOfMeasure) VALUES
('Paracetamol 500mg', 'Medicine', 'Tablet'),
('Amoxicillin 250mg', 'Medicine', 'Capsule'),
('Gauze Pads 4x4', 'Supply', 'Pack'),
('Salbutamol Nebule', 'Medicine', 'Nebule'),
('Losartan 50mg', 'Medicine', 'Tablet'),
('Antiseptic Solution 500ml', 'Medicine', 'Bottle'),
('Disposable Syringes 5ml', 'Supply', 'Box'),
('Surgical Gloves', 'Supply', 'Pack'),
('Patient File Folder', 'Office Supply', 'Box'),
('Thermometer', 'Equipment', 'Piece'),
('Ibuprofen 200mg', 'Medicine', 'Tablet'),
('Cough Syrup 100ml', 'Medicine', 'Bottle'),
('Metformin 500mg', 'Medicine', 'Tablet'),
('Vitamin C 500mg', 'Medicine', 'Tablet'),
('Insulin 10ml', 'Medicine', 'Vial'),
('Ciprofloxacin 500mg', 'Medicine', 'Tablet'),
('Omeprazole 20mg', 'Medicine', 'Capsule'),
('Dextrose 5% 500ml', 'Medicine', 'Bag'),
('Saline 0.9% 1000ml', 'Medicine', 'Bag'),
('Antibiotic Ointment 15g', 'Medicine', 'Tube'),
('Cotton Balls', 'Supply', 'Box'),
('Bandage Roll 2in', 'Supply', 'Roll'),
('IV Cannula 18G', 'Supply', 'Piece'),
('Oxygen Mask', 'Equipment', 'Piece'),
('Biohazard Bags', 'Supply', 'Pack'),
('Safety Goggles', 'Equipment', 'Piece'),
('Exam Table Paper', 'Supply', 'Roll'),
('Sterile Drapes', 'Supply', 'Pack'),
('Alcohol Swabs', 'Supply', 'Box'),
('Wall Clock', 'Facility Equipment', 'Piece');

-- =========================
-- 3. WAREHOUSE
-- =========================
INSERT INTO Warehouse (WarehouseName, Location, WarehouseType) VALUES
('Main Warehouse', 'Central City', 'Central');

-- =========================
-- 4. SUPPLIERS
-- =========================
INSERT INTO Supplier (Name, Address, ContactInfo) VALUES
('MedSupply Inc.', 'Pharma Lane', '555-1234'),
('Global Health Distributors', 'Wellness Ave', '555-5678');

-- =========================
-- 5. INVENTORY BATCHES
-- =========================
INSERT INTO CentralInventoryBatch
(ItemID, ExpiryDate, QuantityOnHand, UnitCost, WarehouseID, QuantityReleased, DateReceived)
VALUES
(1, '2025-12-31', 4500, 0.10, 1, 0, '2023-01-01'),
(1, '2025-06-30', 3000, 0.11, 1, 0, '2023-01-01'),
(1, '2025-03-15', 1800, 0.09, 1, 0, '2023-02-01'),
(2, '2026-02-28', 1200, 0.25, 1, 0, '2023-01-01'),
(2, '2026-05-31', 900, 0.24, 1, 0, '2023-02-10'),
(3, '2027-01-31', 8000, 1.50, 1, 0, '2023-01-01'),
(3, '2026-07-31', 3500, 1.40, 1, 0, '2023-03-05'),
(4, '2025-05-31', 150, 2.10, 1, 0, '2023-01-01'),
(4, '2025-12-31', 220, 2.00, 1, 0, '2023-04-01'),
(5, '2026-08-31', 2500, 0.50, 1, 0, '2023-01-01'),
(5, '2027-01-31', 1800, 0.48, 1, 0, '2023-05-20'),
(6, '2028-01-01', 500, 3.00, 1, 0, '2023-01-01'),
(6, '2027-06-30', 320, 2.95, 1, 0, '2023-06-15'),
(11, '2026-11-30', 3200, 0.08, 1, 0, '2024-01-10'),
(11, '2026-06-30', 2100, 0.09, 1, 0, '2024-02-02'),
(12, '2025-09-30', 750, 3.50, 1, 0, '2024-02-05'),
(12, '2025-12-31', 450, 3.45, 1, 0, '2024-02-20'),
(13, '2027-12-31', 1800, 0.12, 1, 0, '2024-03-01'),
(13, '2027-08-31', 1000, 0.11, 1, 0, '2024-03-15'),
(14, '2027-12-31', 2000, 0.07, 1, 0, '2024-03-04'),
(14, '2028-05-31', 1100, 0.06, 1, 0, '2024-03-28'),
(15, '2026-05-31', 1200, 15.00, 1, 0, '2024-03-10'),
(16, '2026-08-31', 900, 0.45, 1, 0, '2024-03-15'),
(17, '2027-01-31', 1300, 0.30, 1, 0, '2024-03-18'),
(18, '2025-11-30', 400, 1.20, 1, 0, '2024-04-01'),
(19, '2025-11-30', 500, 0.90, 1, 0, '2024-04-01'),
(20, '2026-02-28', 650, 2.75, 1, 0, '2024-04-05'),
(21, '2030-01-01', 1200, 5.00, 1, 0, '2024-04-08'),
(22, '2030-01-01', 900, 2.00, 1, 0, '2024-04-10'),
(23, '2030-01-01', 800, 1.50, 1, 0, '2024-04-12'),
(24, '2030-01-01', 450, 8.00, 1, 0, '2024-04-15'),
(25, '2030-01-01', 600, 3.20, 1, 0, '2024-04-18'),
(26, '2030-01-01', 350, 4.50, 1, 0, '2024-04-20'),
(27, '2030-01-01', 1500, 1.10, 1, 0, '2024-04-22'),
(28, '2030-01-01', 700, 6.00, 1, 0, '2024-04-25'),
(29, '2030-01-01', 1400, 0.50, 1, 0, '2024-04-28'),
(30, '2030-01-01', 80, 12.00, 1, 0, '2024-05-01'),
(8, '2030-01-01', 300, 1.60, 1, 0, '2024-05-05'),
(9, '2030-01-01', 500, 0.20, 1, 0, '2024-05-10');

-- =========================
-- 6. PROCUREMENT ORDER
-- =========================
INSERT INTO ProcurementOrder
(UserID, SupplierID, HealthCenterID, PONumber, PODate, StatusType)
VALUES
(2, 1, 1, 'PO-240001', '2023-10-10 09:00:00', 'Approved'),
(3, 2, 2, 'PO-240002', '2023-10-12 14:30:00', 'Pending'),
(5, 1, NULL, 'PO-230003', '2023-09-15 11:00:00', 'Completed');

-- =========================
-- 7. PROCUREMENT ORDER ITEMS
-- =========================
INSERT INTO ProcurementOrderItem (POID, ItemID, QuantityOrdered) VALUES
(1, 1, 5000),
(1, 3, 2000),
(2, 5, 3000),
(3, 2, 1500);

-- =========================
-- 8. REQUISITIONS
-- =========================
INSERT INTO Requisition (HealthCenterID, UserID, RequestDate, StatusType) VALUES
(1, 2, '2023-10-01 10:00:00', 'Approved'),
(2, 2, '2023-10-05 09:00:00', 'Pending'),
(3, 3, '2023-10-06 11:30:00', 'Rejected');

-- =========================
-- 9. REQUISITION ITEMS
-- =========================
INSERT INTO RequisitionItem (RequisitionID, ItemID, QuantityRequested) VALUES
(1, 1, 1000),
(1, 3, 500),
(2, 2, 500),
(2, 4, 200),
(2, 6, 100),
(3, 5, 2000);

-- =========================
-- 10. APPROVAL LOG
-- =========================
INSERT INTO ApprovalLog (RequisitionID, UserID, Decision, DecisionDate) VALUES
(1, 4, 'Approved', '2023-10-02 14:00:00'),
(3, 1, 'Rejected', '2023-10-07 16:00:00');

-- =========================
-- 11. RECEIVING
-- =========================
INSERT INTO Receiving (UserID, POID, ReceivedDate) VALUES
(5, 3, '2023-09-20 10:00:00');

-- =========================
-- 12. RECEIVING ITEMS
-- =========================
INSERT INTO ReceivingItem (ReceivingID, BatchID, QuantityReceived) VALUES
(1, 2, 1500);

-- =========================
-- 13. NOTIFICATIONS
-- =========================
INSERT INTO Notifications (id, title, message, timestamp, isRead, type, targetRoles) VALUES
(1, 'System Update', 'Scheduled maintenance tonight', NOW(), 1, 'system', NULL),
(2, 'Low Stock Alert', 'Paracetamol running low', NOW(), 1, 'alert', NULL),
(3, 'PO Approved', 'PO approved', NOW(), 0, 'po', NULL);

SET FOREIGN_KEY_CHECKS = 1;
