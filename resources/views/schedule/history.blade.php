<x-app-layout>
    <style>
        /* Transiciones suaves para el modal */
        .modal-backdrop {
            transition: opacity 0.25s ease-out;
        }

        .modal-panel {
            transition: all 0.25s ease-out;
        }

        .modal-open .modal-backdrop {
            opacity: 1;
            pointer-events: auto;
        }

        .modal-open .modal-panel {
            opacity: 1;
            transform: scale(1);
        }

        .modal-closed .modal-backdrop {
            opacity: 0;
            pointer-events: none;
        }

        .modal-closed .modal-panel {
            opacity: 0;
            transform: scale(0.95);
        }

        /* Transiciones para las notificaciones (Toasts) */
        .toast-enter {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .toast-enter-active {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .toast-leave {
            opacity: 1;
            transform: translateY(0) scale(1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .toast-leave-active {
            opacity: 0;
            transform: translateY(-20px) scale(0.95);
        }
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Historial de Cronogramas Generados
            </h2>
            <a href="{{ route('schedule.index') }}"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Volver al Cronograma
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Nombre del Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Fecha de Generación</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($documents as $doc)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $doc->file_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $doc->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td
                                    class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium flex justify-end gap-2">
                                    <a href="{{ Storage::url($doc->file_path) }}" target="_blank"
                                        class="inline-flex items-center px-3 py-1 bg-white border border-indigo-200 text-indigo-600 rounded-md hover:bg-indigo-50 focus:outline-none transition-colors shadow-sm">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        Abrir PDF
                                    </a>

                                    <button type="button"
                                        onclick="openDeleteModal('{{ route('schedule.history.destroy', $doc) }}', '{{ addslashes($doc->file_name) }}')"
                                        class="inline-flex items-center px-3 py-1 bg-white border border-red-200 text-red-600 rounded-md hover:bg-red-50 focus:outline-none transition-colors shadow-sm">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-gray-500 font-medium text-base">No hay cronogramas guardados en el historial
                                        todavía.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center hidden modal-closed">
        <div class="absolute inset-0 bg-gray-900/40 backdrop-blur-sm modal-backdrop"
            onclick="closeModal('deleteModal')"></div>

        <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden modal-panel">
            <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <div class="bg-red-100 p-1.5 rounded-full text-red-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Eliminar PDF</h3>
                </div>
                <button onclick="closeModal('deleteModal')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-6 bg-white">
                <p class="text-sm text-gray-600 mb-2">¿Estás seguro de que deseas eliminar este documento del sistema?
                </p>
                <p id="fileNameToDelete"
                    class="text-sm font-semibold text-gray-900 bg-gray-50 p-2 rounded border border-gray-100 break-words">
                </p>
                <p class="text-xs text-red-500 mt-3 font-medium">Esta acción no se puede deshacer.</p>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-100">
                <button type="button" onclick="closeModal('deleteModal')"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-colors">
                    Cancelar
                </button>
                <form method="POST" id="deleteForm">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 rounded-md text-sm font-medium text-white hover:bg-red-700 shadow-sm transition-colors">
                        Sí, eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="successToast"
        class="fixed top-5 right-5 bg-white border border-green-200 px-4 py-3 rounded-lg shadow-lg z-50 flex items-center gap-3 hidden toast-enter">
        <div class="bg-green-100 p-1.5 rounded-full text-green-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        <div>
            <h4 class="font-bold text-sm text-gray-900">Éxito</h4>
            <span id="successMessage" class="text-sm text-gray-600"></span>
        </div>
        <button onclick="hideToast('successToast')" class="ml-4 text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Revisamos si hay un mensaje de éxito en la sesión (desde el controlador)
            @if(session('success'))
                showToast('successToast', "{{ session('success') }}");
            @endif
        });

        // Lógica del Modal
        function openDeleteModal(actionUrl, fileName) {
            document.getElementById('fileNameToDelete').textContent = fileName;
            document.getElementById('deleteForm').action = actionUrl;

            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');

            // Pequeño retraso para permitir que el display:block se aplique antes de animar
            setTimeout(() => {
                modal.classList.remove('modal-closed');
                modal.classList.add('modal-open');
            }, 10);
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('modal-open');
            modal.classList.add('modal-closed');

            // Esperamos que termine la transición de 0.25s antes de ocultarlo
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 250);
        }

        // Lógica del Toast (Notificación suave)
        function showToast(toastId, message) {
            const toast = document.getElementById(toastId);
            document.getElementById('successMessage').textContent = message;

            toast.classList.remove('hidden');

            // Forzar reflow para que la animación funcione
            void toast.offsetWidth;

            toast.classList.remove('toast-enter');
            toast.classList.add('toast-enter-active');

            // Ocultar automáticamente después de 4 segundos
            setTimeout(() => {
                hideToast(toastId);
            }, 4000);
        }

        function hideToast(toastId) {
            const toast = document.getElementById(toastId);

            toast.classList.remove('toast-enter-active');
            toast.classList.add('toast-leave');

            setTimeout(() => {
                toast.classList.add('toast-leave-active');

                // Limpiar clases y ocultar después de la animación
                setTimeout(() => {
                    toast.classList.add('hidden');
                    toast.classList.remove('toast-leave', 'toast-leave-active');
                    toast.classList.add('toast-enter'); // Reset para la próxima vez
                }, 300);
            }, 10);
        }
    </script>
</x-app-layout>