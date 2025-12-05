<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Vite (CSS + JS principal) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    {{-- SweetAlert2 para mensajes flash --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: @json(session('success')),
                    confirmButtonColor: '#4f46e5',
                });
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: @json(session('error')),
                    confirmButtonColor: '#ef4444',
                });
            });
        </script>
    @endif

    @if ($errors->any())
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                Swal.fire({
                    icon: 'error',
                    title: 'Hay errores en el formulario',
                    html: `<ul class="text-left">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>`,
                    confirmButtonColor: '#ef4444',
                });
            });
        </script>
    @endif

    {{-- Confirm global para formularios con data-confirm --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('form[data-confirm]').forEach((form) => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const message = this.getAttribute('data-confirm') || '¿Estás seguro?';

                    Swal.fire({
                        title: 'Confirmar acción',
                        text: message,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.submit();
                        }
                    });
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
