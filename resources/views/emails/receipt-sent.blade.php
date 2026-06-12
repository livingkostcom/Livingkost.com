@component('mail::message')
    # Bukti Pembayaran Diterima

    Halo **{{ $tenant->display_name }}**,

    Terima kasih atas pembayaran Anda. Kami dengan senang hati mengonfirmasi bahwa pembayaran Anda telah diterima dan
    diverifikasi.

    ## Detail Pembayaran

    @component('mail::table')
        | Informasi | Detail |
        |-----------|--------|
        | **Nomor Receipt** | {{ $receipt->receipt_number }} |
        | **Nomor Invoice** | {{ $receipt->invoice->reference_number }} |
        | **Jumlah Pembayaran** | Rp {{ number_format($receipt->invoice->amount, 0, ',', '.') }} |
        | **Periode** | {{ \Carbon\Carbon::createFromFormat('Y-m', $receipt->invoice->month_year)->translatedFormat('F Y') }} |
        | **Tanggal Verifikasi** | {{ $receipt->invoice->verified_at->translatedFormat('d F Y H:i') }} |
        | **Status** | Terbayar (Paid) |
    @endcomponent

    ## File Bukti Pembayaran

    Receipt Anda telah disertakan dalam attachment email ini sebagai file PDF. Anda dapat mengunduhnya dan menyimpannya
    untuk referensi Anda.

    Nama File: **{{ $receipt->receipt_number }}.pdf**

    ## Informasi Kamar

    - **Nomor Kamar**: {{ $receipt->invoice->lease->room->room_number }}
    - **Tipe Kamar**: {{ $receipt->invoice->lease->room->roomType->name ?? '-' }}

    ## Pertanyaan?

    Jika Anda memiliki pertanyaan tentang pembayaran ini atau memerlukan bantuan lebih lanjut, silakan hubungi kami.

    ---

    Terima kasih,

    **{{ strtoupper(\App\Models\Setting::getValue('app_name')) }} - {{ \App\Models\Setting::getValue('app_tagline') }}**
@endcomponent
