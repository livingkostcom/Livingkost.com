<script>
document.querySelectorAll('[data-copy]').forEach(button => button.addEventListener('click', async function () {
    try { await navigator.clipboard.writeText(this.dataset.copy); this.textContent = 'Tautan tersalin ✓'; }
    catch (_) { this.textContent = 'Salin tautan di atas secara manual'; }
}));
</script>
