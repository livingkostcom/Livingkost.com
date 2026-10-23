<div class="rd-table-wrap">
<table class="rd-table ref-table">
<thead><tr><th>Pendaftar</th>@if($ownerView)<th>Sales</th>@endif<th>Pendaftaran</th><th>DP</th><th>Kamar</th><th>Reward</th>@if($ownerView)<th>Tindakan</th>@endif</tr></thead>
<tbody>
@forelse($leads as $lead)
<tr>
    <td><strong>{{ $lead->name }}</strong><small>{{ $lead->phone }} · {{ \Carbon\Carbon::parse($lead->created_at)->format('d/m/Y') }}</small></td>
    @if($ownerView)<td>{{ $lead->sales_name }}</td>@endif
    <td><span class="ref-badge {{ $lead->status === 'approved' ? 'ref-green' : ($lead->status === 'rejected' ? 'ref-red' : 'ref-orange') }}">{{ ['approved'=>'Disetujui','rejected'=>'Ditolak','pending'=>'Menunggu review'][$lead->status] ?? $lead->status }}</span></td>
    <td>
        @if($lead->dp_status === 'paid' && $lead->dp_paid_at)<span class="ref-badge ref-green">DP terverifikasi</span>
        @elseif($lead->dp_status === 'submitted')<span class="ref-badge ref-orange">Bukti menunggu verifikasi</span>
        @elseif(!$lead->dp_method)<span class="ref-badge">Tanpa DP</span>
        @else<span class="ref-badge ref-orange">{{ ['unpaid'=>'Belum bayar','expired'=>'Kedaluwarsa','failed'=>'Gagal bayar'][$lead->dp_status] ?? 'Belum terverifikasi' }}</span>@endif
        <small>Rp {{ number_format($lead->dp_amount ?? 0, 0, ',', '.') }}</small>
    </td>
    <td>{{ $lead->room_number ?? 'Belum ada kontrak' }}</td>
    <td>
        <span class="ref-badge {{ $lead->earned_at ? 'ref-green' : '' }}">{{ $lead->paid_at ? 'Dibayarkan' : ($lead->earned_at ? 'Reward tercapai' : 'Belum memenuhi syarat') }}</span>
        <small>{{ $lead->earned_at ? '' : 'Potensi: ' }}Rp {{ number_format($lead->reward_amount ?? $lead->referral_reward_amount, 0, ',', '.') }}</small>
        @if($lead->earned_at)<small>Lunas: {{ \Carbon\Carbon::parse($lead->earned_at)->format('d/m/Y') }}</small>@endif
        @if($lead->paid_at)<small>{{ \Carbon\Carbon::parse($lead->paid_at)->format('d/m/Y') }} · {{ $lead->payment_reference }}</small>@endif
    </td>
    @if($ownerView)<td>
        @if($lead->dp_method === 'manual' && $lead->dp_status === 'submitted')
            <a href="{{ route('tenant-registrations.index') }}">Periksa bukti di Pendaftaran ↗</a>
            <form method="POST" action="{{ route('referrals.dp', $lead->id) }}" onsubmit="return confirm('Bukti DP sudah diperiksa dan uang sudah diterima?')">@csrf<button class="rd-button secondary" type="submit" style="font-size:12px;padding:8px 12px;margin-top:6px">Verifikasi DP</button></form>
        @endif
        @if($lead->earned_at && !$lead->paid_at && $lead->reward_amount > 0)
            <form method="POST" action="{{ route('referrals.paid', $lead->reward_id) }}" onsubmit="return confirm('Catat reward sebagai sudah dibayarkan? Pastikan pembayaran di luar aplikasi telah dilakukan.')">@csrf
                <label class="rd-small" for="reward-{{ $lead->reward_id }}">Referensi pembayaran reward</label><input id="reward-{{ $lead->reward_id }}" name="reference" placeholder="Nomor bukti / catatan transfer" maxlength="200" required>
                <button class="rd-button" type="submit" style="font-size:12px;padding:8px 12px;margin-top:6px">Catat sudah dibayarkan</button>
            </form>
        @elseif(!$lead->earned_at && $lead->status !== 'rejected')<small>Tinjau pendaftaran dan verifikasi invoice bulan pertama.</small>@endif
    </td>@endif
</tr>
@empty<tr><td class="rd-empty" colspan="{{ $ownerView ? 8 : 6 }}">Belum ada pendaftar dari tautan referral.</td></tr>@endforelse
</tbody></table>
</div>
<div class="ref-pagination">{{ $leads->links('referrals.pagination') }}</div>
