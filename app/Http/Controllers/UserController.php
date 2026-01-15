<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::when(request()->search, function ($query) {
            $search = request()->search;
            $query->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('kode_pengguna', 'like', '%' . $search . '%')
                  ->orWhere('no_telepon', 'like', '%' . $search . '%');
        })
        ->orderBy('role', 'desc') // Admin dulu
        ->orderBy('name')
        ->paginate(10);
        
        return view('users.index', compact('users'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'role' => 'required|in:admin,petugas',
            'status_akun' => 'required|in:Aktif,Nonaktif',
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:255',
            'password' => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required|same:password'
        ]);

        try {
            // Generate kode pengguna otomatis
            $kode_pengguna = User::generateKodePengguna($request->role);
            
            $user = User::create([
                'kode_pengguna' => $kode_pengguna,
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'status_akun' => $request->status_akun,
                'no_telepon' => $request->no_telepon,
                'alamat' => $request->alamat,
                'password' => Hash::make($request->password),
            ]);

            return redirect()->route('users.index')
                ->with('success', 'Pengguna ' . $user->name . ' berhasil ditambahkan!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'role' => 'required|in:admin,petugas',
            'status_akun' => 'required|in:Aktif,Nonaktif',
            'no_telepon' => 'nullable|string|max:15',
            'alamat' => 'nullable|string|max:255',
            'password' => 'nullable|string|min:6|confirmed',
            'password_confirmation' => 'nullable|same:password'
        ]);

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
                'status_akun' => $request->status_akun,
                'no_telepon' => $request->no_telepon,
                'alamat' => $request->alamat,
            ];

            // Update kode_pengguna jika role berubah
            if ($user->role != $request->role) {
                $data['kode_pengguna'] = User::generateKodePengguna($request->role);
            }

            // Update password jika diisi
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            return redirect()->route('users.index')
                ->with('success', 'Pengguna ' . $user->name . ' berhasil diperbarui!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        try {
            // Cek apakah user sedang login
            if (auth()->id() == $user->id) {
                return back()->with('error', 'Tidak dapat menghapus akun yang sedang login!');
            }

            $name = $user->name;
            $user->delete();

            return redirect()->route('users.index')
                ->with('success', 'Pengguna ' . $name . ' berhasil dihapus!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }

    /**
     * Nonaktifkan pengguna (soft delete alternative)
     */
    public function deactivate($id)
    {
        try {
            $user = User::findOrFail($id);
            
            // Cek apakah user sedang login
            if (auth()->id() == $user->id) {
                return back()->with('error', 'Tidak dapat menonaktifkan akun yang sedang login!');
            }

            $user->update(['status_akun' => 'Nonaktif']);

            return redirect()->route('users.index')
                ->with('success', 'Pengguna ' . $user->name . ' berhasil dinonaktifkan!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }

    /**
     * Aktifkan kembali pengguna
     */
    public function activate($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update(['status_akun' => 'Aktif']);

            return redirect()->route('users.index')
                ->with('success', 'Pengguna ' . $user->name . ' berhasil diaktifkan kembali!');
        } catch (\Throwable $th) {
            return back()->with('error', 'Terjadi kesalahan: ' . $th->getMessage());
        }
    }

    /**
     * Get user statistics
     */
    public function statistics()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('status_akun', 'Aktif')->count();
        $admins = User::where('role', 'admin')->count();
        $petugas = User::where('role', 'petugas')->count();
        
        return response()->json([
            'total' => $totalUsers,
            'active' => $activeUsers,
            'admins' => $admins,
            'petugas' => $petugas
        ]);
    }
}