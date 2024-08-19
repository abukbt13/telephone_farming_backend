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
        $request->validate([
            'document' => 'required',
            'title' => 'required',
        ]);
        $data = $request->all();

        $documentsave = new Education();
        $documentsave->description = $data['description'];
        $documentsave->title = $data['title'];
        $document = $request->file('document');

        // Generate a unique name for the file
        $documentName = time() . '_' . $document->getClientOriginalName();


        $documentsave->document = $documentName;

        $documentsave->save();

        return[
            'status'=>'success',
            'data' =>$documentsave
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
    public function edit(Education $education)
    {
        //
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
