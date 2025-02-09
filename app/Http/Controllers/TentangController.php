<?php

namespace App\Http\Controllers;

use App\Models\Tentang;
use Illuminate\Http\Request;

class TentangController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tentang = Tentang::first();
        return view('admin.settings.tentang', compact('tentang'));
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Tentang $tentang)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tentang $tentang)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $tentang = Tentang::find($request->id);

        if ($request->hasFile('logo')) {
            $fileName = time() . '.' . $request->logo->extension();
            $request->logo->storeAs('tentang', $fileName, 'public');
            $tentang->logo = $fileName;
        }

        $tentang->judul = $request->judul;
        $tentang->deskripsi = $request->deskripsi;
        $tentang->kontak_email = $request->kontak_email;
        $tentang->kontak_telepon = $request->kontak_telepon;
        $tentang->alamat = $request->alamat;
        $tentang->save();

        return response()->json(['message' => __('saved successfully')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tentang $tentang)
    {
        //
    }
}
