# Sample Seed Data for Iloilo City Warehouse Management System

-- HEALTH CENTERS
INSERT INTO HealthCenters (Name, Address) VALUES
('Jaro Health Center', 'Jaro, Iloilo City'),
('Mandurriao Health Center', 'Mandurriao, Iloilo City'),
('La Paz Health Center', 'La Paz, Iloilo City');

-- SUPPLIERS
INSERT INTO Supplier (Name, Address, ContactInfo) VALUES
('Unilab Pharmaceuticals', 'Makati City, Philippines', '09171234567'),
('Mercury Drug Supplier Division', 'Quezon City, Philippines', '09181234567'),
('Southstar Medical Supply', 'Cebu City, Philippines', '09221234567');

-- ITEMS
INSERT INTO Item (ItemName, Brand, ItemType, UnitOfMeasure, DosageUnit) VALUES
('Paracetamol', 'Biogesic', 'Medicine', 'Box', '500mg'),
('Amoxicillin', 'RiteMed', 'Medicine', 'Bottle', '250mg/5mL'),
('Vitamin C', 'Ceelin', 'Medicine', 'Bottle', '100mg/mL'),
('Face Mask', 'Generic', 'Supply', 'Box', NULL),
('Syringe', 'Terumo', 'Supply', 'Piece', NULL),
('Alcohol', 'Green Cross', 'Supply', 'Bottle', '500mL'),
('Ibuprofen', 'Advil', 'Medicine', 'Box', '200mg'),
('Cetirizine', 'Zyrtec', 'Medicine', 'Box', '10mg'),
('Metformin', 'Generic', 'Medicine', 'Bottle', '500mg'),
('Insulin Syringe', 'Terumo', 'Supply', 'Piece', NULL),
('Disposable Gloves', 'Generic', 'Supply', 'Box', NULL),
('Cotton Balls', 'Generic', 'Supply', 'Pack', NULL),
('Bandage', 'Generic', 'Supply', 'Roll', NULL),
('ORS', 'Hydrite', 'Medicine', 'Sachet', '4.1g'),
('Nebulizer Mask', 'Omron', 'Supply', 'Piece', NULL),
('Salbutamol', 'Ventolin', 'Medicine', 'Bottle', '2mg/5mL');

-- WAREHOUSES
INSERT INTO Warehouse (WarehouseName, Location, WarehouseType) VALUES
('Main Central Warehouse', 'Iloilo City Proper', 'Central');

-- USERS
INSERT INTO Users (Username, FName, LName, Role, Password, HealthCenterID) VALUES
('admin', 'Admin', 'User', 'Administrator', '$2y$12$O7RbF0hFRFrrEBmXVFq6ouNuO.s.ua1pObOPTPoWgBCVqlpuL4y9y', NULL),
('hstaff1', 'Health', 'Staff', 'Health Center Staff', '$2y$12$O7RbF0hFRFrrEBmXVFq6ouNuO.s.ua1pObOPTPoWgBCVqlpuL4y9y', 1),
('hstaff2', 'Center', 'User', 'Health Center Staff', '$2y$12$O7RbF0hFRFrrEBmXVFq6ouNuO.s.ua1pObOPTPoWgBCVqlpuL4y9y', 2),
('hpharm', 'Head', 'Pharmacist', 'Head Pharmacist', '$2y$12$O7RbF0hFRFrrEBmXVFq6ouNuO.s.ua1pObOPTPoWgBCVqlpuL4y9y', NULL),
('wstaff', 'Warehouse', 'Staff', 'Warehouse Staff', '$2y$12$O7RbF0hFRFrrEBmXVFq6ouNuO.s.ua1pObOPTPoWgBCVqlpuL4y9y', NULL),
('frank', 'Frank', 'Gold', 'Accounting Office User', '$2y$12$O7RbF0hFRFrrEBmXVFq6ouNuO.s.ua1pObOPTPoWgBCVqlpuL4y9y', NULL),
('grace', 'Grace', 'Silver', 'CMO/GSO/COA User', '$2y$12$O7RbF0hFRFrrEBmXVFq6ouNuO.s.ua1pObOPTPoWgBCVqlpuL4y9y', NULL);

-- CONTRACTS
INSERT INTO Contract (SupplierID, ContractNumber, StartDate, EndDate, ContractAmount, StatusType) VALUES
(1, 'CONT-2026-001', '2026-01-01', '2026-12-31', 500000.00, 'Active'),
(2, 'CONT-2026-002', '2026-01-15', '2026-12-31', 300000.00, 'Active');

-- PROCUREMENT ORDERS
INSERT INTO ProcurementOrder (UserID, SupplierID, HealthCenterID, ContractID, PONumber, PODate, StatusType, DocumentType) VALUES
(1, 1, NULL, 1, 'PO-2026-0001', '2026-01-10 09:00:00', 'Approved', 'Purchase Order'),
(1, 2, NULL, 2, 'PO-2026-0002', '2026-01-15 10:30:00', 'Approved', 'Purchase Order');

-- PROCUREMENT ORDER ITEMS
INSERT INTO ProcurementOrderItem (POID, ItemID, QuantityOrdered, UnitCost, ExpiryDate) VALUES
(1, 1, 1000, 5.50, '2027-01-31'),
(1, 2, 500, 12.00, '2027-06-30'),
(1, 4, 200, 150.00, NULL),
(2, 3, 300, 85.00, '2027-03-31'),
(2, 6, 150, 75.00, '2028-01-15'),
(2, 7, 800, 6.50, '2027-08-31'),
(2, 8, 600, 4.25, '2027-09-30'),
(2, 9, 400, 8.75, '2028-02-28'),
(2, 10, 1000, 12.00, NULL),
(2, 11, 500, 250.00, NULL),
(2, 12, 300, 45.00, NULL),
(2, 13, 250, 35.00, NULL),
(2, 14, 1200, 3.00, '2027-12-31'),
(2, 15, 150, 180.00, NULL),
(2, 16, 350, 65.00, '2027-11-30');

-- CENTRAL INVENTORY BATCHES
INSERT INTO CentralInventoryBatch (LotNumber, BatchNumber, ItemID, WarehouseID, ExpiryDate, QuantityOnHand, QuantityReleased, UnitCost) VALUES
('LOT-001', 'BATCH-PARA-001', 1, 1, '2027-01-31', 1000, 200, 5.50),
('LOT-002', 'BATCH-AMOX-001', 2, 1, '2027-06-30', 500, 100, 12.00),
('LOT-003', 'BATCH-VITC-001', 3, 1, '2027-03-31', 300, 50, 85.00),
('LOT-004', 'BATCH-MASK-001', 4, 1, NULL, 200, 20, 150.00),
('LOT-005', 'BATCH-ALCOHOL-001', 6, 1, '2028-01-15', 150, 25, 75.00),
('LOT-006', 'BATCH-IBU-001', 7, 1, '2027-08-31', 800, 120, 6.50),
('LOT-007', 'BATCH-CET-001', 8, 1, '2027-09-30', 600, 80, 4.25),
('LOT-008', 'BATCH-MET-001', 9, 1, '2028-02-28', 400, 40, 8.75),
('LOT-009', 'BATCH-SYR-001', 10, 1, NULL, 1000, 150, 12.00),
('LOT-010', 'BATCH-GLOVE-001', 11, 1, NULL, 500, 50, 250.00),
('LOT-011', 'BATCH-COTTON-001', 12, 1, NULL, 300, 20, 45.00),
('LOT-012', 'BATCH-BANDAGE-001', 13, 1, NULL, 250, 15, 35.00),
('LOT-013', 'BATCH-ORS-001', 14, 1, '2027-12-31', 1200, 200, 3.00),
('LOT-014', 'BATCH-NEB-001', 15, 1, NULL, 150, 10, 180.00),
('LOT-015', 'BATCH-SALB-001', 16, 1, '2027-11-30', 350, 30, 65.00);

-- RECEIVING
INSERT INTO Receiving (UserID, POID, ReceivedDate) VALUES
(2, 1, '2026-01-20 08:00:00'),
(2, 2, '2026-01-25 09:30:00');

-- RECEIVING ITEMS
INSERT INTO ReceivingItem (ReceivingID, ItemID, BatchID, QuantityReceived, UnitCost, DateReceived, WarehouseID) VALUES
(1, 1, 1, 1000, 5.50, '2026-01-20', 1),
(1, 2, 2, 500, 12.00, '2026-01-20', 1),
(1, 4, 4, 200, 150.00, '2026-01-20', 1),
(2, 3, 3, 300, 85.00, '2026-01-25', 1),
(2, 6, 5, 150, 75.00, '2026-01-25', 1),
(2, 7, 6, 800, 6.50, '2026-01-25', 1),
(2, 8, 7, 600, 4.25, '2026-01-25', 1),
(2, 9, 8, 400, 8.75, '2026-01-25', 1),
(2, 10, 9, 1000, 12.00, '2026-01-25', 1),
(2, 11, 10, 500, 250.00, '2026-01-25', 1),
(2, 12, 11, 300, 45.00, '2026-01-25', 1),
(2, 13, 12, 250, 35.00, '2026-01-25', 1),
(2, 14, 13, 1200, 3.00, '2026-01-25', 1),
(2, 15, 14, 150, 180.00, '2026-01-25', 1),
(2, 16, 15, 350, 65.00, '2026-01-25', 1);

-- REQUISITIONS
INSERT INTO Requisition (RequisitionNumber, HealthCenterID, UserID, RequestDate, StatusType) VALUES
('REQ-2026-0001', 1, 3, '2026-02-01 10:00:00', 'Approved'),
('REQ-2026-0002', 2, 4, '2026-02-03 14:00:00', 'Pending');

-- REQUISITION ITEMS
INSERT INTO RequisitionItem (RequisitionID, ItemID, QuantityRequested) VALUES
(1, 1, 200),
(1, 4, 20),
(2, 2, 100),
(2, 6, 30);

-- ISSUANCE
INSERT INTO Issuance (RequisitionID, UserID, IssueDate, StatusType) VALUES
(1, 2, '2026-02-05 09:00:00', 'Issued');

-- ISSUANCE ITEMS
INSERT INTO IssuanceItem (IssuanceID, BatchID, RequisitionItemID, QuantityIssued) VALUES
(1, 1, 1, 200),
(1, 4, 2, 20);

-- HEALTH CENTER INVENTORY BATCHES
INSERT INTO HCInventoryBatch (HealthCenterID, ItemID, BatchID, LotNumber, QuantityReceived, QuantityOnHand, UnitCost) VALUES
(1, 1, 1, 'LOT-001', 200, 180, 5.50),
(1, 4, 4, 'LOT-004', 20, 18, 150.00),
(2, 2, 2, 'LOT-002', 100, 100, 12.00);

-- ADJUSTMENT REQUESTS
INSERT INTO AdjustmentRequest (UserID, AdjustmentType, Reason, StatusType, AdjustmentDate) VALUES
(2, 'Disposal', 'Expired stock found during inspection', 'Pending', '2026-03-01 13:00:00');

-- ADJUSTMENT ITEMS
INSERT INTO AdjustmentItem (AdjustmentID, BatchID, HCBatchID, QuantityAdjusted, StatusType) VALUES
(1, 1, 1, 20, 'Pending');

-- PATIENTS
INSERT INTO HCPatient (HealthCenterID, FName, MName, LName, Age, Gender, Address, ContactNumber) VALUES
(1, 'Juan', 'Santos', 'Dela Cruz', 35, 'Male', 'Jaro, Iloilo City', '09170000001'),
(1, 'Maria', 'Lopez', 'Fernandez', 28, 'Female', 'Jaro, Iloilo City', '09170000002');

-- PATIENT REQUISITIONS
INSERT INTO HCPatientRequisition (PatientID, UserID, HealthCenterID, RequisitionNumber, RequestDate, StatusType, Diagnosis) VALUES
(1, 3, 1, 'PR-2026-0001', '2026-02-10 11:00:00', 'Approved', 'Fever and cough'),
(2, 3, 1, 'PR-2026-0002', '2026-02-11 15:00:00', 'Pending', 'Vitamin deficiency');

-- PATIENT REQUISITION ITEMS
INSERT INTO HCPatientRequisitionItem (PatientReqID, ItemID, QuantityRequested) VALUES
(1, 1, 10),
(1, 6, 1),
(2, 3, 2);

-- APPROVAL LOGS
INSERT INTO ApprovalLog (RequisitionID, UserID, Decision, DecisionDate) VALUES
(1, 1, 'Approved', '2026-02-02 08:30:00');

-- NOTIFICATIONS
INSERT INTO Notifications (UserID, Title, Message, Priority) VALUES
(3, 'Requisition Approved', 'Your requisition REQ-2026-0001 has been approved.', 'High'),
(2, 'New Receiving Task', 'A new receiving schedule has been assigned.', 'Normal');

-- SECURITY LOGS
INSERT INTO SecurityLog (UserID, ActionType, ActionDescription, IPAddress, ModuleAffected, ActionDate) VALUES
(1, 'Login', 'Administrator logged in successfully', '192.168.1.10', 'Authentication', NOW()),
(2, 'Update', 'Warehouse inventory updated', '192.168.1.11', 'Inventory', NOW());

-- AUDIT LOGS
INSERT INTO TransactionAuditLog (UserID, ReferenceType, ReferenceID, ActionType, ActionDetails, ActionDate) VALUES
(1, 'ProcurementOrder', '1', 'Create', 'Created procurement order PO-2026-0001', NOW()),
(2, 'Receiving', '1', 'Receive', 'Received stock for PO-2026-0001', NOW());
```
