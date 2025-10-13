<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\GiftboxUsers;
use Illuminate\Support\Facades\Validator;
use App\Mail\GiftboxWelcomeEmail;
use Illuminate\Support\Facades\Mail;

class GiftDataController extends Controller
{
    public function fashionSector(){
     return view('gift-data.fashion-sector');
    }

    public function furnitureSector(){
     return view('gift-data.furniture-sector');
    }

    public function homeAppliancesSector(){
     return view('gift-data.home-appliances-sector');
    }
    
    public function healthBeautySector(){
     return view('gift-data.health-beauty-sector');
    }

    public function electronicsSector(){
     return view('gift-data.electronics-sector');
    }
    
    // POST Methods for each sector
    public function storeFashionSector(Request $request)
    {
        return $this->storeSectorData($request, 'fashion');
    }
    
    public function storeFurnitureSector(Request $request)
    {
        return $this->storeSectorData($request, 'furniture');
    }
    
    public function storeHomeAppliancesSector(Request $request)
    {
        return $this->storeSectorData($request, 'home-appliances');
    }
    
    public function storeHealthBeautySector(Request $request)
    {
        return $this->storeSectorData($request, 'health-beauty');
    }
    
    public function storeElectronicsSector(Request $request)
    {
        return $this->storeSectorData($request, 'electronics');
    }
    
    private function storeSectorData(Request $request, $sector)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'surname' => 'required|string|max:255',
            'mail' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'visitors' => 'nullable|string|max:50',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Lütfen tüm gerekli alanları doldurun.',
                'errors' => $validator->errors()
            ], 422);
        }
        
        try {
            $giftboxUser = GiftboxUsers::create([
                'name' => $request->name,
                'surname' => $request->surname,
                'mail' => $request->mail,
                'phone' => $request->phone,
                'visitors' => $request->visitors,
                'sector' => $sector
            ]);
            
            // Send welcome email
            try {
                Mail::to($giftboxUser->mail)->send(new GiftboxWelcomeEmail($giftboxUser));
            } catch (\Exception $mailException) {
                // Log mail error but don't fail the request
                \Log::error('Giftbox email sending failed: ' . $mailException->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Mail adresinize gönderildi.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bir hata oluştu. Lütfen tekrar deneyin.'
            ], 500);
        }
    }

    // Admin Methods
    /**
     * Display a listing of giftbox users.
     */
    public function adminIndex(Request $request)
    {
        $query = GiftboxUsers::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('mail', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('sector', 'like', "%{$search}%");
            });
        }

        // Filter by sector
        if ($request->has('sector') && $request->sector) {
            $query->where('sector', $request->sector);
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        if (in_array($sortBy, ['name', 'surname', 'mail', 'sector', 'created_at'])) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $giftboxUsers = $query->paginate(20);

        // Get sector statistics
        $sectorStats = GiftboxUsers::selectRaw('sector, COUNT(*) as count')
            ->groupBy('sector')
            ->orderBy('count', 'desc')
            ->get();

        // Get total count
        $totalCount = GiftboxUsers::count();

        return view('admin.giftbox-data.index', compact(
            'giftboxUsers', 
            'sectorStats', 
            'totalCount',
            'request'
        ));
    }

    /**
     * Show the specified giftbox user.
     */
    public function adminShow(GiftboxUsers $giftboxUser)
    {
        return view('admin.giftbox-data.show', compact('giftboxUser'));
    }

    /**
     * Remove the specified giftbox user from storage.
     */
    public function adminDestroy(GiftboxUsers $giftboxUser)
    {
        try {
            $giftboxUser->delete();
            
            return redirect()->route('admin.giftbox-data.index')
                ->with('success', 'Giftbox kullanıcısı başarıyla silindi.');
        } catch (\Exception $e) {
            \Log::error('Giftbox user deletion failed: ' . $e->getMessage());
            
            return redirect()->route('admin.giftbox-data.index')
                ->with('error', 'Kullanıcı silinirken bir hata oluştu. Lütfen tekrar deneyin.');
        }
    }

    /**
     * Export giftbox users data.
     */
    public function adminExport(Request $request)
    {
        $query = GiftboxUsers::query();

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('surname', 'like', "%{$search}%")
                  ->orWhere('mail', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('sector', 'like', "%{$search}%");
            });
        }

        if ($request->has('sector') && $request->sector) {
            $query->where('sector', $request->sector);
        }

        $giftboxUsers = $query->orderBy('created_at', 'desc')->get();

        $filename = 'giftbox_users_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($giftboxUsers) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Ad', 'Soyad', 'E-posta', 'Telefon', 
                'Ziyaretçi Sayısı', 'Sektör', 'Kayıt Tarihi'
            ]);

            // CSV data
            foreach ($giftboxUsers as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->surname,
                    $user->mail,
                    $user->phone,
                    $user->visitors,
                    $user->sector,
                    $user->created_at->format('d.m.Y H:i')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
    
}
