<?php

namespace App\Http\Controllers;

use App\Libs\MongoManager;
use Illuminate\Http\Request;
use Auth;

class DashboardController extends Controller
{
    private $database;

    /**
     * DashboardController constructor.
     * @param Request $request
     */
    function __construct(Request $request)
    {
        $this->database = new MongoManager(env('DB_HOST'), env('DB_DATABASE'));
    }

    /**
     * Get data
     * @return \Illuminate\Http\JsonResponse
     */
    public function companyData(Request $request)
    {
        try {
            $document = $this->database->getDocuments('company_data', 1000);
            $data = isset($document[0]) ? $document[0] : [];

            $response = [
                'status' => 'ok',
                'message' => 'success',
                'data' => $data
            ];
        }
        catch (Execption $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }

        return response()->json($response);
    }

    /**
     * Get data
     * @return \Illuminate\Http\JsonResponse
     */
    public function companyDataUpdate(Request $request)
    {
        $payload = json_decode($request->payload);

        $company_name = isset($payload->company_name) ? $payload->company_name : '';
        $company_spend = isset($payload->company_spend) ? $payload->company_spend : null;
        $company_min_spend = isset($payload->company_min_spend) ? $payload->company_min_spend : null;
        $company_max_spend = isset($payload->company_max_spend) ? $payload->company_max_spend : null;
        $additional_notes = isset($payload->additional_notes) ? $payload->additional_notes : '';

        try {
            $document = $this->database->getDocuments('company_data', 1000);
            $company_data = isset($document[0]) ? $document[0] : false;

            if ($company_data) {
                $status = $this->database->updateDocumentByField(
                    '_id',
                    $company_data['_id'],
                    [
                        'company_name' => $company_name,
                        'company_spend' => $company_spend,
                        'company_min_spend' => $company_min_spend,
                        'company_max_spend' => $company_max_spend,
                        'additional_notes' => $additional_notes
                    ],
                    'company_data'
                );

                $response = [
                    'status' => 'ok',
                    'message' => 'Updated with success'
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Error trying to update company data'
                ];
            }
        }
        catch (Execption $e) {
            $response = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }

        return response()->json($response);
    }
}
