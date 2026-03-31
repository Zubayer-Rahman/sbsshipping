<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('item_name', 'like', "%{$s}%")
                  ->orWhere('item_code', 'like', "%{$s}%");
            });
        }

        $items = $query->paginate(20);
        return view('items.list', compact('items'));
    }

    public function create()
    {
        return view('items.create');
    }

    public function store(Request $request)
    {
        $request->validate(['item_name' => 'required|string|max:255']);

        $data = $request->except('_token');
        $data['user_id'] = Auth::id();

        foreach ($data as $k => $v) {
            if ($v === '') $data[$k] = null;
        }

        Item::create($data);

        if ($request->input('action') === 'save_and_add') {
            return redirect()->route('items.create')
                ->with('success', 'Item saved! Add another.');
        }

        return redirect()->route('items.list')
            ->with('success', 'Item added successfully!');
    }

    public function edit(Item $item)
    {
        return view('items.edit', compact('item'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate(['item_name' => 'required|string|max:255']);

        $data = $request->except('_token', '_method');
        foreach ($data as $k => $v) {
            if ($v === '') $data[$k] = null;
        }

        $item->update($data);

        return redirect()->route('items.list')
            ->with('success', 'Item updated successfully!');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('items.list')
            ->with('success', 'Item deleted.');
    }
}