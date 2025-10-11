@extends('layouts.dashboard')

@section('title', 'AI Token Hesaplayıcı & Fiyat Stratejisi')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                    🧮 AI Token Hesaplayıcı & Fiyat Stratejisi
                </h1>
                <p class="text-gray-600 dark:text-gray-400">
                    OpenAI token maliyetlerini hesaplayın ve AI destekli fiyatlandırma önerisi alın
                </p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Sol Panel - Token Hesaplayıcı -->
        <div class="lg:col-span-2">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                    💰 Token Kullanım Hesaplayıcı
                </h2>

                <form id="tokenCalculatorForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Session Sayısı -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Session Sayısı
                            </label>
                            <input type="number" 
                                   id="sessions" 
                                   name="sessions" 
                                   value="10" 
                                   min="1" 
                                   max="1000000"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                   required>
                            <p class="text-xs text-gray-500 mt-1">Toplam chat session sayısı</p>
                        </div>

                        <!-- Mesaj/Session -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Mesaj / Session
                            </label>
                            <input type="number" 
                                   id="messages_per_session" 
                                   name="messages_per_session" 
                                   value="5" 
                                   min="1" 
                                   max="100"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white"
                                   required>
                            <p class="text-xs text-gray-500 mt-1">Her session'daki ortalama mesaj</p>
                        </div>

                        <!-- Model Seçimi -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                OpenAI Model
                            </label>
                            <select id="model" 
                                    name="model"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white">
                                <option value="gpt-4o-mini" selected>GPT-4o Mini (Önerilen - Projede kullanılan)</option>
                                <option value="gpt-4o">GPT-4o (Premium)</option>
                                <option value="gpt-3.5-turbo">GPT-3.5 Turbo (Ekonomik)</option>
                            </select>
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200">
                        🔍 Hesapla
                    </button>
                </form>
            </div>

            <!-- Hesaplama Sonuçları -->
            <div id="calculationResults" class="hidden">
                <!-- Token Kullanımı -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📊 Token Kullanımı</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-600" id="totalMessages">-</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Toplam Mesaj</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-green-600" id="inputTokens">-</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Input Tokens</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-purple-600" id="outputTokens">-</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Output Tokens</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-orange-600" id="totalTokens">-</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Toplam Tokens</p>
                        </div>
                    </div>
                </div>

                <!-- Maliyet Analizi -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">💵 Maliyet Analizi</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Toplam Maliyet (USD)</p>
                            <p class="text-2xl font-bold text-blue-600" id="totalCostUSD">$-</p>
                        </div>
                        <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Toplam Maliyet (TRY)</p>
                            <p class="text-2xl font-bold text-green-600" id="totalCostTRY">₺-</p>
                        </div>
                        <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Session Başı</p>
                            <p class="text-xl font-bold text-purple-600" id="costPerSession">$-</p>
                        </div>
                    </div>
                </div>

                <!-- Senaryolar -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📈 Aylık Kullanım Senaryoları</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-4 py-2 text-left">Senaryo</th>
                                    <th class="px-4 py-2 text-right">Sessions</th>
                                    <th class="px-4 py-2 text-right">Mesajlar</th>
                                    <th class="px-4 py-2 text-right">Aylık (USD)</th>
                                    <th class="px-4 py-2 text-right">Yıllık (USD)</th>
                                </tr>
                            </thead>
                            <tbody id="scenariosTable" class="divide-y divide-gray-200 dark:divide-gray-700">
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Grafik -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📊 Maliyet Dağılımı</h3>
                    <div id="chartContainer" style="position: relative; height: 300px;">
                        <canvas id="costChart"></canvas>
                        <!-- Loading State -->
                        <div id="chartLoading" style="display: none;" class="absolute inset-0 items-center justify-center bg-white dark:bg-gray-800">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-2"></div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Grafik yükleniyor...</p>
                            </div>
                        </div>
                        <!-- Error State -->
                        <div id="chartError" style="display: none;" class="absolute inset-0 items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="text-center p-4">
                                <p class="text-sm text-gray-600 dark:text-gray-400">⚠️ Grafik yüklenirken sorun oluştu</p>
                                <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Tablodaki verileri kullanabilirsiniz</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Export Button -->
                <div class="text-right">
                    <button onclick="exportCalculation()" 
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                        📥 Excel'e Aktar
                    </button>
                </div>
            </div>
        </div>

        <!-- Sağ Panel - AI Fiyat Önerisi Agent -->
        <div class="lg:col-span-1">
            <div class="bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg shadow-lg p-6 text-white mb-6 sticky top-4">
                <h2 class="text-xl font-bold mb-4">🤖 AI Fiyat Önerisi Agent</h2>
                <p class="text-sm opacity-90 mb-4">
                    Token maliyetlerinizi analiz ederek akıllı plan fiyatlandırması önerir
                </p>

                <form id="aiPriceForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Aylık Session Hedefi</label>
                        <input type="number" 
                               id="ai_sessions" 
                               name="sessions_per_month" 
                               value="1000" 
                               min="1"
                               class="w-full px-4 py-2 rounded-lg text-gray-900 focus:ring-2 focus:ring-white"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Mesaj / Session</label>
                        <input type="number" 
                               id="ai_messages" 
                               name="messages_per_session" 
                               value="5" 
                               min="1"
                               class="w-full px-4 py-2 rounded-lg text-gray-900 focus:ring-2 focus:ring-white"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Model</label>
                        <select id="ai_model" 
                                name="model"
                                class="w-full px-4 py-2 rounded-lg text-gray-900 focus:ring-2 focus:ring-white">
                            <option value="gpt-4o-mini">GPT-4o Mini</option>
                            <option value="gpt-4o">GPT-4o</option>
                            <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Kar Marjı (%)</label>
                        <input type="number" 
                               id="profit_margin" 
                               name="profit_margin" 
                               value="200" 
                               min="0" 
                               max="500"
                               class="w-full px-4 py-2 rounded-lg text-gray-900 focus:ring-2 focus:ring-white"
                               required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Hedef Pazar</label>
                        <select id="target_market" 
                                name="target_market"
                                class="w-full px-4 py-2 rounded-lg text-gray-900 focus:ring-2 focus:ring-white">
                            <option value="budget">Ekonomik</option>
                            <option value="standard" selected>Standart</option>
                            <option value="premium">Premium</option>
                            <option value="enterprise">Kurumsal</option>
                        </select>
                    </div>

                    <button type="submit" 
                            class="w-full bg-white text-purple-600 font-semibold py-3 px-6 rounded-lg hover:bg-gray-100 transition duration-200">
                        ✨ AI Önerisi Al
                    </button>
                </form>

                <!-- Loading Spinner -->
                <div id="aiLoading" class="hidden text-center py-8">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-white mx-auto mb-4"></div>
                    <p class="text-sm">AI fiyat önerileri hazırlanıyor...</p>
                </div>
            </div>

            <!-- AI Önerisi Sonuçları -->
            <div id="aiRecommendations" class="hidden">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📋 Önerilen Planlar</h3>
                    <div id="plansList" class="space-y-4"></div>
                    
                    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">💡 Pazar Analizi</h4>
                        <p id="marketAnalysis" class="text-sm text-gray-700 dark:text-gray-300"></p>
                    </div>

                    <div class="mt-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-2">📌 Öneriler</h4>
                        <p id="recommendations" class="text-sm text-gray-700 dark:text-gray-300"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let currentCalculation = null;
let costChart = null;

// Token Calculator Form Submit
document.getElementById('tokenCalculatorForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('{{ route("admin.token-calculator.calculate") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            currentCalculation = result.data;
            displayResults(result.data);
        }
    } catch (error) {
        console.error('Calculation error:', error);
        alert('Hesaplama sırasında bir hata oluştu');
    }
});

// Display Results
function displayResults(data) {
    // Show results section
    document.getElementById('calculationResults').classList.remove('hidden');
    
    // Token kullanımı
    document.getElementById('totalMessages').textContent = data.input.total_messages.toLocaleString();
    document.getElementById('inputTokens').textContent = data.tokens.input_tokens.toLocaleString();
    document.getElementById('outputTokens').textContent = data.tokens.output_tokens.toLocaleString();
    document.getElementById('totalTokens').textContent = data.tokens.total_tokens.toLocaleString();
    
    // Maliyet
    document.getElementById('totalCostUSD').textContent = '$' + data.costs.total_cost_usd;
    document.getElementById('totalCostTRY').textContent = '₺' + data.costs.total_cost_try.toLocaleString('tr-TR');
    document.getElementById('costPerSession').textContent = '$' + data.costs.cost_per_session_usd;
    
    // Senaryolar tablosu
    const scenariosTable = document.getElementById('scenariosTable');
    scenariosTable.innerHTML = '';
    
    const scenarioNames = {
        'low': '🟢 Düşük Kullanım',
        'medium': '🟡 Orta Kullanım',
        'high': '🟠 Yüksek Kullanım',
        'enterprise': '🔴 Kurumsal'
    };
    
    Object.entries(data.scenarios).forEach(([key, scenario]) => {
        const row = `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-4 py-2 font-medium">${scenarioNames[key]}</td>
                <td class="px-4 py-2 text-right">${scenario.sessions.toLocaleString()}</td>
                <td class="px-4 py-2 text-right">${scenario.messages.toLocaleString()}</td>
                <td class="px-4 py-2 text-right font-semibold">$${scenario.monthly_cost_usd}</td>
                <td class="px-4 py-2 text-right text-gray-600">$${scenario.yearly_cost_usd}</td>
            </tr>
        `;
        scenariosTable.innerHTML += row;
    });
    
    // Grafik çiz
    drawCostChart(data);
    
    // Smooth scroll
    document.getElementById('calculationResults').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Draw Cost Chart
function drawCostChart(data) {
    const ctx = document.getElementById('costChart');
    const loadingEl = document.getElementById('chartLoading');
    const errorEl = document.getElementById('chartError');
    
    if (!ctx) {
        console.error('Canvas element not found');
        if (errorEl) errorEl.style.display = 'flex';
        return;
    }
    
    // Show loading
    if (loadingEl) loadingEl.style.display = 'flex';
    if (errorEl) errorEl.style.display = 'none';
    
    // Check if Chart.js is loaded
    if (typeof Chart === 'undefined') {
        console.error('Chart.js is not loaded');
        if (loadingEl) loadingEl.style.display = 'none';
        if (errorEl) errorEl.style.display = 'flex';
        return;
    }
    
    try {
        if (costChart) {
            costChart.destroy();
        }
        
        const scenarios = Object.values(data.scenarios);
        
        // Dark mode detection
        const isDarkMode = document.documentElement.classList.contains('dark');
        const textColor = isDarkMode ? '#e5e7eb' : '#374151';
        const gridColor = isDarkMode ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
        
        costChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Düşük', 'Orta', 'Yüksek', 'Kurumsal'],
                datasets: [
                    {
                        label: 'Aylık Maliyet (USD)',
                        data: scenarios.map(s => s.monthly_cost_usd),
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgb(59, 130, 246)',
                        borderWidth: 2,
                        borderRadius: 6,
                        barThickness: 40
                    },
                    {
                        label: 'Yıllık Maliyet (USD)',
                        data: scenarios.map(s => s.yearly_cost_usd),
                        backgroundColor: 'rgba(16, 185, 129, 0.7)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 2,
                        borderRadius: 6,
                        barThickness: 40
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: textColor,
                            font: {
                                size: 12,
                                weight: 'bold'
                            },
                            padding: 15,
                            usePointStyle: true
                        }
                    },
                    title: {
                        display: true,
                        text: 'Kullanım Senaryolarına Göre Maliyet Karşılaştırması',
                        color: textColor,
                        font: {
                            size: 14,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: isDarkMode ? 'rgba(31, 41, 55, 0.9)' : 'rgba(255, 255, 255, 0.9)',
                        titleColor: textColor,
                        bodyColor: textColor,
                        borderColor: gridColor,
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: textColor,
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: gridColor,
                            drawBorder: false
                        },
                        ticks: {
                            color: textColor,
                            font: {
                                size: 11
                            },
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
        
        // Hide loading on success
        if (loadingEl) loadingEl.style.display = 'none';
        
    } catch (error) {
        console.error('Error drawing chart:', error);
        if (loadingEl) loadingEl.style.display = 'none';
        if (errorEl) errorEl.style.display = 'flex';
    }
}

// AI Price Recommendation Form Submit
document.getElementById('aiPriceForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData);
    
    // Show loading
    document.getElementById('aiLoading').classList.remove('hidden');
    document.getElementById('aiRecommendations').classList.add('hidden');
    e.target.classList.add('hidden');
    
    try {
        const response = await fetch('{{ route("admin.token-calculator.ai-price") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            displayAIRecommendations(result.data);
        }
    } catch (error) {
        console.error('AI recommendation error:', error);
        alert('AI önerisi alınırken bir hata oluştu');
    } finally {
        document.getElementById('aiLoading').classList.add('hidden');
        e.target.classList.remove('hidden');
    }
});

// Display AI Recommendations
function displayAIRecommendations(data) {
    document.getElementById('aiRecommendations').classList.remove('hidden');
    
    // Plans
    const plansList = document.getElementById('plansList');
    plansList.innerHTML = '';
    
    data.plans.forEach((plan, index) => {
        const colors = ['blue', 'purple', 'pink', 'orange'];
        const color = colors[index % colors.length];
        
        const planCard = `
            <div class="border-2 border-${color}-200 dark:border-${color}-700 rounded-lg p-4 hover:shadow-lg transition duration-200">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-bold text-lg text-gray-900 dark:text-white">${plan.name}</h4>
                    <span class="text-2xl">${['🥉', '🥈', '🥇', '💎'][index]}</span>
                </div>
                
                <div class="flex items-baseline gap-2 mb-2">
                    <span class="text-3xl font-bold text-${color}-600">₺${plan.monthly_price}</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400">/ay</span>
                </div>
                
                <div class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    Yıllık: <span class="font-semibold text-green-600">₺${plan.yearly_price}</span>
                </div>
                
                <div class="text-xs bg-gray-100 dark:bg-gray-700 rounded px-2 py-1 mb-3">
                    ${plan.usage_tokens.toLocaleString()} token/ay
                </div>
                
                <div class="text-xs text-gray-700 dark:text-gray-300 mb-3">
                    <strong>Hedef:</strong> ${plan.target_customer}
                </div>
                
                <ul class="text-xs space-y-1 mb-3">
                    ${plan.features.map(f => `<li class="flex items-start gap-1"><span class="text-green-500">✓</span>${f}</li>`).join('')}
                </ul>
                
                <button onclick="savePlanToDatabase(${index})" 
                        class="w-full text-xs bg-${color}-600 hover:bg-${color}-700 text-white font-semibold py-2 px-4 rounded transition duration-200">
                    📁 Plana Kaydet
                </button>
                
                <details class="mt-2">
                    <summary class="text-xs text-gray-600 dark:text-gray-400 cursor-pointer">Mantık</summary>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-2">${plan.reasoning}</p>
                </details>
            </div>
        `;
        plansList.innerHTML += planCard;
    });
    
    // Market Analysis
    document.getElementById('marketAnalysis').textContent = data.market_analysis;
    document.getElementById('recommendations').textContent = data.recommendations;
    
    // Store for later use
    window.aiPlans = data.plans;
    
    // Smooth scroll
    document.getElementById('aiRecommendations').scrollIntoView({ behavior: 'smooth' });
}

// Save Plan to Database
async function savePlanToDatabase(index) {
    if (!window.aiPlans || !window.aiPlans[index]) {
        alert('Plan bilgisi bulunamadı');
        return;
    }
    
    const plan = window.aiPlans[index];
    
    if (!confirm(`"${plan.name}" planını veritabanına kaydetmek istediğinize emin misiniz?`)) {
        return;
    }
    
    try {
        const response = await fetch('{{ route("admin.token-calculator.save-plan") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                name: plan.name,
                monthly_price: plan.monthly_price,
                yearly_price: plan.yearly_price,
                usage_tokens: plan.usage_tokens,
                features: plan.features,
                token_reset_period: 'monthly'
            })
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('✅ Plan başarıyla kaydedildi!');
        } else {
            alert('❌ ' + result.message);
        }
    } catch (error) {
        console.error('Save plan error:', error);
        alert('Plan kaydedilirken bir hata oluştu');
    }
}

// Export Calculation
function exportCalculation() {
    if (!currentCalculation) {
        alert('Önce bir hesaplama yapmalısınız');
        return;
    }
    
    const exportData = {
        sessions: currentCalculation.input.sessions,
        messages_per_session: currentCalculation.input.messages_per_session,
        total_messages: currentCalculation.input.total_messages,
        model: currentCalculation.input.model,
        input_tokens: currentCalculation.tokens.input_tokens,
        output_tokens: currentCalculation.tokens.output_tokens,
        total_tokens: currentCalculation.tokens.total_tokens,
        input_cost_usd: currentCalculation.costs.input_cost_usd,
        output_cost_usd: currentCalculation.costs.output_cost_usd,
        total_cost_usd: currentCalculation.costs.total_cost_usd,
        total_cost_try: currentCalculation.costs.total_cost_try,
        cost_per_session_usd: currentCalculation.costs.cost_per_session_usd,
        cost_per_message_usd: currentCalculation.costs.cost_per_message_usd
    };
    
    // Create form and submit
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.token-calculator.export") }}';
    form.style.display = 'none';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    const dataInput = document.createElement('input');
    dataInput.type = 'hidden';
    dataInput.name = 'data';
    dataInput.value = JSON.stringify(exportData);
    form.appendChild(dataInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}
</script>
@endpush

@push('styles')
<style>
    /* Custom animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    #calculationResults, #aiRecommendations {
        animation: fadeIn 0.5s ease-out;
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
@endpush
@endsection

