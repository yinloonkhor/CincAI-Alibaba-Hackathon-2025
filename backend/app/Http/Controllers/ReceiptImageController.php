<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\ReceiptImage;
use App\Services\ReceiptAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ReceiptImageController extends Controller
{
    /**
     * The receipt AI service instance.
     *
     * @var \App\Services\ReceiptAiService
     */
    protected $receiptAiService;

    /**
     * Create a new controller instance.
     *
     * @param \App\Services\ReceiptAiService $receiptAiService
     * @return void
     */
    public function __construct(ReceiptAiService $receiptAiService)
    {
        $this->receiptAiService = $receiptAiService;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Receipt $receipt)
    {
        $this->authorize('update', $receipt);
        
        $request->validate([
            'image' => 'required|image|max:10240',
        ]);

        $path = $request->file('image')->store('receipts/' . Auth::id(), 'public');
        
        $image = $receipt->images()->create([
            'image_path' => $path,
            'original_filename' => $request->file('image')->getClientOriginalName(),
            'mime_type' => $request->file('image')->getMimeType(),
            'file_size' => $request->file('image')->getSize(),
        ]);

        // If this is an AJAX request, return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'image' => $image,
                'url' => Storage::url($path),
            ]);
        }

        return redirect()->route('receipts.show', $receipt)
            ->with('success', 'Receipt image uploaded successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ReceiptImage $image)
    {
        $this->authorize('view', $image->receipt);
        
        return response()->file(Storage::disk('public')->path($image->image_path));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ReceiptImage $image)
    {
        $this->authorize('delete', $image->receipt);
        
        $receipt = $image->receipt;
        
        // Delete the file from storage
        Storage::disk('public')->delete($image->image_path);
        
        // Delete the database record
        $image->delete();

        return redirect()->route('receipts.show', $receipt)
            ->with('success', 'Receipt image deleted successfully.');
    }

   /**
 * Process an uploaded image with AI for text extraction.
 */
/**
 * Process an uploaded image with AI for text extraction.
 */
public function processWithAi(Request $request)
{
    // Validate the request
    $request->validate([
        'image' => 'required|file|mimes:jpeg,png,jpg,gif,pdf|max:10240',
    ]);
    
    try {
        // Get the uploaded file
        $file = $request->file('image');
        
        // Create a temporary object with the image data
        $tempImage = new \stdClass();
        $tempImage->original_filename = $file->getClientOriginalName();
        $tempImage->mime_type = $file->getMimeType();
        $tempImage->file_size = $file->getSize();
        $tempImage->image_data = base64_encode(file_get_contents($file->path()));
        
        // Process the image
        $result = $this->receiptAiService->processUploadedImage($tempImage);
        
        return response()->json([
            'success' => true,
            'data' => $result,
            'filename' => $file->getClientOriginalName()
        ]);
    } catch (\Exception $e) {
        \Log::error('Failed to process receipt image with AI', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to process receipt image with AI',
            'error' => $e->getMessage()
        ], 500);
    }
}


   
}
