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

    @stack('styles')
</head>
<body>

    <main>
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
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
                    // 1. Logika untuk merubah warna icon love
                    if (data.status === 'added') {
                        icon.classList.remove('bi-heart');
                        icon.classList.add('bi-heart-fill', 'text-danger');
                    } else {
                        icon.classList.remove('bi-heart-fill', 'text-danger');
                        icon.classList.add('bi-heart');
                    }

                    // 2. 🌟 FIX: Logika untuk merubah angka di Badge Navbar secara real-time
                    let badge = document.getElementById('wishlist-badge');
                    
                    if (badge) {
                        if (data.total > 0) {
                            // Jika ada isinya, update angkanya dan pastikan badge tidak disembunyikan
                            badge.innerText = data.total > 99 ? '99+' : data.total;
                            badge.classList.remove('d-none');
                        } else {
                            // Jika totalnya 0 (kosong), sembunyikan badge-nya
                            badge.classList.add('d-none');
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        });
    </script>

    @stack('scripts')
</body>
</html>