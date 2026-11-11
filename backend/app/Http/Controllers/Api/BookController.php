<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class BookController extends Controller
{
    public function index(): JsonResponse
    {
        Log::info('Books listed', ['actor_id' => auth()->id()]);
        return response()->json(
            Book::query()->latest('id')->paginate(10)
        );
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'author' => ['required', 'string', 'max:255'],
                'publish_year' => ['required', 'integer', 'min:0', 'max:9999'],
            ]);
        } catch (ValidationException $e) {
            Log::warning('Book validation failed', [
                'actor_id' => auth()->id(),
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);

            throw $e;
        }
        
        try {
            $book = Book::create($validated);
        } catch (QueryException $e) {
            Log::error('Book creation query failed', [
                'actor_id' => auth()->id(),
                'exception' => $e,
            ]);

            throw $e;
        }

        Log::info('Book created', [
            'actor_id' => auth()->id(),
            'book_id' => $book->id,
        ]);

        return response()->json([
            'message' => 'Book created successfully.',
            'data' => $book,
        ], 201);
    }

    public function show(Book $book): JsonResponse
    {
        Log::info('Book viewed', [
            'actor_id' => auth()->id(),
            'book_id' => $book->id,
        ]);

        return response()->json([
            'data' => $book,
        ]);
    }

    public function update(
        Request $request,
        Book $book
    ): JsonResponse {
        try {
            $validated = $request->validate([
                'title' => ['sometimes', 'required', 'string', 'max:255'],
                'author' => ['sometimes', 'required', 'string', 'max:255'],
                'publish_year' => [
                    'sometimes',
                    'required',
                    'integer',
                    'min:0',
                    'max:9999',
                ],
        ]);
        } catch (ValidationException $e) {
            Log::warning('Book validation failed', [
                'actor_id' => auth()->id(),
                'errors' => $e->errors(),
                'request_data' => $request->all(),
            ]);

            throw $e;
        }

        Log::info('Book updated', [
            'actor_id' => auth()->id(),
            'book_id' => $book->id,
        ]);
        
        try {
            $book->update($validated);
        } catch (QueryException $e) {
            Log::error('Book update query failed', [
                'actor_id' => auth()->id(),
                'exception' => $e,
            ]);

            throw $e;
        }

        return response()->json([
            'message' => 'Book updated successfully.',
            'data' => $book->fresh(),
        ]);
    }

    public function destroy(Book $book): JsonResponse
    {
        Log::info('Book deleted', [
            'actor_id' => auth()->id(),
            'book_id' => $book->id,
        ]);
        try {
            $book->delete();
        } catch (QueryException $e) {
            Log::error('Book deletion query failed', [
                'actor_id' => auth()->id(),
                'exception' => $e,
            ]);

            throw $e;
        }

        return response()->json([
            'message' => 'Book deleted successfully.',
        ]);
    }
}