<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/airbnb.css">

    <style>
        .flatpickr-wrapper {
            width: 100% !important;
            display: block !important;
        }

        .flatpickr-input,
        input.flatpickr-input {
            width: 100% !important;
            box-sizing: border-box !important;
        }

        .flatpickr-calendar {
            z-index: 999999 !important;
        }

        .min-w-full td input {
            height: 4rem !important;
            min-height: 4rem !important;
        }

        .modal-backdrop {
            transition: opacity 0.2s ease-out;
        }

        .modal-panel {
            transition: all 0.2s ease-out;
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
    </style>

    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ ucfirst($date->locale('es')->monthName) }} {{ $date->year }}
            </h2>

            <div class="flex gap-2">
                <a href="{{ route('schedule.index', ['date' => $date->copy()->subMonth()->format('Y-m-d')]) }}"
                    class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Anterior
                </a>
                <a href="{{ route('schedule.index', ['date' => $date->copy()->addMonth()->format('Y-m-d')]) }}"
                    class="inline-flex items-center px-3 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none transition-colors shadow-sm">
                    Siguiente
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-6 flex justify-end items-center gap-x-3">
                <button onclick="openModal('activityModal')"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Actividad
                </button>
                <button onclick="openModal('excelModal')"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Exportar
                </button>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider sticky left-0 bg-gray-50 z-20 border-r border-gray-200 min-w-[120px]">
                                    Fecha
                                </th>
                                @foreach($activities as $activity)
                                    <th
                                        class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider min-w-[150px] relative group border-r border-gray-100 last:border-0">
                                        <div class="flex items-center justify-between gap-2">
                                            <span class="truncate"
                                                title="{{ $activity->name }}">{{ $activity->name }}</span>
                                            <div class="flex items-center gap-1">
                                                <button type="button"
                                                    onclick="openExportModal('{{ $activity->id }}', '{{ addslashes($activity->name) }}')"
                                                    class="text-blue-500 hover:text-blue-700 p-1 transition-colors"
                                                    title="Descargar PDF">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                                    </svg>
                                                </button>
                                                <button type="button"
                                                    onclick="openDeleteModal('{{ $activity->id }}', '{{ addslashes($activity->name) }}')"
                                                    class="text-gray-400 hover:text-red-600 p-1 transition-colors"
                                                    title="Eliminar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
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
                                    $rowClass = ($isSunday || $isHoliday) ? 'bg-red-50' : 'bg-white hover:bg-gray-50';
                                    $dayName = ucfirst($day->locale('es')->dayName);
                                    $dateStr = $day->format('Y-m-d');
                                @endphp
                                <tr class="{{ $rowClass }} transition-colors duration-150 group">
                                    <td
                                        class="px-4 py-3 text-sm sticky left-0 {{ $rowClass }} group-hover:bg-gray-50 z-10 border-r border-gray-200">
                                        <div class="font-medium text-gray-900">
                                            {{ $day->format('d/m/Y') }} <br>
                                            <span
                                                class="text-xs text-gray-500 uppercase tracking-wide">{{ $dayName }}</span>
                                        </div>
                                    </td>
                                    @foreach($activities as $activity)
                                        @php
                                            $key = $dateStr . '_' . $activity->id;
                                            $value = $assignments[$key] ?? '';
                                        @endphp
                                        <td class="p-0 border-r border-gray-100 last:border-0 relative">
                                            <input type="text"
                                                class="w-full h-16 px-3 py-2 text-sm text-gray-900 placeholder-gray-400 border-0 bg-transparent focus:ring-2 focus:ring-inset focus:ring-indigo-500 focus:bg-white transition-all duration-150"
                                                value="{{ $value }}"
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

    <div id="activityModal" class="fixed inset-0 z-50 flex items-center justify-center hidden modal-closed">
        <div class="absolute inset-0 bg-gray-900/50 modal-backdrop" onclick="closeModal('activityModal')"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-hidden modal-panel">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Nueva Actividad</h3>
                <button onclick="closeModal('activityModal')"
                    class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
            </div>
            <form action="{{ route('schedule.activity.store') }}" method="POST" id="activityForm">
                @csrf
                <div class="px-6 py-6 space-y-4">
                    <div>
                        <label for="activityName" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                        <input type="text" name="name" id="activityName"
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2"
                            placeholder="Ej: Mantenimiento" required>
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 border-t border-gray-200">
                    <button type="button" onclick="closeModal('activityModal')"
                        class="px-4 py-2 bg-white text-gray-700 border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50 shadow-sm">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 shadow-sm">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 z-50 flex items-center justify-center hidden modal-closed">
        <div class="absolute inset-0 bg-gray-900/50 modal-backdrop" onclick="closeModal('deleteModal')"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-sm mx-4 overflow-hidden modal-panel">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Eliminar Actividad</h3>
                <button onclick="closeModal('deleteModal')"
                    class="text-gray-400 hover:text-gray-600 font-bold text-xl">&times;</button>
            </div>
            <div class="p-6">
                <p class="text-sm text-gray-600">
                    ¿Estás seguro de que deseas eliminar "<span id="activityNameToDelete"
                        class="font-bold text-gray-800"></span>"?
                </p>
            </div>
            <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 border-t border-gray-200">
                <button type="button" onclick="closeModal('deleteModal')"
                    class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm">Cancelar</button>
                <form method="POST" id="deleteForm">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 rounded-md text-sm font-medium text-white hover:bg-red-700 shadow-sm">Eliminar</button>
                </form>
            </div>
        </div>
    </div>

    <div id="exportModal" class="fixed inset-0 z-50 flex items-center justify-center hidden modal-closed">
        <div class="absolute inset-0 bg-gray-900/50 modal-backdrop" onclick="closeModal('exportModal')"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-visible modal-panel">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                <h3 class="text-lg font-bold text-gray-800">Generar PDF</h3>
                <p class="text-xs text-gray-500 mt-0.5">Actividad: <span id="exportActivityName"
                        class="font-bold"></span></p>
            </div>
            <form action="{{ route('schedule.export-pdf') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="activity_id" id="exportActivityId">
                <div class="px-6 py-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rango de Fechas</label>
                    <input type="text" name="date_range" id="dateRangePicker"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 pl-3 bg-white"
                        placeholder="Seleccionar fechas..." required>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 border-t border-gray-200 rounded-b-xl">
                    <button type="button" onclick="closeModal('exportModal')"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 shadow-sm">Descargar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="excelModal" class="fixed inset-0 z-50 flex items-center justify-center hidden modal-closed">
        <div class="absolute inset-0 bg-gray-900/50 modal-backdrop" onclick="closeModal('excelModal')"></div>
        <div class="relative bg-white rounded-xl shadow-xl w-full max-w-md mx-4 overflow-visible modal-panel">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 rounded-t-xl">
                <h3 class="text-lg font-bold text-gray-800">Reporte Excel</h3>
                <p class="text-xs text-gray-500 mt-0.5">Exportar todo el cronograma</p>
            </div>
            <form action="{{ route('schedule.export-excel') }}" method="POST">
                @csrf
                <div class="px-6 py-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rango de Fechas</label>
                    <input type="text" name="date_range" id="excelDateRangePicker"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-2 pl-3 bg-white"
                        placeholder="Seleccionar fechas..." required>
                </div>
                <div class="px-6 py-4 bg-gray-50 flex justify-end gap-3 border-t border-gray-200 rounded-b-xl">
                    <button type="button" onclick="closeModal('excelModal')"
                        class="px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm">Cancelar</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded-md text-sm font-medium hover:bg-green-700 shadow-sm">Descargar</button>
                </div>
            </form>
        </div>
    </div>

    <div id="successToast"
        class="fixed top-5 right-5 bg-white border border-green-200 text-gray-800 px-6 py-4 rounded shadow-lg hidden z-50 flex items-center gap-4">
        <div>
            <h4 class="font-bold text-sm text-green-700">Éxito</h4>
            <span id="successMessage" class="text-sm"></span>
        </div>
        <button onclick="hideToast('successToast')" class="text-gray-400 hover:text-gray-600 font-bold">&times;</button>
    </div>

    <div id="errorToast"
        class="fixed top-5 right-5 bg-white border border-red-200 text-gray-800 px-6 py-4 rounded shadow-lg hidden z-50 flex items-center gap-4">
        <div>
            <h4 class="font-bold text-sm text-red-700">Error</h4>
            <span id="errorMessage" class="text-sm"></span>
        </div>
        <button onclick="hideToast('errorToast')" class="text-gray-400 hover:text-gray-600 font-bold">&times;</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://npmcdn.com/flatpickr/dist/l10n/es.js"></script>

    <script>
        let fpInstance, fpExcelInstance;

        document.addEventListener('DOMContentLoaded', function () {
            const commonConfig = {
                mode: "range", dateFormat: "Y-m-d", locale: "es",
                defaultDate: ["{{ $date->copy()->startOfMonth()->format('Y-m-d') }}", "{{ $date->copy()->endOfMonth()->format('Y-m-d') }}"],
                altInput: true, altFormat: "j F, Y",
                static: false
            };

            fpInstance = flatpickr("#dateRangePicker", {
                ...commonConfig,
                altInputClass: "w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-2 pl-3 bg-white"
            });

            fpExcelInstance = flatpickr("#excelDateRangePicker", {
                ...commonConfig,
                altInputClass: "w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 py-2 pl-3 bg-white"
            });

            const pendingSuccess = sessionStorage.getItem('schedule_success');
            if (pendingSuccess) { showToast('success', pendingSuccess); sessionStorage.removeItem('schedule_success'); }

            @if(session('success')) showToast('success', "{{ session('success') }}"); @endif
            @if($errors->any()) showToast('error', "{{ $errors->first() }}"); @endif
        });

        function openModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.classList.remove('modal-closed');
                modal.classList.add('modal-open');
            }, 10);

            const input = modal.querySelector('input[type="text"]');
            if (input && input.id !== 'dateRangePicker' && input.id !== 'excelDateRangePicker') {
                setTimeout(() => input.focus(), 100);
            }
        }

        function closeModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.remove('modal-open');
            modal.classList.add('modal-closed');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }

        function openDeleteModal(activityId, activityName) {
            document.getElementById('activityNameToDelete').textContent = activityName;
            document.getElementById('deleteForm').action = `/schedule/activity/${activityId}`;
            openModal('deleteModal');
        }

        function openExportModal(activityId, activityName) {
            document.getElementById('exportActivityId').value = activityId;
            document.getElementById('exportActivityName').innerText = activityName;
            fpInstance.setDate(["{{ $date->copy()->startOfMonth()->format('Y-m-d') }}", "{{ $date->copy()->endOfMonth()->format('Y-m-d') }}"], true);
            openModal('exportModal');
        }

        function showToast(type, message) {
            const toast = document.getElementById(type + 'Toast');
            const msgId = type + 'Message';
            document.getElementById(msgId).textContent = message;
            toast.classList.remove('hidden');
            setTimeout(() => hideToast(type + 'Toast'), 4000);
        }

        function hideToast(toastId) {
            document.getElementById(toastId).classList.add('hidden');
        }

        async function saveAssignment(input, date, activityId) {
            const originalValue = input.defaultValue;
            const newValue = input.value.trim();
            if (originalValue === newValue) return;

            input.classList.remove('bg-red-50');
            input.classList.add('bg-yellow-50');

            try {
                const response = await fetch("{{ route('schedule.assignment.update') }}", {
                    method: "POST",
                    headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
                    body: JSON.stringify({ date: date, activity_id: activityId, assignee_name: newValue })
                });

                if (response.ok) {
                    input.classList.remove('bg-yellow-50');
                    input.classList.add('bg-green-50');
                    setTimeout(() => input.classList.remove('bg-green-50'), 1000);
                    input.defaultValue = newValue;
                } else throw new Error();
            } catch {
                input.classList.remove('bg-yellow-50');
                input.classList.add('bg-red-50');
                showToast('error', 'Error al guardar');
            }
        }

        ['activityForm', 'deleteForm'].forEach(id => {
            const form = document.getElementById(id);
            if (form) {
                form.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const btn = this.querySelector('button[type="submit"]');
                    const originalContent = btn.innerHTML;
                    btn.disabled = true;
                    btn.innerHTML = '...';

                    try {
                        const formData = new FormData(this);
                        const method = this.querySelector('input[name="_method"]')?.value || this.method;

                        const res = await fetch(this.action, {
                            method: method,
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();

                        if (data.success) {
                            sessionStorage.setItem('schedule_success', data.message);
                            window.location.reload();
                        } else {
                            showToast('error', data.message || 'Error');
                            closeModal(this.closest('.fixed').id);
                            btn.disabled = false;
                            btn.innerHTML = originalContent;
                        }
                    } catch {
                        showToast('error', 'Error de conexión');
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                    }
                });
            }
        });
    </script>
</x-app-layout>