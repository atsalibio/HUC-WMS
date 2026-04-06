<?php

namespace App\Http\Controllers;

use App\Services\PatientRequisitionService;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientRequisitionController extends Controller
{
    protected $patientRequisitionService;

    public function __construct(PatientRequisitionService $patientRequisitionService)
    {
        $this->patientRequisitionService = $patientRequisitionService;
    }

    public function storePatient(Request $request)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'HealthCenterID' => 'nullable|integer',
            'FName' => 'required|string',
            'MName' => 'nullable|string',
            'LName' => 'required|string',
            'Age' => 'required|integer',
            'Gender' => 'required|string',
            'Address' => 'nullable|string',
            'ContactNumber' => 'nullable|string',
        ]);

        // Enforce HealthCenterID exclusivity
        if ($user->Role === 'Health Center Staff') {
            $data['HealthCenterID'] = $user->HealthCenterID;
        } elseif (!$data['HealthCenterID']) {
            return response()->json(['success' => false, 'message' => 'Health Center ID is required for administrative staff'], 400);
        }

        try {
            $patient = $this->patientRequisitionService->createPatient($data);
            return response()->json(['success' => true, 'patient' => $patient]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to save patient: ' . $e->getMessage()], 500);
        }
    }

    public function searchPatients(Request $request)
    {
        $user = Auth::user();
        $query = $request->get('q', '');

        try {
            $patients = $this->patientRequisitionService->searchPatients($query, $user->HealthCenterID, $user->Role);
            return response()->json(['success' => true, 'patients' => $patients]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeRequisition(Request $request)
    {
        $user = Auth::user();
        
        $data = $request->validate([
            'patientId' => 'required|integer',
            'healthCenterId' => 'nullable|integer',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
            'idProof' => 'nullable|file|image|max:5120',
            'items' => 'required|array|min:1',
            'items.*.itemId' => 'required|integer',
            'items.*.quantity' => 'required|numeric|min:1',
        ]);

        if ($request->hasFile('idProof')) {
            $file = $request->file('idProof');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/img/uploads/patients'), $filename);
            $data['idProofPath'] = 'assets/img/uploads/patients/' . $filename;
        }

        // Enforce HealthCenterID exclusivity
        if ($user->Role === 'Health Center Staff') {
            $data['healthCenterId'] = $user->HealthCenterID;
        } elseif (!$data['healthCenterId']) {
             return response()->json(['success' => false, 'message' => 'Health Center ID is required for administrative staff'], 400);
        }

        try {
            $requisition = $this->patientRequisitionService->createRequisition($data, $user->UserID);
            return response()->json(['success' => true, 'requisition' => $requisition]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to store patient requisition: ' . $e->getMessage()], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $data = $request->validate([
            'status' => 'required|string|in:Approved,Rejected,Pending,Completed',
        ]);

        $user = Auth::user();

        try {
            $requisition = $this->patientRequisitionService->updateStatus($id, $data['status'], $user->UserID);
            return response()->json(['success' => true, 'requisition' => $requisition]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update requisition status'], 500);
        }
    }
}
