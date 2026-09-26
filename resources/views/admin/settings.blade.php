@extends('layouts.admin')

@section('content')
<div class="space-y-8 max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-white dark:bg-slate-900 p-6 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm flex justify-between items-center">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-900/50 rounded-full mb-2">
                <span class="w-2 h-2 bg-emerald-600 rounded-full animate-ping"></span>
                <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-300 tracking-wider uppercase">CMS SIFAT</span>
            </div>
            <h2 class="text-2xl font-bold text-slate-900 dark:text-white tracking-tight">Pengaturan Kop Surat & Dokumen Resmi</h2>
            <p class="text-slate-500 dark:text-slate-400 text-sm mt-0.5">Ubah identitas sekolah, nama Kepala Sekolah, dan NIP untuk dokumen laporan PDF.</p>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
    <div class="p-4 bg-emerald-600 text-white rounded-2xl font-medium text-sm shadow-lg shadow-emerald-950 flex items-center gap-3">
        <span>✨</span>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    <!-- Form Pengaturan -->
    <div class="bg-white dark:bg-slate-900 p-8 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-sm">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Nama Yayasan / Pemerintah</label>
                    <input type="text" name="school_foundation" value="{{ \App\Models\Setting::getVal('school_foundation') }}" required 
                           class="w-full p-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold outline-none text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Nama Sekolah / Lembaga</label>
                    <input type="text" name="school_name" value="{{ \App\Models\Setting::getVal('school_name') }}" required 
                           class="w-full p-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold outline-none text-slate-900 dark:text-white">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Tagline / Keterangan Sistem (Kop Surat)</label>
                <input type="text" name="school_tagline" value="{{ \App\Models\Setting::getVal('school_tagline') }}" required 
                       class="w-full p-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold outline-none text-slate-900 dark:text-white">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Alamat & Kontak Sekolah</label>
                <textarea name="school_address" rows="2" required 
                          class="w-full p-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold outline-none text-slate-900 dark:text-white">{{ \App\Models\Setting::getVal('school_address') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Nama Kepala Sekolah & Gelar</label>
                    <input type="text" name="headmaster_name" value="{{ \App\Models\Setting::getVal('headmaster_name') }}" required 
                           class="w-full p-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold outline-none text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2">NIP Kepala Sekolah</label>
                    <input type="text" name="headmaster_nip" value="{{ \App\Models\Setting::getVal('headmaster_nip') }}" required 
                           class="w-full p-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold outline-none text-slate-900 dark:text-white">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-500 mb-2">Kota / Lokasi Dokumen</label>
                    <input type="text" name="city_location" value="{{ \App\Models\Setting::getVal('city_location') }}" required 
                           class="w-full p-3.5 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl text-sm font-semibold outline-none text-slate-900 dark:text-white">
                </div>
            </div>

            <div class="pt-4">
                <button type="submit" class="px-8 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-sm transition shadow-lg shadow-emerald-600/20">
                    💾 Simpan Perubahan Pengaturan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection