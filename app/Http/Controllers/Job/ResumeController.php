<?php

namespace App\Http\Controllers\Job;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Models\Resume;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ResumeController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $resumes = Resume::where('user_id', auth()->id())->get();
        return inertia('ResumeUploader', [
            'resumes' => $resumes,
        ]);
    }
    
    /**
     * Show the form for creating a new resource.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,doc,docx|max:2048',
            'title' => 'required|string|max:255',
            'type' => 'required|in:resume,cover_letter',
        ]);

        $path = $request->file('file')->store('resumes', 'public');
        // Store the file in the 'resumes' directory in the 'public' disk

        $resume = Resume::create([
            'user_id' => auth()->id(),
            'title' => $request->input('title'),
            'file_path' => $path,
            'type' => $request->input('type'),
        ]);

        return response()->json($resume, 201);
    }
    public function destroy(Resume $resume)
    {
        $this->authorize('delete', $resume); // optional if you're using policies

        Storage::disk('public')->delete($resume->file_path);
        $resume->delete();

        return response()->json(['message' => 'Deleted']);
    }

    public function download(Resume $resume)
    {
        $this->authorize('download', $resume);
        
        return response()->download(storage_path("app/public/{$resume->file_path}"));
    }
}
