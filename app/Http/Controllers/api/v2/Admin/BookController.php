<?php

namespace App\Http\Controllers\api\v2\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\v2\Admin\BookListResource;
use App\Models\LibrarySubject;
use App\Models\SmAcademicYear;
use App\Models\SmBook;
use App\Models\SmBookCategory;
use App\Scopes\AcademicSchoolScope;
use App\Scopes\StatusAcademicSchoolScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    public function bookList()
    {
        $books = SmBook::where('school_id', auth()->user()->school_id)->get();
        $anonymousResourceCollection = BookListResource::collection($books);


        return response()->json([
            'success' => true,
            'data' => $anonymousResourceCollection,
            'message' => 'The book list successful',
        ]);
    }

    public function addBookDropdowns()
    {
        $categories = SmBookCategory::withoutGlobalScope(AcademicSchoolScope::class)
            ->where('school_id', auth()->user()->school_id)
            ->select('id', 'category_name')->get();
        $subjects = LibrarySubject::withoutGlobalScope(StatusAcademicSchoolScope::class)
            ->where('school_id', auth()->user()->school_id)
            ->select('id', 'subject_name')->get();

        $dropdownItems = [
            'categories' => $categories,
            'subjects' => $subjects,
        ];

        return response()->json( [
            'success' => true,
            'data' => $dropdownItems,
            'message' => 'Category & subject list',
        ]);
    }

    public function storeBook(Request $request)
    {
        $this->validate($request, [
            'book_title' => 'required|max:200',
            'book_category_id' => 'required',
            'subject_id' => 'required',
            'quantity' => 'sometimes|nullable|integer|min:0',
            'book_number' => 'sometimes|nullable',
            'isbn_no' => 'sometimes|nullable|different:book_number',
            'publisher_name' => 'sometimes|nullable',
            'author_name' => 'sometimes|nullable',
            'details' => 'sometimes|nullable',
            'book_price' => 'sometimes|nullable|integer|min:0',
            'rack_number' => 'sometimes|nullable',
            'post_date' => 'required|date_format:Y-m-d',
        ]);

        $smBook = new SmBook();
        $smBook->book_title = $request->book_title;
        $smBook->book_category_id = $request->book_category_id;
        $smBook->book_number = $request->book_number;
        $smBook->isbn_no = $request->isbn_no;
        $smBook->publisher_name = $request->publisher_name;
        $smBook->author_name = $request->author_name;
        $smBook->book_subject_id = $request->subject_id;
        $smBook->rack_number = $request->rack_number;
        $smBook->quantity = $request->quantity;
        $smBook->book_price = $request->book_price;
        $smBook->details = $request->details;
        $smBook->post_date = date('Y-m-d', strtotime($request->post_date));
        $smBook->created_by = Auth::user()->id;
        $smBook->school_id = Auth::user()->school_id;
        $smBook->academic_id = SmAcademicYear::SINGLE_SCHOOL_API_ACADEMIC_YEAR();

        $smBook->save();

        return response()->json( [
            'success' => true,
            'data' => null,
            'message' => 'The book created successfully',
        ]);
    }
}
