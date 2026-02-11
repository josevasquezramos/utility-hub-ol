<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel Principal') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            <section>
                <div class="flex items-center gap-2 mb-4 px-4 sm:px-0">
                    <span class="bg-indigo-100 text-indigo-700 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                    </span>
                    <h3 class="ps-2 text-lg font-bold text-gray-700 tracking-wide">Oficina</h3>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4 sm:px-0">
                    <a href="{{ route('schedule.index') }}" class="block group">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-300 border border-transparent hover:border-indigo-300 h-full flex flex-col justify-between">
                            <div class="p-6 flex items-start space-x-4">
                                <div class="p-3 bg-indigo-50 rounded-lg group-hover:bg-indigo-100 transition-colors">
                                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors">Cronograma</h3>
                                    <p class="mt-2 text-sm text-gray-600">Turnos, actividades y calendario mensual.</p>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-end">
                                <span class="text-sm font-medium text-indigo-600 group-hover:translate-x-1 transition-transform">Ingresar &rarr;</span>
                            </div>
                        </div>
                    </a>
                </div>
            </section>

            <section>
                <div class="flex items-center gap-2 mb-4 px-4 sm:px-0">
                    <span class="bg-orange-100 text-orange-700 p-1.5 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                    </span>
                    <h3 class="ps-2 text-lg font-bold text-gray-700 tracking-wide">Logística</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4 sm:px-0">
                    <a href="{{ route('conversions.index') }}" class="block group">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-300 border border-transparent hover:border-purple-300 h-full flex flex-col justify-between">
                            <div class="p-6 flex items-start space-x-4">
                                <div class="p-3 bg-purple-50 rounded-lg group-hover:bg-purple-100 transition-colors">
                                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-purple-600 transition-colors">Conversiones</h3>
                                    <p class="mt-2 text-sm text-gray-600">Calculadora de fracciones y regla digital.</p>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-end">
                                <span class="text-sm font-medium text-purple-600 group-hover:translate-x-1 transition-transform">Ingresar &rarr;</span>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('warehouse3d.index') }}" class="block group">
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-300 border border-transparent hover:border-orange-300 h-full flex flex-col justify-between">
                            <div class="p-6 flex items-start space-x-4">
                                <div class="p-3 bg-orange-50 rounded-lg group-hover:bg-orange-100 transition-colors">
                                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 10l-2 1m0 0l-2-1m2 1v2.5M20 7l-2 1m2-1l-2-1m2 1v2.5M14 4l-2-1-2 1M4 7l2-1M4 7l2 1M4 7v2.5M12 21l-2-1m2 1l2-1m-2 1v-2.5M6 18l-2-1v-2.5M18 18l2-1v-2.5" /></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-orange-600 transition-colors">Almacén 3D</h3>
                                    <p class="mt-2 text-sm text-gray-600">Visor interactivo de espacios y etiquetas.</p>
                                </div>
                            </div>
                            <div class="bg-gray-50 px-6 py-3 border-t border-gray-100 flex justify-end">
                                <span class="text-sm font-medium text-orange-600 group-hover:translate-x-1 transition-transform">Ingresar &rarr;</span>
                            </div>
                        </div>
                    </a>
                </div>
            </section>

        </div>
    </div>
</x-app-layout>