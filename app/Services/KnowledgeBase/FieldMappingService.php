<?php

namespace App\Services\KnowledgeBase;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\FieldMapping;
use App\Models\KnowledgeBase;

class FieldMappingService
{
    /**
     * Detect fields from uploaded file
     */
    public function detectFields(string $filePath, string $fileType): array
    {
        try {
            $detectedFields = [];
            
            switch ($fileType) {
                case 'csv':
                    $detectedFields = $this->detectCsvFields($filePath);
                    break;
                case 'xlsx':
                case 'xls':
                    $detectedFields = $this->detectExcelFields($filePath);
                    break;
                case 'json':
                    $detectedFields = $this->detectJsonFields($filePath);
                    break;
                case 'xml':
                    $detectedFields = $this->detectXmlFields($filePath);
                    break;
                default:
                    throw new \Exception("Unsupported file type: {$fileType}");
            }
            
            // Detect field types
            $detectedFields = $this->detectFieldTypes($filePath, $fileType, $detectedFields);
            
            return $detectedFields;
            
        } catch (\Exception $e) {
            Log::error('Field detection error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Detect CSV fields
     */
    private function detectCsvFields(string $filePath): array
    {
        $fields = [];
        
        // Try different storage paths
        $possiblePaths = [
            Storage::path($filePath),
            storage_path('app/public/' . $filePath),
            storage_path('app/private/' . $filePath),
            public_path('storage/' . $filePath)
        ];
        
        $handle = false;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $handle = fopen($path, 'r');
                break;
            }
        }
        
        if ($handle !== false) {
            $headers = fgetcsv($handle);
            if ($headers) {
                $fields = array_map('trim', $headers);
            }
            fclose($handle);
        }
        
        return $fields;
    }
    
    /**
     * Detect Excel fields
     */
    private function detectExcelFields(string $filePath): array
    {
        $fields = [];
        
        // Try different storage paths
        $possiblePaths = [
            Storage::path($filePath),
            storage_path('app/public/' . $filePath),
            storage_path('app/private/' . $filePath),
            public_path('storage/' . $filePath)
        ];
        
        $actualPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $actualPath = $path;
                break;
            }
        }
        
        if ($actualPath) {
            try {
                $data = Excel::toArray([], $actualPath);
                if (!empty($data[0]) && !empty($data[0][0])) {
                    $fields = array_map('trim', $data[0][0]);
                }
            } catch (\Exception $e) {
                Log::error('Excel field detection error: ' . $e->getMessage());
            }
        }
        
        return $fields;
    }
    
    /**
     * Detect JSON fields
     */
    private function detectJsonFields(string $filePath): array
    {
        $fields = [];
        
        // Try different storage paths
        $possiblePaths = [
            Storage::path($filePath),
            storage_path('app/public/' . $filePath),
            storage_path('app/private/' . $filePath),
            public_path('storage/' . $filePath)
        ];
        
        $actualPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $actualPath = $path;
                break;
            }
        }
        
        if ($actualPath) {
            try {
                $content = file_get_contents($actualPath);
                $data = json_decode($content, true);
                
                if (is_array($data) && !empty($data)) {
                    $firstItem = is_array($data[0]) ? $data[0] : $data;
                    $fields = array_keys($firstItem);
                }
            } catch (\Exception $e) {
                Log::error('JSON field detection error: ' . $e->getMessage());
            }
        }
        
        return $fields;
    }
    
    /**
     * Detect XML fields
     */
    private function detectXmlFields(string $filePath): array
    {
        $fields = [];
        
        // Try different storage paths
        $possiblePaths = [
            Storage::path($filePath),
            storage_path('app/public/' . $filePath),
            storage_path('app/private/' . $filePath),
            public_path('storage/' . $filePath)
        ];
        
        $actualPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $actualPath = $path;
                break;
            }
        }
        
        if ($actualPath) {
            try {
                $xml = simplexml_load_file($actualPath);
                if ($xml) {
                    $firstItem = $xml->children()->first();
                    if ($firstItem) {
                        $fields = array_keys((array) $firstItem);
                    }
                }
            } catch (\Exception $e) {
                Log::error('XML field detection error: ' . $e->getMessage());
            }
        }
        
        return $fields;
    }
    
    /**
     * Detect field types based on sample data
     */
    private function detectFieldTypes(string $filePath, string $fileType, array $fields): array
    {
        $fieldTypes = [];
        
        try {
            $sampleData = $this->getSampleData($filePath, $fileType, 5);
            
            foreach ($fields as $field) {
                $fieldTypes[$field] = $this->determineFieldType($field, $sampleData);
            }
        } catch (\Exception $e) {
            Log::error('Field type detection error: ' . $e->getMessage());
            // Default to text if type detection fails
            foreach ($fields as $field) {
                $fieldTypes[$field] = 'text';
            }
        }
        
        return $fieldTypes;
    }
    
    /**
     * Get sample data from file
     */
    public function getSampleData(string $filePath, string $fileType, int $rows = 5): array
    {
        $sampleData = [];
        
        // Try different storage paths
        $possiblePaths = [
            Storage::path($filePath),
            storage_path('app/public/' . $filePath),
            storage_path('app/private/' . $filePath),
            public_path('storage/' . $filePath)
        ];
        
        $actualPath = null;
        foreach ($possiblePaths as $path) {
            if (file_exists($path)) {
                $actualPath = $path;
                break;
            }
        }
        
        if (!$actualPath) {
            return $sampleData;
        }
        
        switch ($fileType) {
            case 'csv':
                $handle = fopen($actualPath, 'r');
                if ($handle !== false) {
                    $headers = fgetcsv($handle);
                    $rowCount = 0;
                    while (($row = fgetcsv($handle)) !== false && $rowCount < $rows) {
                        $sampleData[] = array_combine($headers, $row);
                        $rowCount++;
                    }
                    fclose($handle);
                }
                break;
                
            case 'xlsx':
            case 'xls':
                try {
                    $data = Excel::toArray([], $actualPath);
                    if (!empty($data[0])) {
                        $headers = $data[0][0];
                        for ($i = 1; $i < min(count($data[0]), $rows + 1); $i++) {
                            $sampleData[] = array_combine($headers, $data[0][i]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Excel sample data error: ' . $e->getMessage());
                }
                break;
        }
        
        return $sampleData;
    }
    
    /**
     * Determine field type based on sample data
     */
    private function determineFieldType(string $field, array $sampleData): string
    {
        if (empty($sampleData)) {
            return 'text';
        }
        
        $values = array_column($sampleData, $field);
        $values = array_filter($values, function($value) {
            return $value !== null && $value !== '';
        });
        
        if (empty($values)) {
            return 'text';
        }
        
        // Check if all values are numeric
        $allNumeric = true;
        $allDates = true;
        $allBooleans = true;
        
        foreach ($values as $value) {
            if (!is_numeric($value)) {
                $allNumeric = false;
            }
            
            if (!$this->isValidDate($value)) {
                $allDates = false;
            }
            
            if (!$this->isBoolean($value)) {
                $allBooleans = false;
            }
        }
        
        if ($allBooleans) {
            return 'boolean';
        } elseif ($allDates) {
            return 'date';
        } elseif ($allNumeric) {
            return 'number';
        } else {
            return 'text';
        }
    }
    
    /**
     * Check if value is a valid date
     */
    private function isValidDate($value): bool
    {
        if (empty($value)) {
            return false;
        }
        
        $dateFormats = [
            'Y-m-d', 'd/m/Y', 'm/d/Y', 'Y/m/d',
            'd-m-Y', 'm-d-Y', 'Y-m-d H:i:s',
            'd/m/Y H:i', 'm/d/Y H:i'
        ];
        
        foreach ($dateFormats as $format) {
            if (\DateTime::createFromFormat($format, $value) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Check if value is boolean
     */
    private function isBoolean($value): bool
    {
        $booleanValues = ['true', 'false', '1', '0', 'yes', 'no', 'on', 'off'];
        return in_array(strtolower($value), $booleanValues);
    }
    
    /**
     * Suggest field mappings based on detected fields
     */
    public function suggestMappings(array $detectedFields): array
    {
        $suggestions = [];
        
        foreach ($detectedFields as $sourceField => $fieldType) {
            $bestMatch = $this->findBestMatch($sourceField);
            if ($bestMatch) {
                $suggestions[$sourceField] = [
                    'target_field' => $bestMatch,
                    'confidence' => $this->calculateConfidence($sourceField, $bestMatch),
                    'field_type' => $fieldType
                ];
            }
        }
        
        return $suggestions;
    }
    
    /**
     * Find best matching target field for source field
     */
    private function findBestMatch(string $sourceField): ?string
    {
        $sourceFieldLower = strtolower($sourceField);
        $bestMatch = null;
        $bestScore = 0;
        
        foreach (FieldMapping::STANDARD_FIELDS as $targetField => $suggestions) {
            foreach ($suggestions as $suggestion) {
                $score = $this->calculateSimilarity($sourceFieldLower, strtolower($suggestion));
                if ($score > $bestScore && $score > 0.6) {
                    $bestScore = $score;
                    $bestMatch = $targetField;
                }
            }
        }
        
        return $bestMatch;
    }
    
    /**
     * Calculate similarity between two strings
     */
    private function calculateSimilarity(string $str1, string $str2): float
    {
        $levenshtein = levenshtein($str1, $str2);
        $maxLength = max(strlen($str1), strlen($str2));
        
        if ($maxLength === 0) {
            return 1.0;
        }
        
        return 1 - ($levenshtein / $maxLength);
    }
    
    /**
     * Calculate confidence score for mapping suggestion
     */
    private function calculateConfidence(string $sourceField, string $targetField): float
    {
        $sourceFieldLower = strtolower($sourceField);
        $suggestions = FieldMapping::STANDARD_FIELDS[$targetField] ?? [];
        
        $maxScore = 0;
        foreach ($suggestions as $suggestion) {
            $score = $this->calculateSimilarity($sourceFieldLower, strtolower($suggestion));
            $maxScore = max($maxScore, $score);
        }
        
        return $maxScore;
    }
    
    /**
     * Create field mappings for knowledge base
     */
    public function createMappings(int $knowledgeBaseId, array $mappings): bool
    {
        try {
            // Delete existing mappings
            FieldMapping::where('knowledge_base_id', $knowledgeBaseId)->delete();
            
            // Create new mappings
            foreach ($mappings as $index => $mapping) {
                FieldMapping::create([
                    'knowledge_base_id' => $knowledgeBaseId,
                    'source_field' => $mapping['source_field'],
                    'target_field' => $mapping['target_field'],
                    'field_type' => $mapping['field_type'] ?? 'text',
                    'is_required' => $mapping['is_required'] ?? false,
                    'default_value' => $mapping['default_value'] ?? null,
                    'transformation' => $mapping['transformation'] ?? null,
                    'validation_rules' => $mapping['validation_rules'] ?? null,
                    'mapping_order' => $index
                ]);
            }
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Create mappings error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Transform data using field mappings
     */
    public function transformData(array $rawData, array $mappings): array
    {
        $transformedData = [];
        
        foreach ($rawData as $row) {
            $transformedRow = [];
            
            foreach ($mappings as $mapping) {
                $sourceField = $mapping['source_field'];
                $targetField = $mapping['target_field'];
                $fieldType = $mapping['field_type'] ?? 'text';
                $transformation = $mapping['transformation'] ?? null;
                
                $value = $row[$sourceField] ?? null;
                
                // Apply transformations
                if ($transformation && $value !== null) {
                    $value = $this->applyTransformation($value, $transformation);
                }
                
                // Type casting
                $value = $this->castValue($value, $fieldType);
                
                $transformedRow[$targetField] = $value;
            }
            
            $transformedData[] = $transformedRow;
        }
        
        return $transformedData;
    }
    
    /**
     * Apply transformation rules to value
     */
    private function applyTransformation($value, array $transformation)
    {
        // Currency conversion
        if (isset($transformation['currency_conversion'])) {
            $from = $transformation['currency_conversion']['from'] ?? 'USD';
            $to = $transformation['currency_conversion']['to'] ?? 'TRY';
            $rate = $transformation['currency_conversion']['rate'] ?? 1;
            
            if (is_numeric($value)) {
                $value = $value * $rate;
            }
        }
        
        // Date format conversion
        if (isset($transformation['date_format'])) {
            $from = $transformation['date_format']['from'] ?? 'Y-m-d';
            $to = $transformation['date_format']['to'] ?? 'd/m/Y';
            
            if ($this->isValidDate($value)) {
                $date = \DateTime::createFromFormat($from, $value);
                if ($date) {
                    $value = $date->format($to);
                }
            }
        }
        
        // Text processing
        if (isset($transformation['text_processing'])) {
            $textProcessing = $transformation['text_processing'];
            
            if ($textProcessing['uppercase'] ?? false) {
                $value = strtoupper($value);
            }
            
            if ($textProcessing['trim'] ?? false) {
                $value = trim($value);
            }
            
            if ($textProcessing['remove_special_chars'] ?? false) {
                $value = preg_replace('/[^a-zA-Z0-9\s]/', '', $value);
            }
        }
        
        return $value;
    }
    
    /**
     * Cast value to specified type
     */
    private function castValue($value, string $fieldType)
    {
        if ($value === null || $value === '') {
            return null;
        }
        
        switch ($fieldType) {
            case 'number':
                return is_numeric($value) ? (float) $value : null;
            case 'boolean':
                return $this->isBoolean($value) ? (bool) $value : null;
            case 'date':
                return $this->isValidDate($value) ? $value : null;
            case 'array':
                return is_array($value) ? $value : explode(',', $value);
            default:
                return (string) $value;
        }
    }
    
    /**
     * Validate field mappings
     */
    public function validateMappings(array $mappings): array
    {
        $errors = [];
        
        foreach ($mappings as $index => $mapping) {
            // Required fields
            if (empty($mapping['source_field'])) {
                $errors[] = "Row {$index}: Source field is required";
            }
            
            if (empty($mapping['target_field'])) {
                $errors[] = "Row {$index}: Target field is required";
            }
            
            // Field type validation
            if (isset($mapping['field_type']) && !FieldMapping::isValidFieldType($mapping['field_type'])) {
                $errors[] = "Row {$index}: Invalid field type '{$mapping['field_type']}'";
            }
            
            // Target field validation
            if (isset($mapping['target_field']) && !FieldMapping::isStandardField($mapping['target_field'])) {
                $errors[] = "Row {$index}: Invalid target field '{$mapping['target_field']}'";
            }
        }
        
        return $errors;
    }

    /**
     * Validate data against validation rules
     */
    public function validateData($value, array $validationRules): array
    {
        $errors = [];

        if (empty($validationRules)) {
            return $errors;
        }

        // Required field validation
        if (isset($validationRules['required']) && $validationRules['required']) {
            if (empty($value) && $value !== '0') {
                $errors[] = 'This field is required';
            }
        }

        // Text validation
        if (isset($validationRules['min_length']) && strlen($value) < $validationRules['min_length']) {
            $errors[] = "Minimum length is {$validationRules['min_length']} characters";
        }

        if (isset($validationRules['max_length']) && strlen($value) > $validationRules['max_length']) {
            $errors[] = "Maximum length is {$validationRules['max_length']} characters";
        }

        if (isset($validationRules['email']) && $validationRules['email']) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            }
        }

        if (isset($validationRules['url']) && $validationRules['url']) {
            if (!filter_var($value, FILTER_VALIDATE_URL)) {
                $errors[] = 'Invalid URL format';
            }
        }

        // Number validation
        if (isset($validationRules['min_value']) && is_numeric($value)) {
            if ((float) $value < $validationRules['min_value']) {
                $errors[] = "Minimum value is {$validationRules['min_value']}";
            }
        }

        if (isset($validationRules['max_value']) && is_numeric($value)) {
            if ((float) $value > $validationRules['max_value']) {
                $errors[] = "Maximum value is {$validationRules['max_value']}";
            }
        }

        if (isset($validationRules['integer']) && $validationRules['integer']) {
            if (!is_numeric($value) || (float) $value != (int) $value) {
                $errors[] = 'Value must be an integer';
            }
        }

        // Date validation
        if (isset($validationRules['min_date']) && !empty($validationRules['min_date'])) {
            try {
                $inputDate = new \DateTime($value);
                $minDate = new \DateTime($validationRules['min_date']);
                if ($inputDate < $minDate) {
                    $errors[] = "Date must be after {$validationRules['min_date']}";
                }
            } catch (\Exception $e) {
                $errors[] = 'Invalid date format';
            }
        }

        if (isset($validationRules['max_date']) && !empty($validationRules['max_date'])) {
            try {
                $inputDate = new \DateTime($value);
                $maxDate = new \DateTime($validationRules['max_date']);
                if ($inputDate > $maxDate) {
                    $errors[] = "Date must be before {$validationRules['max_date']}";
                }
            } catch (\Exception $e) {
                $errors[] = 'Invalid date format';
            }
        }

        if (isset($validationRules['future_only']) && $validationRules['future_only']) {
            try {
                $inputDate = new \DateTime($value);
                $now = new \DateTime();
                if ($inputDate <= $now) {
                    $errors[] = 'Date must be in the future';
                }
            } catch (\Exception $e) {
                $errors[] = 'Invalid date format';
            }
        }

        return $errors;
    }

    /**
     * Process data in batches
     */
    public function processBatchData(array $data, array $mappings, int $chunkSize = 100): array
    {
        $results = [
            'processed' => 0,
            'errors' => 0,
            'chunks' => 0,
            'data' => []
        ];

        $chunks = array_chunk($data, $chunkSize);
        $results['chunks'] = count($chunks);

        foreach ($chunks as $chunkIndex => $chunk) {
            try {
                $processedChunk = $this->transformData($chunk, $mappings);
                $results['data'] = array_merge($results['data'], $processedChunk);
                $results['processed'] += count($chunk);
            } catch (\Exception $e) {
                $results['errors'] += count($chunk);
                Log::error("Batch processing error in chunk {$chunkIndex}: " . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Get processing statistics
     */
    public function getProcessingStats(array $data, array $mappings): array
    {
        $stats = [
            'total_rows' => count($data),
            'total_fields' => count($mappings),
            'required_fields' => count(array_filter($mappings, fn($m) => $m['is_required'] ?? false)),
            'transformation_rules' => count(array_filter($mappings, fn($m) => !empty($m['transformation'] ?? []))),
            'validation_rules' => count(array_filter($mappings, fn($m) => !empty($m['validation_rules'] ?? [])))
        ];

        return $stats;
    }
}
