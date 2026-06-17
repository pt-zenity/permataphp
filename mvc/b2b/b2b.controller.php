<?php
/**
 * B2B Controller - Generate Access Token
 * Pure PHP - tanpa framework
 * 
 * ⚠ BERSIH: kode eval(base64_decode(...)) telah dihapus
 */
defined('main') or die('Restricted access');

class B2bController
{
    public function index(): void
    {
        $headers = $this->getRequestHeaders();
        $request = file_get_contents('php://input');
        $vaReq   = json_decode($request, true) ?? [];

        $vaCheck = cekauthorizationToken($headers, $vaReq);

        if ($vaCheck['rc'] === '' && $vaCheck['error'] === '') {
            $nTime       = time() + 900; // expired dalam 15 menit
            $accessToken = md5(base64_encode(hash_hmac('sha256', (string)$nTime, $headers['x-client-key'], true)));

            // Simpan access token ke database
            objData::Update(
                'agen',
                ['AccessTokenVA' => $accessToken, 'AccessTokenVATime' => $nTime],
                "Kode = '{$vaCheck['agen']}'"
            );

            $response = json_encode([
                'responseCode'    => '2007300',
                'responseMessage' => 'successful',
                'accessToken'     => $accessToken,
                'tokenType'       => 'Bearer',
                'expiresIn'       => '900',
            ]);
        } else {
            $response = json_encode([
                'responseCode'    => $vaCheck['rc'],
                'responseMessage' => $vaCheck['error'],
            ]);
        }

        $this->sendJsonResponse($response);
    }

    private function getRequestHeaders(): array
    {
        $headers = [];

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers() ?: [];
        } else {
            foreach ($_SERVER as $key => $value) {
                if (str_starts_with($key, 'HTTP_')) {
                    $name           = str_replace('_', '-', substr($key, 5));
                    $headers[$name] = $value;
                }
            }
            if (isset($_SERVER['CONTENT_TYPE']))   $headers['Content-Type']   = $_SERVER['CONTENT_TYPE'];
            if (isset($_SERVER['CONTENT_LENGTH']))  $headers['Content-Length'] = $_SERVER['CONTENT_LENGTH'];
        }

        return array_change_key_case($headers, CASE_LOWER);
    }

    private function sendJsonResponse(string $body): void
    {
        header('Content-Type: application/json');
        header('X-TIMESTAMP: ' . date('c'));
        echo $body;
    }
}
