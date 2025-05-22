<?php

namespace App\Http\Controllers\Job;

use App\Models\JobListing;
use App\Models\Reminder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReminderController extends Controller
{
    // This controller handles the reminders for job listings.
    public function index(JobListing $jobListing)
    {
        return response()->json($jobListing->reminders()->latest()->get());
    }
    // This method retrieves all reminders for a specific job listing.
    public function store(Request $request, JobListing $jobListing)
    {
        $data = $request->validate([
            'remind_at' => 'required|date|after_or_equal:today',
            'note' => 'nullable|string|max:1000',
        ]);

        $reminder = $jobListing->reminders()->create($data);

        return response()->json($reminder, 201);
    }
    // This method creates a new reminder for a specific job listing.
    public function update(Request $request, JobListing $jobListing, Reminder $reminder)
    {
        if ($reminder->job_listing_id !== $jobListing->id) {
            return response()->json(['error' => 'Reminder not associated with this job listing'], 403);
        }

        $data = $request->validate([
            'remind_at' => 'required|date|after_or_equal:today',
            'note' => 'nullable|string|max:1000',
        ]);

        $reminder->update($data);

        return response()->json($reminder);
    }
    // This method updates an existing reminder for a specific job listing.
    public function destroy(JobListing $jobListing, Reminder $reminder)
    {
        if ($reminder->job_listing_id !== $jobListing->id) {
            return response()->json(['error' => 'Reminder not associated with this job listing'], 403);
        }

        $reminder->delete();

        return response()->json(['message' => 'Reminder deleted']);
    }
    // This method deletes a specific reminder for a job listing.
    public function markAsNotified(JobListing $jobListing, Reminder $reminder)
    {
        if ($reminder->job_listing_id !== $jobListing->id) {
            return response()->json(['error' => 'Reminder not associated with this job listing'], 403);
        }

        $reminder->update([
            'notified' => true,
            // 'updated_at' => now(), // Laravel does this automatically when updating
        ]);

        return response()->json($reminder->fresh());
    }


}
