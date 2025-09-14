<?php

namespace App\Http\Controllers;

use App\Models\EnhancedChatSession;
use App\Models\ProductInteraction;
use App\Models\Product;
use App\Models\Project;
use App\Models\WidgetTracking;
use App\Http\Services\ProjectKnowledgeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AnalyticsController extends Controller
{

    /**
     * Funnel intent istatistiklerini hesapla
     */
    private function calculateFunnelStats($projectId = null)
    {
        // Yeni funnel intent'leri tanımla (analytics için)
        $funnelIntents = [
            'capabilities_inquiry',
            'project_info',
            'conversion_guidance',
            'pricing_guidance',
            'demo_request',
            'contact_request',
            'product_recommendations'
        ];

        // Query builder'ı başlat
        $query = ProductInteraction::whereIn('intent', $funnelIntents);
        
        // Project bazlı filtreleme
        if ($projectId) {
            $query->whereHas('chatSession', function($q) use ($projectId) {
                $q->where('project_id', $projectId);
            });
        }

        // Genel kullanım istatistikleri
        $funnelUsage = [];
        foreach ($funnelIntents as $intent) {
            $funnelUsage[$intent] = $query->where('intent', $intent)->count();
        }

        // Bugünkü kullanım
        $funnelUsageToday = [];
        foreach ($funnelIntents as $intent) {
            $funnelUsageToday[$intent] = $query->where('intent', $intent)
                ->whereDate('created_at', today())
                ->count();
        }

        // Bu haftaki kullanım
        $funnelUsageWeek = [];
        foreach ($funnelIntents as $intent) {
            $funnelUsageWeek[$intent] = $query->where('intent', $intent)
                ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count();
        }

        // Bu ayki kullanım
        $funnelUsageMonth = [];
        foreach ($funnelIntents as $intent) {
            $funnelUsageMonth[$intent] = $query->where('intent', $intent)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count();
        }

        // En popüler funnel intent'ler
        $topFunnelIntents = collect($funnelUsage)
            ->sortDesc()
            ->take(5)
            ->toArray();

        // Conversion rate hesapla
        $funnelConversions = [];
        foreach ($funnelIntents as $intent) {
            $sessionsWithIntent = $query->where('intent', $intent)
                ->distinct('session_id')
                ->pluck('session_id');
            
            $conversions = 0;
            foreach ($sessionsWithIntent as $sessionId) {
                $hasConversion = ProductInteraction::where('session_id', $sessionId)
                    ->whereIn('action', ['buy', 'add_to_cart'])
                    ->exists();
                if ($hasConversion) {
                    $conversions++;
                }
            }
            
            $totalSessions = $sessionsWithIntent->count();
            $funnelConversions[$intent] = $totalSessions > 0 
                ? round(($conversions / $totalSessions) * 100, 2) 
                : 0;
        }

        // Yeni funnel stage dağılımı
        $funnelStages = [
            'awareness' => $funnelUsage['capabilities_inquiry'] + $funnelUsage['project_info'],
            'interest' => $funnelUsage['product_recommendations'] + $funnelUsage['pricing_guidance'],
            'consideration' => $funnelUsage['conversion_guidance'],
            'intent' => $funnelUsage['demo_request'],
            'action' => $funnelUsage['contact_request']
        ];

        return [
            'funnel_intents' => $funnelIntents,
            'usage' => $funnelUsage,
            'usage_today' => $funnelUsageToday,
            'usage_week' => $funnelUsageWeek,
            'usage_month' => $funnelUsageMonth,
            'top_intents' => $topFunnelIntents,
            'conversion_rates' => $funnelConversions,
            'stage_distribution' => $funnelStages,
            'total_funnel_interactions' => array_sum($funnelUsage),
            'total_funnel_interactions_today' => array_sum($funnelUsageToday)
        ];
    }
    /**
     * Get real-time analytics data
     */
    public function getRealTimeAnalytics(): JsonResponse
    {
        try {
            $now = Carbon::now();
            $oneHourAgo = $now->copy()->subHour();
            $twentyFourHoursAgo = $now->copy()->subDay();

            // Active sessions count
            $activeSessions = EnhancedChatSession::where('status', 'active')
                ->where('last_activity', '>=', $now->copy()->subMinutes(30))
                ->count();

            // Interactions in last hour
            $interactionsLastHour = ProductInteraction::where('timestamp', '>=', $oneHourAgo)->count();

            // Conversion rate (buy actions / total interactions)
            $totalInteractions = ProductInteraction::count();
            $buyInteractions = ProductInteraction::where('action', 'buy')->count();
            $conversionRate = $totalInteractions > 0 ? round(($buyInteractions / $totalInteractions) * 100, 2) : 0;

            // Average session duration
            $avgSessionDuration = EnhancedChatSession::whereNotNull('last_activity')
                ->where('created_at', '>=', $twentyFourHoursAgo)
                ->get()
                ->avg(function ($session) {
                    if ($session->last_activity) {
                        return $session->created_at->diffInMinutes($session->last_activity);
                    }
                    return 0;
                });

            // Hourly data for last 24 hours
            $hourlyData = $this->getHourlyData($twentyFourHoursAgo, $now);

            // Intent distribution
            $intentDistribution = $this->getIntentDistribution();

            // Live sessions
            $liveSessions = $this->getLiveSessions();

            // Performance metrics
            $performanceMetrics = $this->getPerformanceMetrics();

            return response()->json([
                'active_sessions' => $activeSessions,
                'interactions_last_hour' => $interactionsLastHour,
                'conversion_rate' => $conversionRate,
                'avg_session_duration' => round($avgSessionDuration, 1),
                'hourly_data' => $hourlyData,
                'intent_distribution' => $intentDistribution,
                'live_sessions' => $liveSessions,
                'performance_metrics' => $performanceMetrics,
                'timestamp' => $now->toISOString()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch analytics data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get hourly data for charts
     */
    private function getHourlyData(Carbon $start, Carbon $end): array
    {
        $hourlyData = [];

        for ($i = 0; $i < 24; $i++) {
            $hourStart = $start->copy()->addHours($i);
            $hourEnd = $hourStart->copy()->addHour();
            $hourKey = $hourStart->format('H:00');

            $sessions = EnhancedChatSession::whereBetween('created_at', [$hourStart, $hourEnd])->count();
            $interactions = ProductInteraction::whereBetween('timestamp', [$hourStart, $hourEnd])->count();

            $hourlyData[$hourKey] = [
                'sessions' => $sessions,
                'interactions' => $interactions
            ];
        }

        return $hourlyData;
    }

    /**
     * Get intent distribution
     */
    private function getIntentDistribution(): array
    {
        $sessions = EnhancedChatSession::where('created_at', '>=', Carbon::now()->subDay())->get();
        
        $intentCounts = [];
        
        foreach ($sessions as $session) {
            // Intent history'den intents - JSON string'i decode et
            if ($session->intent_history) {
                $intentHistory = is_string($session->intent_history) 
                    ? json_decode($session->intent_history, true) 
                    : $session->intent_history;
                
                if (is_array($intentHistory)) {
                    foreach ($intentHistory as $intentData) {
                        $intent = is_array($intentData) ? $intentData['intent'] : $intentData;
                        if ($intent) {
                            $intentCounts[$intent] = ($intentCounts[$intent] ?? 0) + 1;
                        }
                    }
                }
            }
            
            // Chat history'den intents - JSON string'i decode et
            if ($session->chat_history) {
                $chatHistory = is_string($session->chat_history) 
                    ? json_decode($session->chat_history, true) 
                    : $session->chat_history;
                
                if (is_array($chatHistory)) {
                    foreach ($chatHistory as $message) {
                        if (isset($message['intent']) && $message['intent']) {
                            $intentCounts[$message['intent']] = ($intentCounts[$message['intent']] ?? 0) + 1;
                        }
                    }
                }
            }
        }
        
        return $intentCounts;
    }

    /**
     * Get live sessions
     */
    private function getLiveSessions(): array
    {
        return EnhancedChatSession::where('status', 'active')
            ->where('last_activity', '>=', Carbon::now()->subMinutes(30))
            ->with(['productInteractions'])
            ->get()
            ->map(function ($session) {
                return [
                    'session_id' => $session->session_id,
                    'status' => $session->status,
                    'last_activity' => $session->last_activity ? $session->last_activity->diffForHumans() : 'Never',
                    'intent_count' => $this->getIntentCount($session->intent_history),
                    'interaction_count' => $session->productInteractions->count()
                ];
            })
            ->toArray();
    }
    /**
     * Get performance metrics
     */
    private function getPerformanceMetrics(): array
    {
        // Calculate real performance metrics from database
        
        // Average response time (based on chat session response times)
        $avgResponseTime = $this->calculateAverageResponseTime();
        
        // Success rate based on successful vs failed interactions
        $successRate = $this->calculateSuccessRate();
        
        // User satisfaction based on feedback and interactions
        $userSatisfaction = $this->calculateUserSatisfaction();
        
        // Peak hours based on actual usage patterns
        $peakHours = $this->getPeakHours();

        return [
            'avg_response_time' => $avgResponseTime,
            'success_rate' => $successRate,
            'user_satisfaction' => $userSatisfaction,
            'peak_hours' => $peakHours
        ];
    }

    /**
     * Calculate average response time from chat sessions
     */
    private function calculateAverageResponseTime(): int
    {
        // Get recent chat sessions and calculate average response time
        $recentSessions = EnhancedChatSession::where('created_at', '>=', Carbon::now()->subDay())
            ->whereNotNull('last_activity')
            ->get();
        
        if ($recentSessions->isEmpty()) {
            return 200; // Default fallback
        }
        
        $totalResponseTime = 0;
        $validSessions = 0;
        
        foreach ($recentSessions as $session) {
            if ($session->last_activity && $session->created_at) {
                $responseTime = $session->created_at->diffInMilliseconds($session->last_activity);
                if ($responseTime > 0 && $responseTime < 300000) { // Between 0 and 5 minutes
                    $totalResponseTime += $responseTime;
                    $validSessions++;
                }
            }
        }
        
        return $validSessions > 0 ? round($totalResponseTime / $validSessions) : 200;
    }

    /**
     * Calculate success rate based on successful interactions
     */
    private function calculateSuccessRate(): float
    {
        // Count successful vs failed interactions
        $totalInteractions = ProductInteraction::where('timestamp', '>=', Carbon::now()->subDay())->count();
        
        if ($totalInteractions === 0) {
            return 95.0; // Default fallback
        }
        
        // Consider interactions with products as successful
        $successfulInteractions = ProductInteraction::where('timestamp', '>=', Carbon::now()->subDay())
            ->whereNotNull('product_id')
            ->count();
        
        return round(($successfulInteractions / $totalInteractions) * 100, 1);
    }

    /**
     * Calculate user satisfaction score
     */
    private function calculateUserSatisfaction(): float
    {
        // Base satisfaction on session duration and interaction quality
        $recentSessions = EnhancedChatSession::where('created_at', '>=', Carbon::now()->subDay())
            ->whereNotNull('last_activity')
            ->get();
        
        if ($recentSessions->isEmpty()) {
            return 4.2; // Default fallback
        }
        
        $totalScore = 0;
        $validSessions = 0;
        
        foreach ($recentSessions as $session) {
            $score = 4.0; // Base score
            
            // Increase score for longer sessions (more engagement)
            if ($session->last_activity && $session->created_at) {
                $duration = $session->created_at->diffInMinutes($session->last_activity);
                if ($duration > 5) $score += 0.3;
                if ($duration > 15) $score += 0.2;
            }
            
            // Increase score for more interactions
            $interactionCount = $session->productInteractions()->count();
            if ($interactionCount > 3) $score += 0.2;
            if ($interactionCount > 10) $score += 0.3;
            
            // Cap at 5.0
            $score = min(5.0, $score);
            
            $totalScore += $score;
            $validSessions++;
        }
        
        return $validSessions > 0 ? round($totalScore / $validSessions, 1) : 4.2;
    }

    /**
     * Get peak hours based on actual usage patterns
     */
    private function getPeakHours(): string
    {
        // Get hourly usage data for the last 7 days
        $startDate = Carbon::now()->subWeek();
        $endDate = Carbon::now();
        
        $hourlyUsage = [];
        
        // Initialize hourly buckets
        for ($i = 0; $i < 24; $i++) {
            $hourlyUsage[$i] = 0;
        }
        
        // Get sessions created in the last week
        $sessions = EnhancedChatSession::whereBetween('created_at', [$startDate, $endDate])->get();
        
        foreach ($sessions as $session) {
            $hour = (int) $session->created_at->format('G'); // 0-23 hour format
            $hourlyUsage[$hour]++;
        }
        
        // Get interactions in the last week
        $interactions = ProductInteraction::whereBetween('timestamp', [$startDate, $endDate])->get();
        
        foreach ($interactions as $interaction) {
            $hour = (int) $interaction->timestamp->format('G');
            $hourlyUsage[$hour]++;
        }
        
        // Find the peak hours (top 3)
        arsort($hourlyUsage);
        $peakHours = array_slice(array_keys($hourlyUsage), 0, 3, true);
        
        if (empty($peakHours)) {
            return '9:00-11:00, 14:00-16:00, 19:00-21:00'; // Default fallback
        }
        
        // Format peak hours
        $formattedPeakHours = [];
        foreach ($peakHours as $hour) {
            $formattedHour = sprintf('%02d:00', $hour);
            $formattedPeakHours[] = $formattedHour;
        }
        
        return implode(', ', $formattedPeakHours);
    }

    /**
     * Export analytics data
     */
    public function exportAnalytics(Request $request): JsonResponse
    {
        try {
            $format = $request->get('format', 'csv');
            $dateRange = $request->get('date_range', 'last_7_days');

            $data = $this->getExportData($dateRange);

            if ($format === 'json') {
                return response()->json($data);
            }

            // For CSV, return data that can be processed by frontend
            return response()->json([
                'data' => $data,
                'format' => 'csv',
                'filename' => 'analytics_' . $dateRange . '_' . Carbon::now()->format('Y-m-d') . '.csv'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to export analytics data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get export data based on date range
     */
    private function getExportData(string $dateRange): array
    {
        $startDate = $this->getStartDate($dateRange);
        $endDate = Carbon::now();

        $sessions = EnhancedChatSession::whereBetween('created_at', [$startDate, $endDate])
            ->with(['productInteractions', 'user'])
            ->get();

        $interactions = ProductInteraction::whereBetween('timestamp', [$startDate, $endDate])
            ->with(['product', 'chatSession'])
            ->get();

        return [
            'summary' => [
                'total_sessions' => $sessions->count(),
                'total_interactions' => $interactions->count(),
                'conversion_rate' => $this->calculateConversionRate($interactions),
                'avg_session_duration' => $this->calculateAvgSessionDuration($sessions),
                'unique_users' => $sessions->pluck('user_id')->filter()->unique()->count()
            ],
            'sessions' => $sessions->map(function ($session) {
                return [
                    'session_id' => $session->session_id,
                    'user_id' => $session->user_id,
                    'status' => $session->status,
                    'created_at' => $session->created_at->toISOString(),
                    'last_activity' => $session->last_activity ? $session->last_activity->toISOString() : null,
                    'intent_count' => $this->getIntentCount($session->intent_history),
                    'interaction_count' => $session->productInteractions->count(),
                    'daily_view_count' => $session->daily_view_count,
                    'daily_view_limit' => $session->daily_view_limit
                ];
            }),
            'interactions' => $interactions->map(function ($interaction) {
                return [
                    'session_id' => $interaction->session_id,
                    'product_id' => $interaction->product_id,
                    'action' => $interaction->action,
                    'timestamp' => $interaction->timestamp->toISOString(),
                    'source' => $interaction->source,
                    'product_name' => $interaction->product ? ($interaction->product->title ?? $interaction->product->name) : null,
                    'metadata' => $interaction->metadata
                ];
            })
        ];
    }

    /**
     * Get start date based on date range
     */
    private function getStartDate(string $dateRange): Carbon
    {
        return match ($dateRange) {
            'last_24_hours' => Carbon::now()->subDay(),
            'last_7_days' => Carbon::now()->subWeek(),
            'last_30_days' => Carbon::now()->subMonth(),
            'last_90_days' => Carbon::now()->subMonths(3),
            default => Carbon::now()->subWeek()
        };
    }

    /**
     * Calculate conversion rate
     */
    private function calculateConversionRate($interactions): float
    {
        $total = $interactions->count();
        $conversions = $interactions->where('action', 'buy')->count();

        return $total > 0 ? round(($conversions / $total) * 100, 2) : 0;
    }

    /**
     * Calculate average session duration
     */
    private function calculateAvgSessionDuration($sessions): float
    {
        $durations = $sessions->map(function ($session) {
            if ($session->last_activity) {
                return $session->created_at->diffInMinutes($session->last_activity);
            }
            return 0;
        })->filter();

        return $durations->count() > 0 ? round($durations->avg(), 1) : 0;
    }

    /**
     * Get custom date range analytics
     */
    public function getCustomRangeAnalytics(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after:start_date'
            ]);

            $startDate = Carbon::parse($request->start_date);
            $endDate = Carbon::parse($request->end_date);

            $data = $this->getExportData('custom');
            $data['date_range'] = [
                'start' => $startDate->toISOString(),
                'end' => $endDate->toISOString()
            ];

            return response()->json($data);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch custom range analytics',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show analytics dashboard
     */
    public function index(Request $request)
    {
        try {
            // Project ID parametresini al
            $projectId = $request->query('project_id');
            
            // Kullanıcının projelerini al (sadece kendi oluşturduğu projeler)
            $userProjects = Project::where('created_by', auth()->id())
                ->select('id', 'name')
                ->orderBy('name')
                ->get();
            
            // Eğer project_id yoksa ve sadece 1 proje varsa, o projeyi seç
            if (!$projectId && $userProjects->count() == 1) {
                $projectId = $userProjects->first()->id;
            }
            
            // Proje ismini bul ve kullanıcıya ait olduğunu kontrol et
            $projectName = null;
            if ($projectId) {
                $project = Project::where('id', $projectId)
                    ->where('created_by', auth()->id())
                    ->first();
                $projectName = $project ? $project->name : null;
                
                // Eğer proje kullanıcıya ait değilse, projectId'yi sıfırla
                if (!$project) {
                    $projectId = null;
                }
            }
            
            // Funnel intent istatistiklerini hesapla
            $funnelStats = $this->calculateFunnelStats($projectId);
            
            // Widget tracking istatistiklerini hesapla
            $widgetTrackingStats = $this->calculateWidgetTrackingStats($projectId);
            
            // Project knowledge bilgilerini al
            $projectKnowledge = null;
            if ($projectId) {
                $projectKnowledgeService = app(ProjectKnowledgeService::class);
                $projectKnowledge = $projectKnowledgeService->getProjectKnowledge($projectId);
            }
            
            return view('dashboard.analytics', compact(
                'funnelStats', 
                'widgetTrackingStats',
                'projectId', 
                'projectName', 
                'projectKnowledge',
                'userProjects'
            ));
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Analytics verileri yüklenirken hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Widget tracking verilerini hesapla
     */
    private function calculateWidgetTrackingStats($projectId = null)
    {
        $query = WidgetTracking::query();
        
        if ($projectId) {
            $query->where('project_id', $projectId);
        }

        // Genel istatistikler
        $totalClicks = $query->where('event_type', 'product_link_click')->count();
        $totalInteractions = $query->count();
        
        // Bugünkü tıklamalar
        $todayClicks = $query->where('event_type', 'product_link_click')
            ->whereDate('created_at', today())
            ->count();
            
        // Intent bazlı tıklamalar
        $intentClicks = $query->where('event_type', 'product_link_click')
            ->select('intent', DB::raw('count(*) as count'))
            ->groupBy('intent')
            ->pluck('count', 'intent')
            ->toArray();
            
        // En çok tıklanan ürünler
        $topProducts = $query->where('event_type', 'product_link_click')
            ->whereNotNull('product_name')
            ->select('product_name', DB::raw('count(*) as count'))
            ->groupBy('product_name')
            ->orderBy(DB::raw('count(*)'), 'desc')
            ->limit(5)
            ->pluck('count', 'product_name')
            ->toArray();
            
        // Son 7 günlük tıklama trendi
        $weeklyTrend = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $clicks = $query->where('event_type', 'product_link_click')
                ->whereDate('created_at', $date)
                ->count();
            $weeklyTrend[$date] = $clicks;
        }

        return [
            'total_clicks' => $totalClicks,
            'total_interactions' => $totalInteractions,
            'today_clicks' => $todayClicks,
            'intent_clicks' => $intentClicks,
            'top_products' => $topProducts,
            'weekly_trend' => $weeklyTrend
        ];
    }

    /**
     * Widget event tracking endpoint
     */
    public function trackWidgetEvent(Request $request)
    {
        try {
            $validated = $request->validate([
                'session_id' => 'required|string',
                'project_id' => 'nullable|string',
                'event_type' => 'required|string',
                'intent' => 'nullable|string',
                'product_name' => 'nullable|string',
                'product_url' => 'nullable|string',
                'metadata' => 'nullable|array'
            ]);

            WidgetTracking::create([
                'session_id' => $validated['session_id'],
                'project_id' => $validated['project_id'],
                'event_type' => $validated['event_type'],
                'intent' => $validated['intent'],
                'product_name' => $validated['product_name'],
                'product_url' => $validated['product_url'],
                'metadata' => $validated['metadata'] ?? [],
                'user_agent' => $request->userAgent(),
                'ip_address' => $request->ip()
            ]);

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Intent count hesapla - JSON string'i decode et
     */
    private function getIntentCount($intentHistory): int
    {
        if (is_string($intentHistory)) {
            $decoded = json_decode($intentHistory, true);
            return is_array($decoded) ? count($decoded) : 0;
        }
        
        if (is_array($intentHistory)) {
            return count($intentHistory);
        }
        
        return 0;
    }
}
