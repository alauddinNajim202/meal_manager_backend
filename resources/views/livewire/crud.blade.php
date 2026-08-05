<div>
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row">
        <div class="col-5">
            <div class="card product-sales-main">
                <div class="card-header border-bottom">
                    <div class="card-body">
                        <form wire:submit.prevent="store" enctype="multipart/form-data">

                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" wire:model="name" class="form-control">
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" wire:model="email" class="form-control">
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Avatar</label>
                                <input type="file" wire:model="avatar" class="form-control" accept="image/*">
                                <div wire:loading wire:target="avatar" class="text-muted mt-1">
                                    <small>Uploading...</small>
                                </div>
                                @if($avatar)
                                    <div class="mt-2">
                                        <img src="{{ $avatar->temporaryUrl() }}" width="50" class="rounded" alt="Avatar preview">
                                    </div>
                                @endif
                                @error('avatar') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" wire:model="password" class="form-control">
                                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirm Password</label>
                                <input type="password" wire:model.defer="password_confirmation" class="form-control">
                                @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" wire:model.live="agree" id="agreeCheck">
                                <label class="form-check-label" for="agreeCheck">
                                    I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">terms and conditions</a>
                                </label>
                                @error('agree') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            @if($agree)
                                <p wire:tarnsition class="text-success">You have agreed to the terms and conditions.</p>
                            @endif

                            <button class="btn btn-primary" type="submit" wire:loading.attr="disabled" wire:target="store">
                                <span wire:loading.remove wire:target="store">Create User</span>
                                <span wire:loading wire:target="store">Creating...</span>
                            </button>

                            <button class="btn btn-secondary" type="reset" wire:loading.attr="disabled" wire:click="resetForm">Reset</button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-7">
            <div class="card product-sales-main">
                <div class="card-header border-bottom">
                    <div class="btn-group" role="group" aria-label="Basic example">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal-import">Import</button>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal-export">Export</button>
                    </div>
                    <div class="card-options ms-auto">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Add</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="me-auto">
                            <select wire:model.live="perPage" class="form-select" style="width: 100px">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div class="ms-auto">
                            <input type="text" wire:model.live="search" class="form-control" placeholder="Search">
                        </div>
                    </div>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Avatar</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>
                                <th scope="col" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>
                                    @if($user->avatar)
                                        <img wire:click="download('{{ $user->avatar }}')" 
                                             src="{{ Storage::url($user->avatar) }}" 
                                             width="50" 
                                             class="rounded" 
                                             alt="avatar"
                                             style="cursor: pointer;">
                                    @else
                                        <span class="text-muted">No avatar</span>
                                    @endif
                                </td>
                                <td>{{ $user->name }}</td>
                                <td>{{ Str::limit($user->email, 20, '...') }}</td>
                                <td class="text-center">
                                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                                    <a href="#" class="btn btn-sm btn-danger" 
                                    wire:click="delete({{ $user->id }})" 
                                    wire:loading.attr="disabled" 
                                    wire:confirm="confirmDelete({{ $user->id }})">Delete</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No users found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>