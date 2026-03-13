<x-modal
    id="change_role"
    title="Change Role"
    :buttons="[
        [
            'label' => 'Save',
            'type' => 'submit',
            'color' => 'success',
            'form' => 'changeRoleForm'
        ]
    ]"
>
    <form action="{{ route('user.change_role') }}" method="POST" enctype="multipart/form-data" class="space-y-2" id="changeRoleForm">
        @csrf

        {{-- Role --}}
        <div>
            <label class="label">
                <span class="label-text">Role</span>
            </label>
            <input type="text" value="{{ $users->id }}" name="user_id">
            <x-select name="role" size="sm" caption="Select Role" >
                @foreach ($rolesList as $r)
                    <option value="{{ $r->name }}" {{ $role->contains('name', $r->name) ? 'selected' : '' }}>
                        {{ strtoupper($r->name) }}
                    </option>
                @endforeach
            </x-select>
        </div>

    </form>
</x-modal>
