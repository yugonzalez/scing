<?php

namespace App\Http\Controllers\ExternalServices;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ServicreditoAPIController extends Controller
{
    private const URL = 'https://apipruebas.servicredito.com.co/api/';
    private const AUTH_USER = '368|13256|556545254658752|0001';
    private const AUTH_PASS = 'Hi&wwOW@kqK';

    public function getClientInfo($identification)
    {
        $response = Http::withBasicAuth(self::AUTH_USER, self::AUTH_PASS)
        ->withOptions(['verify' => false])
        ->post(self::URL . 'ClienteInfoGeneral/ClienteInfoPersonal', [
            'TipoDocumento' => 'C',
            'Identificacion' => $identification,
        ]);

        return $response->json();
    }

    public function loginClient($params)
    {
        $response = Http::withBasicAuth(self::AUTH_USER, self::AUTH_PASS)
        ->withOptions(['verify' => false])
        ->post(self::URL . 'Usuario/LoginCliente', [
            'Login' => $params['username'],
            'Contrasena' => $params['password'],
        ]);

        return $response->json();
    }

    public function getCreditsInfo($identification)
    {
        $response = Http::withBasicAuth(self::AUTH_USER, self::AUTH_PASS)
        ->withOptions(['verify' => false])
        ->post(self::URL . 'ClienteInfoGeneral/ConsultarInfoGeneral', [
            'TipoDocumento' => 'C',
            'Identificacion' => $identification,
        ]);

        return $response->json();
    }

    public function passwordReset($params)
    {
        $response = Http::withBasicAuth(self::AUTH_USER, self::AUTH_PASS)
        ->withOptions(['verify' => false])
        ->post(self::URL . 'Usuario/SolicitarLoginCliente', [
            'TipoDocumento' => 'C',
            'Identificacion' => $params['identification'],
            'Fechanacimiento' => $params['birthDate'],
            'noRobot' => 'true',
            'url' => 'https://portalclientespruebas.servicredito.com.co/password-reset/{{tkn}}/{{codusu}}',
        ]);

        return $response->json();
    }

    public function getLastPayment($creditNumber)
    {
        $response = Http::withBasicAuth(self::AUTH_USER, self::AUTH_PASS)
        ->withOptions(['verify' => false])
        ->post(self::URL . 'EstadoMisCreditos/PagosRealizados', [
            'NumeroCredito' => $creditNumber,
        ]);

        return $response->json();
    }

    public function getPaymentPlan($creditNumber)
    {
        $response = Http::withBasicAuth(self::AUTH_USER, self::AUTH_PASS)
        ->withOptions(['verify' => false])
        ->post(self::URL . 'EstadoSolicitud/DescargarPlanPago', [
            'CodigoPagare' => $creditNumber,
        ]);

        return $response->json();
    }

    public function generateCertificate($params)
    {
        $response = Http::withBasicAuth(self::AUTH_USER, self::AUTH_PASS)
        ->withOptions(['verify' => false])
        ->post(self::URL . 'ObligacionesSaldoCero/Generar', [
            'Identificacion' => $params['identification'],
            'NombreDestinatario' => $params['destination'],
        ]);

        return $response->json();
    }

}
