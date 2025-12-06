<?php

namespace App\Livewire\Pengelola;

use App\Models\Inventory;
use Livewire\Component;
use Livewire\WithFileUploads;

class TambahHapus extends Component
{
    use WithFileUploads;

    public $category;      // Barang | Ruangan
    public $name;
    public $description;
    public $status = 'Tersedia'; // (belum disimpan di DB)
    public $stock;
    public $capacity;
    public $image;         // file upload

    protected function rules()
    {
        return [
            'category'    => 'required|in:Barang,Ruangan',
            'name'        => 'required|string|max:150',
            'description' => 'nullable|string|max:2000',
            'stock'       => $this->category === 'Barang'   ? 'required|integer|min:0' : 'nullable|integer',
            'capacity'    => $this->category === 'Ruangan'  ? 'required|integer|min:1' : 'nullable|integer',
            'image'       => 'nullable|image|max:5120', // 5 MB
        ];
    }

    public function save()
    {
        $this->validate();

        $path = $this->image
            ? $this->image->store('inventories', 'public') // storage/app/public/inventories
            : null;

        Inventory::create([
            'category'   => strtolower($this->category), // 'barang' / 'ruangan'
            'name'       => $this->name,
            'description'=> $this->description,
            'stock'      => $this->category === 'Barang'  ? (int) $this->stock    : null,
            'capacity'   => $this->category === 'Ruangan' ? (int) $this->capacity : null,
            'image_path' => $path,
        ]);

        session()->flash('success', 'Berhasil menambahkan data!');
        return redirect()->route('pengelola.beranda');
    }

    public function render()
    {
        return view('livewire.pengelola.tambah-hapus')
            ->layout('pengelola.layouts.pengelola');
    }
}
