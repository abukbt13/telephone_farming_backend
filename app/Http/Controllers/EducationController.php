<?php

namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;

class EducationController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
//        // Validate the incoming request
//        $request->validate([
//            'document' => 'required', // Adjust MIME types and max size as needed
//            'title' => 'required|string',
//        ]);


        $data = $request->all();

        // Create a new Education model instance
        $documentsave = new Education();
        $documentsave->description = $data['description'];
        $documentsave->title = $data['title'];
        // Handle the document upload


            $document = $request->file('document_file');

            // Generate a unique name for the file
            $documentName = time() . '_' . $document->getClientOriginalName();

            // Move the file to the public/documents directory
            $document->move(public_path('documents'), $documentName);

            // Save the document name in the database
            $documentsave->document = $documentName;


        // Save the record in the database
        $documentsave->save();

        // Return a success response
        return [
            'status' => 'success',
            'data' => $documentsave
        ];
    }

    /**
     * Display the specified resource.
     */
    public function showDocuments()
    {
     $documents = Education::all();
     return response()->json([
         'status'=>'success',
         'documents'=>$documents,
     ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function getDocument($id)
    {
        $document = Education::findOrFail($id);
        return response()->json([
            'status'=>'success',
            'document'=>$document,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Education $education)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Education $education)
    {
        //
    }
}
