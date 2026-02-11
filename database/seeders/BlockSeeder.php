<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BlockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $blocks = [
            ['name' => 'bloque1', 'display_name' => 'Edificio A', 'tags' => ['termocontraibles', 'terminales']],
            ['name' => 'bloque2', 'display_name' => 'Almacén', 'tags' => ['carga', 'inventario']],
            ['name' => 'bloque3', 'display_name' => 'Comedor', 'tags' => ['alimentación', 'descanso']],
        ];

        foreach ($blocks as $block) {
            \App\Models\Block::updateOrCreate(['name' => $block['name']], $block);
        }
    }
}
