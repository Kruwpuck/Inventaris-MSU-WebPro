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
    public $status = 'Tersedia'; // Default value
    public $stock;
    public $capacity;
    public $image;         // file upload

    // Rules validasi
    protected function rules()
    {
        return [
            'category' => 'required|in:Barang,Ruangan',
            'name' => 'required|string|max:150',
            'description' => 'nullable|string|max:2000',
            'status' => 'required|in:Tersedia,Tidak Tersedia,Perawatan,Dipakai',

            // Validasi Kondisional
            'stock' => $this->category === 'Barang' ? 'required|integer|min:0' : 'nullable',
            'capacity' => $this->category === 'Ruangan' ? 'required|integer|min:1' : 'nullable',

            // Validasi Gambar (Strict JPG/PNG, Max 5MB)
            'image' => 'nullable|mimes:jpg,jpeg,png|max:5120', 
        ];
    }

    // Custom Error Messages (Indonesian)
    protected function messages()
    {
        return [
            'image.mimes' => 'Format gambar harus JPG atau PNG.',
            'image.max'   => 'Ukuran gambar tidak boleh lebih dari 5MB.',
            'image.image' => 'File yang diupload harus berupa gambar.',
            // Validation messages for other fields
            'required' => 'Kolom ini wajib diisi.',
            'integer'  => 'Harus berupa angka.',
            'min'      => 'Nilai minimal tidak valid.',
        ];
    }

    // Real-time validation saat gambar dipilih
    public function updatedImage()
    {
        try {
            $this->validateOnly('image');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Reset image on error so the invalid file doesn't persist
            $this->image = null;
            throw $e;
        }
    }

    // Fungsi Reset Form (Tombol Bersihkan)
    public function resetForm()
    {
        $this->reset(['category', 'name', 'description', 'stock', 'capacity', 'image']);
        $this->status = 'Tersedia'; // Reset status ke default
    }

    public function save()
    {
        $this->validate();

        // Proses upload gambar
        $path = $this->image
            ? $this->image->store('inventories', 'public') // storage/app/public/inventories
            : null;

        // Simpan ke Database
        Inventory::create([
            'category' => strtolower($this->category),
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'stock' => $this->category === 'Barang' ? (int) $this->stock : null,
            'capacity' => $this->category === 'Ruangan' ? (int) $this->capacity : null,
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