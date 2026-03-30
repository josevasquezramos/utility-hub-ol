<x-app-layout>
    <div class="flex flex-col h-[calc(100vh-80px)] max-w-4xl mx-auto sm:px-6 lg:px-8 py-6">
        
        <div id="chat-box" class="flex-1 overflow-y-auto px-2 space-y-6 flex flex-col">
            <div class="text-center text-gray-400 text-sm m-auto" id="welcome-msg">
                Haz tu consulta. El historial se limpiará con cada nueva pregunta.
            </div>
        </div>

        <form id="chat-form" class="flex gap-2 pt-4 mt-auto">
            @csrf
            <input type="text" id="pregunta" class="flex-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-full shadow-sm px-5 py-3 text-sm" placeholder="cuantas evidencias se subieron el febrero de 2026" required autocomplete="off">
            
            <button type="submit" id="btn-enviar" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition ease-in-out duration-150 shadow-sm">
                Enviar
            </button>
        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <script>
        document.getElementById('chat-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const inputPregunta = document.getElementById('pregunta');
            const chatBox = document.getElementById('chat-box');
            const btnEnviar = document.getElementById('btn-enviar');
            const token = document.querySelector('input[name="_token"]').value;
            
            const pregunta = inputPregunta.value.trim();
            if (!pregunta) return;

            // 1. Limpiar historial anterior para dejar solo el chat actual
            chatBox.innerHTML = '';

            // 2. Mostrar la pregunta del usuario en globo blanco
            agregarMensaje('Tú', pregunta, 'user');
            inputPregunta.value = '';
            
            // Bloquear input mientras carga
            inputPregunta.disabled = true;
            btnEnviar.disabled = true;
            btnEnviar.innerText = 'Pensando...';

            // 3. Hacer la petición al controlador (Fetch)
            try {
                const response = await fetch("{{ route('chat.ask') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify({ pregunta: pregunta })
                });

                const data = await response.json();

                // 4. Mostrar la respuesta
                if (data.error) {
                    agregarMensaje('Error', data.error, 'error');
                } else {
                    const htmlFormateado = marked.parse(data.respuesta);
                    agregarMensaje('Pancho Julio', htmlFormateado, 'ai', true);
                    
                    if (data.sql_debug) {
                        console.log("SQL Ejecutado:", data.sql_debug);
                    }
                }

            } catch (error) {
                agregarMensaje('Error', 'Hubo un problema de conexión con el servidor.', 'error');
            } finally {
                // Desbloquear input
                inputPregunta.disabled = false;
                btnEnviar.disabled = false;
                btnEnviar.innerText = 'Enviar';
                inputPregunta.focus();
                
                // Hacer auto-scroll hacia abajo
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        });

        function agregarMensaje(remitente, contenido, tipo, esHTML = false) {
            const chatBox = document.getElementById('chat-box');
            
            const divWrapper = document.createElement('div');
            divWrapper.className = 'flex flex-col w-full';
            
            if (tipo === 'user') {
                // Globo BLANCO para el usuario alineado a la derecha
                const burbuja = document.createElement('div');
                burbuja.className = 'bg-white text-gray-800 px-5 py-3 rounded-2xl rounded-tr-sm self-end max-w-[85%] shadow-sm text-sm border border-gray-100';
                burbuja.innerText = contenido;
                divWrapper.appendChild(burbuja);

            } else if (tipo === 'ai') {
                // Contenedor principal de Pancho Julio
                const aiWrapper = document.createElement('div');
                aiWrapper.className = 'flex flex-col self-start w-full mt-2';

                // Cabecera: Icono + Nombre
                const header = document.createElement('div');
                header.className = 'flex items-center gap-2 mb-2';
                
                // Icono SVG de Bot (Pancho Julio)
                const icono = document.createElement('div');
                icono.className = 'flex items-center justify-center w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 shadow-sm border border-indigo-200';
                icono.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><circle cx="12" cy="5" r="2"/><path d="M12 7v4"/><line x1="8" y1="16" x2="8" y2="16"/><line x1="16" y1="16" x2="16" y2="16"/></svg>`;

                // Nombre "Pancho Julio"
                const nombre = document.createElement('span');
                nombre.className = 'text-sm font-bold text-gray-700 tracking-wide';
                nombre.innerText = remitente; // Pasará 'Pancho Julio'

                header.appendChild(icono);
                header.appendChild(nombre);

                // Respuesta libre dibujada directamente en el fondo
                const cuerpo = document.createElement('div');
                // Se agregó ml-10 para que el texto se alinee con el nombre y no se meta debajo del ícono
                cuerpo.className = 'prose prose-sm max-w-none text-gray-800 ml-10'; 
                cuerpo.innerHTML = contenido; 

                aiWrapper.appendChild(header);
                aiWrapper.appendChild(cuerpo);
                divWrapper.appendChild(aiWrapper);

            } else if (tipo === 'error') {
                // Mensaje de error suelto en texto rojo
                const errorBox = document.createElement('div');
                errorBox.className = 'text-red-600 self-start w-full text-sm mt-2 font-medium';
                errorBox.innerText = remitente + ': ' + contenido;
                divWrapper.appendChild(errorBox);
            }

            chatBox.appendChild(divWrapper);
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>
</x-app-layout>