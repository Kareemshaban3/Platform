<?php

namespace App\Services;

use App\Models\Attachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AttachmentsService
{
    public function handleStore(array $data)
    {
        return DB::transaction(function () use ($data) {
            $results = [];

            if (isset($data['main_image'])) {
                $results[] = $this->createAttachment($data, $data['main_image'], true);
            }

            return $results;
        });
    }

    public function handleUpdate(Attachment $attachment, array $data)
    {
        return DB::transaction(function () use ($attachment, $data) {
            if (isset($data['main_image'])) {
                if ($attachment->path) {
                    Storage::disk('public')->delete($attachment->path);
                }

                $fileInfo = $this->uploadToDisk($data['main_image']);
                $data['path'] = $fileInfo['path'];
                $data['file_type'] = $fileInfo['type'];
            }

            $attachment->update($data);
            return $attachment->load(['category']);
        });
    }

    private function createAttachment($data, $file, $isMain)
    {
        $fileInfo = $this->uploadToDisk($file);

        return Attachment::create([
            'categories_id' => $data['categories_id'],
            'name'          => $data['name'] ?? $file->getClientOriginalName(),
            'description'   => $data['description'] ?? null,
            'path'          => $fileInfo['path'],
            'file_type'     => $fileInfo['type'],
            'is_main'       => $isMain,
        ]);
    }

    private function uploadToDisk($file)
    {
        $mime = $file->getMimeType();
        $fileType = 'image';

        if (str_contains($mime, 'video')) {
            $fileType = 'video';
        } elseif (str_contains($mime, 'pdf')) {
            $fileType = 'pdf';
        }

        $folder = "attachments/{$fileType}s";
        return [
            'path' => $file->store($folder, 'public'),
            'type' => $fileType
        ];
    }
}
