<?php
/**
 * Inquiry Controller - VA Inquiry
 * Pure PHP - tanpa framework
 * 
 * ⚠ BERSIH: kode eval(base64_decode(...)) telah dihapus
 */
defined('main') or die('Restricted access');

class InquiryController
{
    public function index(): void
    {
        $headers  = $this->getRequestHeaders();
        $request  = file_get_contents('php://input');
        $vaReq    = json_decode(preg_replace('/\s+/', '', $request), true) ?? [];

        $externalId = $headers['x-external-id'] ?? '';

        // Log request awal
        updLogSnap('', $externalId, 'VA', 'I', '', $headers, $request, '');

        if (isset($vaReq['inquiryRequestId'])) {
            $vaCheck = cekauthorizationTrx($headers, $request, 'inquiry');
            $agen    = $vaCheck['agen'];

            if ($vaCheck['rc'] === '' && $vaCheck['error'] === '') {
                // Cek apakah layanan sedang tutup (akhir tahun)
                date_default_timezone_set('Asia/Jakarta');
                $now = time();
                if ($now >= TutupPPOB::AKHIRTAHUN_FROMTIME && $now <= TutupPPOB::AKHIRTAHUN_TOTIME) {
                    $response = json_encode([
                        'responseCode'    => TutupPPOB::AKHIRTAHUN_RCRQ,
                        'responseMessage' => TutupPPOB::AKHIRTAHUN_MSG_THNBARU,
                    ]);
                } else {
                    // Override URL untuk agen tertentu
                    if ($agen === 'A-000115') {
                        $vaCheck['urlagen'] = 'aa.submodule.sis1.net/assist-bpr.net_fanani/index_mobile.php';
                    }

                    $response = SendHTTPPost($vaCheck['urlagen'], $request, $vaCheck['vaheader']);
                    $vaResp   = json_decode($response, true);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $vaCheck['error'] = $response;
                        $rc               = '4042415';
                        $msg              = 'Transaction Not Permitted. Invalid Response of Inquiry';
                        $response         = json_encode(['responseCode' => $rc, 'responseMessage' => $msg]);
                        $vaCheck['rc']    = $rc;
                    } else {
                        $vaCheck['rc']    = $vaResp['responseCode']    ?? '';
                        $vaCheck['error'] = $vaResp['responseMessage'] ?? '';
                    }
                }
            } else {
                $response = json_encode([
                    'responseCode'    => $vaCheck['rc'],
                    'responseMessage' => $vaCheck['error'],
                ]);
            }
        } else {
            $agen             = '';
            $vaCheck['rc']    = '4042415';
            $vaCheck['error'] = 'Transaction Not Permitted. Invalid Request of Inquiry';
            $response         = json_encode([
                'responseCode'    => $vaCheck['rc'],
                'responseMessage' => $vaCheck['error'],
            ]);
        }

        // Log response
        $timestamp      = date('c');
        $headerResponse = [
            'Content-Type' => 'application/json',
            'X-TIMESTAMP'  => $timestamp,
        ];
        updLogSnap(
            $agen,
            $externalId,
            'VA', 'S',
            $vaCheck['rc'] ?? '',
            $headerResponse,
            $response,
            $vaCheck['error'] ?? ''
        );

        header('Content-Type: application/json');
        header('X-TIMESTAMP: ' . $timestamp);
        echo $response;
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
}
