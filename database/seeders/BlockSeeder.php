<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Block;

class BlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blocks = [
            ['name' => 'A1'], ['name' => 'A2'], ['name' => 'A3'], ['name' => 'A4'], ['name' => 'A5'], ['name' => 'A6'],
            ['name' => 'B1'], ['name' => 'B2'], ['name' => 'B3'], ['name' => 'B4'], ['name' => 'B5'], ['name' => 'B6'],
            ['name' => 'C1'], ['name' => 'C2'], ['name' => 'C3'], ['name' => 'C4'], ['name' => 'C5'], ['name' => 'C6'],
            ['name' => 'D1'], ['name' => 'D2'], ['name' => 'D3'], ['name' => 'D4'], ['name' => 'D5'], ['name' => 'D6'],
            ['name' => 'E1'], ['name' => 'E2'], ['name' => 'E3'], ['name' => 'E4'], ['name' => 'E5'], ['name' => 'E6'],
            ['name' => 'F1'], ['name' => 'F2'], ['name' => 'F3'], ['name' => 'F4'], ['name' => 'F5'], ['name' => 'F6'],
            ['name' => 'G1'], ['name' => 'G2'], ['name' => 'G3'], ['name' => 'G4'], ['name' => 'G5'], ['name' => 'G6'],
            ['name' => 'H1'], ['name' => 'H2'], ['name' => 'H3'], ['name' => 'H4'], ['name' => 'H5'], ['name' => 'H6'],
            ['name' => 'I1'], ['name' => 'I2'], ['name' => 'I3'], ['name' => 'I4'], ['name' => 'I5'], ['name' => 'I6'],
            ['name' => 'J1'], ['name' => 'J2'], ['name' => 'J3'], ['name' => 'J4'], ['name' => 'J5'], ['name' => 'J6'],
            ['name' => 'K1'], ['name' => 'K2'], ['name' => 'K3'], ['name' => 'K4'], ['name' => 'K5'], ['name' => 'K6'],
            ['name' => 'L1'], ['name' => 'L2'], ['name' => 'L3'], ['name' => 'L4'], ['name' => 'L5'], ['name' => 'L6'],
            ['name' => 'M1'], ['name' => 'M2'], ['name' => 'M3'], ['name' => 'M4'], ['name' => 'M5'], ['name' => 'M6'],
            ['name' => 'N1'], ['name' => 'N2'], ['name' => 'N3'], ['name' => 'N4'], ['name' => 'N5'], ['name' => 'N6'],
            ['name' => 'Ñ1'], ['name' => 'Ñ2'], ['name' => 'Ñ3'], ['name' => 'Ñ4'], ['name' => 'Ñ5'], ['name' => 'Ñ6'],
            ['name' => 'O1'], ['name' => 'O2'], ['name' => 'O3'], ['name' => 'O4'], ['name' => 'O5'], ['name' => 'O6'],
            ['name' => 'P1'], ['name' => 'P2'], ['name' => 'P3'], ['name' => 'P4'], ['name' => 'P5'], ['name' => 'P6'],
            ['name' => 'Q1'], ['name' => 'Q2'], ['name' => 'Q3'], ['name' => 'Q4'], ['name' => 'Q5'], ['name' => 'Q6'],
            ['name' => 'R1'], ['name' => 'R2'], ['name' => 'R3'], ['name' => 'R4'], ['name' => 'R5'], ['name' => 'R6'],
            ['name' => 'S1'], ['name' => 'S2'], ['name' => 'S3'], ['name' => 'S4'], ['name' => 'S5'], ['name' => 'S6'],
        ];

        foreach ($blocks as $block) {
            Block::updateOrCreate(['name' => $block['name']], $block);
        }
    }
}