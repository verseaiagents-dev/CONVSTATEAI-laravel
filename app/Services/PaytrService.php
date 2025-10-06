<?php

namespace App\Services;

class PaytrService
{
    public static function getIframeToken($order)
    {
        $merchant_id   = config('services.paytr.merchant_id');
        $merchant_key  = config('services.paytr.merchant_key');
        $merchant_salt = config('services.paytr.merchant_salt');

        $user_ip = request()->ip();
        $merchant_oid = $order->uuid; // benzersiz sipariş/abonelik numarası
        $email = $order->user->email;
        $payment_amount = $order->amount * 100; // KDV dahil tutar - 99.90 → 9990
        $currency = "TL";

        $user_basket = base64_encode(json_encode([
            [$order->plan->name, number_format($order->amount, 2, '.', ''), 1] // KDV dahil tutar
        ]));

        $no_installment = 0;
        $max_installment = 0;
        $test_mode = config('services.paytr.test_mode', 1);

        $merchant_ok_url = route('payment.success');
        $merchant_fail_url = route('payment.fail');
        $merchant_callback_url = url('/api/payment/callback');

        $hash_str = $merchant_id.$user_ip.$merchant_oid.$email.$payment_amount.$user_basket.$no_installment.$max_installment.$currency.$test_mode;
        $paytr_token = base64_encode(hash_hmac('sha256', $hash_str.$merchant_salt, $merchant_key, true));

        $post_vals = [
            'merchant_id'    => $merchant_id,
            'user_ip'        => $user_ip,
            'merchant_oid'   => $merchant_oid,
            'email'          => $email,
            'payment_amount' => $payment_amount,
            'paytr_token'    => $paytr_token,
            'user_basket'    => $user_basket,
            'debug_on'       => 1,
            'no_installment' => $no_installment,
            'max_installment'=> $max_installment,
            'user_name'      => $order->full_name ?? $order->user->name,
            'user_address'   => $order->address_line,
            'user_phone'     => $order->phone,
            'merchant_ok_url'=> $merchant_ok_url,
            'merchant_fail_url'=> $merchant_fail_url,
            'merchant_callback_url' => $merchant_callback_url,
            'timeout_limit'  => 30,
            'currency'       => $currency,
            'test_mode'      => $test_mode
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_vals);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        $result = @curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result, true);

        if($result['status'] != 'success') {
            throw new \Exception("PAYTR Hatası: ".$result['reason']);
        }

        return $result['token'];
    }
}
