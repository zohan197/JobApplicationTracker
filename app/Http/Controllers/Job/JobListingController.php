<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Controller;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class JobListingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();

        if(!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        //return $user->jobListings()->latest()->get();

        $jobListings = $user->jobListings()->latest()->get();

        return inertia('JobListings', [
            'listings' => $jobListings,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'company' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'status' => 'required|string',
            'url' => 'nullable|url',
            'applied_at' => 'nullable|date',
        ]);

        return Auth::user()->jobListings()->create($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(JobListing $jobListing)
    {
        $this->authorize('view', $jobListing);
        return inertia('Listing/Show', ['listing' => $jobListing]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobListing $jobListing)
    {
        $this->authorize('update', $jobListing);

        $data = $request->validate([
            'company' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'status' => 'required|string',
            'url' => 'nullable|url',
            'applied_at' => 'nullable|date',
        ]);

        $jobListing->update($data);

        return $jobListing;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobListing $jobListing)
    {
        $this->authorize('delete', $jobListing);
        $jobListing->delete();

        return response()->noContent();
    }
}
