<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FonepayController extends Controller
{
    public function index()
    {
        $MD = 'P';
        $AMT = '10';
        $CRN = 'NPR';
        $DT = date('m/d/Y');
        $R1 = 'test';
        $R2 = 'test';
        $RU = route('verifyPayment');
        $PRN = uniqid();
        $PID = 'NBQM';
        $sharedSecretKey = 'a7e3512f5032480a83137793cb2021dc';

        $DV = hash_hmac('sha512', $PID.','.$MD.','.$PRN.','.$AMT.','.$CRN.','.$DT.','.$R1.','.$R2.','.$RU, $sharedSecretKey);

        $paymentLiveUrl = 'https://clientapi.fonepay.com/api/merchantRequest';
        $paymentDevUrl = 'https://dev-clientapi.fonepay.com/api/merchantRequest';

        return view('user.order.fonepay-check', compact('MD', 'AMT', 'CRN', 'DT', 'R1', 'R2', 'RU', 'PRN', 'PID', 'DV', 'paymentDevUrl'));
    }

    public function verifyPayment(Request $request)
    {
        $PID = 'NBQM';
        $sharedSecretKey = 'a7e3512f5032480a83137793cb2021dc';
        $prn = $request->PRN;
        $pid = $request->PID;
        $bid = $request->BID ?? '';
        $uid = $request->UID;
        $amount = 10;

        $requestData = [
            'PRN' => $prn,
            'PID' => $pid,
            'BID' => $bid,
            'AMT' => $amount,
            'UID' => $uid,
            'DV' => hash_hmac('sha512', $PID.','.$amount.','.$prn.','.$bid.','.$uid, $sharedSecretKey)
        ];

        //For Test Server
        $verifyDevUrl = "https://dev-clientapi.fonepay.com/api/merchantRequest/verificationMerchant";

        //For Live Server
        $verifyLiveUrl = "https://clientapi.fonepay.com/api/merchantRequest/verificationMerchant";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $verifyDevUrl.'?'.http_build_query($requestData));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $responseXML = curl_exec($ch);

        if ($response = simplexml_load_string($responseXML)) {
            if ($response->success == "true") {
                echo "Payment Verification Completed: ".$response->message;
            } else {
                echo "Payment Verification Failed: ".$response->message;
            }
        }
    }
}
