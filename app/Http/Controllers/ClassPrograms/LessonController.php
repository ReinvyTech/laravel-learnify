<?php

namespace App\Http\Controllers\ClassPrograms;

use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use App\Models\Lesson;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lessons = Lesson::with(['teachers', 'students', 'studies'])->get();
        return response()->json([
            'message' => 'Get data success',
            'data' => $lessons,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'steps' => 'nullable|integer',
            'teacher_ids' => 'nullable|array',
            'teacher_ids.*' => 'exists:users,id',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:users,id',
            'studies' => 'nullable|array',
            'studies.*.description' => 'nullable|string',
            'studies.*.step' => 'nullable|integer',
            'studies.*.video' => 'nullable|string|max:255',
            'studies.*.book' => 'nullable|string|max:255',
        ]);

        // Buat lesson baru
        $lesson = Lesson::create([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'steps' => $validated['steps'] ?? 0,
        ]);

        if (isset($validated['teacher_ids'])) {
            $lesson->teachers()->attach($validated['teacher_ids']);
        }
        if (isset($validated['student_ids'])) {
            $lesson->students()->attach($validated['student_ids']);
        }
        if (isset($validated['studies'])) {
            foreach ($validated['studies'] as $study) {
                $lesson->studies()->create($study);
            }
        }

        return response()->json([
            'message' => 'Store data success',
            'data' => $lesson->load(['teachers', 'students', 'studies']),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lesson $lesson)
    {
        return response()->json([
            'message' => 'Get data success',
            'data' => $lesson->load(['teachers', 'students']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lesson $lesson) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lesson $lesson)
    {
        if (auth()->user()->role == 'admin') {
            $validated = $request->validate([
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'steps' => 'sometimes|integer',
                'teacher_ids' => 'nullable|array',
                'teacher_ids.*' => 'exists:users,id',
                'student_ids' => 'nullable|array',
                'student_ids.*' => 'exists:users,id',
                'studies' => 'nullable|array',
                'studies.*.id' => 'nullable|exists:studies,id',
                'studies.*.description' => 'nullable|string',
                'studies.*.step' => 'nullable|integer',
                'studies.*.video' => 'nullable|string|max:255',
                'studies.*.book' => 'nullable|string|max:255',
            ]);

            $lesson->update([
                'name' => $validated['name'] ?? $lesson->name,
                'description' => $validated['description'] ?? $lesson->description,
                'steps' => $validated['steps'] ?? $lesson->steps,
            ]);
            if ($request->has('teacher_ids')) {
                $lesson->teachers()->sync($validated['teacher_ids']);
            }

            if ($request->has('student_ids')) {
                $lesson->students()->sync($validated['student_ids']);
            }

            if ($request->has('studies')) {
                foreach ($validated['studies'] as $studyData) {
                    if (isset($studyData['id'])) {
                        $study = $lesson->studies()->find($studyData['id']);

                        if ($study) {
                            $study->update([
                                'step' => $studyData['step'] ?? $study->step,
                                'description' => $studyData['description'] ?? $study->description,
                                'video' => $studyData['video'] ?? $study->video,
                                'book' => $studyData['book'] ?? $study->book,
                            ]);
                        } else {
                            return response()->json([
                                'message' => 'Study not found for this lesson',
                            ], 404);
                        }
                    } else {
                        $lesson->studies()->create($studyData);
                    }
                }
            }

            return response()->json([
                'message' => 'Lesson updated successfully by admin',
                'data' => $lesson->load(['teachers', 'students', 'studies']),
            ]);
        } elseif (auth()->user()->role == 'teacher') {
            $teacherId = auth()->user()->id;

            $validated = $request->validate([
                'studies' => 'nullable|array',
                'studies.*.id' => 'nullable|exists:studies,id',
                'studies.*.description' => 'nullable|string',
                'studies.*.step' => 'nullable|integer',
                'studies.*.video' => 'nullable|string|max:255',
                'studies.*.book' => 'nullable|string|max:255',
            ]);

            if ($request->has('studies')) {
                foreach ($validated['studies'] as $studyData) {
                    if (isset($studyData['id'])) {
                        $study = $lesson->studies()->find($studyData['id']);

                        if ($study) {
                            $study->update([
                                'step' => $studyData['step'] ?? $study->step,
                                'description' => $studyData['description'] ?? $study->description,
                                'video' => $studyData['video'] ?? $study->video,
                                'book' => $studyData['book'] ?? $study->book,
                            ]);
                        } else {
                            return response()->json([
                                'message' => 'Study not found for this lesson',
                            ], 404);
                        }
                    } else {
                        $lesson->studies()->create($studyData);
                    }
                }
            }

            if ($request->has('action') && $request->action == 'remove') {
                $lesson->teachers()->detach($teacherId);
            } else {
                if (!$lesson->teachers()->where('teacher_id', $teacherId)->exists()) {
                    $lesson->teachers()->attach($teacherId);
                }
            }

            return response()->json([
                'message' => 'Lesson updated by teacher',
                'data' => $lesson->load('teachers'),
            ]);
        } else {
            $studentId = auth()->user()->id;

            if ($request->has('action') && $request->action == 'remove') {
                $lesson->students()->detach($studentId);
            } else {
                if (!$lesson->students()->where('student_id', $studentId)->exists()) {
                    $lesson->students()->attach($studentId);
                }
            }

            return response()->json([
                'message' => 'Lesson updated by student',
                'data' => $lesson->load('students'),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $lesson->teachers()->detach();
        $lesson->delete();

        return response()->json([
            'message' => 'Lesson deleted successfully',
        ]);
    }
}
