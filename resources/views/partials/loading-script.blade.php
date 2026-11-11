<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.addEventListener('submit', function(e) {
            const form = e.target;
            if (!(form instanceof HTMLFormElement)) return;
            if (form.method.toUpperCase() !== 'POST') return;
            if (form.hasAttribute('data-no-loading')) return;

            const submitBtn = form.querySelector('button[type="submit"]:not([data-no-loading])');
            if (!submitBtn || submitBtn.disabled) return;

            const loadingText = submitBtn.getAttribute('data-loading-text') || 'Memproses...';

            setTimeout(function() {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed', 'pointer-events-none');
                submitBtn.innerHTML = '<span class="w-4 h-4 border-2 border-current border-t-transparent rounded-full animate-spin inline-block mr-2 shrink-0"></span><span>' + loadingText + '</span>';
            }, 10);
        });
    });
</script>
