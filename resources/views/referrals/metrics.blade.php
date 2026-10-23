<div class="ref-grid">
    <div class="ref-metric"><small>Pendaftar referral</small><strong>{{ $summary['registered'] }}</strong></div>
    <div class="ref-metric"><small>DP terverifikasi</small><strong>{{ $summary['dp_paid'] }}</strong></div>
    <div class="ref-metric"><small>Sewa bulan pertama lunas</small><strong>{{ $summary['converted'] }}</strong></div>
    <div class="ref-metric"><small>Total reward tercapai</small><strong>Rp {{ number_format($summary['earned'], 0, ',', '.') }}</strong></div>
    <div class="ref-metric"><small>Reward belum dibayarkan</small><strong>Rp {{ number_format($summary['available'], 0, ',', '.') }}</strong></div>
    <div class="ref-metric"><small>Reward sudah dibayarkan</small><strong>Rp {{ number_format($summary['paid'], 0, ',', '.') }}</strong></div>
</div>
