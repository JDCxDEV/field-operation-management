<x-auth-layout>
    <reset-password
    submit-url="{{ route("password.update") }}"
    token="{{ $token }}"
    request-email="{{ $request->email }}"
    >
    </reset-password>
</x-auth-layout>
