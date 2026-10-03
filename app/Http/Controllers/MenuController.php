<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index(Request $request)
{
    $sort = $request->get('sort', 'name'); 
    $direction = $request->get('direction', 'asc');

    $categories = \App\Models\Category::where('type', 'pemasukan')->get();

    $menus = Menu::with('category')
                ->join('kategori', 'menu.category_id', '=', 'kategori.id') 
                ->select('menu.*')
                ->orderBy($sort, $direction)
                ->get();

    return view('menu.index', compact('menus', 'categories', 'sort', 'direction'));
}

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => [
                'required',
                Rule::exists('kategori', 'id')->where(function ($query) {
                    return $query->where('type', 'pemasukan');
                }),
            ],
            'price' => 'required|numeric|min:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->except(['_token', 'image']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menu_images', 'public');
            $data['image'] = $path;
        }

        Menu::create($data);

        return redirect()->route('menu.index')->with('success', 'Menu berhasil ditambahkan!');
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => [
                'required',
                Rule::exists('kategori', 'id')->where(function ($query) {
                    return $query->where('type', 'pemasukan');
                }),
            ],
            'price' => 'required|numeric|min:100',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->except(['_token', '_method', 'image']);

        if ($request->hasFile('image')) {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $path = $request->file('image')->store('menu_images', 'public');
            $data['image'] = $path;
        }

        $menu->update($data);

        return redirect()->route('menu.index')->with('success', 'Menu berhasil diperbarui!');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->transactionDetails()->exists()) {
            return redirect()->route('menu.index')
                ->with('error', 'Menu "'.$menu->name.'" tidak bisa dihapus karena sudah memiliki riwayat transaksi pelanggan.');
        }

        try {
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }

            $menu->delete();

            return redirect()->route('menu.index')->with('success', 'Menu berhasil dihapus!');

        } catch (\Exception $e) {
            return redirect()->route('menu.index')->with('error', 'Gagal menghapus menu: Terjadi kesalahan sistem.');
        }
    }

    public function categoryIndex()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki akses ke Master Kategori.');
        }

        $categories = Category::orderBy('type', 'desc')->get();
        $incomeCategories = $categories->where('type', 'pemasukan');
        $expenseCategories = $categories->where('type', 'pengeluaran');

        return view('master.kategori.index', compact('categories', 'incomeCategories', 'expenseCategories'));
    }

    public function categoryStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:pemasukan,pengeluaran',
        ]);

        Category::create([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        return redirect()->route('master.kategori.index')->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function categoryUpdate(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:pemasukan,pengeluaran',
        ]);

        $category->update([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        return redirect()->route('master.kategori.index')->with('success', 'Kategori berhasil diperbarui!');
    }

    public function categoryDestroy(Category $category)
    {
        try {
            if ($category->menus()->exists() || $category->expenses()->exists()) {
                return back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan dalam transaksi/menu.');
            }

            $category->delete();

            return redirect()->route('master.kategori.index')->with('success', 'Kategori berhasil dihapus.');

        } catch (\Illuminate\Database\QueryException $e) {
            return back()->with('error', 'Gagal menghapus kategori: Pastikan tidak ada menu atau pengeluaran yang terkait.');
        }
    }
}
