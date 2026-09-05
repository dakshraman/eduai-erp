<?php

namespace App\Http\Controllers\api\v2\Teacher;

use App\Http\Controllers\Controller;
use App\Http\Resources\v2\Teacher\Book\BooklistResource;
use App\Models\SmBook;
use App\Scopes\ActiveStatusSchoolScope;

class BookController extends Controller
{
    public function bookList()
    {
        $bookSearch = SmBook::withoutGlobalScope(ActiveStatusSchoolScope::class)->with('bookSubject')->where('school_id', auth()->user()->school_id)->get();
        $anonymousResourceCollection = BooklistResource::collection($bookSearch);
        return response()->json([
                'success' => true,
                'data' => $anonymousResourceCollection,
                'message' => 'Book list',
            ]);
    }
}
