<?php

namespace App\Livewire\Pengelola;

use Livewire\Component;
use App\Models\Inventory;

class Beranda extends Component
{
    public $q = '';

    // ===== MODAL EDIT =====
    public $editId;
    public $editCategory; // 'barang' / 'ruangan'
    public $editName;
    public $editDescription;
    public $editStock;
    public $editCapacity;

    // ===== MODAL HAPUS =====
    public $deleteId;
    public $deleteName;

    protected function rules()
    {
        // rules dinamis biar validasi gak berat & sesuai kategori
        $rules = [
            'editId'          => 'required|exists:inventories,id',
            'editCategory'    => 'required|in:barang,ruangan',
            'editName'        => 'required|string|max:150',
            'editDescription' => 'nullable|string|max:500',
        ];

        if ($this->editCategory === 'barang') {
            $rules['editStock'] = 'required|integer|min:0';
        } else {
            $rules['editCapacity'] = 'required|integer|min:1';
        }

        return $rules;
    }

    // ===== OPEN EDIT =====
    public function openEdit($id)
    {
        // select kolom yang perlu aja (lebih cepat)
        $item = Inventory::select('id','category','name','description','stock','capacity')
            ->findOrFail($id);

        $this->editId          = $item->id;
        $this->editCategory    = $item->category;
        $this->editName        = $item->name;
        $this->editDescription = $item->description;
        $this->editStock       = $item->stock;
        $this->editCapacity    = $item->capacity;

        $this->dispatch('open-edit-modal');
    }

    // ===== SAVE EDIT =====
    public function saveEdit()
    {
        $this->validate();

        $item = Inventory::findOrFail($this->editId);

        $item->name        = $this->editName;
        $item->description = $this->editDescription;

        if ($this->editCategory === 'barang') {
            $item->stock    = (int) $this->editStock;
            $item->capacity = null;
        } else {
            $item->capacity = (int) $this->editCapacity;
            $item->stock    = null;
        }

        $item->save();

        session()->flash('success', 'Data berhasil diperbarui!');
        $this->dispatch('close-edit-modal');

        $this->reset([
            'editId','editCategory','editName',
            'editDescription','editStock','editCapacity'
        ]);
    }

    // ===== CONFIRM DELETE =====
    public function confirmDelete($id)
    {
        $item = Inventory::select('id','name')->findOrFail($id);

        $this->deleteId   = $item->id;
        $this->deleteName = $item->name;

        $this->dispatch('open-delete-modal');
    }

    // ===== DELETE ITEM =====
    public function deleteItem()
    {
        Inventory::whereKey($this->deleteId)->delete();

        session()->flash('success', 'Item berhasil dihapus!');
        $this->dispatch('close-delete-modal');

        $this->reset(['deleteId','deleteName']);
    }

    public function render()
    {
        // ambil sekali aja
        $items = Inventory::query()
            ->select('id','name','description','category','stock','capacity','image_path','is_active')
            ->when($this->q, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', "%{$this->q}%")
                      ->orWhere('description', 'like', "%{$this->q}%");
                });
            })
            ->orderBy('name')
            ->get();

        // pisah di memory (tanpa query ulang)
        $barangs   = $items->where('category', 'barang');
        $fasilitas = $items->where('category', 'ruangan');

        return view('livewire.pengelola.beranda', [
            'barangs'   => $barangs,
            'fasilitas' => $fasilitas,
        ])->layout('pengelola.layouts.pengelola');
    }
}
