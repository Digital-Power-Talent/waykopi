<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Kelola Pelanggan — Admin Way Kopi')]
class CustomerManager extends Component
{
    use WithPagination;

    public string $search = '';

    public string $roleFilter = '';

    // Modal state
    public bool $showCustomerModal = false;

    public ?int $editingUserId = null;

    public string $name = '';

    public string $email = '';

    public string $phone = '';

    public string $role = 'customer';

    public string $password = '';

    public string $statusMessage = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function openCustomerModal(?int $userId = null): void
    {
        $this->resetForm();
        $this->showCustomerModal = true;

        if ($userId) {
            /** @var User|null $user */
            $user = User::query()->find($userId, ['*']);
            if ($user) {
                $this->editingUserId = $user->id;
                $this->name = $user->name;
                $this->email = $user->email;
                $this->phone = $user->phone ?? '';
                $this->role = $user->role ?? 'customer';
            }
        }
    }

    public function closeCustomerModal(): void
    {
        $this->showCustomerModal = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->editingUserId = null;
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->role = 'customer';
        $this->password = '';
        $this->resetValidation();
    }

    public function saveCustomer(): void
    {
        $passwordRules = $this->editingUserId
            ? ['nullable', 'string', 'min:6']
            : ['required', 'string', 'min:6'];

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->editingUserId],
            'phone' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'string', 'in:customer,admin'],
            'password' => $passwordRules,
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Kata sandi wajib diisi untuk akun baru.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'role' => $this->role,
        ];

        if (! empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $user = User::query()->updateOrCreate(
            ['id' => $this->editingUserId],
            $data
        );

        $actionText = $this->editingUserId ? 'diperbarui' : 'ditambahkan';
        $this->statusMessage = "Akun pelanggan '{$user->name}' berhasil {$actionText}.";
        $this->closeCustomerModal();
    }

    public function deleteCustomer(int $userId): void
    {
        if ($userId === Auth::id()) {
            $this->statusMessage = 'Anda tidak dapat menghapus akun admin yang sedang login.';

            return;
        }

        /** @var User|null $user */
        $user = User::query()->find($userId, ['*']);
        if ($user) {
            $name = $user->name;
            User::destroy($userId);
            $this->statusMessage = "Akun pelanggan '{$name}' berhasil dihapus.";
        }
    }

    public function render(): View
    {
        $query = User::query()->latest('id');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('phone', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->roleFilter) {
            $query->where('role', $this->roleFilter);
        }

        $users = $query->paginate(10);

        return view('livewire.admin.customer-manager', [
            'users' => $users,
        ]);
    }
}
