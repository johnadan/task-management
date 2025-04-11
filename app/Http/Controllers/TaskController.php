<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use App\Models\{Task, TaskImage, User};
// use App\Models\Task;
// use App\Models\TaskImage;
// use App\Models\User;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        // Debug log
        \Log::info('Index method called. Auth ID: ' . auth()->id() . ', User: ' . (auth()->user() ? auth()->user()->email : 'none'));
        \Log::info('Request user: ' . ($request->user() ? $request->user()->id . ' - ' . $request->user()->email : 'none'));

        // Use the request user instead of auth helper
        $user = $request->user();

        if (!$user) {
            return response()->json(['data' => []], 200);
        }

        // $query = Task::where('user_id', auth()->id());
        $query = Task::where('user_id', $user->id);

        // Apply filters
        // if ($request->has('status')) {
        //     $query->where('status', $request->status);
        // }

        // if ($request->has('search')) {
        //     $query->where('title', 'like', '%' . $request->search . '%');
        // }
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Apply sorting
        $sortField = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        // Debug log - count tasks for this user
        $totalTasks = $query->count();
        \Log::info("Found {$totalTasks} tasks for user {$user->id}");

        // Pagination
        $perPage = $request->get('per_page', 10);
        return $query->with('images')->paginate($perPage);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100|unique:tasks',
            'content' => 'required|string',
            'status' => ['required', Rule::in(['to do', 'in progress', 'done'])],
            'is_published' => 'boolean'
        ]);

        // $task = auth()->user()->tasks()->create($validated);

        // Get authenticated user and create the task
        $user = auth()->user();
        // For debugging
        \Log::info('Creating task for user: ' . ($user ? $user->id . ' - ' . $user->email : 'User not found'));

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Create the task with explicit user_id
        $task = new Task($validated);
        // $task->user_id = auth()->id();
        $task->user_id = $user->id;
        $task->save();

        if ($request->hasFile('images')) {
            $this->handleImages($request, $task);
        }

        return response()->json($task->load('images'), 201);
    }

    // public function show(Task $task)
    // public function show(Request $request, Task $task)
    public function show(Request $request, $id)
    {
        // $this->authorize('view', $task);
        // Debug logs
        // \Log::info('Show method called for task ID: ' . $task->id);
        \Log::info('Show method called for task ID: ' . $id);
        \Log::info('Request user: ' . ($request->user() ? $request->user()->id . ' - ' . $request->user()->email : 'none'));
        // \Log::info('Task user_id: ' . $task->user_id);

        // Get authenticated user from request
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Check if the task belongs to this user
        // if ($task->user_id !== $user->id) {
        //     \Log::info('Task does not belong to the authenticated user');
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        // Find the task by ID
        $task = Task::where('id', $id)->where('user_id', $user->id)->first();

        if (!$task) {
            \Log::info('Task not found or does not belong to the authenticated user');
            return response()->json(['message' => 'Task not found'], 404);
        }

        \Log::info('Task found: ' . $task->id . ' - ' . $task->title);
        return $task->load('images');
    }

    // public function update(Request $request, Task $task)
    public function update(Request $request, $id)
    {
        // $this->authorize('update', $task);

        // Debug logs
        \Log::info('Update method called for task ID: ' . $id);
        \Log::info('Request user: ' . ($request->user() ? $request->user()->id . ' - ' . $request->user()->email : 'none'));

        // Get authenticated user from request
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Find the task by ID
        $task = Task::where('id', $id)->where('user_id', $user->id)->first();

        if (!$task) {
            \Log::info('Task not found or does not belong to the authenticated user');
            return response()->json(['message' => 'Task not found'], 404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100', Rule::unique('tasks')->ignore($task->id)],
            'content' => 'required|string',
            'status' => ['required', Rule::in(['to do', 'in progress', 'done'])],
            'is_published' => 'boolean'
        ]);

        $task->update($validated);

        // Handle image uploads during task update
        if ($request->hasFile('images')) {
            $this->handleImages($request, $task);
        }

        return response()->json($task->load('images'));
    }

    // public function destroy(Task $task)
    public function destroy(Request $request, $id)
    {
        // $this->authorize('delete', $task);

        // Get authenticated user from request
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

    // Find the task by ID
    $task = Task::where('id', $id)->where('user_id', $user->id)->first();

    if (!$task) {
        return response()->json(['message' => 'Task not found or unauthorized'], 404);
    }

        // Delete associated images from storage
        foreach ($task->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }

        $task->delete();
        return response()->json(null, 204);
    }

    // public function uploadImages(Request $request, Task $task)
    public function uploadImages(Request $request, $id)
    {
        // $this->authorize('update', $task);

        // Get authenticated user from request
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Find the task by ID
        $task = Task::where('id', $id)->where('user_id', $user->id)->first();

        if (!$task) {
            return response()->json(['message' => 'Task not found'], 404);
        }

        $request->validate([
            'images.*' => 'required|image|mimes:jpg,jpeg,png|max:4096'
        ]);

        $this->handleImages($request, $task);

        return response()->json($task->load('images'));
    }

    // public function deleteImage(Task $task, TaskImage $image)
    public function deleteImage(Request $request, $taskId, $imageId)
    {
        // $this->authorize('update', $task);

        // if ($image->task_id !== $task->id) {
        //     return response()->json(['error' => 'Image does not belong to this task'], 403);
        // }

        // Debug logs
        \Log::info('DeleteImage method called for task ID: ' . $taskId . ', image ID: ' . $imageId);
        \Log::info('Request user: ' . ($request->user() ? $request->user()->id . ' - ' . $request->user()->email : 'none'));

        // Get authenticated user from request
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }

        // Find the task by ID and check it belongs to the user
        $task = Task::where('id', $taskId)->where('user_id', $user->id)->first();

        if (!$task) {
            \Log::info('Task not found or does not belong to the authenticated user');
            return response()->json(['message' => 'Task not found'], 404);
        }

        // Find the image and check it belongs to the task
        $image = TaskImage::where('id', $imageId)->where('task_id', $taskId)->first();

        if (!$image) {
            \Log::info('Image not found or does not belong to this task');
            return response()->json(['message' => 'Image not found'], 404);
        }

        // Delete the image from storage
        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(null, 204);
    }

    private function handleImages(Request $request, Task $task)
    {
        foreach ($request->file('images') as $image) {
            $path = $image->store('task-images', 'public');
            $task->images()->create(['image_path' => $path]);
        }
    }
}
