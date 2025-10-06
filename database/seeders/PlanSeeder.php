<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'price' => 0,
                'yearly_price' => 0,
                'billing_cycle' => 'monthly',
                'features' => [
                    'max_projects' => 1,
                    'max_knowledge_bases' => 2,
                    'max_chat_sessions' => 50,
                    'max_products' => 100,
                    'ai_responses_per_month' => 50,
                    'widget_customization' => false,
                    'api_access' => false,
                    'priority_support' => false,
                    'analytics' => 'basic'
                ],
                'is_active' => true,
                'trial_days' => 7,
                'usage_tokens' => 50,
                'token_reset_period' => 'monthly'
            ],
            [
                'name' => 'Starter',
                'price' => 1000,
                'yearly_price' => 10000,
                'billing_cycle' => 'monthly',
                'features' => [
                    'max_projects' => 3,
                    'max_knowledge_bases' => 5,
                    'max_chat_sessions' => 100,
                    'max_products' => 100,
                    'ai_responses_per_month' => 1000,
                    'widget_customization' => true,
                    'api_access' => true,
                    'priority_support' => false,
                    'analytics' => 'standard'
                ],
                'is_active' => true,
                'trial_days' => 0,
                'usage_tokens' => 1000,
                'token_reset_period' => 'monthly'
            ],
            [
                'name' => 'Professional',
                'price' => 1500,
                'yearly_price' => 15000,
                'billing_cycle' => 'monthly',
                'features' => [
                    'max_projects' => 10,
                    'max_knowledge_bases' => 20,
                    'max_chat_sessions' => 150,
                    'max_products' => 150,
                    'ai_responses_per_month' => 1500,
                    'widget_customization' => true,
                    'api_access' => true,
                    'priority_support' => true,
                    'analytics' => 'advanced'
                ],
                'is_active' => true,
                'trial_days' => 0,
                'usage_tokens' => 1500,
                'token_reset_period' => 'monthly'
            ],
            [
                'name' => 'Enterprise',
                'price' => 2500,
                'yearly_price' => 20000,
                'billing_cycle' => 'monthly',
                'features' => [
                    'max_projects' => 5, // Unlimited
                    'max_knowledge_bases' => 50, // Unlimited
                    'max_chat_sessions' => 250, // Unlimited
                    'max_products' => 250, // Unlimited
                    'ai_responses_per_month' => 2500, // Unlimited
                    'widget_customization' => true,
                    'api_access' => true,
                    'priority_support' => true,
                    'analytics' => 'enterprise',
                    'dedicated_support' => true,
                    'custom_integrations' => true
                ],
                'is_active' => true,
                'trial_days' => 0,
                'usage_tokens' => 2500, 
                'token_reset_period' => 'monthly'
            ]
        ];

        foreach ($plans as $planData) {
            Plan::create($planData);
        }

        $this->command->info('Plans created successfully!');
        $this->command->info('Created: Free, Starter, Professional, Enterprise');
    }
}