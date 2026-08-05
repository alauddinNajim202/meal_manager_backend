<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Url;
use Livewire\WithPagination;
use App\Models\User;
use Exception;
use FontLib\Table\Type\name;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;
use Str;

class Crud extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    #[Url(history: true, keep: true)]
    public $search = '';

    public $perPage = 10;

    public $name, $email, $password, $password_confirmation, $avatar;

    public $agree = false;

    public function mount()
    {
        $this->search = request()->query('search', $this->search);
    }


    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = User::where('id', '!=', auth('web')->id());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->orderBy('id', 'desc')->paginate($this->perPage);

        return view('livewire.crud', compact('users'));
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try{
            $filepath = $this->avatar->store('avatars', 'public');
            User::create([
                'name' => $this->name,
                'slug' => str()->slug($this->name),
                'email' => $this->email,
                'password' => bcrypt($this->password),
                'avatar' => $filepath
            ]);

            session()->put('success', 'User created successfully');
            $this->reset(['name', 'email', 'password', 'password_confirmation', 'avatar']);

        } catch (Exception $e) {
           session()->put('error', $e->getMessage());
           return;
        }
    }

    public function resetForm()
    {
        session()->put('success', 'User created successfully');
        $this->reset(['name', 'email', 'password', 'password_confirmation', 'avatar']);
    }

    public function delete(User $user)
    {
        $user->delete();
        session()->put('success', 'User deleted successfully');
    }

    public function download($filepath)
    {
        return Storage::download($filepath);
    }
}

