<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ ucfirst($date->locale('es')->monthName) }} {{ $date->year }}
            </h2>

            <div class="flex gap-4">
                <a href="{{ route('schedule.index', ['date' => $date->copy()->subMonth()->format('Y-m-d')]) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    <span class="hidden md:inline">Anterior</span>
                </a>
                <a href="{{ route('schedule.index', ['date' => $date->copy()->addMonth()->format('Y-m-d')]) }}"
                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <span class="hidden md:inline">Siguiente</span>
                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6">
                <button onclick="openModal('activityModal')"
                    class="inline-flex items-center px-3 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Actividad
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-10 border-r border-gray-200 min-w-[120px]">
                                    Fecha
                                </th>
                                @foreach($activities as $activity)
                                    <th
                                        class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-[150px] relative group">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="truncate">{{ $activity->name }}</span>

                                            <button type="button"
                                                onclick="openDeleteModal('{{ $activity->id }}', '{{ addslashes($activity->name) }}')"
                                                class="flex-shrink-0 text-gray-400 hover:text-red-600 transition-colors duration-150"
                                                title="Eliminar actividad">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($period as $day)
                                @php
                                    $isSunday = $day->dayOfWeek === 0;
                                    $isHoliday = in_array($day->format('d-m'), $holidays);
                                    $rowClass = ($isSunday || $isHoliday)
                                        ? 'bg-red-100'
                                        : 'bg-white hover:bg-gray-50';
                                    $dayName = ucfirst($day->locale('es')->dayName);
                                    $dateStr = $day->format('Y-m-d');
                                @endphp

                                <tr class="{{ $rowClass }} transition-colors duration-150">
                                    <td
                                        class="px-4 py-3 text-sm sticky left-0 {{ $rowClass }} z-10 border-r border-gray-200">
                                        <div class="font-medium text-gray-900">
                                            {{ $day->format('d/m/Y') }} <br> <span
                                                class="text-gray-500">{{ $dayName }}</span>
                                        </div>
                                    </td>

                                    @foreach($activities as $activity)
                                        @php
                                            $key = $dateStr . '_' . $activity->id;
                                            $value = $assignments[$key] ?? '';
                                        @endphp
                                        <td class="p-0 border-l border-gray-100">
                                            <input type="text"
                                                class="w-full h-16 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 border-0 bg-transparent focus:ring-2 focus:ring-inset focus:ring-indigo-500 focus:bg-white transition-all duration-150"
                                                value="{{ $value }}" placeholder=""
                                                onblur="saveAssignment(this, '{{ $dateStr }}', '{{ $activity->id }}')">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="activityModal"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Agregar Nueva Actividad</h3>
            </div>
            <form action="{{ route('schedule.activity.store') }}" method="POST" id="activityForm">
                @csrf
                <div class="px-6 py-4">
                    <div>
                        <label for="activityName" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre de la actividad
                        </label>
                        <input type="text" name="name" id="activityName"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                            placeholder="Ej: Limpieza" required>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex justify-end gap-3">
                    <button type="button" onclick="closeModal('activityModal')"
                        class="px-4 py-2 border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Agregar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal"
        class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden flex items-center justify-center p-4 z-50">
        <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Confirmar Eliminación</h3>
            </div>
            <div class="px-6 py-4">
                <p class="text-sm text-gray-600">¿Está seguro de eliminar la actividad "<span
                        id="activityNameToDelete"></span>"?</p>
            </div>
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg flex justify-end gap-3">
                <button type="button" onclick="closeModal('deleteModal')"
                    class="px-4 py-2 border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancelar
                </button>
                <form method="POST" id="deleteForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div id="successToast"
        class="fixed top-4 right-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg shadow-lg hidden z-50 max-w-md">
        <div class="flex items-start justify-between">
            <div>
                <span id="successMessage"></span>
            </div>
            <button onclick="hideToast('successToast')" class="ml-4 text-green-600 hover:text-green-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div id="errorToast"
        class="fixed top-4 right-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-lg hidden z-50 max-w-md">
        <div class="flex items-start justify-between">
            <div>
                <span id="errorMessage"></span>
            </div>
            <button onclick="hideToast('errorToast')" class="ml-4 text-red-600 hover:text-red-800">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <script>
        let currentActivityId = null;

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
            const input = modal.querySelector('input[type="text"]');
            if (input) {
                setTimeout(() => {
                    input.focus();
                    input.select();
                }, 50);
            }
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function openDeleteModal(activityId, activityName) {
            currentActivityId = activityId;
            document.getElementById('activityNameToDelete').textContent = activityName;
            document.getElementById('deleteForm').action = `/schedule/activity/${activityId}`;
            openModal('deleteModal');
        }

        function showToast(type, message) {
            const successToast = document.getElementById('successToast');
            const errorToast = document.getElementById('errorToast');
            successToast.classList.add('hidden');
            errorToast.classList.add('hidden');

            if (type === 'success') {
                document.getElementById('successMessage').textContent = message;
                successToast.classList.remove('hidden');
                setTimeout(() => successToast.classList.add('hidden'), 5000);
            } else {
                document.getElementById('errorMessage').textContent = message;
                errorToast.classList.remove('hidden');
                setTimeout(() => errorToast.classList.add('hidden'), 5000);
            }
        }

        function hideToast(toastId) {
            document.getElementById(toastId).classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function () {

            const pendingSuccess = sessionStorage.getItem('schedule_success');
            if (pendingSuccess) {
                showToast('success', pendingSuccess);
                sessionStorage.removeItem('schedule_success');
            }

            const modals = ['activityModal', 'deleteModal'];
            modals.forEach(modalId => {
                const modal = document.getElementById(modalId);
                if (modal) {
                    modal.addEventListener('click', function (e) {
                        if (e.target === this) closeModal(modalId);
                    });
                }
            });

            @if(session('success'))
                showToast('success', "{{ session('success') }}");
            @endif
            @if($errors->any())
                showToast('error', "{{ $errors->first() }}");
            @endif
    });

        async function saveAssignment(input, date, activityId) {
            const originalValue = input.defaultValue;
            const newValue = input.value.trim();

            if (originalValue === newValue) return;

            input.classList.remove('bg-red-100', 'ring-red-400');
            input.classList.add('bg-yellow-50', 'ring-2', 'ring-yellow-400');

            try {
                const response = await fetch("{{ route('schedule.assignment.update') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({
                        date: date,
                        activity_id: activityId,
                        assignee_name: newValue
                    })
                });

                if (response.ok) {
                    input.classList.remove('bg-yellow-50', 'ring-yellow-400');
                    input.classList.add('bg-green-50', 'ring-2', 'ring-green-400');

                    setTimeout(() => {
                        input.classList.remove('bg-green-50', 'ring-2', 'ring-green-400');
                    }, 1000);

                    input.defaultValue = newValue;
                } else {
                    throw new Error('Error al guardar');
                }
            } catch (error) {
                console.error(error);
                input.classList.remove('bg-yellow-50', 'ring-yellow-400');
                input.classList.add('bg-red-100', 'ring-2', 'ring-red-400');
                showToast('error', 'No se pudo guardar el cambio.');
            }
        }

        document.getElementById('activityForm')?.addEventListener('submit', async function (e) {
            e.preventDefault();
            handleFormSubmit(this, 'POST');
        });

        document.getElementById('deleteForm')?.addEventListener('submit', async function (e) {
            e.preventDefault();
            handleFormSubmit(this, 'DELETE');
        });

        async function handleFormSubmit(form, method) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            const formData = new FormData(form);

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="animate-spin inline-block mr-2">↻</span> Procesando...';

            try {
                const options = {
                    method: method,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                };

                if (method === 'POST') {
                    options.body = formData;
                }

                const response = await fetch(form.action, options);
                const data = await response.json();

                if (data.success) {
                    sessionStorage.setItem('schedule_success', data.message);
                    window.location.reload();
                } else {
                    showToast('error', data.message || 'Ocurrió un error.');
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalText;
                    closeModal(form.closest('.fixed').id);
                }

            } catch (error) {
                console.error('Error:', error);
                showToast('error', 'Error de conexión.');
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        }
    </script>

    <style>
        .min-w-full td input {
            height: 4rem !important;
            min-height: 4rem !important;
        }

        .overflow-x-auto {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f7fafc;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background-color: #cbd5e0;
            border-radius: 4px;
        }
    </style>
</x-app-layout>