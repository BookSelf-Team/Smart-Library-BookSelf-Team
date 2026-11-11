{{--
    Animasi gerak halus untuk seluruh aplikasi (gaya Material 3 expressive motion).
    Dimuat oleh semua layout lewat <head>. Semua animasi hanya aktif ketika
    pengguna tidak meminta pengurangan gerak (prefers-reduced-motion).
--}}
<style>
    @media (prefers-reduced-motion: no-preference) {
        main > div {
            animation: page-in 0.45s cubic-bezier(0.2, 0.8, 0.2, 1) both;
        }

        .stagger-in > * {
            animation: rise-in 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) both;
        }

        .stagger-in > *:nth-child(2) { animation-delay: 60ms; }
        .stagger-in > *:nth-child(3) { animation-delay: 120ms; }
        .stagger-in > *:nth-child(4) { animation-delay: 180ms; }
        .stagger-in > *:nth-child(5) { animation-delay: 240ms; }
        .stagger-in > *:nth-child(n+6) { animation-delay: 300ms; }

        .pop-in {
            animation: pop-in 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) both;
        }

        .flash-in {
            animation: flash-in 0.4s cubic-bezier(0.2, 0.8, 0.2, 1) both;
        }

        .float-soft {
            animation: float-soft 3.2s ease-in-out infinite;
        }
    }

    @keyframes page-in {
        from { opacity: 0; transform: translateY(14px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes rise-in {
        from { opacity: 0; transform: translateY(18px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    @keyframes pop-in {
        from { opacity: 0; transform: translateY(10px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    @keyframes flash-in {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes float-soft {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-6px); }
    }
</style>
