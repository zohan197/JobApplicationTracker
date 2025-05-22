<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobListing;
use App\Models\Note;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class NoteController extends Controller
{
    use AuthorizesRequests;
    public function index(JobListing $jobListing)
    {
        $this->authorize('view', $jobListing);
        return $jobListing->notes()->latest()->get();
    }

    public function store(Request $request, JobListing $jobListing)
    {
        $this->authorize('update', $jobListing);

        $data = $request->validate([
            'content' => 'required|string',
        ]);

        return $jobListing->notes()->create($data);
    }

    public function update(Request $request, Note $note)
    {
        $this->authorize('update', $note->jobListing);

        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $note->update($data);
        return $note;
    }

    public function destroy(Note $note)
    {
        $this->authorize('delete', $note->jobListing);
        $note->delete();

        return response()->noContent();
    }
}
