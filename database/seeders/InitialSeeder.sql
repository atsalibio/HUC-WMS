SET FOREIGN_KEY_CHECKS = 0;

-- =========================
-- 1. HEALTH CENTERS
-- =========================
INSERT INTO HealthCenters (Name, Address) VALUES
('Central Health Unit', 'Central City'),
('North District Clinic', 'Northtown'),
('Southside Medical Center', 'Southville');

-- =========================
-- 2. USERS (DO NOT TOUCH)
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
INSERT INTO Item (ItemName, Brand, ItemType, UnitOfMeasure, DosageUnit) VALUES
('Paracetamol 500mg', 'Generic', 'Analgesic', 'Tablet', '500mg'),
('Amoxicillin 250mg', 'Generic', 'Antibiotic', 'Capsule', '250mg'),
('Gauze Pads 4x4', 'MedSupply', 'Medical Supply', 'Pack', NULL),
('Salbutamol Nebule', 'AstraZeneca', 'Respiratory', 'Nebule', '2.5mg/ml'),
('Losartan 50mg', 'Generic', 'Cardiovascular', 'Tablet', '50mg'),
('Antiseptic Solution 500ml', 'Generic', 'Antiseptic', 'Bottle', '500ml');

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
-- 6. CONTRACTS
-- =========================
INSERT INTO Contract (SupplierID, ContractNumber, StartDate, EndDate, ContractAmount, StatusType) VALUES
(1, 'CON-2024-001', '2024-01-01', '2026-12-31', 50000.00, 'Active'),
(2, 'CON-2024-002', '2024-02-01', '2025-12-31', 75000.00, 'Active');

-- =========================
-- 7. INVENTORY BATCHES
-- =========================
INSERT INTO CentralInventoryBatch
(ItemID, LotNumber, WarehouseID, ExpiryDate, QuantityOnHand, QuantityReleased, UnitCost, DateReceived)
VALUES
(1, 'LOT001', 1, '2025-12-31', 4500, 0, 0.10, '2023-01-01'),
(1, 'LOT002', 1, '2025-06-30', 3000, 0, 0.11, '2023-01-01'),
(2, 'LOT003', 1, '2026-02-28', 1200, 0, 0.25, '2023-01-01'),
(3, 'LOT004', 1, '2027-01-31', 8000, 0, 1.50, '2023-01-01'),
(4, 'LOT005', 1, '2025-05-31', 150, 0, 2.10, '2023-01-01'),
(5, 'LOT006', 1, '2026-08-31', 2500, 0, 0.50, '2023-01-01'),
(6, 'LOT007', 1, '2028-01-01', 500, 0, 3.00, '2023-01-01');

-- =========================
-- 8. PROCUREMENT ORDER
-- =========================
INSERT INTO ProcurementOrder
(UserID, SupplierID, SupplierName, SupplierAddress, HealthCenterID, ContractID, PONumber, PODate, StatusType, PhotoPath, DocumentType)
VALUES
(2, 1, 'MedSupply Inc.', 'Pharma Lane', 1, 1, 'PO-240001', '2023-10-10 09:00:00', 'Approved', NULL, 'Purchase Order'),
(3, 2, 'Global Health Distributors', 'Wellness Ave', 2, 2, 'PO-240002', '2023-10-12 14:30:00', 'Pending', NULL, 'Purchase Order'),
(5, 1, 'MedSupply Inc.', 'Pharma Lane', NULL, 1, 'PO-230003', '2023-09-15 11:00:00', 'Completed', NULL, 'Purchase Order');

-- =========================
-- 9. PROCUREMENT ORDER ITEMS
-- =========================
INSERT INTO ProcurementOrderItem (POID, ItemID, QuantityOrdered, UnitCost, ExpiryDate) VALUES
(1, 1, 5000, 0.10, '2025-12-31'),
(1, 3, 2000, 1.50, '2027-01-31'),
(2, 5, 3000, 0.50, '2026-08-31'),
(3, 2, 1500, 0.25, '2026-02-28');

-- =========================
-- 10. RECEIVING
-- =========================
INSERT INTO Receiving (UserID, POID, ReceivedDate) VALUES
(5, 3, '2023-09-20 10:00:00');

-- =========================
-- 11. RECEIVING ITEMS
-- =========================
INSERT INTO ReceivingItem (ReceivingID, ItemID, BatchID, QuantityReceived, ExpiryDate, UnitCost, DateReceived, WarehouseID) VALUES
(1, 2, 3, 1500, '2026-02-28', 0.25, '2023-09-20', 1);

-- =========================
-- 12. REQUISITIONS
-- =========================
INSERT INTO Requisition (RequisitionNumber, HealthCenterID, UserID, RequestDate, StatusType) VALUES
('REQ-2023-001', 1, 2, '2023-10-01 10:00:00', 'Approved'),
('REQ-2023-002', 2, 2, '2023-10-05 09:00:00', 'Pending'),
('REQ-2023-003', 3, 3, '2023-10-06 11:30:00', 'Rejected');

-- =========================
-- 13. REQUISITION ITEMS
-- =========================
INSERT INTO RequisitionItem (RequisitionID, ItemID, QuantityRequested) VALUES
(1, 1, 1000),
(1, 3, 500),
(2, 2, 500),
(2, 4, 200),
(2, 6, 100),
(3, 5, 2000);

-- =========================
-- 14. APPROVAL LOG
-- =========================
INSERT INTO ApprovalLog (RequisitionID, UserID, Decision, DecisionDate) VALUES
(1, 4, 'Approved', '2023-10-02 14:00:00'),
(3, 1, 'Rejected', '2023-10-07 16:00:00');

-- =========================
-- 15. ISSUANCE
-- =========================
INSERT INTO Issuance (RequisitionID, UserID, IssueDate, StatusType) VALUES
(1, 5, '2023-10-03 15:00:00', 'Issued');

-- =========================
-- 16. ISSUANCE ITEMS
-- =========================
INSERT INTO IssuanceItem (IssuanceID, BatchID, RequisitionItemID, QuantityIssued) VALUES
(1, 1, 1, 1000),
(1, 4, 2, 500);

-- =========================
-- 17. REQUISITION ADJUSTMENT
-- =========================
INSERT INTO RequisitionAdjustment (IssuanceID, UserID, AdjustmentType, AdjustmentDate, Reason) VALUES
(1, 5, 'Return', '2023-10-04 10:00:00', 'Excess quantity');

-- =========================
-- 18. REQUISITION ADJUSTMENT DETAIL
-- =========================
INSERT INTO RequisitionAdjustmentDetail (RequisitionAdjustmentID, BatchID, QuantityAdjusted) VALUES
(1, 1, -100);

-- =========================
-- 19. INVENTORY ADJUSTMENT
-- =========================
INSERT INTO InventoryAdjustment (BatchID, UserID, AdjustmentType, AdjustmentQuantity, Reason, AdjustmentDate) VALUES
(2, 5, 'Disposal', -50, 'Expired', '2023-10-05 12:00:00');

-- =========================
-- 20. HC INVENTORY BATCH
-- =========================
INSERT INTO HCInventoryBatch (HealthCenterID, ItemID, BatchID, LotNumber, ExpiryDate, QuantityReceived, QuantityOnHand, UnitCost, DateReceivedAtHC) VALUES
(1, 1, 1, 'LOT001', '2025-12-31', 1000, 900, 0.10, '2023-10-03'),
(2, 2, 3, 'LOT003', '2026-02-28', 500, 500, 0.25, '2023-10-04');

-- =========================
-- 21. NOTICE OF ISSUE
-- =========================
INSERT INTO NoticeOfIssue (BatchID, UserID, ReportDate, IssueType, QuantityAffected, StatusType, Remarks) VALUES
(2, 5, '2023-10-06 14:00:00', 'Damaged', 20, 'Reported', 'Packaging damaged');

-- =========================
-- 22. TRANSACTION AUDIT LOG
-- =========================
INSERT INTO TransactionAuditLog (UserID, ReferenceType, ReferenceID, ActionType, ActionDate) VALUES
(5, 'Issuance', 1, 'Created', '2023-10-03 15:00:00'),
(5, 'InventoryAdjustment', 1, 'Created', '2023-10-05 12:00:00');

-- =========================
-- 23. SECURITY LOG
-- =========================
INSERT INTO SecurityLog (UserID, ActionType, ActionDescription, IPAddress, ModuleAffected, ActionDate) VALUES
(1, 'Login', 'Successful login', '192.168.1.1', 'Authentication', '2023-10-01 08:00:00'),
(5, 'Data Export', 'Exported inventory report', '192.168.1.2', 'Reports', '2023-10-02 16:00:00');

-- =========================
-- 24. REPORT
-- =========================
INSERT INTO Report (UserID, ReportType, GeneratedDate, GeneratedForOffice) VALUES
(1, 'Inventory Summary', '2023-10-01 12:00:00', 'Central Office'),
(4, 'Requisition Report', '2023-10-03 09:00:00', 'Pharmacy');

-- =========================
-- 25. NOTIFICATION
-- =========================
INSERT INTO Notification (UserID, TargetRole, Title, Message, Link, Priority, IsRead) VALUES
(NULL, 'Administrator', 'System Update', 'Scheduled maintenance tonight', NULL, 'Normal', 1),
(NULL, 'Warehouse Staff', 'Low Stock Alert', 'Paracetamol running low', NULL, 'High', 1),
(2, NULL, 'PO Approved', 'Your PO has been approved', '/po/1', 'Normal', 0);

-- =========================
-- 26. HC PATIENT
-- =========================
INSERT INTO HCPatient (HealthCenterID, FName, MName, LName, Age, Gender, Address, ContactNumber) VALUES
(1, 'John', 'Michael', 'Doe', 35, 'Male', '123 Main St', '555-1111'),
(2, 'Jane', 'Marie', 'Smith', 28, 'Female', '456 Oak Ave', '555-2222');

-- =========================
-- 27. HC PATIENT REQUISITION
-- =========================
INSERT INTO HCPatientRequisition (PatientID, UserID, HealthCenterID, RequisitionNumber, RequestDate, StatusType, Diagnosis, Notes, ContactInfo, IDProof) VALUES
(1, 2, 1, 'PAT-REQ-2023-001', '2023-10-07 10:00:00', 'Pending', 'Hypertension', 'Needs medication refill', '555-1111', 'ID123'),
(2, 3, 2, 'PAT-REQ-2023-002', '2023-10-08 11:00:00', 'Approved', 'Asthma', 'Inhaler needed', '555-2222', 'ID456');

-- =========================
-- 28. HC PATIENT REQUISITION ITEM
-- =========================
INSERT INTO HCPatientRequisitionItem (PatientReqID, ItemID, QuantityRequested) VALUES
(1, 5, 30),
(2, 4, 10);

SET FOREIGN_KEY_CHECKS = 1;
