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
            ['name' => 'bloque1', 'tags' => ['termocontraibles', 'terminales']],
            ['name' => 'bloque2', 'tags' => ['carga', 'inventario']],
            ['name' => 'bloque3', 'tags' => ['alimentación', 'descanso']],
        ];

        foreach ($blocks as $block) {
            Block::updateOrCreate(['name' => $block['name']], $block);
        }
    }
}