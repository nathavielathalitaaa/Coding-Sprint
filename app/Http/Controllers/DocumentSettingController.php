<?php

namespace App\Http\Controllers;

use App\Models\DocumentSetting;
use Illuminate\Http\Request;

class DocumentSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'company_name' => DocumentSetting::get('company_name', 'HR Sinergi Hotel & Villa'),
            'accent_color' => DocumentSetting::get('accent_color', '#04A54C'),
            'font_family' => DocumentSetting::get('font_family', 'Arial'),
            'footer_text' => DocumentSetting::get('footer_text', 'Dokumen ini sah dan ditandatangani secara digital.'),
            'logo_path' => DocumentSetting::get('logo_path', ''),
        ];

        return view('hr.settings.document', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'accent_color' => 'required|string|size:7',
            'font_family' => 'required|string|in:Arial,Times New Roman,Helvetica,Georgia',
            'footer_text' => 'nullable|string|max:500',
        ]);

        DocumentSetting::set('company_name', $request->company_name);
        DocumentSetting::set('accent_color', $request->accent_color);
        DocumentSetting::set('font_family', $request->font_family);
        DocumentSetting::set('footer_text', $request->footer_text);

        flash()->success('Pengaturan dokumen berhasil disimpan.');
        return redirect()->route('hr.settings.document');
    }

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('document-logos', 'public');
            DocumentSetting::set('logo_path', $path);
            flash()->success('Logo dokumen berhasil diunggah.');
        }

        return redirect()->route('hr.settings.document');
    }
}
