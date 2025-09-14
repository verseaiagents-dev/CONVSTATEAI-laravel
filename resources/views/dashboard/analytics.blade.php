@extends('layouts.dashboard')

@section('title', 'Gerçek Zamanlı Analitik')

@section('content')
<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<div class="space-y-6">
    <!-- Analytics Dashboard Header -->
    <div class="glass-effect rounded-2xl p-8 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-purple-glow rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        <div class="absolute bottom-0 left-0 w-40 h-40 bg-neon-purple rounded-full mix-blend-multiply filter blur-xl opacity-20"></div>
        
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h1 class="text-4xl font-bold mb-4">
                        <span class="gradient-text">Gerçek Zamanlı Analitik </span>
                    </h1>
                    <p class="text-xl text-gray-300">
                        Gerçek zamanlı analitik veriler ve performans metrikleri
                    </p>
                </div>
                
                @if($userProjects->count() > 1)
                    <div class="relative">
                        <select onchange="changeProject(this.value)" class="px-4 py-3 bg-gray-800 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-purple-glow focus:border-transparent">
                            <option value="">Tüm Projeler</option>
                            @foreach($userProjects as $project)
                                <option value="{{ $project->id }}" {{ $projectId == $project->id ? 'selected' : '' }}>
                                    {{ $project->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Real-time Stats Row 1 -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Active Sessions -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-purple-glow/50 transition-all duration-300 hover:shadow-lg hover:shadow-purple-glow/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Aktif Oturum</p>
                    <p class="mt-2 text-3xl font-bold text-white" id="activeSessions">-</p>
                </div>
                <div class="p-3 bg-purple-glow/20 rounded-full">
                    <svg class="w-8 h-8 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-400" id="activeSessionsChange">Yükleniyor...</span>
            </div>
        </div>

        <!-- Interactions Last Hour -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-green-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-green-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Son Saat Etkileşim</p>
                    <p class="mt-2 text-3xl font-bold text-white" id="interactionsLastHour">-</p>
                </div>
                <div class="p-3 bg-green-500/20 rounded-full">
                    <svg class="w-8 h-8 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-400" id="interactionsChange">Yükleniyor...</span>
            </div>
        </div>

        <!-- Conversion Rate -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-blue-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Dönüşüm Oranı</p>
                    <p class="mt-2 text-3xl font-bold text-white" id="conversionRate">-</p>
                </div>
                <div class="p-3 bg-blue-500/20 rounded-full">
                    <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-400" id="conversionChange">Yükleniyor...</span>
            </div>
        </div>

        <!-- Avg Session Duration -->
        <div class="glass-effect rounded-xl p-6 border border-gray-700 hover:border-yellow-500/50 transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/10">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-400 uppercase tracking-wider">Ortalama Oturum Süresi</p>
                    <p class="mt-2 text-3xl font-bold text-white" id="avgSessionDuration">-</p>
                </div>
                <div class="p-3 bg-yellow-500/20 rounded-full">
                    <svg class="w-8 h-8 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm">
                <span class="text-gray-400" id="durationChange">Yükleniyor...</span>
            </div>
        </div>
    </div>



    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Real-time Activity Chart -->
        <div class="glass-effect rounded-xl border border-gray-700">
            <div class="p-6 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Gerçek Zamanlı Aktivite</h3>
            </div>
            <div class="p-6">
                <div class="chart-container" style="height: 300px;">
                    <canvas id="realTimeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- User Engagement Chart -->
        <div class="glass-effect rounded-xl border border-gray-700">
            <div class="p-6 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">Kullanıcı Etkileşimi</h3>
            </div>
            <div class="p-6">
                <div class="chart-container" style="height: 300px;">
                    <canvas id="engagementChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="glass-effect rounded-xl border border-gray-700">
        <div class="p-6 border-b border-gray-700">
            <h3 class="text-lg font-semibold text-white">Performans Metrikleri</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Response Time -->
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-2" id="avgResponseTime">-</div>
                    <div class="text-sm text-gray-400">Ortalama Yanıt Süresi (ms)</div>
                    <div class="mt-2 flex items-center justify-center">
                        <span class="text-gray-400 text-sm" id="responseTimeChange">Yükleniyor...</span>
                    </div>
                </div>

                <!-- Error Rate -->
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-2" id="errorRate">-</div>
                    <div class="text-sm text-gray-400">Başarı Oranı (%)</div>
                    <div class="mt-2 flex items-center justify-center">
                        <span class="text-gray-400 text-sm" id="errorRateChange">Yükleniyor...</span>
                    </div>
                </div>

                <!-- User Satisfaction -->
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-2" id="userSatisfaction">-</div>
                    <div class="text-sm text-gray-400">Kullanıcı Memnuniyet Skoru</div>
                    <div class="mt-2 flex items-center justify-center">
                        <span class="text-gray-400 text-sm" id="satisfactionChange">Yükleniyor...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js" 
        onerror="this.onerror=null; this.src='https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js';"></script>

<script>
let realTimeChart, engagementChart;
let previousData = {};
let activeRequests = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Wait for Chart.js to load with timeout
    let chartLoadAttempts = 0;
    const maxAttempts = 10;
    
    function waitForChart() {
        if (typeof Chart !== 'undefined') {
            // Chart.js loaded successfully
            initializeCharts();
            loadAnalyticsData();
            
            // Set up real-time updates
            setInterval(function() {
                loadAnalyticsData();
            }, 30000); // Update every 30 seconds
        } else if (chartLoadAttempts < maxAttempts) {
            chartLoadAttempts++;
            setTimeout(waitForChart, 100); // Try again in 100ms
        } else {
            console.error('Chart.js failed to load after multiple attempts');
            // Show error message to user
            document.querySelectorAll('.chart-container').forEach(container => {
                container.innerHTML = '<div class="text-center text-red-400 py-8">Chart kütüphanesi yüklenemedi. Lütfen sayfayı yenileyin.</div>';
            });
        }
    }
    
    waitForChart();
});

function initializeCharts() {
    try {
        // Real-time Activity Chart
        const realTimeCtx = document.getElementById('realTimeChart').getContext('2d');
        realTimeChart = new Chart(realTimeCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Aktif Oturumlar',
                data: [],
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Etkileşimler',
                data: [],
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    labels: {
                        color: '#9ca3af'
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(31, 41, 55, 0.9)',
                    titleColor: '#ffffff',
                    bodyColor: '#d1d5db',
                    borderColor: '#4b5563',
                    borderWidth: 1,
                    cornerRadius: 8
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(75, 85, 99, 0.3)'
                    },
                    ticks: {
                        color: '#9ca3af'
                    }
                },
                x: {
                    grid: {
                        color: 'rgba(75, 85, 99, 0.3)'
                    },
                    ticks: {
                        color: '#9ca3af'
                    }
                }
            }
        }
    });

    // User Engagement Chart
    const engagementCtx = document.getElementById('engagementChart').getContext('2d');
    engagementChart = new Chart(engagementCtx, {
        type: 'doughnut',
        data: {
            labels: ['Sohbet', 'Arama', 'Gezinti', 'Satın Alma'],
            datasets: [{
                data: [0, 0, 0, 0],
                backgroundColor: [
                    '#8b5cf6',
                    '#10b981',
                    '#3b82f6',
                    '#f59e0b'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#9ca3af',
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(31, 41, 55, 0.9)',
                    titleColor: '#ffffff',
                    bodyColor: '#d1d5db',
                    borderColor: '#4b5563',
                    borderWidth: 1,
                    cornerRadius: 8
                }
            },
            cutout: '60%'
        }
    });
    
    } catch (error) {
        console.error('Error initializing charts:', error);
        // Show error message in chart containers
        document.querySelectorAll('.chart-container').forEach(container => {
            container.innerHTML = '<div class="text-center text-red-400 py-8">Chart başlatılamadı: ' + error.message + '</div>';
        });
    }
}

async function loadAnalyticsData() {
    try {
        activeRequests++;
        const response = await fetch('/api/analytics/real-time', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        const data = await response.json();
        
        if (response.ok) {
            updateDashboard(data);
            updateCharts(data);
            // Note: Live sessions and recent interactions containers are not implemented in the current UI
            // updateLiveSessions(data.live_sessions || []);
            // updateRecentInteractions(data.recent_interactions || []);
        } else {
            console.error('Failed to load analytics data:', data.error);
            // Show user-friendly error message
            showErrorMessage('Analytics verileri yüklenirken hata oluştu: ' + (data.error || 'Bilinmeyen hata'));
        }
    } catch (error) {
        console.error('Error loading analytics data:', error);
        // Show user-friendly error message
        showErrorMessage('Analytics verileri yüklenirken hata oluştu: ' + error.message);
    } finally {
        activeRequests--;
    }
}

// Helper function to show error messages
function showErrorMessage(message) {
    // Try to find a suitable place to show the error message
    const errorContainer = document.querySelector('.analytics-error-container');
    if (errorContainer) {
        errorContainer.innerHTML = `<div class="text-red-400 text-center py-4">${message}</div>`;
    } else {
        // Fallback: show in console only
        console.warn('Error message:', message);
    }
}



function updateDashboard(data) {
    // Update main metrics
    document.getElementById('activeSessions').textContent = data.active_sessions || 0;
    document.getElementById('interactionsLastHour').textContent = data.interactions_last_hour || 0;
    document.getElementById('conversionRate').textContent = (data.conversion_rate || 0).toFixed(1) + '%';
    document.getElementById('avgSessionDuration').textContent = (data.avg_session_duration || 0).toFixed(1) + 'm';
    
    // Update performance metrics
    if (data.performance_metrics) {
        document.getElementById('avgResponseTime').textContent = data.performance_metrics.avg_response_time || 0;
        document.getElementById('errorRate').textContent = data.performance_metrics.success_rate || 0;
        document.getElementById('userSatisfaction').textContent = data.performance_metrics.user_satisfaction || 0;
    }
    
    // Calculate changes
    calculateChanges(data);
}

function calculateChanges(currentData) {
    try {
        if (!previousData.active_sessions) {
            // First load, no changes to show
            previousData = currentData;
            return;
        }
        
        // Calculate percentage changes
        const activeSessionsChange = calculatePercentageChange(previousData.active_sessions, currentData.active_sessions);
        const interactionsChange = calculatePercentageChange(previousData.interactions_last_hour, currentData.interactions_last_hour);
        const conversionChange = calculatePercentageChange(previousData.conversion_rate, currentData.conversion_rate);
        const durationChange = calculatePercentageChange(previousData.avg_session_duration, currentData.avg_session_duration);
        
        // Update change indicators
        updateChangeIndicator('activeSessionsChange', activeSessionsChange, 'Active Sessions');
        updateChangeIndicator('interactionsChange', interactionsChange, 'Interactions');
        updateChangeIndicator('conversionChange', conversionChange, 'Conversion Rate');
        updateChangeIndicator('durationChange', durationChange, 'Session Duration');
        
        previousData = currentData;
    } catch (error) {
        console.error('Error calculating changes:', error);
    }
}

function calculatePercentageChange(oldValue, newValue) {
    if (oldValue === 0) return newValue > 0 ? 100 : 0;
    return ((newValue - oldValue) / oldValue) * 100;
}

function updateChangeIndicator(elementId, change, label) {
    try {
        const element = document.getElementById(elementId);
        if (!element) {
            console.warn(`Element with id '${elementId}' not found`);
            return;
        }
        
        if (change > 0) {
            element.innerHTML = `<span class="text-green-400">+${change.toFixed(1)}%</span> <span class="text-gray-400 ml-2">vs previous</span>`;
        } else if (change < 0) {
            element.innerHTML = `<span class="text-red-400">${change.toFixed(1)}%</span> <span class="text-gray-400 ml-2">vs previous</span>`;
        } else {
            element.innerHTML = `<span class="text-gray-400">No change</span>`;
        }
    } catch (error) {
        console.error(`Error updating change indicator for ${elementId}:`, error);
    }
}

function updateCharts(data) {
    try {
        if (data.hourly_data && realTimeChart) {
            const labels = Object.keys(data.hourly_data);
            const sessionsData = labels.map(key => data.hourly_data[key].sessions || 0);
            const interactionsData = labels.map(key => data.hourly_data[key].interactions || 0);
            
            realTimeChart.data.labels = labels;
            realTimeChart.data.datasets[0].data = sessionsData;
            realTimeChart.data.datasets[1].data = interactionsData;
            realTimeChart.update();
        }
        
        if (data.intent_distribution && engagementChart) {
            const labels = Object.keys(data.intent_distribution);
            const values = Object.values(data.intent_distribution);
            
            engagementChart.data.labels = labels;
            engagementChart.data.datasets[0].data = values;
            engagementChart.update();
        }
    } catch (error) {
        console.error('Error updating charts:', error);
    }
}

function updateLiveSessions(sessions) {
    const container = document.getElementById('liveSessionsContainer');
    
    // Check if container exists before proceeding
    if (!container) {
        console.warn('liveSessionsContainer element not found');
        return;
    }
    
    if (sessions.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-400 py-8">Şu anda aktif oturum yok</div>';
        return;
    }
    
    const sessionsHtml = sessions.map(session => `
        <div class="flex items-center justify-between p-4 bg-gray-800/30 rounded-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-glow/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-glow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-medium">Oturum ${session.session_id}</p>
                    <p class="text-gray-400 text-sm">${session.intent_count} amaç, ${session.interaction_count} etkileşim</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-400">${session.last_activity}</div>
                <div class="text-xs text-green-400">${session.status}</div>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = sessionsHtml;
}

function updateRecentInteractions(interactions) {
    const container = document.getElementById('recentInteractionsContainer');
    
    // Check if container exists before proceeding
    if (!container) {
        console.warn('recentInteractionsContainer element not found');
        return;
    }
    
    if (interactions.length === 0) {
        container.innerHTML = '<div class="text-center text-gray-400 py-8">Son etkileşim yok</div>';
        return;
    }
    
    const interactionsHtml = interactions.map(interaction => `
        <div class="flex items-center justify-between p-4 bg-gray-800/30 rounded-lg">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-green-500/20 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-white font-medium">${interaction.action}</p>
                    <p class="text-gray-400 text-sm">${interaction.product_name || 'N/A'} - ${interaction.timestamp}</p>
                </div>
            </div>
            <div class="text-right">
                <div class="text-sm text-gray-400">Oturum ${interaction.session_id}</div>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = interactionsHtml;
}



// Project değiştirme fonksiyonu
function changeProject(projectId) {
    const url = new URL(window.location);
    if (projectId) {
        url.searchParams.set('project_id', projectId);
    } else {
        url.searchParams.delete('project_id');
    }
    window.location.href = url.toString();
}
</script>

<!-- Funnel Intent İstatistikleri -->
@if(isset($funnelStats))
<div class="glass-effect rounded-2xl p-8 border border-gray-700">
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <h2 class="text-2xl font-bold text-white">🎯 Funnel Intent İstatistikleri</h2>
                <div class="group relative">
                    <svg class="w-5 h-5 text-gray-400 cursor-help" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                        Funnel intent'lerin kullanım istatistikleri ve conversion oranları
                    </div>
                </div>
            </div>
            <p class="text-gray-300">
                @if($projectName)
                    {{ $projectName }} projesi için funnel intent analizi
                @else
                    Tüm projeler için funnel intent analizi
                @endif
            </p>
        </div>
        <div class="text-right">
            <div class="text-3xl font-bold text-purple-glow">{{ $funnelStats['total_funnel_interactions'] }}</div>
            <div class="text-sm text-gray-400">Toplam Funnel Etkileşim</div>
        </div>
    </div>

    <!-- Funnel Intent Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-8">
        @foreach($funnelStats['funnel_intents'] as $intent)
            @php
                $intentDescriptions = [
                    'capabilities_inquiry' => 'Kullanıcıların sistem yeteneklerini öğrenme istekleri',
                    'project_info' => 'Proje bilgisi ve hizmet detayları alma talepleri',
                    'conversion_guidance' => 'Dönüşüm rehberliği ve satın alma yönlendirmesi',
                    'pricing_guidance' => 'Fiyatlandırma rehberliği ve paket bilgileri',
                    'demo_request' => 'Demo talep etme ve ürün tanıtımı istekleri',
                    'contact_request' => 'Müşteri hizmetleri ve iletişim talepleri',
                    'product_recommendations' => 'Ürün önerisi ve tavsiye istekleri'
                ];
                $intentNames = [
                    'capabilities_inquiry' => 'Yetenek Sorgulama',
                    'project_info' => 'Proje Bilgisi',
                    'conversion_guidance' => 'Dönüşüm Rehberliği',
                    'pricing_guidance' => 'Fiyat Rehberliği',
                    'demo_request' => 'Demo Talebi',
                    'contact_request' => 'İletişim Talebi',
                    'product_recommendations' => 'Ürün Önerileri'
                ];
                $intentName = $intentNames[$intent] ?? ucfirst(str_replace('_', ' ', $intent));
                $usage = $funnelStats['usage'][$intent] ?? 0;
                $conversionRate = $funnelStats['conversion_rates'][$intent] ?? 0;
                $todayUsage = $funnelStats['usage_today'][$intent] ?? 0;
            @endphp
            <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700 hover:border-purple-glow/50 transition-all duration-300">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-lg font-semibold text-white group relative">
                        {{ $intentName }}
                        <svg class="w-4 h-4 text-gray-400 cursor-help inline ml-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="absolute bottom-full left-0 mb-2 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                            {{ $intentDescriptions[$intent] ?? 'Funnel intent açıklaması' }}
                        </div>
                    </h3>
                    <span class="text-2xl font-bold text-purple-glow">{{ $usage }}</span>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Bugün:</span>
                        <span class="text-green-400 font-medium">{{ $todayUsage }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">Conversion:</span>
                        <span class="text-blue-400 font-medium">{{ $conversionRate }}%</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Funnel Stage Dağılımı -->
    <div class="mb-8">
        <div class="bg-gray-800/30 rounded-lg p-6">
            <div class="flex items-center space-x-2 mb-4">
                <h3 class="text-lg font-semibold text-white">📊 Funnel Stage Dağılımı</h3>
                <div class="relative group">
                    <svg class="w-4 h-4 text-gray-400 hover:text-white cursor-help transition-colors duration-200" 
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <!-- Tooltip -->
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10 w-64">
                        <div class="font-semibold text-purple-glow mb-1">Funnel Stage Dağılımı</div>
                        <div>Kullanıcıların müşteri kazanma funnel'ındaki aşamalara göre dağılımı. Her aşama farklı bir rengle gösterilir ve yüzdelik oranları ile birlikte analiz edilebilir.</div>
                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                @foreach($funnelStats['stage_distribution'] as $stage => $count)
                    @php
                        $stageLabels = [
                            'awareness' => 'Farkındalık',
                            'interest' => 'İlgi',
                            'consideration' => 'Değerlendirme',
                            'intent' => 'Niyet',
                            'action' => 'Aksiyon'
                        ];
                        $stageColors = [
                            'awareness' => 'bg-blue-500',
                            'interest' => 'bg-green-500',
                            'consideration' => 'bg-yellow-500',
                            'intent' => 'bg-orange-500',
                            'action' => 'bg-red-500'
                        ];
                        $total = array_sum($funnelStats['stage_distribution']);
                        $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                    @endphp
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <div class="flex items-center space-x-2">
                                <span class="text-gray-300">{{ $stageLabels[$stage] ?? ucfirst($stage) }}</span>
                                <div class="relative group">
                                    <svg class="w-4 h-4 text-gray-400 hover:text-white cursor-help transition-colors duration-200" 
                                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <!-- Tooltip -->
                                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none z-10 w-64">
                                        @php
                                            $stageDescriptions = [
                                                'awareness' => 'Kullanıcıların ürünleri keşfetmeye başladığı aşama. "Ürün ara", "Ne var?" gibi arama ve keşif soruları.',
                                                'interest' => 'Kullanıcıların belirli ürünlere ilgi duymaya başladığı aşama. "Fiyat nedir?", "Özellikler neler?" gibi detay soruları.',
                                                'consideration' => 'Kullanıcıların ürünleri değerlendirdiği aşama. "Sepete ekle", "Karşılaştır" gibi karşılaştırma işlemleri.',
                                                'intent' => 'Kullanıcıların satın alma niyeti gösterdiği aşama. "Nasıl satın alırım?", "Ödeme nasıl?" gibi satın alma soruları.',
                                                'action' => 'Kullanıcıların gerçek satın alma işlemini tamamladığı aşama. "Sipariş ver", "Destek al" gibi final işlemler.'
                                            ];
                                        @endphp
                                        <div class="font-semibold text-purple-glow mb-1">{{ $stageLabels[$stage] ?? ucfirst($stage) }}</div>
                                        <div>{{ $stageDescriptions[$stage] ?? 'Bu aşama hakkında bilgi bulunmuyor.' }}</div>
                                        <div class="absolute top-full left-1/2 transform -translate-x-1/2 w-0 h-0 border-l-4 border-r-4 border-t-4 border-transparent border-t-gray-900"></div>
                                    </div>
                                </div>
                            </div>
                            <span class="text-white font-medium">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="{{ $stageColors[$stage] }} h-2 rounded-full transition-all duration-500" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- En Popüler Funnel Intent'ler -->
    <div>
        <div class="flex items-center space-x-3 mb-4">
            <h3 class="text-xl font-semibold text-white">🏆 En Popüler Funnel Intent'ler</h3>
            <div class="group relative">
                <svg class="w-5 h-5 text-gray-400 cursor-help" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                </svg>
                <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                    En çok kullanılan funnel intent'ler
                </div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($funnelStats['top_intents'] as $intent => $count)
                @php
                    $topIntentNames = [
                        'capabilities_inquiry' => 'Yetenek Sorgulama',
                        'project_info' => 'Proje Bilgisi',
                        'conversion_guidance' => 'Dönüşüm Rehberliği',
                        'pricing_guidance' => 'Fiyat Rehberliği',
                        'demo_request' => 'Demo Talebi',
                        'contact_request' => 'İletişim Talebi',
                        'product_recommendations' => 'Ürün Önerileri'
                    ];
                    $topIntentName = $topIntentNames[$intent] ?? ucfirst(str_replace('_', ' ', $intent));
                @endphp
                <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-semibold text-white">{{ $topIntentName }}</h4>
                            <p class="text-sm text-gray-400">{{ $count }} kullanım</p>
                        </div>
                        <div class="text-2xl font-bold text-purple-glow">{{ $count }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- React Widget Takibi -->
@if(isset($widgetTrackingStats))
<div class="glass-effect rounded-2xl p-8 border border-gray-700 mt-8">
    <div class="flex items-center justify-between mb-6">
        <div>
            <div class="flex items-center space-x-3 mb-2">
                <h2 class="text-2xl font-bold text-white">👥 Kullanıcı Etkileşimleri</h2>
                <div class="group relative">
                    <svg class="w-5 h-5 text-gray-400 cursor-help" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-800 text-white text-sm rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none whitespace-nowrap z-10">
                        React widget'tan yapılan ürün link tıklamaları ve etkileşimler
                    </div>
                </div>
            </div>
            <p class="text-gray-300">
                @if($projectName)
                    {{ $projectName }} projesi için widget etkileşim analizi
                @else
                    Tüm projeler için widget etkileşim analizi
                @endif
            </p>
        </div>
        <div class="text-right">
            <div class="text-3xl font-bold text-blue-400">{{ $widgetTrackingStats['total_clicks'] }}</div>
            <div class="text-sm text-gray-400">Toplam Link Tıklaması</div>
        </div>
    </div>

    <!-- Widget Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Bugünkü Tıklamalar -->
        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white">Bugünkü Tıklamalar</h3>
                    <p class="text-2xl font-bold text-green-400 mt-2">{{ $widgetTrackingStats['today_clicks'] }}</p>
                </div>
                <div class="p-3 bg-green-500/20 rounded-full">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Toplam Etkileşim -->
        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white">Toplam Etkileşim</h3>
                    <p class="text-2xl font-bold text-blue-400 mt-2">{{ $widgetTrackingStats['total_interactions'] }}</p>
                </div>
                <div class="p-3 bg-blue-500/20 rounded-full">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Intent Dağılımı -->
        <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-white">Intent Dağılımı</h3>
                    <p class="text-2xl font-bold text-purple-400 mt-2">{{ count($widgetTrackingStats['intent_clicks']) }}</p>
                </div>
                <div class="p-3 bg-purple-500/20 rounded-full">
                    <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- En Çok Tıklanan Ürünler -->
    @if(!empty($widgetTrackingStats['top_products']))
    <div class="mb-8">
        <div class="glass-effect rounded-xl border border-gray-700">
            <div class="p-6 border-b border-gray-700">
                <h3 class="text-lg font-semibold text-white">🏆 En Çok Tıklanan Ürünler</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-800/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Sıra</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Ürün Adı</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Tıklama Sayısı</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">Popülerlik</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wider">İşlemler</th>
                            </tr>
                        </thead>
                        <tbody class="bg-transparent divide-y divide-gray-700">
                            @foreach($widgetTrackingStats['top_products'] as $productName => $clickCount)
                                @php
                                    $maxClicks = max(array_values($widgetTrackingStats['top_products']));
                                    $popularityPercentage = $maxClicks > 0 ? round(($clickCount / $maxClicks) * 100) : 0;
                                @endphp
                                <tr class="hover:bg-gray-800/30 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-orange-500/20 rounded-full flex items-center justify-center">
                                                <span class="text-sm font-medium text-orange-400">{{ $loop->iteration }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-gradient-to-r from-orange-500/20 to-red-500/20 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-white">{{ $productName }}</div>
                                                <div class="text-sm text-gray-400">Ürün Detayı</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-500/20 text-orange-400 border border-orange-500/30">
                                            {{ $clickCount }} tıklama
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="w-full bg-gray-700 rounded-full h-2">
                                            <div class="h-2 rounded-full transition-all duration-300 bg-gradient-to-r from-orange-500 to-red-500" style="width: {{ $popularityPercentage }}%"></div>
                                        </div>
                                        <div class="text-xs text-gray-400 mt-1">{{ $popularityPercentage }}%</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="viewProductDetails('{{ $productName }}')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                                Detay
                                            </button>
                                            <button onclick="analyzeProduct('{{ $productName }}')" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                </svg>
                                                Analiz
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Intent Bazlı Tıklamalar -->
    @if(!empty($widgetTrackingStats['intent_clicks']))
    <div>
        <h3 class="text-xl font-semibold text-white mb-4">🎯 Intent Bazlı Tıklamalar</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($widgetTrackingStats['intent_clicks'] as $intent => $clickCount)
                <div class="bg-gray-800/50 rounded-lg p-4 border border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-lg font-semibold text-white">{{ ucfirst($intent) }}</h4>
                            <p class="text-sm text-gray-400">{{ $clickCount }} tıklama</p>
                        </div>
                        <div class="text-2xl font-bold text-cyan-400">{{ $clickCount }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endif

<script>
// Ürün detaylarını görüntüleme fonksiyonu
function viewProductDetails(productName) {
    // Modal veya detay sayfası açma işlemi
    alert('Ürün detayları: ' + productName);
    // Burada modal açma veya detay sayfasına yönlendirme yapılabilir
}

// Ürün analizi fonksiyonu
function analyzeProduct(productName) {
    // Analiz sayfasına yönlendirme veya modal açma
    alert('Ürün analizi: ' + productName);
    // Burada analiz sayfasına yönlendirme yapılabilir
}
</script>
@endsection
