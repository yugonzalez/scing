<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ExternalServices\ServicreditoAPIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class ServicreditoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getClientInfo($id)
    {
        $scapi = new ServicreditoAPIController();
        if ($id) {
            $data = $scapi->getClientInfo($id);
            return response()->json([
                'isSuccess' => true,
                'passToAgent' => false,
                'message' => '',
                'data' => $data
                ]);
        } else {
            return response()->json([
                'isSuccess' => false,
                'passToAgent' => false,
                'message' => 'Se ha presentado un error consultando los datos del cliente.',
                'data' => null
                ]);
        }

    }

    public function loginClient(Request $request)
    {
        $scapi = new ServicreditoAPIController();
        if ($request->json()->all()) {
            $data = $scapi->loginClient($request->json()->all());
            return response()->json([
                'isSuccess' => true,
                'passToAgent' => false,
                'message' => '',
                'data' => $data
                ]);
        } else {
            return response()->json([
                'isSuccess' => false,
                'passToAgent' => false,
                'message' => 'Se ha presentado un error consultando los datos del cliente.',
                'data' => null
                ]);
        }

    }

    public function getCreditsInfo($id)
    {
        $scapi = new ServicreditoAPIController();
        if ($id) {
            $data = $scapi->getCreditsInfo($id);
            $credits = '';
            foreach ($data['listClienteInfoRespuesta'] as $key => $value) {
               $credits .= $key. '. '. $value['NumeroCredito'] . ': '. $value['Destino'] . '
';
            }
            return response()->json([
                'isSuccess' => true,
                'passToAgent' => false,
                'message' => '',
                'data' => $credits
                ]);
        } else {
            return response()->json([
                'isSuccess' => false,
                'passToAgent' => false,
                'message' => 'Se ha presentado un error consultando los datos del cliente.',
                'data' => null
                ]);
        }

    }

    public function getCreditDetail(Request $request)
    {
        $scapi = new ServicreditoAPIController();

        if ($request->json()->all() ) {
            $params = $request->json()->all();
            $id = $params['credit'];
            $data = $scapi->getCreditsInfo($params['identification']);

            if (!empty($data['listClienteInfoRespuesta'][ $id])) {
                return response()->json([
                    'isSuccess' => true,
                    'passToAgent' => false,
                    'message' => '',
                    'data' => $data['listClienteInfoRespuesta'][ $id]
                    ]);
            }

        } else {
            return response()->json([
                'isSuccess' => false,
                'passToAgent' => false,
                'message' => 'Se ha presentado un error consultando los datos del cliente.',
                'data' => null
                ]);
        }

    }


    public function passwordReset(Request $request)
    {
        $scapi = new ServicreditoAPIController();
        if ($request->json()->all()) {
            $data = $scapi->passwordReset($request->json()->all());
            return response()->json([
                'isSuccess' => true,
                'passToAgent' => false,
                'message' => '',
                'data' => $data
                ]);
        } else {
            return response()->json([
                'isSuccess' => false,
                'passToAgent' => false,
                'message' => 'Se ha presentado un error consultando los datos del cliente.',
                'data' => null
                ]);
        }
    }

    public function getLastPayment(Request $request)
    {
        $scapi = new ServicreditoAPIController();
        if ($request->json()->all() ) {
            $params = $request->json()->all();
            $id = $params['credit'];
            $data = $scapi->getCreditsInfo($params['identification']);
            $lastCredit = $data['listClienteInfoRespuesta'][ $id];
            $dataHistory = $scapi->getLastPayment($lastCredit['NumeroCredito']);


            if (!empty($dataHistory)) {
                return response()->json([
                    'isSuccess' => true,
                    'passToAgent' => false,
                    'message' => '',
                    'data' => $dataHistory['listPagosRealizados'][0]
                    ]);
            }

        } else {
            return response()->json([
                'isSuccess' => false,
                'passToAgent' => false,
                'message' => 'Se ha presentado un error consultando los datos del cliente.',
                'data' => null
                ]);
        }
    }

    public function getPaymentPlan(Request $request)
    {
        $scapi = new ServicreditoAPIController();
        if ($request->json()->all() ) {
            $params = $request->json()->all();
            $id = $params['credit'];
            $data = $scapi->getCreditsInfo($params['identification']);
            $credit = $data['listClienteInfoRespuesta'][$id];
            $paymentPlan = $scapi->getPaymentPlan($credit['NumeroCredito']);


            if (!empty($paymentPlan)) {
                $uuid =  Str::lower(Str::random(40));
                $file = Storage::disk('public')->put("plans/$uuid.pdf", base64_decode($paymentPlan['PlanPagosPDF']));


                return response()->json([
                    'isSuccess' => true,
                    'passToAgent' => false,
                    'message' =>  $file,
                    'data' => $uuid
                    ]);
            }

        } else {
            return response()->json([
                'isSuccess' => false,
                'passToAgent' => false,
                'message' => 'Se ha presentado un error consultando los datos del cliente.',
                'data' => null
                ]);
        }
    }

    public function generateCertificate(Request $request)
    {
        $scapi = new ServicreditoAPIController();
        if ($request->json()->all() ) {
            $data = $scapi->generateCertificate($request->json()->all());

            if (!empty($data)) {
                $uuid =  Str::lower(Str::random(40));
                $file = Storage::disk('public')->put("certificates/$uuid.pdf", base64_decode($data['SolicitudPDF']));
                return response()->json([
                    'isSuccess' => true,
                    'passToAgent' => false,
                    'message' =>  $file,
                    'data' => $uuid
                    ]);
            }

        } else {
            return response()->json([
                'isSuccess' => false,
                'passToAgent' => false,
                'message' => 'Se ha presentado un error consultando los datos del cliente.',
                'data' => null
                ]);
        }
    }

    public function downloadPaymentPlan($uuid) {
        $pathToFile = storage_path('app/public/plans/'. $uuid . '.pdf');
        return response()->download($pathToFile);
    }

    public function downloadCertificate($uuid) {
        $pathToFile = storage_path('app/public/certificates/'. $uuid . '.pdf');
        return response()->download($pathToFile);
    }
}
