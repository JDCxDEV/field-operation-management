<x-auth-layout>
    <verify-code
        validate-url="{{ route("validate-code") }}"
        type="{{ $auth_code->type }}"
        phone="{{ $auth_code->phone }}"
        email="{{ $auth_code->email }}"
        token="{{ request()->token }}"
        user="{{ $auth_code->user_id }}"
    >
    </verify-code>
</x-auth-layout>
