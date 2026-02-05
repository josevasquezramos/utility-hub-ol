<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Conversor de Medidas') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Calculadora de Conversión</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Pulgadas
                            </label>
                            <input type="text" id="inputInch" placeholder="Ej: 1/2, 1 1/2, 0.5"
                                class="w-full px-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <div class="mt-4 p-4 bg-gray-50 rounded-md min-h-[120px]">
                                <div class="text-sm text-gray-600 mb-2">Resultado:</div>
                                <div id="resultMm" class="text-2xl font-bold text-indigo-600 h-8">0 mm</div>
                                <div id="resultMmCm" class="text-base text-gray-700 mt-1 h-6"></div>
                                <div id="resultMmDecimal" class="text-sm text-gray-500 mt-1 h-5"></div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Milímetros
                            </label>
                            <input type="number" id="inputMm" placeholder="Ej: 25.4" step="0.01"
                                class="w-full px-3 py-2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <div class="mt-4 p-4 bg-gray-50 rounded-md min-h-[120px]">
                                <div class="text-sm text-gray-600 mb-2">Resultado:</div>
                                <div id="resultInchFraction" class="text-2xl font-bold text-indigo-600 h-8">0"</div>
                                <div id="resultInch" class="text-base text-gray-700 mt-1 h-6"></div>
                                <div id="resultInchCm" class="text-sm text-gray-500 mt-1 h-5"></div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Regla Virtual</h3>
                    <p class="text-sm text-gray-600 mb-4">Deslice horizontalmente para explorar las equivalencias</p>

                    <div class="overflow-x-auto border border-gray-300 rounded-lg">
                        <div id="rulerContainer" style="min-width: 100%; width: max-content;">
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function gcd(a, b) {
            return b === 0 ? a : gcd(b, a % b);
        }

        function decimalToFraction(decimal, maxDenominator = 16) {
            const whole = Math.floor(decimal);
            const fractional = decimal - whole;

            if (Math.abs(fractional) < 0.0001) {
                return whole > 0 ? `${whole}"` : '0"';
            }

            let bestNumerator = 0;
            let bestDenominator = 1;
            let bestError = Math.abs(fractional);

            for (let den = 1; den <= maxDenominator; den++) {
                let num = Math.round(fractional * den);
                if (num > den) continue;

                const error = Math.abs(fractional - (num / den));

                if (error < bestError) {
                    bestError = error;
                    bestNumerator = num;
                    bestDenominator = den;
                }
            }

            if (bestNumerator === 0) {
                return whole > 0 ? `${whole}"` : '0"';
            }

            const divisor = gcd(bestNumerator, bestDenominator);
            const simplifiedNum = bestNumerator / divisor;
            const simplifiedDen = bestDenominator / divisor;

            if (simplifiedNum === simplifiedDen) {
                return `${whole + 1}"`;
            }

            if (whole > 0) {
                return `${whole} ${simplifiedNum}/${simplifiedDen}"`;
            } else {
                return `${simplifiedNum}/${simplifiedDen}"`;
            }
        }

        function parseInchInput(input) {
            input = input.trim();
            if (!input) return 0;

            if (input.includes(' ') && input.includes('/')) {
                const parts = input.split(' ');
                const whole = parseFloat(parts[0]);
                const fraction = parts[1].split('/');
                return whole + (parseFloat(fraction[0]) / parseFloat(fraction[1]));
            }

            if (input.includes('/')) {
                const parts = input.split('/');
                return parseFloat(parts[0]) / parseFloat(parts[1]);
            }

            return parseFloat(input);
        }

        document.getElementById('inputInch').addEventListener('input', function () {
            const input = this.value.trim();

            if (!input) {
                document.getElementById('resultMm').textContent = '0 mm';
                document.getElementById('resultMmCm').textContent = '';
                document.getElementById('resultMmDecimal').textContent = '';
                return;
            }

            const inches = parseInchInput(input);

            if (isNaN(inches)) {
                document.getElementById('resultMm').textContent = 'Valor inválido';
                document.getElementById('resultMmCm').textContent = '';
                document.getElementById('resultMmDecimal').textContent = '';
                return;
            }

            const mm = inches * 25.4;
            const cm = mm / 10;

            document.getElementById('resultMm').textContent = mm.toFixed(3) + ' mm';
            document.getElementById('resultMmCm').textContent = cm.toFixed(3) + ' cm';
            document.getElementById('resultMmDecimal').textContent = inches.toFixed(6) + '"';
        });

        document.getElementById('inputMm').addEventListener('input', function () {
            const input = parseFloat(this.value);

            if (isNaN(input) || !this.value) {
                document.getElementById('resultInchFraction').textContent = '0"';
                document.getElementById('resultInch').textContent = '';
                document.getElementById('resultInchCm').textContent = '';
                return;
            }

            const inches = input / 25.4;
            const fraction = decimalToFraction(inches, 16);
            const cm = input / 10;

            document.getElementById('resultInchFraction').textContent = fraction;

            document.getElementById('resultInch').textContent = inches.toFixed(6) + '"';


            document.getElementById('resultInchCm').textContent = cm.toFixed(3) + ' cm';
        });

        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('rulerContainer');
            const totalInches = 24;
            const pixelsPerSixteenth = 65;

            const rulerDiv = document.createElement('div');
            rulerDiv.style.cssText = 'display: flex; padding: 20px; background: white;';

            for (let inch = 0; inch <= totalInches; inch++) {
                for (let sixteenth = 0; sixteenth < 16; sixteenth++) {
                    if (inch === totalInches && sixteenth > 0) break;

                    const decimal = inch + (sixteenth / 16);
                    const mm = decimal * 25.4;

                    let fractionStr = '';
                    let lineHeight = 30;
                    let lineWidth = 1;

                    if (sixteenth === 0) {
                        fractionStr = inch.toString();
                        lineHeight = 100;
                        lineWidth = 3;
                    } else {
                        const gcdVal = gcd(sixteenth, 16);
                        const num = sixteenth / gcdVal;
                        const den = 16 / gcdVal;

                        if (inch > 0) {
                            fractionStr = `${inch} ${num}/${den}`;
                        } else {
                            fractionStr = `${num}/${den}`;
                        }

                        if (den === 2) {
                            lineHeight = 85;
                            lineWidth = 2.5;
                        } else if (den === 4) {
                            lineHeight = 70;
                            lineWidth = 2;
                        } else if (den === 8) {
                            lineHeight = 55;
                            lineWidth = 1.5;
                        } else {
                            lineHeight = 40;
                            lineWidth = 1;
                        }
                    }

                    const markDiv = document.createElement('div');
                    markDiv.style.cssText = `
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        width: ${pixelsPerSixteenth}px;
                    `;

                    const line = document.createElement('div');
                    line.style.cssText = `
                        width: ${lineWidth}px;
                        height: ${lineHeight}px;
                        background-color: ${lineWidth >= 2.5 ? '#1F2937' : '#4B5563'};
                        margin-bottom: 6px;
                    `;

                    const fractionLabel = document.createElement('div');
                    const fontSize = lineHeight >= 85 ? '16px' :
                        lineHeight >= 70 ? '14px' :
                            lineHeight >= 55 ? '13px' :
                                '12px';

                    fractionLabel.style.cssText = `
                        font-size: ${fontSize};
                        font-weight: ${lineHeight >= 85 ? 'bold' : lineHeight >= 70 ? '600' : 'normal'};
                        color: #1F2937;
                        white-space: nowrap;
                        margin-bottom: 3px;
                        min-height: 22px;
                        display: flex;
                        align-items: center;
                    `;
                    fractionLabel.textContent = fractionStr;

                    const mmLabel = document.createElement('div');
                    mmLabel.style.cssText = `
                        font-size: 11px;
                        color: #059669;
                        font-weight: 500;
                        white-space: nowrap;
                        margin-bottom: 3px;
                        min-height: 18px;
                    `;
                    mmLabel.textContent = mm.toFixed(1);

                    const decimalLabel = document.createElement('div');
                    decimalLabel.style.cssText = `
                        font-size: 10px;
                        color: #6B7280;
                        white-space: nowrap;
                        min-height: 16px;
                    `;
                    decimalLabel.textContent = decimal.toFixed(3);

                    markDiv.appendChild(line);
                    markDiv.appendChild(fractionLabel);
                    markDiv.appendChild(mmLabel);
                    markDiv.appendChild(decimalLabel);

                    rulerDiv.appendChild(markDiv);
                }
            }

            container.appendChild(rulerDiv);
        });
    </script>

    <style>
        #rulerContainer::-webkit-scrollbar {
            height: 8px;
        }

        #rulerContainer::-webkit-scrollbar-track {
            background: #F3F4F6;
        }

        #rulerContainer::-webkit-scrollbar-thumb {
            background: #9CA3AF;
            border-radius: 4px;
        }

        #rulerContainer::-webkit-scrollbar-thumb:hover {
            background: #6B7280;
        }
    </style>
</x-app-layout>