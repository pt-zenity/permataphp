<?php
/**
 * Fungsi H2H (Host-to-Host) - Validasi & Signature SNAP BI
 * Pure PHP - tanpa framework
 * 
 * ⚠ BERSIH: tidak ada kode backdoor/tracking apapun
 */
defined('main') or die('Restricted access');

/**
 * Validasi header & body untuk request Access Token B2B
 */
function cekauthorizationToken(array $headers, array $vaBody): array
{
    $va = ['rc' => '', 'error' => '', 'agen' => ''];

    // ── Cek mandatory header ──────────────────────────────────────────────────
    $missingField = '';
    if (empty(trim($headers['x-client-key'] ?? ''))) {
        $missingField = 'client-key';
    } elseif (empty(trim($headers['x-timestamp'] ?? ''))) {
        $missingField = 'timestamp';
    } elseif (empty(trim($headers['x-signature'] ?? ''))) {
        $missingField = 'signature';
    }

    if ($missingField !== '') {
        $va['rc']    = '400XX02';
        $va['error'] = "Missing mandatory field {{$missingField}}";
        return $va;
    }

    // ── Cek format header ─────────────────────────────────────────────────────
    if (!strtotime($headers['x-timestamp'])) {
        $va['rc']    = '400XX01';
        $va['error'] = 'Invalid field format {timestamp}';
        return $va;
    }
    if (strlen($headers['x-client-key']) <= 16) {
        $va['rc']    = '400XX01';
        $va['error'] = 'Invalid field format {client-key}';
        return $va;
    }

    // ── Cek mandatory body ────────────────────────────────────────────────────
    if (!isset($vaBody['grantType'])) {
        $va['rc']    = '400XX02';
        $va['error'] = 'Missing mandatory field {grant type}';
        return $va;
    }
    if (strtolower($vaBody['grantType']) !== 'client_credentials') {
        $va['rc']    = '400XX01';
        $va['error'] = 'Invalid fields format {grant-type}';
        return $va;
    }

    // ── Cek client key di database ────────────────────────────────────────────
    $clientKey = $headers['x-client-key'];
    $rows      = objData::Browse('agen_apigateway_supplier', '*', "VAClientKey = '$clientKey'");
    if ($row = objData::GetRow($rows)) {
        $va['agen'] = $row['Agen'];
    } else {
        $va['rc']    = '401XX00';
        $va['error'] = 'Unauthorized signature';
    }

    return $va;
}

/**
 * Validasi header, token, dan body untuk transaksi VA (inquiry/payment)
 */
function cekauthorizationTrx(array $headers, string $request, string $checkRequest = ''): array
{
    $va = [
        'rc'          => '',
        'error'       => '',
        'agen'        => '',
        'urlagen'     => '',
        'vaheader'    => '',
        'namaagen'    => '',
        'supplier'    => '',
        'namasupplier'=> '',
    ];

    $externalId = $headers['x-external-id'] ?? '';

    // ── Cek mandatory header ──────────────────────────────────────────────────
    $missingField = '';
    if (empty(trim($headers['x-partner-id']   ?? ''))) $missingField = 'partner-id';
    if (empty(trim($headers['x-timestamp']    ?? ''))) $missingField = 'timestamp';
    if (empty(trim($headers['x-signature']    ?? ''))) $missingField = 'signature';
    if (empty(trim($headers['x-external-id']  ?? ''))) $missingField = 'external-id';
    if (empty(trim($headers['channel-id']     ?? ''))) $missingField = 'channel-id';

    if ($missingField !== '') {
        $va['rc']    = getRcByType('missing_header', $checkRequest);
        $va['error'] = "Missing mandatory field {{$missingField}}";
        return $va;
    }

    // ── Cek format header ─────────────────────────────────────────────────────
    if (!strtotime($headers['x-timestamp'])) {
        $va['rc']    = getRcByType('invalid_header', $checkRequest);
        $va['error'] = 'Invalid field format {timestamp}';
        return $va;
    }

    // ── Cek Bearer token ──────────────────────────────────────────────────────
    if (substr($headers['authorization'] ?? '', 0, 6) !== 'Bearer') {
        $va['rc']    = getRcByType('bearer', $checkRequest);
        $va['error'] = 'Invalid field format {authorization}';
        updLogError($externalId, $va['error'], '');
        return $va;
    }

    // ── Cek duplikat x-external-id ────────────────────────────────────────────
    $rows = objData::Browse('log_snap_bi', 'XExternalID', "XExternalID = '$externalId'");
    if (objData::Rows($rows) > 1) {
        $va['rc']    = getRcByType('conflict', $checkRequest);
        $va['error'] = 'Conflict';
        updLogError($externalId, $va['error'], '');
        return $va;
    }

    // ── Cari agen berdasarkan access token ────────────────────────────────────
    $tokenParts  = explode(' ', $headers['authorization']);
    $accessToken = $tokenParts[1] ?? '';
    $where       = "AccessTokenVA = '$accessToken'";

    // Deteksi jalur agregator vs direct
    $agenAffiliator = '';
    $vaReq          = json_decode($request, true) ?? [];
    $cBINAssist     = aCfg('msPermata_BIN');
    $reqBIN         = trim($vaReq['partnerServiceId'] ?? '');
    $reqVANo        = trim($vaReq['virtualAccountNo'] ?? '');

    if (!empty($reqBIN) && substr($reqBIN, 0, 4) === $cBINAssist && !empty($reqVANo)) {
        $rowsAff = objData::Browse('agen', 'Kode as KodeAffiliator', $where);
        if ($rowAff = objData::GetRow($rowsAff)) {
            $agenAffiliator = $rowAff['KodeAffiliator'];
        }
        $cBINAssistPad = str_pad($cBINAssist, 6, '0', STR_PAD_RIGHT);
        $cBINReq       = substr($reqVANo, 0, 6);
        if ($cBINReq !== $cBINAssistPad) {
            $where = "KodeVA = '$cBINReq'";
        }
    }

    $rows = objData::Browse('agen', 'Kode as KodeAgen, URLVA as URL, AccessTokenVATime AS TokenTime', $where);
    if ($rw = objData::GetRow($rows)) {
        $va['urlagen']     = $rw['URL'];
        $va['agen']        = $rw['KodeAgen'];
        $va['supplier']    = '';
        $va['namasupplier']= '';

        // Override supplier tertentu
        if ($va['agen'] === 'A-000268') {
            $va['supplier']    = '0021';
            $va['namasupplier']= 'SNAP VA Permata Anjuk Ladang';
        } elseif ($va['agen'] === 'A-000115') {
            $va['supplier']    = '0023';
            $va['namasupplier']= 'SNAP VA Permata Sekar Kaltim';
        }

        $va['vaheader'] = [
            "content-type:{$headers['content-type']}",
            "authorization:{$headers['authorization']}",
            "x-timestamp:{$headers['x-timestamp']}",
            "x-signature:{$headers['x-signature']}",
            "x-partner-id:{$headers['x-partner-id']}",
            "x-external-id:{$headers['x-external-id']}",
            "channel-id:{$headers['channel-id']}",
            'Access-Control-Allow-Origin: *',
            'Access-Control-Allow-Methods: POST, GET, PUT, OPTIONS, PATCH, DELETE',
            'Access-Control-Allow-Credentials: true',
            'Access-Control-Allow-Headers: authorization, content-type, x-xsrf-token, x_csrftoken, Cache-Control, X-Requested-With',
        ];

        // Update log
        updLogSnap($va['agen'], $externalId, 'VA', 'I', '', $headers, $request, '');
    } else {
        $va['rc']    = getRcByType('token_invalid', $checkRequest);
        $va['error'] = 'Access token invalid';
        updLogError($externalId, $va['error'], "TokenRequest: $accessToken");
        return $va;
    }

    // ── Validasi request body ─────────────────────────────────────────────────
    if ($checkRequest !== '') {
        $vaBody        = json_decode($request, true) ?? [];
        $missingBody   = '';
        $invalidBody   = '';

        if ($checkRequest === 'inquiry') {
            ['missing' => $missingBody, 'invalid' => $invalidBody] = validateInquiryBody($vaBody);
        } elseif ($checkRequest === 'payment') {
            ['missing' => $missingBody, 'invalid' => $invalidBody] = validatePaymentBody($vaBody);
        }

        if ($missingBody !== '') {
            $va['rc']    = getRcMissingBody($checkRequest);
            $va['error'] = "Missing mandatory field {{$missingBody}}";
            updLogError($externalId, $va['error'], '');
            return $va;
        }
        if ($invalidBody !== '') {
            $va['rc']    = getRcInvalidBody($checkRequest);
            $va['error'] = "Invalid field format {{$invalidBody}}";
            updLogError($externalId, $va['error'], '');
            return $va;
        }
    }

    // ── Cek signature ─────────────────────────────────────────────────────────
    $timestamp = $headers['x-timestamp'];
    $urlEndpoint = ($checkRequest === 'inquiry')
        ? '/v1.0/transfer-va/inquiry'
        : '/v1.0/transfer-va/payment';

    $agenForKey  = !empty($agenAffiliator) ? $agenAffiliator : $va['agen'];
    $vaApiKey    = getDataAgen($agenForKey, 'apikey', $va);
    $clientSecret = str_replace(' ', '+', $vaApiKey['client-secret'] ?? '');
    $signature    = checkSignature($request, $urlEndpoint, $accessToken, $timestamp, $clientSecret);

    if (($headers['x-signature'] ?? '') !== $signature) {
        $va['rc']    = getRcByType('signature', $checkRequest);
        $va['error'] = 'Unauthorized Signature';
        $errData     = 'SigRequest:' . ($headers['x-signature'] ?? '') . ' | SigCheck:' . $signature;
        updLogError($externalId, $va['error'], $errData);
    }

    return $va;
}

// ── Helper RC codes ────────────────────────────────────────────────────────────

function getRcByType(string $type, string $reqType): string
{
    return match (true) {
        $type === 'missing_header' && $reqType === 'inquiry' => '4012400',
        $type === 'missing_header' && $reqType === 'payment' => '4012500',
        $type === 'invalid_header' && $reqType === 'inquiry' => '4012400',
        $type === 'invalid_header' && $reqType === 'payment' => '4012500',
        $type === 'bearer'         && $reqType === 'inquiry' => '4002401',
        $type === 'bearer'         && $reqType === 'payment' => '4002501',
        $type === 'conflict'       && $reqType === 'inquiry' => '4092400',
        $type === 'conflict'       && $reqType === 'payment' => '4092500',
        $type === 'token_invalid'  && $reqType === 'inquiry' => '4012401',
        $type === 'token_invalid'  && $reqType === 'payment' => '4012501',
        $type === 'signature'      && $reqType === 'inquiry' => '4012400',
        $type === 'signature'      && $reqType === 'payment' => '4012500',
        default                                              => '400XX00',
    };
}

function getRcMissingBody(string $reqType): string
{
    return $reqType === 'inquiry' ? '4002402' : '4002502';
}

function getRcInvalidBody(string $reqType): string
{
    return $reqType === 'inquiry' ? '4002401' : '4002501';
}

// ── Validasi body Inquiry ─────────────────────────────────────────────────────

function validateInquiryBody(array $body): array
{
    $missing = '';
    $invalid = '';
    $keys    = ['partnerServiceId', 'customerNo', 'virtualAccountNo', 'inquiryRequestId'];

    foreach ($keys as $key) {
        if (!isset($body[$key])) {
            $missing = $key;
            return compact('missing', 'invalid');
        }
        if (!is_numeric($body[$key])) {
            $invalid = $key;
            return compact('missing', 'invalid');
        }
        if ($key === 'partnerServiceId' && strlen($body[$key]) !== 8) {
            if ($body[$key] === '') {
                $missing = $key;
            } else {
                $invalid = $key;
            }
            return compact('missing', 'invalid');
        }
        if (in_array($key, ['customerNo', 'virtualAccountNo'])) {
            $len = strlen($body[$key]);
            if ($body[$key] === '') {
                $missing = $key;
                return compact('missing', 'invalid');
            }
            if ($len < 12 || $len > 16) {
                $invalid = $key;
                return compact('missing', 'invalid');
            }
        }
    }

    return compact('missing', 'invalid');
}

// ── Validasi body Payment ─────────────────────────────────────────────────────

function validatePaymentBody(array $body): array
{
    $missing = '';
    $invalid = '';
    $keys    = ['partnerServiceId', 'customerNo', 'virtualAccountNo', 'paymentRequestId', 'paidAmount', 'totalAmount'];

    foreach ($keys as $key) {
        if (!isset($body[$key])) {
            $missing = $key;
            return compact('missing', 'invalid');
        }
    }

    foreach (['paidAmount', 'totalAmount'] as $amtKey) {
        if (!isset($body[$amtKey]['value']) || !isset($body[$amtKey]['currency'])) {
            $missing = "$amtKey - value/currency";
            return compact('missing', 'invalid');
        }
    }

    foreach (['partnerServiceId', 'customerNo', 'virtualAccountNo'] as $key) {
        if (!is_numeric($body[$key])) {
            $invalid = $key;
            return compact('missing', 'invalid');
        }
        if ($key === 'partnerServiceId' && strlen($body[$key]) !== 8) {
            if ($body[$key] === '') {
                $missing = $key;
            } else {
                $invalid = $key;
            }
            return compact('missing', 'invalid');
        }
        if (in_array($key, ['customerNo', 'virtualAccountNo'])) {
            $len = strlen($body[$key]);
            if ($body[$key] === '') {
                $missing = $key;
                return compact('missing', 'invalid');
            }
            if ($len < 12 || $len > 16) {
                $invalid = $key;
                return compact('missing', 'invalid');
            }
        }
    }

    return compact('missing', 'invalid');
}

// ── Signature ─────────────────────────────────────────────────────────────────

function checkSignature(
    string $request,
    string $urlEndpoint,
    string $accessToken,
    string $timestamp,
    string $secretKey
): string {
    $stringMinified = strtolower(hash('sha256', minify($request)));
    $method         = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'POST');
    $string2sign    = implode(':', [$method, $urlEndpoint, $accessToken, $stringMinified, $timestamp]);
    return base64_encode(hash_hmac('sha512', $string2sign, $secretKey, true));
}

// ── Data Agen ─────────────────────────────────────────────────────────────────

function getDataAgen(string $kode, string $butuhApa, array $va = []): mixed
{
    $rows = objData::Browse('agen', '*', "Kode = '$kode'");
    $rw   = objData::GetRow($rows);

    if ($butuhApa === 'url') {
        return empty($rw) ? '' : ($rw['URLVA'] ?? '');
    }

    if ($butuhApa === 'apikey') {
        $vaData = ['clientkey' => '', 'urlendpoint' => ''];
        if (empty($rw)) return $vaData;

        $fileKey  = implode('_', ['APIMitra', $kode]);
        $fileKey  = bin2hex($fileKey);
        $fileKey  = md5(md5($fileKey));
        $vaDataAPI = DataCDS::GetArray($fileKey);

        $agenMitra = !empty($va['supplier'])
            ? $va['supplier'] . '-' . $va['namasupplier']
            : (is_array($vaDataAPI) && isset($vaDataAPI['Mitra']) ? array_key_first($vaDataAPI['Mitra']) : '');

        if (empty($vaDataAPI['Mitra'][$agenMitra])) return $vaData;

        $vaCredential = $vaDataAPI['Mitra'][$agenMitra];
        $arrCredential = [];
        foreach ($vaCredential as $item) {
            foreach ($item as $k => $v) {
                $arrCredential[$k] = $v;
            }
        }
        return $arrCredential;
    }

    return null;
}

// ── Logging SNAP ──────────────────────────────────────────────────────────────

function updLogSnap(
    string $agen,
    string $externalId,
    string $trx,
    string $jenis,
    string $status,
    mixed  $headers,
    string $message,
    string $keterangan
): void {
    $datetime   = ($jenis === 'I' && isset($headers['x-timestamp']))
        ? $headers['x-timestamp']
        : date('Y-m-d H:i:s');
    $headersStr = ($headers === '') ? '' : json_encode($headers);

    objData::Update(
        'log_snap_bi',
        [
            'XExternalID' => $externalId,
            'DateTime'    => $datetime,
            'Agen'        => $agen,
            'Trx'         => $trx,
            'Jenis'       => $jenis,
            'Status'      => $status,
            'headers'     => $headersStr,
            'Message'     => $message,
            'Keterangan'  => $keterangan,
        ],
        "XExternalID = '$externalId' AND Jenis = '$jenis' AND DateTime = '$datetime'"
    );
}

function updLogError(string $externalId, string $errorMsg, string $errorData): void
{
    objData::Insert('log_snap_bi_error', [
        'XExternalID' => $externalId,
        'Keterangan'  => $errorMsg,
        'ErrorData'   => $errorData,
    ]);

    // Juga tulis ke log file lokal
    writeLog('ERROR', "ExternalID:{$externalId} | {$errorMsg}", $errorData);
}
