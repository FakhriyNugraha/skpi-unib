<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\SkpiData;
use Illuminate\Support\Facades\Storage;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class DocumentController extends Controller
{
    use AuthorizesRequests;
    public function upload(Request $request, SkpiData $skpi)
    {
        $this->authorize('update', $skpi);
        
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'document_type' => 'required|string|in:ijazah,transkrip,sertifikat,pendukung',
        ]);

        $file = $request->file('document');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents/' . $skpi->user_id, $fileName, 'private');

        Document::create([
            'skpi_data_id' => $skpi->id,
            'document_type' => $request->document_type,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()->back()->with('success', 'Dokumen berhasil diupload.');
    }

    public function download(Document $document)
    {
        $skpi = $document->skpiData;
        $this->authorize('view', $skpi);

        if (!Storage::disk('private')->exists($document->file_path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $filePath = Storage::disk('private')->path($document->file_path);
        return response()->download($filePath, $document->file_name);
    }

    public function delete(Document $document)
    {
        $skpi = $document->skpiData;
        $this->authorize('update', $skpi);

        if (!$skpi->canBeEdited()) {
            return redirect()->back()->with('error', 'Dokumen tidak dapat dihapus.');
        }

        Storage::disk('private')->delete($document->file_path);
        $document->delete();

        return redirect()->back()->with('success', 'Dokumen berhasil dihapus.');
    }
}