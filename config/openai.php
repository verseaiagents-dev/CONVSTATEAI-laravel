<?php

return [
    'api_key' => env('OPENAI_API_KEY'),
    'organization' => env('OPENAI_ORGANIZATION'),
    'request_timeout' => env('OPENAI_REQUEST_TIMEOUT', 30),
    
    'models' => [
        'chat' => env('OPENAI_CHAT_MODEL', 'gpt-4o-mini'),
        'embedding' => env('OPENAI_EMBEDDING_MODEL', 'text-embedding-ada-002'),
        'completion' => env('OPENAI_COMPLETION_MODEL', 'text-davinci-003'),
    ],
    
    'defaults' => [
        'temperature' => env('OPENAI_DEFAULT_TEMPERATURE', 0.7),
        'max_tokens' => env('OPENAI_DEFAULT_MAX_TOKENS', 1000),
        'top_p' => env('OPENAI_DEFAULT_TOP_P', 1),
        'frequency_penalty' => env('OPENAI_DEFAULT_FREQUENCY_PENALTY', 0),
        'presence_penalty' => env('OPENAI_DEFAULT_PRESENCE_PENALTY', 0),
    ],
    
    'cache' => [
        'enabled' => env('OPENAI_CACHE_ENABLED', true),
        'ttl' => env('OPENAI_CACHE_TTL', 3600 * 24 * 7), // 7 days
    ],
];
