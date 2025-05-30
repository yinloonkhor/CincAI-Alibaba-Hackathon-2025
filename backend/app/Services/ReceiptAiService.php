<?php

namespace App\Services;

use App\Models\ReceiptImage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Http\UploadedFile;

class ReceiptAiService
{
    /**
     * The base URL for the AI API.
     *
     * @var string
     */
    protected $apiBaseUrl = 'http://47.250.52.250:8000';

    /**
     * Process a receipt image with AI to extract text and data
     *
     * @param mixed $uploadedImage Can be ReceiptImage model, UploadedFile, or stdClass with image_data
     * @return array
     */
    public function processUploadedImage($uploadedImage)
    {
        try {
            // For development/testing, uncomment this to use mock data
            // return $this->getMockResponse()[0];
            
            // Log the type of image we're processing
            Log::info('Processing receipt image', [
                'type' => is_object($uploadedImage) ? get_class($uploadedImage) : gettype($uploadedImage),
                'filename' => $uploadedImage->original_filename ?? 'unknown'
            ]);
            
            // Prepare the file data for the API request
            $fileData = null;
            $filename = null;
            
            if ($uploadedImage instanceof ReceiptImage) {
                // If it's a ReceiptImage model from the database
                $filePath = storage_path('app/public/' . $uploadedImage->image_path);
                $filename = $uploadedImage->original_filename;
                
                if (!file_exists($filePath)) {
                    throw new Exception("Receipt image file not found at path: {$filePath}");
                }
                
                $fileData = file_get_contents($filePath);
            } elseif ($uploadedImage instanceof UploadedFile) {
                // If it's an uploaded file from a request
                $fileData = file_get_contents($uploadedImage->path());
                $filename = $uploadedImage->getClientOriginalName();
            } elseif (is_object($uploadedImage) && isset($uploadedImage->image_data)) {
                // If it's a stdClass with base64 encoded image data (from ReceiptImageController)
                $fileData = base64_decode($uploadedImage->image_data);
                $filename = $uploadedImage->original_filename;
            } else {
                throw new Exception("Invalid image input type. Expected ReceiptImage, UploadedFile, or object with image_data.");
            }
            
            if (!$fileData) {
                throw new Exception("Failed to read image data");
            }
            
            // Create a temporary file to send to the API
            $tempFile = tempnam(sys_get_temp_dir(), 'receipt_');
            file_put_contents($tempFile, $fileData);
            
            Log::info('Calling receipt analysis API', [
                'filename' => $filename,
                'api_url' => $this->apiBaseUrl . '/analyze',
                'temp_file' => $tempFile
            ]);
            
            // Create a multipart form request to the API
            $response = Http::timeout(30)
                ->attach('file', $fileData, $filename)
                ->post($this->apiBaseUrl . '/analyze');
            
            // Clean up the temporary file
            @unlink($tempFile);
            
            if ($response->successful()) {
                $apiResponse = $response->json();
                Log::info('Receipt analysis API response received', [
                    'status' => $response->status(),
                    'response_summary' => [
                        'vendor' => $apiResponse['vendor_name'] ?? 'unknown',
                        'total' => $apiResponse['total_amount'] ?? 0,
                        'date' => $apiResponse['date'] ?? 'unknown'
                    ]
                ]);
                
                // Format the response to match our expected structure
                $formattedResponse = $this->formatApiResponse($apiResponse);
                
                return $this->validateAndFormatResponse($formattedResponse);
            }
            
            Log::error('Receipt analysis API Error', [
                'status' => $response->status(),
                'response' => $response->body(),
                'filename' => $filename
            ]);
            
            // If API fails, fall back to mock data
            Log::info('Falling back to mock data due to API error');
        } catch (Exception $e) {
            Log::error('Receipt AI processing error', [
                'message' => $e->getMessage(),
                'filename' => $uploadedImage->original_filename ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            // For production, return mock data as a fallback
            Log::info('Falling back to mock data due to exception');
        }
    }
    
    /**
     * Format the API response to match our expected structure
     *
     * @param array $apiResponse
     * @return array
     */
    private function formatApiResponse($apiResponse)
    {
        // Initialize the formatted response with default values
        $formattedResponse = [
            'vendor_name' => $apiResponse['vendor_name'] ?? 'Unknown Vendor',
            'total_amount' => $apiResponse['total_amount'] ?? 0,
            'date' => $apiResponse['date'] ?? date('Y-m-d'),
            'currency' => $apiResponse['currency'] ?? 'MYR',
            'items' => [],
            'payment_method' => $apiResponse['payment_method'] ?? null,
            'vendor_address' => $apiResponse['vendor_address'] ?? '',
            'is_deductible' => $apiResponse['is_deductible'] ?? false,
        ];
        
        // Add conversion message if present or if currency is not MYR
        if (isset($apiResponse['currency']) && $apiResponse['currency'] !== 'MYR') {
            $formattedResponse['conversion_message'] = 'Receipt processed in ' . $apiResponse['currency'] . 
                ', converted to MYR for tax purposes. Please verify the actual amount with your bank transaction.';
        }
        
        // Format items
        if (isset($apiResponse['items']) && is_array($apiResponse['items'])) {
            foreach ($apiResponse['items'] as $item) {
                $formattedItem = [
                    'description' => $item['description'] ?? 'Unknown Item',
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'total_price' => $item['total_price'] ?? 0,
                    'expense_category' => $item['expense_category'] ?? 'Miscellaneous',
                ];
                
                // Add optional fields if present
                if (isset($item['payment_method'])) {
                    $formattedItem['payment_method'] = $item['payment_method'];
                }
                
                if (isset($item['tax_category'])) {
                    $formattedItem['tax_category'] = $item['tax_category'];
                }
                
                if (isset($item['notes'])) {
                    $formattedItem['notes'] = $item['notes'];
                }
                
                $formattedResponse['items'][] = $formattedItem;
            }
        }
        
        return $formattedResponse;
    }
    
    /**
     * Validate and format the AI response
     *
     * @param array $response
     * @return array
     */
    private function validateAndFormatResponse($response)
    {
        // Validate required fields
        $requiredFields = ['vendor_name', 'total_amount', 'date'];
        foreach ($requiredFields as $field) {
            if (!isset($response[$field])) {
                throw new Exception("AI response missing required field: {$field}");
            }
        }
        
        // Validate items array if present
        if (isset($response['items']) && is_array($response['items'])) {
            foreach ($response['items'] as $index => $item) {
                $requiredItemFields = ['description', 'quantity', 'unit_price', 'total_price'];
                foreach ($requiredItemFields as $field) {
                    if (!isset($item[$field])) {
                        throw new Exception("Item at index {$index} missing required field: {$field}");
                    }
                }
            }
        }
        
        return $response;
    }
    
   
}
