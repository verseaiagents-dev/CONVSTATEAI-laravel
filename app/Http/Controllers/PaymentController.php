<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\PaytrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    /**
     * Billing form sayfası - Fatura bilgileri
     */
    public function billingForm($planId)
    {
        $plan = Plan::findOrFail($planId);
        $user = Auth::user();

        // Kullanıcının zaten aktif aboneliği var mı kontrol et
        if ($user->activeSubscription) {
            return redirect()->route('dashboard.subscription.index')
                ->with('warning', 'Zaten aktif bir aboneliğiniz bulunmaktadır.');
        }

        return view('payment.billing-form', compact('plan'));
    }

    /**
     * Checkout sayfası - Plan seçimi ve ödeme başlatma
     */
    public function checkout(Request $request, $planId)
    {
        $plan = Plan::findOrFail($planId);
        $user = Auth::user();

        // Form validasyonu
        $request->validate([
            'company_name' => 'required|string|max:255',
            'full_name' => 'required|string|max:255',
            'tax_number' => 'required|string|max:50',
            'tax_office' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'address_line' => 'required|string|max:500',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255'
        ]);

        // Kullanıcının zaten aktif aboneliği var mı kontrol et
        if ($user->activeSubscription) {
            return redirect()->route('dashboard.subscription.index')
                ->with('warning', 'Zaten aktif bir aboneliğiniz bulunmaktadır.');
        }

        // Order kaydı oluştur
        $order = Order::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'uuid' => 'SUB_' . uniqid() . '_' . time(),
            'status' => 'pending',
            'payment_method' => 'paytr',
            'company_name' => $request->company_name,
            'full_name' => $request->full_name,
            'tax_number' => $request->tax_number,
            'tax_office' => $request->tax_office,
            'country' => $request->country,
            'city' => $request->city,
            'district' => $request->district,
            'address_line' => $request->address_line,
            'postal_code' => $request->postal_code,
            'phone' => $request->phone,
            'email' => $request->email
        ]);

        try {
            // Debug: PayTR config değerlerini kontrol et
            Log::info('PayTR Config Check', [
                'merchant_id' => config('services.paytr.merchant_id'),
                'merchant_key' => config('services.paytr.merchant_key') ? 'SET' : 'NOT SET',
                'merchant_salt' => config('services.paytr.merchant_salt') ? 'SET' : 'NOT SET',
                'test_mode' => config('services.paytr.test_mode')
            ]);
            
            $token = PaytrService::getIframeToken($order);
            return view('payment.iframe', compact('token', 'order'));
        } catch (\Exception $e) {
            Log::error('PayTR token alma hatası: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ödeme sistemi hatası: ' . $e->getMessage());
        }
    }

    /**
     * Başarılı ödeme sayfası
     */
    public function success()
    {
        return view('payment.success');
    }

    /**
     * Başarısız ödeme sayfası
     */
    public function fail()
    {
        return view('payment.fail');
    }

    /**
     * PayTR Callback - Ödeme sonucu bildirimi
     */
    public function callback(Request $request)
    {
        $post = $request->all();

        // PayTR'dan gelen hash doğrulaması
        $merchant_key = config('services.paytr.merchant_key');
        $merchant_salt = config('services.paytr.merchant_salt');

        $hash = base64_encode(hash_hmac('sha256', 
            $post['merchant_oid'] . $merchant_salt . $post['status'] . $post['total_amount'], 
            $merchant_key, true));

        if ($hash != $post['hash']) {
            Log::error('PayTR callback hash doğrulama hatası', $post);
            abort(403, "Bad Hash");
        }

        $order = Order::where('uuid', $post['merchant_oid'])->first();

        if (!$order) {
            Log::error('PayTR callback - Order bulunamadı', ['merchant_oid' => $post['merchant_oid']]);
            echo "OK";
            return;
        }

        if ($order->status == "paid") {
            echo "OK";
            return;
        }

        DB::beginTransaction();
        try {
            if ($post['status'] == "success") {
                // Ödeme başarılı
                $order->update([
                    'status' => 'paid',
                    'transaction_id' => $post['payment_id'] ?? null,
                    'paid_at' => now()
                ]);

                // Mevcut aktif subscription varsa iptal et
                if ($order->user->activeSubscription) {
                    $order->user->activeSubscription->update(['status' => 'cancelled']);
                }

                // Yeni subscription oluştur
                $subscription = Subscription::create([
                    'tenant_id' => $order->user->id,
                    'plan_id' => $order->plan_id,
                    'start_date' => now(),
                    'end_date' => $this->calculateEndDate($order->plan),
                    'status' => 'active',
                    'trial_ends_at' => $order->plan->trial_days ? now()->addDays($order->plan->trial_days) : null
                ]);

                // Plan ve token'ları kullanıcıya ata
                $order->user->assignPlan($order->plan, $subscription->id);

                // SubscriptionInvoice oluştur
                $order->user->subscriptionInvoices()->create([
                    'user_id' => $order->user->id,
                    'subscription_id' => $subscription->id,
                    'amount' => $order->amount,
                    'status' => 'paid',
                    'payment_gateway' => 'paytr',
                    'paid_at' => now()
                ]);

                Log::info('PayTR ödeme başarılı', [
                    'order_id' => $order->id,
                    'user_id' => $order->user->id,
                    'plan_id' => $order->plan_id,
                    'amount' => $order->amount
                ]);

            } else {
                // Ödeme başarısız
                $order->update(['status' => 'failed']);
                
                Log::info('PayTR ödeme başarısız', [
                    'order_id' => $order->id,
                    'user_id' => $order->user->id,
                    'status' => $post['status']
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('PayTR callback işlem hatası: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'post_data' => $post
            ]);
        }

        echo "OK"; // PayTR'a yanıt
    }

    /**
     * Plan süresine göre bitiş tarihini hesapla
     */
    private function calculateEndDate(Plan $plan): \Carbon\Carbon
    {
        return match($plan->billing_cycle) {
            'yearly' => now()->addYear(),
            'monthly' => now()->addMonth(),
            'trial' => now()->addDays($plan->trial_days ?? 30),
            default => now()->addMonth()
        };
    }
}
