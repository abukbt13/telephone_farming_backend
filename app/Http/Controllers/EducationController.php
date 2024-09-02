<?php

namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;
use League\CommonMark\Node\Block\Document;

class EducationController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
           'document_file'=>'required|mimes:pdf,docx,doc|max:2048',
            'title'=>'required',
        ]);


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
    public function downloadDocument($id)
    {
        // Fetch the document from the database using the ID
        $document = Education::find($id);

        // Check if the document exists
        if (!$document) {
            return response()->json([
                'status'=>'failed',
                'message'=>'document not found',
            ]);
        }

        /// Construct the file path based on the public directory
        $filePath = public_path('documents/' . $document->document);

        // Check if the file exists in the public directory
        if (!file_exists($filePath)) {
            return response()->json([
                'status'=>'failed',
                'message'=>'Document not found',
            ]);
        }

        // Return the file as a download response
        return response()->download($filePath);
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
