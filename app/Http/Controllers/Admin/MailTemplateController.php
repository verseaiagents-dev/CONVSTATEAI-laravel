<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailTemplate;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MailTemplateController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Mail template listesini göster
     */
    public function index()
    {
        $mailTemplates = MailTemplate::with(['creator', 'updater'])
            ->latest()
            ->paginate(15);

        $categories = (new MailTemplate())::CATEGORIES;
        $stats = [
            'total' => MailTemplate::count(),
            'active' => MailTemplate::where('is_active', true)->count(),
            'inactive' => MailTemplate::where('is_active', false)->count(),
        ];

        return view('admin.mail-templates.index', compact('mailTemplates', 'categories', 'stats'));
    }

    /**
     * Mail template oluşturma formunu göster
     */
    public function create()
    {
        // Sabitleri doğrudan tanımlayalım
        $categories = [
            'welcome' => 'Hoşgeldin',
            'notification' => 'Bildirim',
            'security' => 'Güvenlik',
            'subscription' => 'Abonelik',
            'marketing' => 'Pazarlama',
            'custom' => 'Özel'
        ];
        
        $variables = [
            '{{username}}' => 'Kullanıcı adı',
            '{{useremail}}' => 'Kullanıcı e-posta adresi',
            '{{userplan}}' => 'Kullanıcı planı',
            '{{userplanexpired}}' => 'Plan sona erme tarihi',
            '{{usercreated}}' => 'Kullanıcı oluşturma tarihi',
            '{{userlastlogin}}' => 'Son giriş tarihi',
            '{{userstatus}}' => 'Kullanıcı durumu',
            '{{sitename}}' => 'Site adı',
            '{{siteurl}}' => 'Site URL',
            '{{adminname}}' => 'Admin adı',
            '{{currentdate}}' => 'Güncel tarih',
            '{{loginurl}}' => 'Giriş linki',
            '{{dashboardurl}}' => 'Dashboard linki',
            '{{supportemail}}' => 'Destek e-posta',
            '{{companyname}}' => 'Şirket adı',
            '{{companyaddress}}' => 'Şirket adresi',
            '{{companyphone}}' => 'Şirket telefonu'
        ];
        
        // Debug için log ekleyelim
        
        return view('admin.mail-templates.create', compact('categories', 'variables'));
    }

    /**
     * Mail template oluştur
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:mail_templates',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:' . implode(',', array_keys((new MailTemplate())::CATEGORIES)),
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'variables' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $template = MailTemplate::create([
            'name' => $request->input('name'),
            'subject' => $request->input('subject'),
            'content' => $request->input('content'),
            'category' => $request->input('category'),
            'description' => $request->input('description'),
            'is_active' => $request->has('is_active') ? (bool)$request->input('is_active') : true,
            'variables' => $request->input('variables', []),
            'created_by' => Auth::id(),
            'updated_by' => Auth::id()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Mail template başarıyla oluşturuldu.',
                'template' => $template->fresh()
            ]);
        }

        return redirect()->route('admin.mail-templates.index')
            ->with('success', 'Mail template başarıyla oluşturuldu.');
    }

    /**
     * Mail template detaylarını göster
     */
    public function show($id)
    {
        $mailTemplate = MailTemplate::findOrFail($id);
        return view('admin.mail-templates.show', compact('mailTemplate'));
    }

    /**
     * Mail template düzenleme formunu göster
     */
    public function edit($id)
    {
        $mailTemplate = MailTemplate::findOrFail($id);
        $categories = (new MailTemplate())::CATEGORIES;
        $variables = (new MailTemplate())::DEFAULT_VARIABLES;
        
        return view('admin.mail-templates.edit', compact('mailTemplate', 'categories', 'variables'));
    }

    /**
     * Mail template güncelle
     */
    public function update(Request $request, $id)
    {
        $template = MailTemplate::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:mail_templates,name,' . $id,
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'required|in:' . implode(',', array_keys((new MailTemplate())::CATEGORIES)),
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
            'variables' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $template->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'content' => $request->input('content'),
            'category' => $request->category,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'variables' => $request->variables ?? [],
            'updated_by' => Auth::id()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Mail template başarıyla güncellendi.',
                'template' => $template->fresh()
            ]);
        }

        return redirect()->route('admin.mail-templates.index')
            ->with('success', 'Mail template başarıyla güncellendi.');
    }

    /**
     * Mail template sil
     */
    public function destroy($id)
    {
        $template = MailTemplate::findOrFail($id);
        $template->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Mail template başarıyla silindi.'
            ]);
        }

        return redirect()->route('admin.mail-templates.index')
            ->with('success', 'Mail template başarıyla silindi.');
    }

    /**
     * Mail template durumunu değiştir
     */
    public function toggleStatus($id)
    {
        $template = MailTemplate::findOrFail($id);
        $template->update([
            'is_active' => !$template->is_active,
            'updated_by' => Auth::id()
        ]);

        $status = $template->is_active ? 'aktif' : 'pasif';

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Template durumu {$status} olarak değiştirildi.",
                'is_active' => $template->is_active
            ]);
        }

        return back()->with('success', "Template durumu {$status} olarak değiştirildi.");
    }

    /**
     * Mail template'i test et
     */
    public function test($id)
    {
        $template = MailTemplate::findOrFail($id);
        $testData = $template->test();
        
        return response()->json([
            'success' => true,
            'data' => $testData
        ]);
    }

    /**
     * Mail template'i test e-posta adresine gönder
     */
    public function sendTest($id, Request $request)
    {
        $template = MailTemplate::findOrFail($id);
        $testEmail = $request->input('test_email');

        if (!$testEmail) {
            return response()->json([
                'success' => false,
                'message' => 'Test e-posta adresi gerekli.'
            ], 400);
        }

        // Test verilerini hazırla
        $testData = [
            'username' => 'Test Kullanıcı',
            'useremail' => $testEmail,
            'userplan' => 'Test Plan',
            'userplanexpired' => '2024-12-31',
            'usercreated' => now()->format('Y-m-d'),
            'userlastlogin' => now()->format('Y-m-d'),
            'userstatus' => 'Test Kullanıcı',
            'sitename' => config('app.name'),
            'siteurl' => config('app.url'),
            'adminname' => Auth::user() ? Auth::user()->name : 'Admin User',
            'currentdate' => now()->format('Y-m-d'),
            'loginurl' => config('app.url') . '/login',
            'dashboardurl' => config('app.url') . '/dashboard',
            'supportemail' => 'support@convstateai.com',
            'companyname' => 'ConvState AI',
            'companyaddress' => 'İstanbul, Türkiye',
            'companyphone' => '+90 212 XXX XX XX'
        ];

        try {
            // Test maili gönder
            $this->mailService->sendNotificationEmail(
                $testEmail,
                $template->parseSubject($testData),
                $template->parseContent($testData),
                'Test Kullanıcı'
            );

            return response()->json([
                'success' => true,
                'message' => 'Test maili başarıyla gönderildi.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Test maili gönderilemedi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mail template'i kopyala
     */
    public function duplicate($id)
    {
        $template = MailTemplate::findOrFail($id);
        
        $newTemplate = MailTemplate::create([
            'name' => $template->name . ' (Kopya)',
            'subject' => $template->subject,
            'content' => $template->content,
            'category' => $template->category,
            'description' => $template->description,
            'variables' => $template->variables,
            'is_active' => false,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id()
        ]);

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Template başarıyla kopyalandı.',
                'template' => $newTemplate
            ]);
        }

        return redirect()->route('admin.mail-templates.index')
            ->with('success', 'Template başarıyla kopyalandı.');
    }

    /**
     * Mail template istatistiklerini getir
     */
    public function stats()
    {
        $stats = [
            'total' => MailTemplate::count(),
            'active' => MailTemplate::where('is_active', true)->count(),
            'inactive' => MailTemplate::where('is_active', false)->count(),
            'by_category' => MailTemplate::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->get()
                ->pluck('count', 'category')
                ->toArray(),
            'recent' => MailTemplate::with('creator')
                ->latest()
                ->take(5)
                ->get()
        ];

        return response()->json($stats);
    }
}
