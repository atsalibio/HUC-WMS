SET FOREIGN_KEY_CHECKS = 0;

-- =========================
-- HC Patient Seed
-- =========================
INSERT INTO HCPatient (HealthCenterID, FName, MName, LName, Age, Gender, Address, ContactNumber) VALUES
(1, 'Maria', 'S.', 'Reyes', 34, 'Female', '123 Main St, Central City', '09171234567'),
(1, 'Jose', 'A.', 'Lopez', 58, 'Male', '87 Riverside Ave, Central City', '09179876543'),
(2, 'Anna', 'L.', 'Cruz', 27, 'Female', '24 North St, Northtown', '09172345678'),
(2, 'Rafael', 'M.', 'Santos', 45, 'Male', '80 Pine Road, Northtown', '09173456789'),
(3, 'Dina', 'P.', 'Garcia', 62, 'Female', '5 Southside Blvd, Southville', '09174567890'),
(3, 'Marco', 'T.', 'Delos', 19, 'Male', '16 Orchard Lane, Southville', '09175678901'),
(1, 'Judith', 'R.', 'Mendoza', 51, 'Female', '12 Cherry Street, Central City', '09176789012'),
(2, 'Ramon', 'B.', 'Yap', 38, 'Male', '43 Willow Way, Northtown', '09177890123'),
(1, 'Cecilia', 'H.', 'Navarro', 29, 'Female', '99 Oak Drive, Central City', '09178901234'),
(3, 'Nathan', 'C.', 'Torres', 70, 'Male', '10 Sunset Ave, Southville', '09179012345');

-- =========================
-- HC Patient Requisition Seed
-- =========================
INSERT INTO HCPatientRequisition (PatientID, UserID, HealthCenterID, RequisitionNumber, RequestDate, StatusType, Diagnosis, Notes, ContactInfo, IDProof) VALUES
(1, 2, 1, 'HCPR-1001', '2024-03-05 09:15:00', 'Approved', 'Hypertension follow-up', 'Requires medicines for 30 days', '09171234567', 'SSS12345'),
(2, 2, 1, 'HCPR-1002', '2024-03-07 14:20:00', 'Pending', 'Chest pain evaluation', 'Needs immediate review', '09179876543', 'PHIC67890'),
(3, 3, 2, 'HCPR-1003', '2024-03-08 11:30:00', 'Approved', 'Acute cough and fever', 'Patient has persistent cough', '09172345678', 'DRIV1234'),
(4, 3, 2, 'HCPR-1004', '2024-03-10 16:45:00', 'Rejected', 'Minor wound care', 'Referred to barangay clinic', '09173456789', 'PHIC4321'),
(5, 2, 3, 'HCPR-1005', '2024-03-12 10:00:00', 'Pending', 'Diabetes management', 'Needs blood sugar medication', '09174567890', 'SSS67890'),
(6, 2, 1, 'HCPR-1006', '2024-03-15 13:25:00', 'Approved', 'Sports injury assessment', 'Pain relief and support bandage', '09175678901', 'DRIV5678'),
(7, 3, 1, 'HCPR-1007', '2024-03-18 08:40:00', 'Approved', 'Routine check-up', 'Also requests vitamin supplements', '09176789012', 'PHIC9876'),
(8, 3, 2, 'HCPR-1008', '2024-03-20 12:10:00', 'Pending', 'Allergy symptoms', 'Patient needs antihistamines', '09177890123', 'SSS32109'),
(9, 2, 1, 'HCPR-1009', '2024-03-22 15:50:00', 'Pending', 'Gastric discomfort', 'Prescribe PPI and antacid', '09178901234', 'PHIC24680'),
(10, 3, 3, 'HCPR-1010', '2024-03-25 09:05:00', 'Approved', 'Elderly care visit', 'Needs multivitamins and analgesics', '09179012345', 'DRIV9012');

-- =========================
-- HC Patient Requisition Item Seed
-- =========================
INSERT INTO HCPatientRequisitionItem (PatientReqID, ItemID, QuantityRequested) VALUES
(1, 1, 30),
(1, 6, 2),
(2, 5, 14),
(2, 15, 1),
(3, 12, 1),
(3, 6, 1),
(4, 3, 2),
(4, 25, 1),
(5, 11, 60),
(5, 4, 10),
(6, 9, 1),
(6, 8, 1),
(7, 14, 30),
(7, 16, 15),
(8, 1, 14),
(8, 2, 10),
(9, 14, 14),
(9, 26, 10),
(10, 10, 1),
(10, 13, 1);

-- =========================
-- HC Inventory Batch Seed
-- =========================
INSERT INTO HCInventoryBatch (HealthCenterID, ItemID, BatchID, LotNumber, ExpiryDate, QuantityReceived, QuantityOnHand, UnitCost, DateReceivedAtHC) VALUES
(1, 1, 1, 'HC-LOT-1001', '2025-12-31', 3000, 2500, 0.10, '2024-01-12 08:00:00'),
(1, 1, 2, 'HC-LOT-1002', '2025-06-30', 1500, 1200, 0.11, '2024-02-10 09:30:00'),
(1, 6, 7, 'HC-LOT-1010', '2028-01-01', 400, 360, 3.00, '2024-02-15 13:15:00'),
(2, 2, 3, 'HC-LOT-1020', '2026-02-28', 1000, 850, 0.25, '2024-01-20 10:25:00'),
(2, 3, 4, 'HC-LOT-1030', '2027-01-31', 2500, 2200, 1.50, '2024-01-25 11:00:00'),
(2, 12, 9, 'HC-LOT-1040', '2025-09-30', 600, 520, 3.50, '2024-03-01 14:00:00'),
(3, 4, 5, 'HC-LOT-1050', '2025-05-31', 300, 280, 2.10, '2024-03-05 09:50:00'),
(3, 11, 8, 'HC-LOT-1060', '2026-11-30', 1500, 1300, 0.08, '2024-03-08 10:10:00'),
(1, 14, 11, 'HC-LOT-1070', '2027-12-31', 1800, 1600, 0.07, '2024-03-10 12:20:00'),
(2, 16, 13, 'HC-LOT-1080', '2026-08-31', 700, 620, 0.45, '2024-03-15 08:45:00'),
(1, 8, 28, 'HC-LOT-1090', '2030-01-01', 200, 180, 1.50, '2024-04-01 14:10:00'),
(3, 9, 29, 'HC-LOT-1100', '2030-01-01', 250, 220, 0.50, '2024-04-05 11:35:00');

SET FOREIGN_KEY_CHECKS = 1;
