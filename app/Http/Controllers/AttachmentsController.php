<?php

namespace App\Http\Controllers;

use App\Http\Requests\Attachments\StoreAttachmentsRequest;
use App\Http\Requests\Attachments\UpdateAttachmentsRequest;
use App\Models\Attachment;
use App\Services\AttachmentsService;
use Illuminate\Support\Facades\Storage;

class AttachmentsController extends Controller
{
    protected $attachmentsService;

    public function __construct(AttachmentsService $attachmentsService)
    {
        $this->attachmentsService = $attachmentsService;
    }

    public function index()
    {
        $attachments = Attachment::with('category')->latest()->paginate(15);
        return response()->json($attachments);
    }

    public function store(StoreAttachmentsRequest $request)
    {
        $data = $this->attachmentsService->handleStore($request->validated());
        return response()->json(['message' => 'Uploaded successfully', 'data' => $data], 201);
    }

    public function update(UpdateAttachmentsRequest $request, Attachment $attachment)
    {
        $updated = $this->attachmentsService->handleUpdate($attachment, $request->validated());
        return response()->json(['message' => 'Updated successfully', 'data' => $updated]);
    }

    public function destroy(Attachment $attachment)
    {
        if ($attachment->path) {
            Storage::disk('public')->delete($attachment->path);
        }
        $attachment->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
