<?php

namespace App\Http\Controllers;

use App\Models\PdfDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PdfDocumentController extends Controller
{
    public function index()
    {
        // Traemos los documentos ordenados por el más reciente
        $documents = PdfDocument::latest()->get();
        return view('schedule.history', compact('documents'));
    }

    public function destroy(PdfDocument $document)
    {
        // Eliminamos el archivo físico del storage
        if (Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Eliminamos el registro de la BD
        $document->delete();

        return back()->with('success', 'PDF eliminado del historial exitosamente.');
    }
}