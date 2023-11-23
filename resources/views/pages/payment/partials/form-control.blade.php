
<div class="md:flex md:items-center mb-6 w-full gap-2">
    <x-forms.input id="name" type="text" value="{{ $payment->name }}" name="name" label="Nama Jenis Pembayaran" />
</div>

<div class="bg-white flex gap-5 ">
    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg bg-theme-border-sidebar hover:bg-gray-700 text-white text-sm md:text-left font-medium ">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
        </svg>
        Tambah
    </button>
    <a href="{{ route('admin.payment.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg bg-theme-border-sidebar hover:bg-gray-700 text-white text-sm md:text-left font-medium">
        {{-- <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
        </svg> --}}
        Kembali
    </a>
    </div>
</div>