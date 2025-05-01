<?php

namespace App\Bll;

class MyFatoorah
{
    protected static $domain, $token;
	public static $currency = 'SAR';

	public static function init()
	{

		self::$token = 'tFACJorLeIfnL-QcvtGAPkYN2pA1Zngr7XoQVETfCg4nwbmpWkmDarRgdfPwdF0jRLS-4eUe-J7XQlB3vvsHUdUG2seSm90DXzacoB0U62lkTaA8OZ4UqtMuO5zGiBsWi2Q9twW4zxFN2TiPt1FePV2FCxJl_kz2MFfYbdCAqrvb0i995ZlWAVmqIpjNyCI8C_tYhyhUQTD1tUGyj10NkaSmHMVAd0n4_4RJ_X_taSesSHkuwJAeKShCb9TP3omnmw_hmG-idx5ICL6rSJ7fxkOVyvVlnZi5Kik8dH9bGPVcZ6nL5H_vDSWFt_xsksB7EbmftoTILI3YJeZ214FrGuhrYoeB_HkJiBKOkKB4XB_vZVE5PgdtqoYNoFaHMZDNA12iFCqdM3AgmNRtKKr3bvnVZb6htMK9JXTNjQq5JIsSPt7Eqs-5yanWinVZyngKVp3ELKH41e-BcvCsOz_6TJM58hp7Iid0hB8apF4cZtpo-L6XQA-8fYJyKUArEI5qJkCTT9y54CccYnmgknmaj9QVEy6vXnxOdAjmAKjOKZWo0uQxF1HIG_sYFoEWU2NMQzS6qHfyyYH0CqtAAjxWeEvLcf-b9uUjLo5DRlEeKUC-uMGTkTn-rRbbxZHSYr2dM1cT7ceAImb2ThcYD0tH1y6AxSEIPlzC7aoICN46SVMiWR_GRs82FLCXQVXw7Mfky9Lpig';
		self::$domain = 'api.myfatoorah.com';//'apitest.myfatoorah.com';
        // self::$token = 'rLtt6JWvbUHDDhsZnfpAhpYk4dxYDQkbcPTyGaKp2TYqQgG7FGZ5Th_WD53Oq8Ebz6A53njUoo1w3pjU1D4vs_ZMqFiz_j0urb_BH9Oq9VZoKFoJEDAbRZepGcQanImyYrry7Kt6MnMdgfG5jn4HngWoRdKduNNyP4kzcp3mRv7x00ahkm9LAK7ZRieg7k1PDAnBIOG3EyVSJ5kK4WLMvYr7sCwHbHcu4A5WwelxYK0GMJy37bNAarSJDFQsJ2ZvJjvMDmfWwDVFEVe_5tOomfVNt6bOg9mexbGjMrnHBnKnZR1vQbBtQieDlQepzTZMuQrSuKn-t5XZM7V6fCW7oP-uXGX-sMOajeX65JOf6XVpk29DP6ro8WTAflCDANC193yof8-f5_EYY-3hXhJj7RBXmizDpneEQDSaSz5sFk0sV5qPcARJ9zGG73vuGFyenjPPmtDtXtpx35A-BVcOSBYVIWe9kndG3nclfefjKEuZ3m4jL9Gg1h2JBvmXSMYiZtp9MR5I6pvbvylU_PP5xJFSjVTIz7IQSjcVGO41npnwIxRXNRxFOdIUHn0tjQ-7LwvEcTXyPsHXcMD8WtgBh-wxR8aKX7WPSsT1O8d8reb2aR7K3rkV3K82K_0OgawImEpwSvp9MNKynEAJQS6ZHe_J_l77652xwPNxMRTMASk1ZsJL';
        // self::$domain = 'apitest.myfatoorah.com';

	}

    protected static function doRequest($params, $query)
    {

		self::init();
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $header[0] = "Authorization: bearer " . self::$token;
        $header[1] = 'Content-Type:application/json';
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_URL, $query);
		$result = curl_exec($curl);
        if ($result == false) {
            error_log("Domain::CreateSubSomain Exception curl_exec threw error \"" . curl_error($curl) . "\" for $query");
        }
        curl_close($curl);
        return $result;
    }

    public static function initializePayment($price, $currency)
    {
		self::init();
        $directory = "/v2/InitiatePayment";
        $query = "https://" . self::$domain . "/{$directory}";
        $params = json_encode(["InvoiceAmount" => $price, "CurrencyIso" => $currency]);
        return self::doRequest($params, $query);
    }

    public static function executePayment($params)
    {
        self::init();
        $directory = "/v2/ExecutePayment";
        $query = "https://" . self::$domain . "/{$directory}";
		$params = json_encode($params);
        // dd($result);

        return self::doRequest($params, $query);
    }

    public static function status($key)
    {
		self::init();
        $params = ['Key' => $key, 'KeyType' => 'InvoiceId'];
        $directory = "/v2/GetPaymentStatus";
        $query = "https://" . self::$domain . "/{$directory}";
        $params = json_encode($params);
        $result = self::doRequest($params, $query);
        return json_decode($result);
    }

    public static function directPayment($params, $url)
    {
		self::init();
        $params = json_encode($params);
        return self::doRequest($params, $url);
    }

    public static function createInvoice($params)
    {
        self::init();
        $directory = "/v2/SendPayment";
        $query = "https://" . self::$domain . "/$directory";
        $params = json_encode($params);
        $result = self::doRequest($params, $query);
        return json_decode($result);
    }

    public static function getPaymentStatus($params)
    {
        self::init();
        $directory = "/v2/GetPaymentStatus";
        $query = "https://" . self::$domain . "/$directory";
        $params = json_encode($params);
        $result = self::doRequest($params, $query);
        return json_decode($result);
    }

    public static function makeRefund($params)
    {
		self::init();
        $directory = "v2/MakeRefund";
        $query = "https://" . self::$domain . "/{$directory}";
        $params = json_encode($params);
        $result = self::doRequest($params, $query);
        return json_decode($result);
    }
}
