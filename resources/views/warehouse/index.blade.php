<x-app-layout>
    <div class="relative w-full h-[calc(100vh-65px)] bg-gray-900 overflow-hidden">

        <div id="canvas-container" class="absolute inset-0 z-0 w-full h-full"></div>

        <div id="loaderOverlay"
            class="absolute inset-0 bg-gray-900 flex flex-col items-center justify-center z-40 transition-opacity duration-500">
            <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500 mb-4"></div>
            <span class="text-white text-lg font-light tracking-widest">CARGANDO MODELO</span>
        </div>

        <div id="appModal"
            class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm opacity-0 transition-opacity duration-300">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden transform scale-95 transition-transform duration-300"
                id="modalContent">
                <div class="p-6">
                    <h3 id="modalTitle" class="text-lg font-bold text-gray-900 mb-2">Confirmación</h3>
                    <p id="modalMessage" class="text-sm text-gray-500">¿Estás seguro?</p>
                </div>
                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
                    <button id="btnModalCancel"
                        class="hidden px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Cancelar
                    </button>
                    <button id="btnModalConfirm"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 border border-transparent rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Aceptar
                    </button>
                </div>
            </div>
        </div>

        <div
            class="absolute z-10 top-4 left-4 w-full max-w-sm md:w-80 bg-white/95 backdrop-blur-sm shadow-2xl rounded-xl border border-white/20 overflow-hidden flex flex-col max-h-[calc(100%-2rem)] transition-all duration-300">

            <div class="px-3 py-2 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h3 class="font-bold text-gray-700">Explorador</h3>
            </div>

            <div class="p-3 overflow-y-auto custom-scrollbar">

                <div class="flex bg-gray-200/50 p-1 rounded-lg mb-4">
                    <button id="btnModeView"
                        class="flex-1 py-1.5 text-sm font-semibold rounded-md bg-white text-indigo-600 shadow-sm transition-all">
                        Buscar
                    </button>
                    <button id="btnModeEdit"
                        class="flex-1 py-1.5 text-sm font-semibold rounded-md text-gray-500 hover:text-gray-700 transition-all">
                        Editar
                    </button>
                </div>

                <div id="sectionSearch" class="space-y-3">
                    <div>
                        <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Filtrar</label>
                        <input type="text" id="searchInput"
                            class="mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm py-2"
                            placeholder="Buscar etiqueta...">
                    </div>

                    <div id="searchFeedback" class="hidden rounded-lg text-sm"></div>
                </div>

                <div id="sectionEdit" class="hidden space-y-3">
                    <div id="emptySelection"
                        class="text-center py-8 px-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                        <p class="text-sm text-gray-500">Selecciona un bloque</p>
                    </div>

                    <div id="blockEditor" class="hidden flex-col animate-fadeIn">
                        <div class="mb-3">
                            <span
                                class="text-[10px] font-bold text-indigo-500 uppercase tracking-wider">Seleccionado</span>
                            <h2 id="lblBlockName" class="text-lg font-bold text-gray-800 leading-tight">--</h2>
                        </div>

                        <div class="mb-3">
                            <label class="text-xs font-bold text-gray-500">Etiquetas:</label>

                            <div id="tagsList"
                                class="flex flex-wrap gap-1.5 mt-2 min-h-[30px] max-h-[5.5rem] overflow-y-auto custom-scrollbar content-start">
                            </div>
                        </div>

                        <div class="pt-3 border-t border-gray-100">
                            <label class="text-xs font-bold text-gray-700 mb-1 block">Agregar:</label>
                            <div class="flex gap-2">
                                <input type="text" id="newTagInput"
                                    class="flex-1 border-gray-300 rounded-lg text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-1.5"
                                    placeholder="Nueva etiqueta...">
                                <button id="btnSaveTag"
                                    class="bg-indigo-600 text-white px-3 py-1.5 rounded-lg hover:bg-indigo-700 transition shadow-sm font-medium text-sm">
                                    +
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

        <script type="importmap">
            { "imports": { "three": "https://unpkg.com/three@0.160.0/build/three.module.js", "three/addons/": "https://unpkg.com/three@0.160.0/examples/jsm/" } }
        </script>

        <script type="module">
            import * as THREE from 'three';
            import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
            import { OrbitControls } from 'three/addons/controls/OrbitControls.js';
            import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';

            let dbBlocks = @json($blocks);
            const state = {
                mode: 'view',
                selectedBlockName: null,
                meshes: {},
                originalMaterials: {},
                targetCameraPos: null,
                targetLookAt: null
            };

            const container = document.getElementById('canvas-container');
            const scene = new THREE.Scene();
            scene.background = new THREE.Color(0xf0f0f5);
            scene.fog = new THREE.Fog(0xf0f0f5, 20, 80);

            const camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 1000);
            camera.position.set(15, 15, 15);

            const renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true, powerPreference: "high-performance" });
            renderer.setSize(container.clientWidth, container.clientHeight);
            renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
            renderer.outputColorSpace = THREE.SRGBColorSpace;
            renderer.toneMapping = THREE.ACESFilmicToneMapping;
            renderer.toneMappingExposure = 1.2;
            renderer.shadowMap.enabled = true;
            renderer.shadowMap.type = THREE.PCFSoftShadowMap;
            container.appendChild(renderer.domElement);

            const gridHelper = new THREE.GridHelper(50, 50, 0xcccccc, 0xdedede);
            scene.add(gridHelper);

            const pmremGenerator = new THREE.PMREMGenerator(renderer);
            scene.environment = pmremGenerator.fromScene(new RoomEnvironment(), 0.04).texture;

            const ambientLight = new THREE.AmbientLight(0xffffff, 0.7);
            scene.add(ambientLight);

            const dirLight = new THREE.DirectionalLight(0xfff0dd, 1.5);
            dirLight.position.set(10, 30, 20);
            dirLight.castShadow = true;
            dirLight.shadow.mapSize.width = 2048;
            dirLight.shadow.mapSize.height = 2048;
            dirLight.shadow.bias = -0.0001;
            scene.add(dirLight);

            const fillLight = new THREE.DirectionalLight(0xddeeff, 0.5);
            fillLight.position.set(-10, 10, -10);
            scene.add(fillLight);

            const controls = new OrbitControls(camera, renderer.domElement);
            controls.enableDamping = true;
            controls.dampingFactor = 0.05;
            controls.maxPolarAngle = Math.PI / 2;
            controls.screenSpacePanning = false;

            const loader = new GLTFLoader();

            loader.load("{{ asset('models/warehouse.glb') }}", (gltf) => {
                const model = gltf.scene;
                const box = new THREE.Box3().setFromObject(model);
                const center = box.getCenter(new THREE.Vector3());
                model.position.sub(center);
                model.position.y = 0;
                scene.add(model);

                model.traverse((child) => {
                    if (child.isMesh) {
                        child.castShadow = true;
                        child.receiveShadow = true;

                        if (child.material) {
                            child.material.roughness = 1.0;
                            child.material.metalness = 0.0;
                            child.material.envMapIntensity = 0.2;
                            child.material.needsUpdate = true;
                        }

                        const dbRecord = dbBlocks.find(b => b.name === child.name);
                        if (dbRecord) {
                            child.material = child.material.clone();
                            state.meshes[child.name] = child;
                            state.originalMaterials[child.name] = child.material.clone();
                        }
                    }
                });

                const loaderEl = document.getElementById('loaderOverlay');
                if (loaderEl) {
                    loaderEl.style.opacity = '0';
                    setTimeout(() => loaderEl.remove(), 500);
                }
            }, undefined, (err) => console.error(err));

            const raycaster = new THREE.Raycaster();
            const mouse = new THREE.Vector2();
            let isDown = false;
            let startX = 0;
            let startY = 0;

            renderer.domElement.addEventListener('pointerdown', (e) => {
                isDown = true;
                startX = e.clientX;
                startY = e.clientY;
            });

            renderer.domElement.addEventListener('pointerup', (event) => {
                if (!isDown) return;
                isDown = false;

                const diffX = Math.abs(event.clientX - startX);
                const diffY = Math.abs(event.clientY - startY);

                if (diffX > 5 || diffY > 5) return;

                if (state.mode !== 'edit') return;

                const rect = renderer.domElement.getBoundingClientRect();
                mouse.x = ((event.clientX - rect.left) / rect.width) * 2 - 1;
                mouse.y = -((event.clientY - rect.top) / rect.height) * 2 + 1;

                raycaster.setFromCamera(mouse, camera);
                const intersects = raycaster.intersectObjects(scene.children, true);

                if (intersects.length > 0) {
                    let obj = intersects[0].object;
                    while (obj) {
                        if (state.meshes[obj.name]) {
                            selectBlock(obj.name);
                            break;
                        }
                        obj = obj.parent;
                    }
                }
            });

            function selectBlock(name) {
                state.selectedBlockName = name;
                const blockData = dbBlocks.find(b => b.name === name);
                resetColors();

                if (state.meshes[name]) {
                    state.meshes[name].material.color.setHex(0xffaa00);
                }

                document.getElementById('emptySelection').classList.add('hidden');
                const editor = document.getElementById('blockEditor');
                editor.classList.remove('hidden');
                editor.classList.add('flex');
                document.getElementById('lblBlockName').innerText = blockData.name;
                renderTags(blockData.tags);
            }

            function focusOnMesh(mesh) {
                const box = new THREE.Box3().setFromObject(mesh);
                const center = box.getCenter(new THREE.Vector3());

                state.targetLookAt = center.clone();
                state.targetCameraPos = new THREE.Vector3(
                    center.x + 5,
                    center.y + 5,
                    center.z + 5
                );
            }

            function renderTags(tags) {
                const container = document.getElementById('tagsList');
                container.innerHTML = '';
                if (!tags || tags.length === 0) {
                    container.innerHTML = '<span class="text-xs text-gray-400 italic">Sin etiquetas</span>';
                    return;
                }
                tags.forEach(tag => {
                    const badge = document.createElement('div');
                    badge.className = "flex items-center bg-indigo-50 text-indigo-700 text-[10px] font-bold pl-2 pr-1 py-1 rounded-full border border-indigo-100 shadow-sm shrink-0";
                    const text = document.createElement('span');
                    text.innerText = tag;
                    badge.appendChild(text);
                    const closeBtn = document.createElement('button');
                    closeBtn.innerHTML = "&times;";
                    closeBtn.className = "ml-1.5 text-indigo-400 hover:text-red-500 font-bold px-1 rounded transition";
                    closeBtn.onclick = (e) => {
                        e.stopPropagation();
                        deleteTagAction(tag);
                    };
                    badge.appendChild(closeBtn);
                    container.appendChild(badge);
                });
            }

            function resetColors() {
                Object.keys(state.meshes).forEach(name => {
                    const mesh = state.meshes[name];
                    if (state.originalMaterials[name]) {
                        mesh.material.color.copy(state.originalMaterials[name].color);
                    } else {
                        mesh.material.color.set(0xffffff);
                    }
                });
            }

            const normalizeText = (text) => {
                if (!text) return '';
                return text.toString()
                    .toLowerCase()
                    .normalize("NFD")
                    .replace(/[\u0300-\u036f]/g, "")
                    .replace(/\s+/g, "")
                    .replace(/h/g, "")
                    .replace(/v/g, "b")
                    .replace(/y/g, "i")
                    .replace(/z/g, "s")
                    .replace(/c([ei])/g, "s$1")
                    .replace(/ll/g, "i");
            };

            const searchInput = document.getElementById('searchInput');

            searchInput.addEventListener('input', (e) => {
                const term = normalizeText(e.target.value);
                const feedback = document.getElementById('searchFeedback');
                resetColors();
                if (term.length < 2) {
                    feedback.classList.add('hidden');
                    feedback.innerHTML = '';
                    return;
                }
                const matches = dbBlocks.filter(b =>
                    b.tags && b.tags.some(t => normalizeText(t).includes(term))
                );
                feedback.classList.remove('hidden');
                if (matches.length > 0) {
                    let html = `<div class="mb-2 p-2 bg-indigo-50 border border-indigo-100 rounded flex justify-between items-center"><span class="text-indigo-800 font-bold text-xs">Se encontraron ${matches.length} coincidencias</span></div>`;
                    html += `<div class="max-h-60 overflow-y-auto custom-scrollbar space-y-2 pr-1">`;
                    matches.forEach(m => {
                        if (state.meshes[m.name]) state.meshes[m.name].material.color.set(0x00ff00);
                        const matchedTags = m.tags.filter(t => normalizeText(t).includes(term));
                        const tagsBadges = matchedTags.map(tag => `<span class="inline-block bg-yellow-100 text-yellow-800 px-1.5 py-0.5 rounded border border-yellow-200 mr-1 mb-1 shadow-sm">${tag}</span>`).join('');
                        html += `<div class="result-item bg-white p-2.5 rounded border border-gray-200 shadow-sm hover:border-indigo-300 hover:shadow-md cursor-pointer transition group" data-block="${m.name}"><div class="flex items-start justify-between mb-1"><div><span class="text-sm font-bold text-gray-700 group-hover:text-indigo-600 transition block leading-tight">${m.name}</span></div></div><div class="mt-1.5 pt-1.5 border-t border-gray-50"><div class="flex flex-wrap">${tagsBadges}</div></div></div>`;
                    });
                    html += `</div>`;
                    feedback.innerHTML = html;
                } else {
                    feedback.innerHTML = `<div class="p-3 bg-red-50 text-red-500 text-xs border border-red-300 rounded text-center">No se encontraron etiquetas para "${e.target.value}"</div>`;
                }
            });

            document.getElementById('searchFeedback').addEventListener('click', (e) => {
                const item = e.target.closest('.result-item');
                if (item) {
                    const blockName = item.dataset.block;
                    if (state.meshes[blockName]) {
                        const mesh = state.meshes[blockName];
                        resetColors();
                        mesh.material.color.setHex(0x00ffff);
                        focusOnMesh(mesh);
                        setTimeout(() => { mesh.material.color.setHex(0x00ff00); }, 300);
                    }
                }
            });

            document.getElementById('btnSaveTag').addEventListener('click', saveTag);
            document.getElementById('newTagInput').addEventListener('keypress', (e) => { if (e.key === 'Enter') saveTag() });

            async function saveTag() {
                const input = document.getElementById('newTagInput');
                const tag = input.value.trim();
                if (!tag || !state.selectedBlockName) return;
                try {
                    const res = await axios.post("{{ route('warehouse3d.storeTag') }}", { block_name: state.selectedBlockName, tag: tag });
                    if (res.data.success) {
                        const idx = dbBlocks.findIndex(b => b.name === state.selectedBlockName);
                        if (idx !== -1) {
                            dbBlocks[idx].tags = res.data.tags;
                            renderTags(res.data.tags);
                        }
                        input.value = '';
                    } else {
                        showModal('La etiqueta ya existe', 'alert');
                    }
                } catch (e) { console.error(e); }
            }

            async function deleteTagAction(tag) {
                const confirmed = await showModal(`¿Eliminar la etiqueta "${tag}"?`, 'confirm');
                if (!confirmed) return;
                try {
                    const res = await axios.post("{{ route('warehouse3d.deleteTag') }}", { block_name: state.selectedBlockName, tag: tag });
                    if (res.data.success) {
                        const idx = dbBlocks.findIndex(b => b.name === state.selectedBlockName);
                        if (idx !== -1) {
                            dbBlocks[idx].tags = res.data.tags;
                            renderTags(res.data.tags);
                        }
                    }
                } catch (e) { console.error(e); }
            }

            const modal = document.getElementById('appModal');
            const modalContent = document.getElementById('modalContent');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const btnConfirm = document.getElementById('btnModalConfirm');
            const btnCancel = document.getElementById('btnModalCancel');

            function showModal(message, type = 'alert') {
                return new Promise((resolve) => {
                    modalTitle.innerText = type === 'confirm' ? 'Confirmación' : 'Atención';
                    modalMessage.innerText = message;
                    if (type === 'confirm') {
                        btnCancel.classList.remove('hidden');
                        btnConfirm.classList.replace('w-full', 'w-auto');
                    } else {
                        btnCancel.classList.add('hidden');
                        btnConfirm.classList.add('w-full');
                    }
                    modal.classList.remove('hidden');
                    setTimeout(() => {
                        modal.classList.remove('opacity-0');
                        modalContent.classList.remove('scale-95');
                        modalContent.classList.add('scale-100');
                    }, 10);
                    const close = (result) => {
                        modal.classList.add('opacity-0');
                        modalContent.classList.remove('scale-100');
                        modalContent.classList.add('scale-95');
                        setTimeout(() => { modal.classList.add('hidden'); resolve(result); }, 300);
                    };
                    btnConfirm.onclick = () => close(true);
                    btnCancel.onclick = () => close(false);
                });
            }

            const btnView = document.getElementById('btnModeView');
            const btnEdit = document.getElementById('btnModeEdit');
            btnView.onclick = () => setMode('view');
            btnEdit.onclick = () => setMode('edit');

            function setMode(mode) {
                state.mode = mode;
                resetColors();
                if (mode === 'view') {
                    btnView.className = "flex-1 py-1.5 text-sm font-semibold rounded-md bg-white text-indigo-600 shadow-sm transition-all";
                    btnEdit.className = "flex-1 py-1.5 text-sm font-semibold rounded-md text-gray-500 hover:text-gray-700 transition-all";
                    document.getElementById('sectionSearch').classList.remove('hidden');
                    document.getElementById('sectionEdit').classList.add('hidden');
                } else {
                    btnEdit.className = "flex-1 py-1.5 text-sm font-semibold rounded-md bg-white text-indigo-600 shadow-sm transition-all";
                    btnView.className = "flex-1 py-1.5 text-sm font-semibold rounded-md text-gray-500 hover:text-gray-700 transition-all";
                    document.getElementById('sectionEdit').classList.remove('hidden');
                    document.getElementById('sectionSearch').classList.add('hidden');
                }
            }

            function animate() {
                requestAnimationFrame(animate);

                if (state.targetCameraPos && state.targetLookAt) {
                    camera.position.lerp(state.targetCameraPos, 0.05);
                    controls.target.lerp(state.targetLookAt, 0.05);

                    if (camera.position.distanceTo(state.targetCameraPos) < 0.01) {
                        state.targetCameraPos = null;
                        state.targetLookAt = null;
                    }
                }

                controls.update();
                renderer.render(scene, camera);
            }
            animate();

            window.addEventListener('resize', () => {
                camera.aspect = container.clientWidth / container.clientHeight;
                camera.updateProjectionMatrix();
                renderer.setSize(container.clientWidth, container.clientHeight);
            });
        </script>

        <style>
            .custom-scrollbar::-webkit-scrollbar {
                width: 4px;
            }

            .custom-scrollbar::-webkit-scrollbar-track {
                background: #f1f1f1;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb {
                background: #c7c7c7;
                border-radius: 4px;
            }

            .custom-scrollbar::-webkit-scrollbar-thumb:hover {
                background: #a8a8a8;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(5px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fadeIn {
                animation: fadeIn 0.3s ease-out forwards;
            }
        </style>
    @endpush
</x-app-layout>