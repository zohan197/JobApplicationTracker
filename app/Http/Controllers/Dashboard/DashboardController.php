<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobListing;
use App\Models\Reminder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function dashboardStats()
    {
        $userId = Auth::id();

        // Upcoming reminders for user's job listings
        $upcomingReminders = Reminder::whereHas('jobListing', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->where('remind_at', '>=', now())
            ->where('notified', false)
            ->count();

        // Job application stats
        $jobListings = JobListing::where('user_id', $userId);
        $totalApplications = $jobListings->count();

        $statusCounts = $jobListings->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json([
            'totalApplications' => $totalApplications,
            'statusCounts' => $statusCounts,
            'upcomingReminders' => $upcomingReminders,
        ]);
    }

    public function upcomingReminders()
    {
        $userId = Auth::id();

        $reminders = Reminder::whereHas('jobListing', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereBetween('remind_at', [now(), now()->addDays(7)])
            ->with([
                'jobListing:id,company,position',
                'jobListing.notes:id,job_listing_id,content'
            ])
            ->orderBy('remind_at', 'asc')
            ->get()
            ->map(function ($reminder) {
                return [
                    'id' => $reminder->id,
                    'note' => $reminder->note,
                    'remind_at' => $reminder->remind_at->toDateTimeString(),
                    'company' => $reminder->jobListing->company ?? 'N/A',
                    'position' => $reminder->jobListing->position ?? 'N/A',
                    'notes' => $reminder->jobListing->notes->pluck('content')->toArray(),
                ];
            });

        return response()->json($reminders);
    }

}
