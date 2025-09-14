<?php

namespace App\Http\Services;

use App\Models\Project;
use App\Models\KnowledgeBase;
use App\Models\KnowledgeChunk;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ProjectKnowledgeService
{
    /**
     * Project'e özel knowledge base bilgilerini getir
     */
    public function getProjectKnowledge(int $projectId): array
    {
        try {
            $project = Project::with(['knowledgeBases.chunks'])->find($projectId);
            
            if (!$project) {
                return [
                    'success' => false,
                    'message' => 'Project bulunamadı',
                    'data' => []
                ];
            }

            $knowledgeData = [
                'project_info' => [
                    'name' => $project->name,
                    'description' => $project->description,
                    'url' => $project->url,
                    'status' => $project->status,
                    'created_at' => $project->created_at
                ],
                'knowledge_bases' => [],
                'total_chunks' => 0,
                'active_knowledge_bases' => 0
            ];

            foreach ($project->knowledgeBases as $kb) {
                if ($kb->is_active) {
                    $knowledgeData['active_knowledge_bases']++;
                    
                    $kbData = [
                        'id' => $kb->id,
                        'name' => $kb->name,
                        'description' => $kb->description,
                        'source_type' => $kb->source_type,
                        'total_chunks' => $kb->chunks->count(),
                        'processed_records' => $kb->processed_records,
                        'last_processed' => $kb->last_processed_at,
                        'chunks' => $kb->chunks->take(10)->map(function($chunk) {
                            return [
                                'id' => $chunk->id,
                                'content' => $chunk->content,
                                'metadata' => $chunk->metadata,
                                'created_at' => $chunk->created_at
                            ];
                        })->toArray()
                    ];
                    
                    $knowledgeData['knowledge_bases'][] = $kbData;
                    $knowledgeData['total_chunks'] += $kb->chunks->count();
                }
            }

            return [
                'success' => true,
                'message' => 'Project knowledge base bilgileri başarıyla getirildi',
                'data' => $knowledgeData
            ];

        } catch (\Exception $e) {
            Log::error('ProjectKnowledgeService getProjectKnowledge error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Knowledge base bilgileri alınırken hata oluştu',
                'data' => []
            ];
        }
    }

    /**
     * Project'e özel funnel intent response'ları için knowledge base'den bilgi çek
     */
    public function getProjectSpecificResponse(int $projectId, string $intent, string $userMessage = ''): array
    {
        try {
            $project = Project::with(['knowledgeBases.chunks'])->find($projectId);
            
            if (!$project) {
                return $this->getDefaultResponse($intent);
            }

            $knowledgeData = $this->getProjectKnowledge($projectId);
            
            if (!$knowledgeData['success']) {
                return $this->getDefaultResponse($intent);
            }

            $projectInfo = $knowledgeData['data']['project_info'];
            $knowledgeBases = $knowledgeData['data']['knowledge_bases'];

            // Intent'e göre project-specific response oluştur
            switch ($intent) {
                case 'capabilities_inquiry':
                    return $this->generateCapabilitiesResponse($projectInfo, $knowledgeBases);
                
                case 'project_info':
                    return $this->generateProjectInfoResponse($projectInfo, $knowledgeBases);
                
                case 'conversion_guidance':
                    return $this->generateConversionGuidanceResponse($projectInfo, $knowledgeBases);
                
                case 'pricing_guidance':
                    return $this->generatePricingGuidanceResponse($projectInfo, $knowledgeBases);
                
                case 'demo_request':
                    return $this->generateDemoRequestResponse($projectInfo, $knowledgeBases);
                
                case 'contact_request':
                    return $this->generateContactRequestResponse($projectInfo, $knowledgeBases);
                
                case 'product_recommendations':
                    return $this->generateProductRecommendationsResponse($projectInfo, $knowledgeBases, $userMessage);
                
                default:
                    return $this->getDefaultResponse($intent);
            }

        } catch (\Exception $e) {
            Log::error('ProjectKnowledgeService getProjectSpecificResponse error: ' . $e->getMessage());
            return $this->getDefaultResponse($intent);
        }
    }

    /**
     * Capabilities inquiry için project-specific response
     */
    private function generateCapabilitiesResponse(array $projectInfo, array $knowledgeBases): array
    {
        $capabilities = [
            '🔍 **Ürün Arama & Keşif**',
            '• 15+ kategoride 500+ ürün arasında arama',
            '• Marka, fiyat, özellik bazlı filtreleme',
            '• Akıllı ürün önerileri',
            '',
            '💰 **Fiyat & Stok Bilgisi**',
            '• Anlık fiyat sorgulama',
            '• Stok durumu kontrolü',
            '• Fiyat karşılaştırması',
            '',
            '📦 **Sipariş & Kargo**',
            '• Sipariş takibi',
            '• Kargo durumu sorgulama',
            '• Sipariş geçmişi',
            '',
            '🎯 **Kişisel Öneriler**',
            '• Geçmiş alışverişlere göre öneriler',
            '• Benzer ürün önerileri',
            '• Trend ürünler',
            '',
            '💬 **Destek & İletişim**',
            '• 7/24 canlı destek',
            '• WhatsApp desteği',
            '• Video call danışmanlık'
        ];

        // Project-specific bilgiler ekle
        if (!empty($projectInfo['name'])) {
            array_unshift($capabilities, "**{$projectInfo['name']}** projesi için özel yetenekler:");
            array_unshift($capabilities, '');
        }

        // Knowledge base'den özel yetenekler ekle
        foreach ($knowledgeBases as $kb) {
            if (strpos(strtolower($kb['name']), 'özellik') !== false || 
                strpos(strtolower($kb['name']), 'yetenek') !== false) {
                $capabilities[] = '';
                $capabilities[] = "**{$kb['name']}:**";
                foreach (array_slice($kb['chunks'], 0, 3) as $chunk) {
                    $capabilities[] = "• " . substr($chunk['content'], 0, 100) . "...";
                }
            }
        }

        return [
            'type' => 'capabilities_inquiry',
            'message' => implode("\n", $capabilities),
            'suggestions' => [
                'Ürün arama',
                'Fiyat sorgula',
                'Sipariş takip',
                'Demo talep et',
                'İletişim kur'
            ],
            'data' => [
                'project_name' => $projectInfo['name'] ?? 'Proje',
                'total_knowledge_bases' => count($knowledgeBases),
                'widget_type' => 'capabilities_display'
            ]
        ];
    }

    /**
     * Project info için project-specific response
     */
    private function generateProjectInfoResponse(array $projectInfo, array $knowledgeBases): array
    {
        $info = [
            "**{$projectInfo['name']}** hakkında bilgiler:",
            '',
            "📋 **Proje Detayları:**",
            "• **Açıklama:** " . ($projectInfo['description'] ?? 'Açıklama bulunmuyor'),
            "• **Website:** " . ($projectInfo['url'] ?? 'URL bulunmuyor'),
            "• **Durum:** " . ($projectInfo['status'] ?? 'Bilinmiyor'),
            "• **Oluşturulma:** " . ($projectInfo['created_at'] ?? 'Bilinmiyor'),
            '',
            "📚 **Knowledge Base Bilgileri:**",
            "• **Toplam Knowledge Base:** " . count($knowledgeBases),
            "• **Toplam Chunk:** " . array_sum(array_column($knowledgeBases, 'total_chunks'))
        ];

        // Knowledge base'den özel bilgiler ekle
        foreach ($knowledgeBases as $kb) {
            if (strpos(strtolower($kb['name']), 'hakkında') !== false || 
                strpos(strtolower($kb['name']), 'bilgi') !== false) {
                $info[] = '';
                $info[] = "**{$kb['name']}:**";
                foreach (array_slice($kb['chunks'], 0, 2) as $chunk) {
                    $info[] = "• " . substr($chunk['content'], 0, 150) . "...";
                }
            }
        }

        return [
            'type' => 'project_info',
            'message' => implode("\n", $info),
            'suggestions' => [
                'Yeteneklerini öğren',
                'Fiyat bilgisi al',
                'Demo talep et',
                'İletişim kur'
            ],
            'data' => [
                'project_info' => $projectInfo,
                'knowledge_bases' => $knowledgeBases,
                'widget_type' => 'project_info_display'
            ]
        ];
    }

    /**
     * Conversion guidance için project-specific response
     */
    private function generateConversionGuidanceResponse(array $projectInfo, array $knowledgeBases): array
    {
        $guidance = [
            "**{$projectInfo['name']}** ile müşteri olmak için adımlar:",
            '',
            "🚀 **Hızlı Başlangıç:**",
            "1. **Ürün Keşfi:** Size uygun ürünleri bulun",
            "2. **Fiyat Karşılaştırması:** En uygun fiyatları inceleyin",
            "3. **Demo Talep:** Ürünleri yakından görün",
            "4. **Sipariş Ver:** Güvenli ödeme ile satın alın",
            '',
            "💡 **Öneriler:**",
            "• Size özel ürün önerilerimizi inceleyin",
            "• Kampanya ve indirimleri takip edin",
            "• Canlı destekten yardım alın"
        ];

        // Knowledge base'den conversion bilgileri ekle
        foreach ($knowledgeBases as $kb) {
            if (strpos(strtolower($kb['name']), 'sipariş') !== false || 
                strpos(strtolower($kb['name']), 'satın') !== false ||
                strpos(strtolower($kb['name']), 'rehber') !== false) {
                $guidance[] = '';
                $guidance[] = "**{$kb['name']}:**";
                foreach (array_slice($kb['chunks'], 0, 2) as $chunk) {
                    $guidance[] = "• " . substr($chunk['content'], 0, 120) . "...";
                }
            }
        }

        return [
            'type' => 'conversion_guidance',
            'message' => implode("\n", $guidance),
            'suggestions' => [
                'Ürün ara',
                'Fiyat sorgula',
                'Demo talep et',
                'Sipariş ver',
                'İletişim kur'
            ],
            'data' => [
                'project_name' => $projectInfo['name'] ?? 'Proje',
                'conversion_steps' => [
                    'discovery' => 'Ürün keşfi',
                    'comparison' => 'Fiyat karşılaştırması',
                    'demo' => 'Demo talep',
                    'purchase' => 'Sipariş verme'
                ],
                'widget_type' => 'conversion_guidance_display'
            ]
        ];
    }

    /**
     * Pricing guidance için project-specific response
     */
    private function generatePricingGuidanceResponse(array $projectInfo, array $knowledgeBases): array
    {
        $pricing = [
            "**{$projectInfo['name']}** fiyat bilgileri:",
            '',
            "💰 **Fiyat Seçenekleri:**",
            "• **Temel Paket:** Uygun fiyatlı başlangıç",
            "• **Pro Paket:** Gelişmiş özellikler",
            "• **Premium Paket:** Tüm özellikler + destek",
            '',
            "💳 **Ödeme Seçenekleri:**",
            "• Kredi kartı ile tek seferde ödeme",
            "• Taksitli ödeme seçenekleri",
            "• Kurumsal faturalandırma",
            '',
            "🎁 **Özel Fırsatlar:**",
            "• İlk sipariş indirimi",
            "• Toplu alım avantajları",
            "• Referans bonusu"
        ];

        // Knowledge base'den fiyat bilgileri ekle
        foreach ($knowledgeBases as $kb) {
            if (strpos(strtolower($kb['name']), 'fiyat') !== false || 
                strpos(strtolower($kb['name']), 'ücret') !== false ||
                strpos(strtolower($kb['name']), 'paket') !== false) {
                $pricing[] = '';
                $pricing[] = "**{$kb['name']}:**";
                foreach (array_slice($kb['chunks'], 0, 2) as $chunk) {
                    $pricing[] = "• " . substr($chunk['content'], 0, 120) . "...";
                }
            }
        }

        return [
            'type' => 'pricing_guidance',
            'message' => implode("\n", $pricing),
            'suggestions' => [
                'Paket detayları',
                'Ödeme seçenekleri',
                'Demo talep et',
                'Sipariş ver',
                'İletişim kur'
            ],
            'data' => [
                'project_name' => $projectInfo['name'] ?? 'Proje',
                'pricing_tiers' => [
                    'basic' => 'Temel Paket',
                    'pro' => 'Pro Paket',
                    'premium' => 'Premium Paket'
                ],
                'widget_type' => 'pricing_guidance_display'
            ]
        ];
    }

    /**
     * Demo request için project-specific response
     */
    private function generateDemoRequestResponse(array $projectInfo, array $knowledgeBases): array
    {
        $demo = [
            "**{$projectInfo['name']}** demo talep süreci:",
            '',
            "🎯 **Demo Seçenekleri:**",
            "• **Canlı Demo:** Uzmanımızla birebir görüşme",
            "• **Video Demo:** Hazır tanıtım videoları",
            "• **Test Ortamı:** Kendi başınıza deneme",
            '',
            "📅 **Randevu Seçenekleri:**",
            "• Hemen şimdi başlayın",
            "• Uygun zamanınızda planlayın",
            "• Kurumsal sunum talep edin",
            '',
            "💡 **Demo İçeriği:**",
            "• Temel özellikler tanıtımı",
            "• Gerçek kullanım senaryoları",
            "• Soru-cevap bölümü"
        ];

        return [
            'type' => 'demo_request',
            'message' => implode("\n", $demo),
            'suggestions' => [
                'Canlı demo talep et',
                'Video demo izle',
                'Test ortamına eriş',
                'Randevu planla',
                'İletişim kur'
            ],
            'data' => [
                'project_name' => $projectInfo['name'] ?? 'Proje',
                'demo_types' => [
                    'live' => 'Canlı Demo',
                    'video' => 'Video Demo',
                    'test' => 'Test Ortamı'
                ],
                'widget_type' => 'demo_request_display'
            ]
        ];
    }

    /**
     * Contact request için project-specific response
     */
    private function generateContactRequestResponse(array $projectInfo, array $knowledgeBases): array
    {
        $contact = [
            "**{$projectInfo['name']}** ile iletişim seçenekleri:",
            '',
            "📞 **İletişim Kanalları:**",
            "• **Telefon:** +90 (212) 555-0123",
            "• **Email:** info@{$projectInfo['name']}.com",
            "• **WhatsApp:** +90 (212) 555-0123",
            "• **Canlı Chat:** 7/24 aktif",
            '',
            "🕒 **Çalışma Saatleri:**",
            "• Pazartesi-Cuma: 09:00-18:00",
            "• Cumartesi: 10:00-16:00",
            "• Pazar: Kapalı",
            '',
            "💬 **Destek Türleri:**",
            "• Teknik destek",
            "• Satış danışmanlığı",
            "• Genel bilgi"
        ];

        return [
            'type' => 'contact_request',
            'message' => implode("\n", $contact),
            'suggestions' => [
                'Telefon ara',
                'Email gönder',
                'WhatsApp mesaj',
                'Canlı chat',
                'Randevu al'
            ],
            'data' => [
                'project_name' => $projectInfo['name'] ?? 'Proje',
                'contact_options' => [
                    'phone' => '+90 (212) 555-0123',
                    'email' => "info@{$projectInfo['name']}.com",
                    'whatsapp' => '+90 (212) 555-0123',
                    'live_chat' => '7/24 aktif'
                ],
                'widget_type' => 'contact_request_display'
            ]
        ];
    }

    /**
     * Product recommendations için project-specific response
     */
    private function generateProductRecommendationsResponse(array $projectInfo, array $knowledgeBases, string $userMessage): array
    {
        $recommendations = [
            "**{$projectInfo['name']}** için size özel öneriler:",
            '',
            "🎯 **Popüler Ürünler:**",
            "• En çok tercih edilen ürünler",
            "• Müşteri yorumları ile desteklenen",
            "• Fiyat-performans liderleri",
            '',
            "⭐ **Öne Çıkanlar:**",
            "• Yeni eklenen ürünler",
            "• Özel indirimli ürünler",
            "• Sınırlı sayıda stok",
            '',
            "🔍 **Kişisel Öneriler:**",
            "• Geçmiş tercihlerinize göre",
            "• Benzer müşterilerin seçtikleri",
            "• Size özel fırsatlar"
        ];

        // Knowledge base'den ürün önerileri ekle
        foreach ($knowledgeBases as $kb) {
            if (strpos(strtolower($kb['name']), 'ürün') !== false || 
                strpos(strtolower($kb['name']), 'öneri') !== false ||
                strpos(strtolower($kb['name']), 'popüler') !== false) {
                $recommendations[] = '';
                $recommendations[] = "**{$kb['name']}:**";
                foreach (array_slice($kb['chunks'], 0, 2) as $chunk) {
                    $recommendations[] = "• " . substr($chunk['content'], 0, 120) . "...";
                }
            }
        }

        return [
            'type' => 'product_recommendations',
            'message' => implode("\n", $recommendations),
            'suggestions' => [
                'Ürün detayları',
                'Fiyat sorgula',
                'Karşılaştır',
                'Sepete ekle',
                'Demo talep et'
            ],
            'data' => [
                'project_name' => $projectInfo['name'] ?? 'Proje',
                'recommendation_types' => [
                    'popular' => 'Popüler Ürünler',
                    'featured' => 'Öne Çıkanlar',
                    'personal' => 'Kişisel Öneriler'
                ],
                'widget_type' => 'product_recommendations_display'
            ]
        ];
    }

    /**
     * Default response (knowledge base yoksa)
     */
    private function getDefaultResponse(string $intent): array
    {
        return [
            'type' => $intent,
            'message' => 'Bu intent için project-specific bilgi bulunamadı. Genel bilgi almak için lütfen tekrar deneyin.',
            'suggestions' => ['Yeniden dene', 'Genel bilgi al', 'İletişim kur'],
            'data' => [
                'widget_type' => 'default_response',
                'fallback' => true
            ]
        ];
    }
}
