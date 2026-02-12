<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Block;

class Warehouse3DController extends Controller
{
    public function index()
    {
        $blocks = Block::all();
        return view('warehouse.index', compact('blocks'));
    }

    public function storeTag(Request $request)
    {
        $request->validate([
            'block_name' => 'required|exists:blocks,name',
            'tag' => 'required|string|max:40'
        ]);

        $block = Block::where('name', $request->block_name)->firstOrFail();
        $tags = $block->tags ?? [];
        $tagToAdd = trim($request->tag);

        $exists = collect($tags)->contains(function ($t) use ($tagToAdd) {
            return strtolower($t) === strtolower($tagToAdd);
        });

        if (!$exists) {
            $tags[] = $tagToAdd;
            $block->tags = $tags;
            $block->save();

            return response()->json([
                'success' => true,
                'tags' => $tags
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'La etiqueta ya existe'
        ]);
    }

    public function deleteTag(Request $request)
    {
        $request->validate([
            'block_name' => 'required|exists:blocks,name',
            'tag' => 'required|string'
        ]);

        $block = Block::where('name', $request->block_name)->firstOrFail();
        $tags = $block->tags ?? [];

        $tags = array_values(array_filter($tags, function ($t) use ($request) {
            return $t !== $request->tag;
        }));

        $block->tags = $tags;
        $block->save();

        return response()->json([
            'success' => true,
            'tags' => $tags
        ]);
    }
}