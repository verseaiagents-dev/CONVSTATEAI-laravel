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

     //    // Kullanıcının zaten aktif aboneliği var mı kontrol et
     //    if ($user->activeSubscription) {
     //        return redirect()->route('dashboard.subscription.index')
     //            ->with('warning', 'Zaten aktif bir aboneliğiniz bulunmaktadır.');
     //    }

        return view('payment.billing-form', compact('plan'));
    }

    /**
     * Checkout sayfası - Plan seçimi ve ödeme başlatma Manuel
     */
    public function checkout2(Request $request, $planId)
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

      

        // Order kaydı oluştur
        $order = Order::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'uuid' => 'SUB' . uniqid() . '' . time(),
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

        // PayTR geçici olarak kapatıldı - Plan talebi oluştur
        try {
            // Plan talebi oluştur
            $planRequest = \App\Models\PlanRequest::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'status' => 'pending',
                'amount' => $plan->price,
                'company_name' => $request->company_name,
                'full_name' => $request->full_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'tax_number' => $request->tax_number,
                'tax_office' => $request->tax_office,
                'country' => $request->country,
                'city' => $request->city,
                'district' => $request->district,
                'address_line' => $request->address_line,
                'postal_code' => $request->postal_code
            ]);

            Log::info('Plan talebi oluşturuldu', [
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'request_id' => $planRequest->id
            ]);
            
            return redirect()->route('subscription.plans')
                ->with('success', 'Plan talebiniz başarıyla oluşturuldu! Yöneticiler tarafından incelendikten sonra size bilgi verilecektir.');
        } catch (\Exception $e) {
            Log::error('Plan talebi oluşturma hatası: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Plan talebi oluşturulurken hata oluştu: ' . $e->getMessage());
        }
    }
   public function checkout(Request $request, $planId)
    {
        $plan = Plan::findOrFail($planId);
        $user = Auth::user();

        // PayTR için gerekli alanlar
        $requiredFields = [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address_line' => 'required|string|max:500'
        ];
        
        // Şirket bilgileri opsiyonel
        $optionalFields = [
            'company_name' => 'nullable|string|max:255',
            'tax_number' => 'nullable|string|max:50',
            'tax_office' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'district' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20'
        ];
        
        // Şirket bilgileri gerekiyorsa zorunlu yap
        if ($request->has('company_info_required') && $request->company_info_required) {
            $requiredFields = array_merge($requiredFields, [
                'company_name' => 'required|string|max:255',
                'tax_number' => 'required|string|max:50',
                'tax_office' => 'required|string|max:255'
            ]);
        }
        
        // Tüm validasyon kurallarını birleştir
        $validationRules = array_merge($requiredFields, $optionalFields);
        
        $request->validate($validationRules);

        // Kullanıcının zaten aktif aboneliği var mı kontrol et
       

        // KDV hesaplama (%20 KDV)
        $kdvRate = 0.20; // %20 KDV
        $amountWithoutKdv = $plan->price;
        $kdvAmount = $amountWithoutKdv * $kdvRate;
        $totalAmountWithKdv = $amountWithoutKdv + $kdvAmount;

        // Order kaydı oluştur
        $order = Order::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'amount' => $totalAmountWithKdv, // KDV dahil tutar
            'amount_without_kdv' => $amountWithoutKdv, // KDV hariç tutar
            'kdv_amount' => $kdvAmount, // KDV tutarı
            'kdv_rate' => $kdvRate, // KDV oranı
            'uuid' => 'SUB' . uniqid() . '' . time(),
            'status' => 'pending',
            'payment_method' => 'paytr',
            'company_name' => $request->company_name ?? null,
            'full_name' => $request->full_name,
            'tax_number' => $request->tax_number ?? null,
            'tax_office' => $request->tax_office ?? null,
            'country' => $request->country ?? null,
            'city' => $request->city ?? null,
            'district' => $request->district ?? null,
            'address_line' => $request->address_line,
            'postal_code' => $request->postal_code ?? null,
            'phone' => $request->phone,
            'email' => $request->email
        ]);

        // PayTR geçici olarak kapatıldı - Plan talebi oluştur
        try {
            // Plan talebi oluştur
         

       $token = PaytrService::getIframeToken($order);
return view('payment.iframe', compact('token', 'order'));   } catch (\Exception $e) {
            Log::error('PayTR ödeme başlatma hatası: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Ödeme başlatılırken hata oluştu: ' . $e->getMessage());
        }
            
        
    }
    /**
     * Başarılı ödeme sayfası
     */
    public function success(Request $request)
    {
        // PayTR'dan gelen parametreleri kontrol et
        $merchantOid = $request->get('merchant_oid');
        $status = $request->get('status');
        
        Log::info('Payment success page accessed', [
            'merchant_oid' => $merchantOid,
            'status' => $status,
            'all_params' => $request->all()
        ]);
        
        if ($merchantOid && $status === 'success') {
            // Order'ı bul ve durumunu kontrol et
            $order = Order::where('uuid', $merchantOid)->first();
            
            if ($order) {
                $user = $order->user;
                $planAssigned = $user->current_plan_id === $order->plan_id && $user->tokens_total > 0;
                
                // Success sayfasına daha önce gelinmiş mi kontrol et
                if ($order->status === 'paid' && $planAssigned) {
                    // Plan zaten atanmış, kullanıcıyı dashboard'a yönlendir
                    return redirect()->route('dashboard')->with('success', 'Ödemeniz başarıyla tamamlandı ve planınız aktif edildi!');
                } else {
                    // Success sayfasına daha önce gelinmiş mi kontrol et
                    $sessionKey = 'payment_success_processed_' . $order->uuid;
                    if (session()->has($sessionKey)) {
                        // Daha önce işlenmiş, dashboard'a yönlendir
                        return redirect()->route('dashboard')->with('info', 'Ödeme işleminiz daha önce tamamlanmıştır.');
                    }
                    
                    // Session flag'ini set et
                    session([$sessionKey => true]);
                    
                    // Ödeme henüz işlenmemiş veya plan atanmamış, manuel plan atama dene
                    $this->attemptPlanAssignment($order);
                    
                    // Tekrar kontrol et
                    $user->refresh();
                    $planAssigned = $user->current_plan_id === $order->plan_id && $user->tokens_total > 0;
                    
                    if ($planAssigned) {
                        // Plan atandı, dashboard'a yönlendir
                        return redirect()->route('dashboard')->with('success', 'Ödemeniz başarıyla tamamlandı ve planınız aktif edildi!');
                    } else {
                        // Plan atanamadı, success sayfasını göster
                        return view('payment.success', [
                            'order' => $order,
                            'plan' => $order->plan,
                            'message' => 'Ödemeniz alındı! Planınız kısa süre içinde aktif edilecektir.',
                            'showPending' => true
                        ]);
                    }
                }
            } else {
                // Order bulunamadı
                Log::warning('Payment success - Order not found', ['merchant_oid' => $merchantOid]);
                return redirect()->route('dashboard')->with('error', 'Ödeme işlemi tamamlandı ancak sipariş bilgileri bulunamadı.');
            }
        }
        
        // Genel başarı sayfası (PayTR parametreleri yok) - dashboard'a yönlendir
        return redirect()->route('dashboard')->with('success', 'Ödemeniz başarıyla tamamlandı!');
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
            Log::info('PayTR callback - Order zaten işlenmiş', ['order_id' => $order->id]);
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

                // Kullanıcının mevcut aktif aboneliğini iptal et
                $user = $order->user;
                $activeSubscription = $user->subscriptions()->where('status', 'active')->first();
                if ($activeSubscription) {
                    $activeSubscription->update(['status' => 'cancelled']);
                    Log::info('Mevcut abonelik iptal edildi', [
                        'user_id' => $user->id,
                        'subscription_id' => $activeSubscription->id
                    ]);
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
                $user->assignPlan($order->plan, $subscription->id);

                // SubscriptionInvoice oluştur
                $user->subscriptionInvoices()->create([
                    'user_id' => $user->id,
                    'subscription_id' => $subscription->id,
                    'amount' => $order->amount,
                    'status' => 'paid',
                    'payment_gateway' => 'paytr',
                    'paid_at' => now()
                ]);

                Log::info('PayTR ödeme başarılı ve plan atandı', [
                    'order_id' => $order->id,
                    'user_id' => $user->id,
                    'plan_id' => $order->plan_id,
                    'subscription_id' => $subscription->id,
                    'amount' => $order->amount,
                    'tokens_assigned' => $user->tokens_total
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
                'post_data' => $post,
                'trace' => $e->getTraceAsString()
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

    /**
     * Manuel plan atama denemesi (success sayfasında)
     */
    private function attemptPlanAssignment(Order $order)
    {
        try {
            DB::beginTransaction();
            
            $user = $order->user;
            
            // Mevcut aktif aboneliği iptal et
            $activeSubscription = $user->subscriptions()->where('status', 'active')->first();
            if ($activeSubscription) {
                $activeSubscription->update(['status' => 'cancelled']);
                Log::info('Mevcut abonelik iptal edildi (success page)', [
                    'user_id' => $user->id,
                    'subscription_id' => $activeSubscription->id
                ]);
            }

            // Yeni subscription oluştur
            $subscription = Subscription::create([
                'tenant_id' => $user->id,
                'plan_id' => $order->plan_id,
                'start_date' => now(),
                'end_date' => $this->calculateEndDate($order->plan),
                'status' => 'active',
                'trial_ends_at' => $order->plan->trial_days ? now()->addDays($order->plan->trial_days) : null
            ]);

            // Plan ve token'ları kullanıcıya ata
            $user->assignPlan($order->plan, $subscription->id);

            // SubscriptionInvoice oluştur
            $user->subscriptionInvoices()->create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'amount' => $order->amount,
                'status' => 'paid',
                'payment_gateway' => 'paytr',
                'paid_at' => now()
            ]);

            DB::commit();

            Log::info('Manuel plan atama başarılı (success page)', [
                'order_id' => $order->id,
                'user_id' => $user->id,
                'plan_id' => $order->plan_id,
                'subscription_id' => $subscription->id,
                'tokens_assigned' => $user->tokens_total
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Manuel plan atama hatası (success page): ' . $e->getMessage(), [
                'order_id' => $order->id,
                'user_id' => $order->user->id,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }

    /**
     * Ödeme durumunu kontrol et (AJAX için)
     */
    public function checkPaymentStatus(Request $request, $merchantOid)
    {
        try {
            $order = Order::where('uuid', $merchantOid)->first();
            
            if (!$order) {
                return response()->json([
                    'status' => 'not_found',
                    'message' => 'Order bulunamadı'
                ], 404);
            }
            
            $user = $order->user;
            $planAssigned = $user->current_plan_id === $order->plan_id && $user->tokens_total > 0;
            
            return response()->json([
                'status' => $order->status,
                'plan_assigned' => $planAssigned,
                'order_id' => $order->id,
                'plan_id' => $order->plan_id,
                'user_tokens' => $user->tokens_total,
                'current_plan_id' => $user->current_plan_id
            ]);
            
        } catch (\Exception $e) {
            Log::error('Payment status check error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Durum kontrolü sırasında hata oluştu'
            ], 500);
        }
    }

    /**
     * Test için manuel plan atama (sadece development ortamında)
     */
    public function testAssignPlan(Request $request, $orderId)
    {
        if (!app()->environment('local', 'testing')) {
            abort(403, 'Bu metod sadece test ortamında kullanılabilir');
        }

        $order = Order::findOrFail($orderId);
        
        if ($order->status !== 'paid') {
            return response()->json(['error' => 'Order henüz ödenmemiş'], 400);
        }

        try {
            DB::beginTransaction();
            
            $user = $order->user;
            
            // Mevcut aktif aboneliği iptal et
            $activeSubscription = $user->subscriptions()->where('status', 'active')->first();
            if ($activeSubscription) {
                $activeSubscription->update(['status' => 'cancelled']);
            }

            // Yeni subscription oluştur
            $subscription = Subscription::create([
                'tenant_id' => $user->id,
                'plan_id' => $order->plan_id,
                'start_date' => now(),
                'end_date' => $this->calculateEndDate($order->plan),
                'status' => 'active',
                'trial_ends_at' => $order->plan->trial_days ? now()->addDays($order->plan->trial_days) : null
            ]);

            // Plan ve token'ları kullanıcıya ata
            $user->assignPlan($order->plan, $subscription->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Plan başarıyla atandı',
                'user_id' => $user->id,
                'plan_id' => $order->plan_id,
                'tokens_assigned' => $user->tokens_total
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
