<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>Big Sport - Toko Sepatu & Alat Olahraga</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/customer/css/style.css') }}">

    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    @stack('styles')
</head>
<body>

    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    @vite(['resources/js/app.js'])
    
    <script>
        document.addEventListener('click', function (e) {
            if (e.target.closest('.toggle-wishlist')) {
                const btn = e.target.closest('.toggle-wishlist');
                const productId = btn.getAttribute('data-product-id');
                const icon = btn.querySelector('i');

                fetch("{{ route('wishlist.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ product_id: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'added') {
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill', 'text-danger');
                    } else {
                        icon.classList.remove('bi-heart-fill', 'text-danger');
                        icon.classList.add('bi-heart');
                    }

                    let badge = document.getElementById('wishlist-badge');
                    if (badge) {
                        if (data.total > 0) {
                            badge.innerText = data.total > 99 ? '99+' : data.total;
                            badge.classList.remove('d-none');
                        } else {
                            badge.classList.add('d-none');
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    </script>

    @auth
    <script>
        // Interval untuk memastikan window.Echo sudah di-load oleh Vite
        const checkEcho = setInterval(() => {
            if (typeof window.Echo !== 'undefined') {
                clearInterval(checkEcho);
                initRealtimeNotifications();
            }
        }, 200);

        function initRealtimeNotifications() {

            window.Echo.private(`user-notifications.{{ auth()->id() }}`)
                .listen('.notif.new', (data) => {
                    
                    // A. Play Sound Effect
                    // Pastikan file ada di public/assets/customer/sounds/notification.mp3
                    let notificationSound = new Audio("{{ asset('assets/customer/sounds/notification.mp3') }}");
                    notificationSound.play().catch(error => {
                        console.warn("Autoplay suara diblokir browser, butuh interaksi user pertama kali.");
                    });

                    // B. Tampilkan Pop-up Toastify
                    Toastify({
                        text: `🔔 ${data.title}\n${data.message}`,
                        duration: 8000,
                        gravity: "top", 
                        position: "right", 
                        close: true,
                        stopOnFocus: true,
                        style: {
                            background: "linear-gradient(to right, #000000, #434343)",
                            color: "#ffffff",
                            fontSize: "14px",
                            zIndex: "999999",
                            borderRadius: "8px",
                            boxShadow: "0 4px 15px rgba(0,0,0,0.4)"
                        },
                        onClick: function(){
                            window.location.href = "{{ route('notification') }}";
                        }
                    }).showToast();

                    // C. Update Badge Lonceng di Navbar
                    const badge = document.getElementById('notif-badge-count');
                    const bellIcon = document.getElementById('navbar-bell-icon');

                    if (badge) {
                        let currentCount = parseInt(badge.innerText.replace('+', '')) || 0;
                        badge.innerText = (currentCount + 1) > 99 ? '99+' : (currentCount + 1);
                        badge.classList.remove('d-none');
                    } else if (bellIcon) {
                        const newBadge = document.createElement('span');
                        newBadge.id = 'notif-badge-count';
                        newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                        newBadge.style.fontSize = '9px';
                        newBadge.innerText = '1';
                        bellIcon.appendChild(newBadge);
                    }
                });
        }
    </script>
    @endauth

    @stack('scripts')
</body>
</html>