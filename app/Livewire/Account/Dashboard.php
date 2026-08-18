<?php

namespace App\Livewire\Account;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.storefront')]
#[Title('Akun Saya — Way Kopi')]
class Dashboard extends Component
{
    use WithPagination;

    public string $activeTab = 'orders'; // 'orders', 'addresses', 'profile'

    // Address Form State
    public bool $showAddressModal = false;

    public ?int $editingAddressId = null;

    public string $label = 'Rumah';

    public string $recipient_name = '';

    public string $phone = '';

    public string $full_address = '';

    public string $province = '';

    public string $city = '';

    public string $district = '';

    public string $postal_code = '';

    public bool $is_default = false;

    // Profile Form State
    public string $name = '';

    public string $email = '';

    public string $userPhone = '';

    public string $current_password = '';

    public string $new_password = '';

    public string $new_password_confirmation = '';

    public string $statusMessage = '';

    public string $errorMessage = '';

    public function mount(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->userPhone = $user->phone ?? '';
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetErrorBag();
        $this->statusMessage = '';
        $this->errorMessage = '';
    }

    public function openAddressModal(?int $id = null): void
    {
        $this->resetAddressForm();
        $this->showAddressModal = true;

        if ($id) {
            /** @var Address|null $address */
            $address = Address::where('user_id', '=', Auth::id())->find($id);
            if ($address) {
                $this->editingAddressId = $address->id;
                $this->label = $address->label;
                $this->recipient_name = $address->recipient_name;
                $this->phone = $address->phone;
                $this->full_address = $address->full_address;
                $this->province = $address->province;
                $this->city = $address->city;
                $this->district = $address->district;
                $this->postal_code = $address->postal_code;
                $this->is_default = $address->is_default;
            }
        }
    }

    public function closeAddressModal(): void
    {
        $this->showAddressModal = false;
        $this->resetAddressForm();
    }

    protected function resetAddressForm(): void
    {
        $this->editingAddressId = null;
        $this->label = 'Rumah';
        $this->recipient_name = '';
        $this->phone = '';
        $this->full_address = '';
        $this->province = '';
        $this->city = '';
        $this->district = '';
        $this->postal_code = '';
        $this->is_default = false;
    }

    public function saveAddress(): void
    {
        $this->validate([
            'label' => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'full_address' => ['required', 'string', 'max:500'],
            'province' => ['required', 'string', 'max:100'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['required', 'string', 'max:100'],
            'postal_code' => ['required', 'string', 'max:10'],
        ]);

        if ($this->is_default) {
            Address::where('user_id', '=', Auth::id())->update(['is_default' => false]);
        }

        Address::updateOrCreate(
            ['id' => $this->editingAddressId, 'user_id' => Auth::id()],
            [
                'user_id' => Auth::id(),
                'label' => $this->label,
                'recipient_name' => $this->recipient_name,
                'phone' => $this->phone,
                'full_address' => $this->full_address,
                'province' => $this->province,
                'city' => $this->city,
                'district' => $this->district,
                'postal_code' => $this->postal_code,
                'is_default' => $this->is_default,
            ]
        );

        $this->statusMessage = 'Alamat pengiriman berhasil disimpan.';
        $this->closeAddressModal();
    }

    public function deleteAddress(int $id): void
    {
        Address::where('user_id', '=', Auth::id())->where('id', '=', $id)->delete();
        $this->statusMessage = 'Alamat pengiriman berhasil dihapus.';
    }

    public function updateProfile(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'userPhone' => ['nullable', 'string', 'max:30'],
        ]);

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->userPhone,
        ]);

        $this->statusMessage = 'Profil akun berhasil diperbarui.';
    }

    public function updatePassword(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $this->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'Kata sandi saat ini tidak cocok.');

            return;
        }

        $user->update([
            'password' => Hash::make($this->new_password),
        ]);

        $this->current_password = '';
        $this->new_password = '';
        $this->new_password_confirmation = '';
        $this->statusMessage = 'Kata sandi berhasil diubah.';
    }

    public function render(): View
    {
        $userId = Auth::id();
        /** @var User $user */
        $user = Auth::user();

        $orders = Order::with(['items.productVariant.product', 'shipment'])
            ->where(function ($q) use ($userId, $user) {
                $q->where('user_id', '=', $userId);
                if (! empty($user->email)) {
                    $q->orWhere('guest_email', '=', $user->email);
                }
            })
            ->latest()
            ->paginate(10);

        $addresses = Address::where('user_id', '=', $userId)->get();

        return view('livewire.account.dashboard', [
            'orders' => $orders,
            'addresses' => $addresses,
        ]);
    }
}
